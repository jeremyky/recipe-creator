<?php
/**
 * Front controller for Recipe Creator
 * Deployed URL: https://cs4640.cs.virginia.edu/juh7hc/
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

require __DIR__ . '/lib/session.php';
require __DIR__ . '/lib/util.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/repo.php';
require __DIR__ . '/lib/validate.php';

// Get action from query string
$action = $_GET['action'] ?? 'home';

// Route handling
switch ($action) {
    case 'home':
        render('home');
        break;
    
    case 'recipes':
        // Handle cuisine filter cookie (state management)
        if (isset($_GET['cuisine'])) {
            setcookie('last_cuisine', $_GET['cuisine'], time() + 60 * 60 * 24 * 30, '/');
        }
        $last_cuisine = $_COOKIE['last_cuisine'] ?? '';
        
        $filters = [];
        if (!empty($_GET['search'])) {
            $filters['q'] = $_GET['search'];
        }
        if (!empty($_GET['cuisine'])) {
            $filters['cuisine'] = $_GET['cuisine'];
        }
        
        $recipes = get_recipes(user_id(), $filters);
        render('recipes', ['recipes' => $recipes, 'last_cuisine' => $last_cuisine]);
        break;
    
    case 'upload':
        $flash = get_flash();
        render('upload', ['flash' => $flash, 'old' => $_SESSION['old_input'] ?? []]);
        unset($_SESSION['old_input']);
        break;
    
    case 'upload_submit':
        require_post();
        
        // CSRF check
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid form submission');
            redirect('index.php?action=upload');
        }
        
        $mode = $_GET['mode'] ?? 'manual';
        [$errors, $clean] = validate_recipe($_POST, $mode);
        
        if (!empty($errors)) {
            $_SESSION['old_input'] = $_POST;
            flash('errors', $errors);
            redirect('index.php?action=upload');
        }
        
        // Save recipe
        if ($mode === 'url') {
            // For URL mode, create a placeholder recipe (URL parsing would be implemented later)
            $clean['title'] = 'Imported Recipe';
            $clean['steps'] = 'Recipe imported from: ' . $clean['url'];
            $clean['ingredients'] = '';
        }
        
        $recipeId = save_recipe(user_id(), $clean);
        if ($recipeId > 0 && $mode === 'manual' && !empty($clean['ingredients'])) {
            $ingredient_lines = explode("\n", $clean['ingredients']);
            save_recipe_ingredients($recipeId, $ingredient_lines);
        }
        
        if ($recipeId > 0) {
            flash('success', 'Recipe saved successfully!');
        } else {
            flash('error', 'Database not available. Recipe not saved. (Local development mode)');
        }
        redirect('index.php?action=recipes');
        break;
    
    case 'pantry':
        $flash = get_flash();
        $items = get_pantry(user_id());
        render('pantry', ['items' => $items, 'flash' => $flash, 'old' => $_SESSION['old_input'] ?? []]);
        unset($_SESSION['old_input']);
        break;
    
    case 'pantry_add':
        require_post();
        
        // CSRF check
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid form submission');
            redirect('index.php?action=pantry');
        }
        
        [$errors, $clean] = validate_pantry($_POST);
        
        if (!empty($errors)) {
            $_SESSION['old_input'] = $_POST;
            flash('errors', $errors);
            redirect('index.php?action=pantry');
        }
        
        $itemId = add_pantry_item(user_id(), $clean);
        if ($itemId > 0) {
            flash('success', 'Ingredient added to pantry!');
        } else {
            flash('error', 'Database not available. Item not saved. (Local development mode)');
        }
        redirect('index.php?action=pantry');
        break;
    
    case 'pantry_update':
        require_post();
        
        // CSRF check
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid form submission');
            redirect('index.php?action=pantry');
        }
        
        $itemId = intval($_POST['item_id'] ?? 0);
        $quantity = floatval($_POST['quantity'] ?? 0);
        $unit = trim($_POST['unit'] ?? '');
        
        if ($itemId > 0 && $quantity >= 0 && !empty($unit)) {
            $allowed_units = ['lb', 'oz', 'g', 'kg', 'cup', 'tbsp', 'tsp', 'piece', 'ml'];
            if (in_array($unit, $allowed_units)) {
                // Get existing item to get ingredient name
                $pdo = db_connect();
                $stmt = $pdo->prepare("SELECT ingredient FROM pantry_item WHERE id = :id AND user_id = :user_id");
                $stmt->execute(['id' => $itemId, 'user_id' => user_id()]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    update_pantry_item($itemId, user_id(), [
                        'ingredient' => $existing['ingredient'],
                        'quantity' => $quantity,
                        'unit' => $unit
                    ]);
                    flash('success', 'Pantry item updated');
                }
            } else {
                flash('error', 'Invalid unit');
            }
        } else {
            flash('error', 'Invalid update data');
        }
        redirect('index.php?action=pantry');
        break;
    
    case 'pantry_delete':
        require_post();
        
        $itemId = intval($_POST['item_id'] ?? 0);
        if ($itemId > 0) {
            delete_pantry_item($itemId, user_id());
            flash('success', 'Ingredient removed from pantry');
        }
        redirect('index.php?action=pantry');
        break;
    
    case 'match':
        $pantry_items = get_pantry(user_id());
        $pantry_ingredient_names = array_map(function($item) {
            return strtolower(trim($item['ingredient']));
        }, $pantry_items);
        
        $max_missing = intval($_GET['max-missing'] ?? 3);
        
        // Simple matching logic: get recipes where most ingredients match
        $all_recipes = get_recipes(user_id());
        $matched_recipes = [];
        
        foreach ($all_recipes as $recipe) {
            $recipe_ingredients = get_recipe_ingredients($recipe['id']);
            $recipe_ingredient_names = array_map(function($line) {
                // Extract ingredient name from lines like "2 tbsp olive oil" or "olive oil"
                $parts = preg_split('/\s+/', strtolower(trim($line)), 2);
                return $parts[count($parts) - 1]; // Get last part (usually the ingredient name)
            }, $recipe_ingredients);
            
            $matched = 0;
            foreach ($recipe_ingredient_names as $ing_name) {
                foreach ($pantry_ingredient_names as $pantry_name) {
                    if (strpos($ing_name, $pantry_name) !== false || strpos($pantry_name, $ing_name) !== false) {
                        $matched++;
                        break;
                    }
                }
            }
            
            $missing_count = count($recipe_ingredients) - $matched;
            
            if ($missing_count <= $max_missing) {
                $recipe['missing_count'] = $missing_count;
                $matched_recipes[] = $recipe;
            }
        }
        
        render('match', ['recipes' => $matched_recipes, 'max_missing' => $max_missing]);
        break;
    
    case 'cook':
        $recipes = get_recipes(user_id());
        render('cook', ['recipes' => $recipes]);
        break;
    
    case 'chat':
        // Auto-authenticate chat access for this session (password stored server-side only)
        $required_password = $_ENV['CHAT_PASSWORD'] ?? getenv('CHAT_PASSWORD') ?? 'ShaunBoy123';
        if (!isset($_SESSION['chat_authenticated'])) {
            // Authenticate automatically (password never exposed to client)
            $_SESSION['chat_authenticated'] = true;
        }
        render('chat');
        break;
    
    case 'about':
        render('about');
        break;
    
    default:
        http_response_code(404);
        echo "Page not found";
        exit;
}


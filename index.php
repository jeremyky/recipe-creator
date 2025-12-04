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
require __DIR__ . '/lib/auth.php';

// Get action from query string
$action = $_GET['action'] ?? 'landing';

// Route handling
switch ($action) {
    // Public landing page
    case 'landing':
        if (is_authenticated()) {
            redirect('index.php?action=home');
        }
        include __DIR__ . '/views/landing.php';
        exit;
    
    // Blog articles (public)
    case 'blog_pantry':
        include __DIR__ . '/views/blog_pantry.php';
        exit;
    
    case 'blog_ai':
        include __DIR__ . '/views/blog_ai.php';
        exit;
    
    case 'blog_waste':
        include __DIR__ . '/views/blog_waste.php';
        exit;
    
    // Authentication pages
    case 'login':
        if (is_authenticated()) {
            redirect('index.php?action=home');
        }
        $flash = get_flash();
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        include __DIR__ . '/views/login.php';
        exit;
    
    case 'signup':
        if (is_authenticated()) {
            redirect('index.php?action=home');
        }
        $flash = get_flash();
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        include __DIR__ . '/views/signup.php';
        exit;
    
    case 'login_submit':
        require_post();
        
        // CSRF check
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid form submission');
            redirect('index.php?action=login');
        }
        
        [$errors, $clean] = validate_login($_POST);
        
        if (!empty($errors)) {
            $_SESSION['old_input'] = $_POST;
            flash('error', $errors[0]);
            redirect('index.php?action=login');
        }
        
        // Authenticate user
        $result = authenticate_user($clean['email'], $clean['password']);
        
        if (!$result['success']) {
            $_SESSION['old_input'] = $_POST;
            flash('error', $result['error']);
            redirect('index.php?action=login');
        }
        
        // Login user
        $remember = isset($_POST['remember']) && $_POST['remember'] == '1';
        login_user($result['user']['id'], $remember);
        
        flash('success', 'Welcome back!');
        
        // Redirect to intended page or home
        $redirect_to = $_SESSION['redirect_after_login'] ?? 'index.php?action=home';
        unset($_SESSION['redirect_after_login']);
        redirect($redirect_to);
        break;
    
    case 'signup_submit':
        require_post();
        
        // CSRF check
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid form submission');
            redirect('index.php?action=signup');
        }
        
        [$errors, $clean] = validate_registration($_POST);
        
        if (!empty($errors)) {
            $_SESSION['old_input'] = $_POST;
            flash('errors', $errors);
            redirect('index.php?action=signup');
        }
        
        // Register user
        $result = register_user($clean['name'], $clean['email'], $clean['password']);
        
        if (!$result['success']) {
            $_SESSION['old_input'] = $_POST;
            flash('error', $result['error']);
            redirect('index.php?action=signup');
        }
        
        // Auto-login after registration
        login_user($result['user_id']);
        
        flash('success', 'Account created successfully! Welcome to Recipe Creator!');
        redirect('index.php?action=home');
        break;
    
    case 'logout':
        logout_user();
        flash('success', 'You have been logged out');
        redirect('index.php?action=landing');
        break;
    
    // Protected app pages (require authentication)
    case 'home':
        require_auth();
        render('home');
        break;
    
    case 'recipes':
        require_auth();
        
        // Build filters array from query parameters
        $filters = [];
        
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (!empty($_GET['cuisine'])) {
            $filters['cuisine'] = $_GET['cuisine'];
        }
        
        if (!empty($_GET['favorites_only'])) {
            $filters['favorites_only'] = true;
        }
        
        if (!empty($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }
        
        $recipes = get_recipes(user_id(), $filters);
        render('recipes', ['recipes' => $recipes]);
        break;
    
    case 'upload':
        require_auth();
        $flash = get_flash();
        render('upload', ['flash' => $flash, 'old' => $_SESSION['old_input'] ?? []]);
        unset($_SESSION['old_input']);
        break;
    
    case 'upload_submit':
        require_auth();
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
        require_auth();
        $flash = get_flash();
        $items = get_pantry(user_id());
        render('pantry', ['items' => $items, 'flash' => $flash, 'old' => $_SESSION['old_input'] ?? []]);
        unset($_SESSION['old_input']);
        break;
    
    case 'pantry_add':
        require_auth();
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
        require_auth();
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
        require_auth();
        require_post();
        
        $itemId = intval($_POST['item_id'] ?? 0);
        if ($itemId > 0) {
            delete_pantry_item($itemId, user_id());
            flash('success', 'Ingredient removed from pantry');
        }
        redirect('index.php?action=pantry');
        break;
    
    case 'match':
        require_auth();
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
    
    case 'recipe_detail':
        require_auth();
        $recipeId = intval($_GET['id'] ?? 0);
        if ($recipeId <= 0) {
            redirect('index.php?action=recipes');
        }
        
        $recipe = get_recipe($recipeId, user_id());
        if (!$recipe) {
            flash('error', 'Recipe not found');
            redirect('index.php?action=recipes');
        }
        
        $ingredients = get_recipe_ingredients($recipeId);
        render('recipe_detail', ['recipe' => $recipe, 'ingredients' => $ingredients]);
        break;
    
    case 'recipe_edit':
        require_auth();
        $recipeId = $_GET['id'] ?? '';
        
        // Handle local recipes (for local testing)
        if (strpos($recipeId, 'local_') === 0) {
            // Local recipe - pass minimal data, JavaScript will handle it
            render('recipe_edit', ['recipe' => ['id' => $recipeId]]);
            break;
        }
        
        // Database recipe
        $recipeId = intval($recipeId);
        if ($recipeId <= 0) {
            redirect('index.php?action=recipes');
        }
        
        $recipe = get_recipe($recipeId, user_id());
        if (!$recipe) {
            flash('error', 'Recipe not found');
            redirect('index.php?action=recipes');
        }
        
        // Get ingredients as formatted text
        $ingredients = get_recipe_ingredients($recipeId);
        $recipe['ingredients_text'] = implode("\n", $ingredients);
        
        render('recipe_edit', ['recipe' => $recipe]);
        break;
    
    case 'recipe_update':
        require_auth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=recipes');
        }
        
        $recipeId = intval($_GET['id'] ?? 0);
        if ($recipeId <= 0) {
            redirect('index.php?action=recipes');
        }
        
        // Verify recipe ownership
        $recipe = get_recipe($recipeId, user_id());
        if (!$recipe) {
            flash('error', 'Recipe not found or you do not have permission to edit it');
            redirect('index.php?action=recipes');
        }
        
        // Validate CSRF
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('index.php?action=recipe_edit&id=' . $recipeId);
        }
        
        // Get and validate input
        $title = trim($_POST['title'] ?? '');
        $cuisine = trim($_POST['cuisine'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $ingredients = trim($_POST['ingredients'] ?? '');
        $steps = trim($_POST['steps'] ?? '');
        
        $errors = [];
        
        if (empty($title)) {
            $errors['title'] = 'Recipe title is required';
        } elseif (strlen($title) < 3) {
            $errors['title'] = 'Recipe title must be at least 3 characters';
        }
        
        if (empty($ingredients)) {
            $errors['ingredients'] = 'Ingredients are required';
        }
        
        if (empty($steps)) {
            $errors['steps'] = 'Recipe steps are required';
        } elseif (strlen($steps) < 10) {
            $errors['steps'] = 'Recipe steps must be at least 10 characters';
        }
        
        if (!empty($errors)) {
            flash('errors', $errors);
            set_old($_POST);
            redirect('index.php?action=recipe_edit&id=' . $recipeId);
        }
        
        // Update recipe in database
        $updated = update_recipe($recipeId, user_id(), [
            'title' => $title,
            'cuisine' => $cuisine,
            'image_url' => $image,
            'ingredients' => $ingredients,
            'steps' => $steps
        ]);
        
        if ($updated) {
            flash('success', 'Recipe updated successfully!');
            redirect('index.php?action=recipe_detail&id=' . $recipeId);
        } else {
            flash('error', 'Failed to update recipe. Please try again.');
            set_old($_POST);
            redirect('index.php?action=recipe_edit&id=' . $recipeId);
        }
        break;
    
    case 'recipe_delete':
        require_auth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=recipes');
        }
        
        $recipeId = intval($_GET['id'] ?? 0);
        if ($recipeId <= 0) {
            redirect('index.php?action=recipes');
        }
        
        // Verify recipe ownership
        $recipe = get_recipe($recipeId, user_id());
        if (!$recipe) {
            flash('error', 'Recipe not found or you do not have permission to delete it');
            redirect('index.php?action=recipes');
        }
        
        // Validate CSRF
        if (!verify_csrf($_POST['csrf'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('index.php?action=recipe_detail&id=' . $recipeId);
        }
        
        // Delete recipe
        $deleted = delete_recipe($recipeId, user_id());
        
        if ($deleted) {
            flash('success', 'Recipe "' . $recipe['title'] . '" has been deleted');
            redirect('index.php?action=recipes');
        } else {
            flash('error', 'Failed to delete recipe. Please try again.');
            redirect('index.php?action=recipe_detail&id=' . $recipeId);
        }
        break;
    
    case 'cook':
        require_auth();
        $recipes = get_recipes(user_id());
        render('cook', ['recipes' => $recipes]);
        break;
    
    case 'cook_session':
        require_auth();
        $recipeId = intval($_GET['id'] ?? 0);
        if ($recipeId <= 0) {
            redirect('index.php?action=cook');
        }
        
        $recipe = get_recipe($recipeId, user_id());
        if (!$recipe) {
            flash('error', 'Recipe not found');
            redirect('index.php?action=cook');
        }
        
        $ingredients = get_recipe_ingredients($recipeId);
        render('cook_session', ['recipe' => $recipe, 'ingredients' => $ingredients]);
        break;
    
    case 'chat':
        require_auth();
        // Auto-authenticate chat access for this session (password stored server-side only)
        $required_password = $_ENV['CHAT_PASSWORD'] ?? getenv('CHAT_PASSWORD') ?? 'ShaunBoy123';
        if (!isset($_SESSION['chat_authenticated'])) {
            // Authenticate automatically (password never exposed to client)
            $_SESSION['chat_authenticated'] = true;
        }
        render('chat');
        break;
    
    case 'about':
        require_auth();
        render('about');
        break;
    
    default:
        http_response_code(404);
        echo "Page not found";
        exit;
}


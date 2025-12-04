<?php
/**
 * Repository functions for database operations
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 3
 */

require_once __DIR__ . '/db.php';

/**
 * Get recipes for a user with optional filters
 * @param int $userId
 * @param array $filters ['q' => search, 'cuisine' => filter]
 * @return array
 */
function get_recipes($userId, $filters = []) {
    $pdo = db_connect();
    if ($pdo === null) {
        return []; // Return empty array if no database connection
    }
    
    $sql = "SELECT r.*, 
                   COUNT(ri.id) as ingredient_count,
                   CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END as is_favorite
            FROM recipe r
            LEFT JOIN recipe_ingredient ri ON r.id = ri.recipe_id
            LEFT JOIN favorite f ON r.id = f.recipe_id AND f.user_id = :user_id
            WHERE r.user_id = :user_id";
    
    $params = ['user_id' => $userId];
    
    // Search filter (title or ingredients)
    if (!empty($filters['search'])) {
        $sql .= " AND (r.title ILIKE :search OR EXISTS (
                    SELECT 1 FROM recipe_ingredient ri2 
                    WHERE ri2.recipe_id = r.id AND ri2.line ILIKE :search
                  ))";
        $params['search'] = '%' . $filters['search'] . '%';
    }
    
    // Cuisine filter
    if (!empty($filters['cuisine'])) {
        $sql .= " AND r.cuisine = :cuisine";
        $params['cuisine'] = $filters['cuisine'];
    }
    
    // Favorites only filter
    if (!empty($filters['favorites_only'])) {
        $sql .= " AND f.id IS NOT NULL";
    }
    
    $sql .= " GROUP BY r.id, f.id";
    
    // Sorting (favorites always first, then apply chosen sort)
    $sort = $filters['sort'] ?? 'date_desc';
    $sql .= " ORDER BY is_favorite DESC"; // Favorites first always
    
    switch ($sort) {
        case 'name_asc':
            $sql .= ", LOWER(r.title) ASC";
            break;
        case 'name_desc':
            $sql .= ", LOWER(r.title) DESC";
            break;
        case 'date_asc':
            $sql .= ", r.created_at ASC";
            break;
        case 'ingredients_asc':
            $sql .= ", COUNT(ri.id) ASC";
            break;
        case 'ingredients_desc':
            $sql .= ", COUNT(ri.id) DESC";
            break;
        case 'date_desc':
        default:
            $sql .= ", r.created_at DESC";
            break;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get a single recipe by ID
 * @param int $recipeId
 * @param int $userId
 * @return array|false
 */
function get_recipe($recipeId, $userId) {
    $pdo = db_connect();
    if ($pdo === null) {
        return false; // Return false if no database connection
    }
    
    $stmt = $pdo->prepare("SELECT * FROM recipe WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $recipeId, 'user_id' => $userId]);
    return $stmt->fetch();
}

/**
 * Get ingredients for a recipe
 * @param int $recipeId
 * @return array
 */
function get_recipe_ingredients($recipeId) {
    $pdo = db_connect();
    if ($pdo === null) {
        return []; // Return empty array if no database connection
    }
    
    $stmt = $pdo->prepare("SELECT line FROM recipe_ingredient WHERE recipe_id = :id ORDER BY id");
    $stmt->execute(['id' => $recipeId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Save a new recipe
 * @param int $userId
 * @param array $data
 * @return int recipe ID
 */
function save_recipe($userId, $data) {
    $pdo = db_connect();
    if ($pdo === null) {
        return 0; // Return 0 if no database connection
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO recipe (user_id, title, image_url, steps)
        VALUES (:user_id, :title, :image_url, :steps)
        RETURNING id
    ");
    
    $stmt->execute([
        'user_id' => $userId,
        'title' => $data['title'],
        'image_url' => $data['image_url'] ?? null,
        'steps' => $data['steps']
    ]);
    
    return $stmt->fetchColumn();
}

/**
 * Save recipe ingredients
 * @param int $recipeId
 * @param array $lines
 */
function save_recipe_ingredients($recipeId, $lines) {
    $pdo = db_connect();
    if ($pdo === null) {
        return; // Return early if no database connection
    }
    
    $stmt = $pdo->prepare("INSERT INTO recipe_ingredient (recipe_id, line) VALUES (:recipe_id, :line)");
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $stmt->execute(['recipe_id' => $recipeId, 'line' => $line]);
        }
    }
}

/**
 * Update a recipe
 * @param int $recipeId
 * @param int $userId
 * @param array $data
 * @return bool
 */
function update_recipe($recipeId, $userId, $data) {
    $pdo = db_connect();
    if ($pdo === null) {
        return false; // Return false if no database connection
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Update recipe basic info
        $stmt = $pdo->prepare("
            UPDATE recipe 
            SET title = :title, cuisine = :cuisine, image_url = :image_url, steps = :steps
            WHERE id = :id AND user_id = :user_id
        ");
        
        $stmt->execute([
            'id' => $recipeId,
            'user_id' => $userId,
            'title' => $data['title'],
            'cuisine' => $data['cuisine'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'steps' => $data['steps']
        ]);
        
        // Update ingredients if provided
        if (isset($data['ingredients'])) {
            // Delete existing ingredients
            $stmt = $pdo->prepare("DELETE FROM recipe_ingredient WHERE recipe_id = :recipe_id");
            $stmt->execute(['recipe_id' => $recipeId]);
            
            // Insert new ingredients (using 'line' column as per schema)
            $ingredientLines = explode("\n", $data['ingredients']);
            
            $stmt = $pdo->prepare("
                INSERT INTO recipe_ingredient (recipe_id, line)
                VALUES (:recipe_id, :line)
            ");
            
            foreach ($ingredientLines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                $stmt->execute([
                    'recipe_id' => $recipeId,
                    'line' => $line
                ]);
            }
        }
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating recipe: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a recipe
 * @param int $recipeId
 * @param int $userId
 * @return bool
 */
function delete_recipe($recipeId, $userId) {
    $pdo = db_connect();
    if ($pdo === null) {
        return false; // Return false if no database connection
    }
    
    $stmt = $pdo->prepare("DELETE FROM recipe WHERE id = :id AND user_id = :user_id");
    return $stmt->execute(['id' => $recipeId, 'user_id' => $userId]);
}

/**
 * Get pantry items for a user
 * @param int $userId
 * @return array
 */
function get_pantry($userId) {
    $pdo = db_connect();
    if ($pdo === null) {
        return []; // Return empty array if no database connection
    }
    
    $stmt = $pdo->prepare("SELECT * FROM pantry_item WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

/**
 * Add pantry item
 * @param int $userId
 * @param array $data
 * @return int item ID
 */
function add_pantry_item($userId, $data) {
    $pdo = db_connect();
    if ($pdo === null) {
        return 0; // Return 0 if no database connection
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO pantry_item (user_id, ingredient, quantity, unit)
        VALUES (:user_id, :ingredient, :quantity, :unit)
        RETURNING id
    ");
    
    $stmt->execute([
        'user_id' => $userId,
        'ingredient' => $data['ingredient'],
        'quantity' => $data['quantity'],
        'unit' => $data['unit']
    ]);
    
    return $stmt->fetchColumn();
}

/**
 * Delete pantry item
 * @param int $itemId
 * @param int $userId
 * @return bool
 */
function delete_pantry_item($itemId, $userId) {
    $pdo = db_connect();
    if ($pdo === null) {
        return false; // Return false if no database connection
    }
    
    $stmt = $pdo->prepare("DELETE FROM pantry_item WHERE id = :id AND user_id = :user_id");
    return $stmt->execute(['id' => $itemId, 'user_id' => $userId]);
}

/**
 * Update pantry item
 * @param int $itemId
 * @param int $userId
 * @param array $data
 * @return bool
 */
function update_pantry_item($itemId, $userId, $data) {
    $pdo = db_connect();
    if ($pdo === null) {
        return false; // Return false if no database connection
    }
    
    $stmt = $pdo->prepare("
        UPDATE pantry_item
        SET ingredient = :ingredient, quantity = :quantity, unit = :unit
        WHERE id = :id AND user_id = :user_id
    ");
    
    return $stmt->execute([
        'id' => $itemId,
        'user_id' => $userId,
        'ingredient' => $data['ingredient'],
        'quantity' => $data['quantity'],
        'unit' => $data['unit']
    ]);
}


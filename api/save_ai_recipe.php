<?php
/**
 * API endpoint to save AI-generated recipes
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

require __DIR__ . '/../lib/session.php';
require __DIR__ . '/../lib/util.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/repo.php';
require __DIR__ . '/../lib/validate.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'Method not allowed'], 405);
}

// Check authentication
if (!is_authenticated()) {
    json_out(['error' => 'Unauthorized'], 401);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['title', 'ingredients', 'steps'];
foreach ($required as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        json_out(['error' => "Field '$field' is required"], 400);
    }
}

// Extract and validate data
$title = trim($input['title']);
$cuisine = isset($input['cuisine']) ? trim($input['cuisine']) : null;
$ingredients = trim($input['ingredients']);
$steps = trim($input['steps']);
$user_id = user_id();

// Validate title
if (strlen($title) < 3) {
    json_out(['error' => 'Recipe title must be at least 3 characters'], 400);
}

if (strlen($title) > 200) {
    json_out(['error' => 'Recipe title is too long'], 400);
}

// Validate cuisine if provided
$valid_cuisines = ['italian', 'chinese', 'mexican', 'indian', 'thai', 'greek', 'american', 'other'];
if ($cuisine && !in_array(strtolower($cuisine), $valid_cuisines)) {
    $cuisine = 'other';
}

// Validate ingredients
if (strlen($ingredients) < 10) {
    json_out(['error' => 'Ingredients list is too short'], 400);
}

// Validate steps
if (strlen($steps) < 10) {
    json_out(['error' => 'Recipe steps are too short'], 400);
}

// Check for duplicates (same title for same user)
$existing_recipes = get_recipes($user_id);
foreach ($existing_recipes as $recipe) {
    if (strtolower($recipe['title']) === strtolower($title)) {
        json_out(['error' => 'You already have a recipe with this title'], 400);
    }
}

// Try to insert the recipe
try {
    $db = db();
    
    if (!$db) {
        // Local development mode without database - use localStorage
        json_out([
            'success' => true,
            'message' => 'Recipe saved to local storage',
            'recipe_id' => 'local_' . time(),
            'title' => $title,
            'mode' => 'local',
            'recipe' => [
                'title' => $title,
                'cuisine' => $cuisine,
                'ingredients' => $ingredients,
                'steps' => $steps,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
    
    // Insert recipe
    $stmt = $db->prepare('
        INSERT INTO recipe (user_id, title, cuisine, created_at)
        VALUES (?, ?, ?, NOW())
        RETURNING id
    ');
    
    $stmt->execute([$user_id, $title, $cuisine]);
    $recipe_id = $stmt->fetchColumn();
    
    if (!$recipe_id) {
        json_out(['error' => 'Failed to create recipe'], 500);
    }
    
    // Parse and insert ingredients
    $ingredient_lines = explode("\n", $ingredients);
    $ingredient_order = 0;
    
    foreach ($ingredient_lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $ingredient_order++;
        
        // Try to parse quantity, unit, and ingredient name
        // Format: "quantity unit ingredient" or just "ingredient"
        $parts = preg_split('/\s+/', $line, 3);
        
        if (count($parts) >= 3) {
            // Has quantity, unit, and name
            $quantity = $parts[0];
            $unit = $parts[1];
            $ingredient_name = $parts[2];
        } else if (count($parts) === 2) {
            // Has quantity and name (or unit and name)
            $quantity = $parts[0];
            $unit = '';
            $ingredient_name = $parts[1];
        } else {
            // Just ingredient name
            $quantity = '';
            $unit = '';
            $ingredient_name = $line;
        }
        
        $stmt = $db->prepare('
            INSERT INTO recipe_ingredient (recipe_id, ingredient, quantity, unit, ingredient_order)
            VALUES (?, ?, ?, ?, ?)
        ');
        
        $stmt->execute([$recipe_id, $ingredient_name, $quantity, $unit, $ingredient_order]);
    }
    
    // Parse and insert steps
    $step_lines = explode("\n", $steps);
    $step_order = 0;
    
    foreach ($step_lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $step_order++;
        
        $stmt = $db->prepare('
            INSERT INTO recipe_step (recipe_id, step_order, step_text)
            VALUES (?, ?, ?)
        ');
        
        $stmt->execute([$recipe_id, $step_order, $line]);
    }
    
    json_out([
        'success' => true,
        'message' => 'Recipe saved successfully',
        'recipe_id' => $recipe_id,
        'title' => $title
    ]);
    
} catch (PDOException $e) {
    error_log('Database error saving AI recipe: ' . $e->getMessage());
    json_out(['error' => 'Database error: ' . $e->getMessage()], 500);
}


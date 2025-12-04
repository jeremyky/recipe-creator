<?php
/**
 * API endpoint to save AI-generated recipes
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', '0');

// Start output buffering to catch any stray output
ob_start();

// Load dependencies
require __DIR__ . '/../lib/session.php';
require __DIR__ . '/../lib/util.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/repo.php';
require __DIR__ . '/../lib/auth.php';

// Function to send JSON response
function send_json($data, $code = 200) {
    // Log to file for debugging
    $log_file = sys_get_temp_dir() . '/recipe_api_debug.log';
    $log_entry = date('Y-m-d H:i:s') . " [HTTP $code] " . json_encode($data) . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Discard any buffered output
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    $json = json_encode($data);
    
    // Log the actual output
    file_put_contents($log_file, "  OUTPUT: " . strlen($json) . " bytes\n", FILE_APPEND);
    
    echo $json;
    exit();
}

// Helper function to log debug info
function debug_log_api($message) {
    $log_file = sys_get_temp_dir() . '/recipe_api_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " [DEBUG] " . $message . "\n", FILE_APPEND);
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['error' => 'Method not allowed'], 405);
}

// Check authentication (auto-login on localhost for testing)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    // Auto-login for local testing
    if (!is_authenticated()) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Demo User';
    }
} else {
    // Production: require authentication
    if (!is_authenticated()) {
        send_json(['error' => 'Unauthorized - Please log in'], 401);
    }
}

// Get and parse JSON input
$json_input = file_get_contents('php://input');
$input = json_decode($json_input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_json(['error' => 'Invalid JSON: ' . json_last_error_msg()], 400);
}

// Extract data
$title = isset($input['title']) ? trim($input['title']) : '';
$cuisine = isset($input['cuisine']) ? trim($input['cuisine']) : 'other';
$ingredients = isset($input['ingredients']) ? trim($input['ingredients']) : '';
$steps = isset($input['steps']) ? trim($input['steps']) : '';

// Log what we received
debug_log_api("Received recipe data:");
debug_log_api("  Title: '" . substr($title, 0, 100) . "' (length: " . strlen($title) . ")");
debug_log_api("  Cuisine: " . $cuisine);
debug_log_api("  Ingredients length: " . strlen($ingredients));
debug_log_api("  Steps length: " . strlen($steps));

// Validate required fields
if (empty($title)) {
    send_json(['error' => 'Recipe title is required'], 400);
}

if (empty($ingredients)) {
    send_json(['error' => 'Ingredients are required'], 400);
}

if (empty($steps)) {
    send_json(['error' => 'Steps are required'], 400);
}

// Validate lengths
if (strlen($title) < 3) {
    send_json(['error' => 'Title must be at least 3 characters'], 400);
}

if (strlen($title) > 2000) {
    send_json(['error' => 'Title is too long (max 2000 characters, got ' . strlen($title) . ')'], 400);
}

// Get user ID
$user_id = user_id();

// Try database connection
$db = db_connect();

// If no database, return local storage mode
if (!$db) {
    send_json([
        'success' => true,
        'message' => 'Recipe saved to local storage',
        'recipe_id' => 'local_' . time() . '_' . rand(1000, 9999),
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

// Save to database
try {
    // Insert recipe
    $stmt = $db->prepare('
        INSERT INTO recipe (user_id, title, cuisine, steps, created_at)
        VALUES (?, ?, ?, ?, NOW())
        RETURNING id
    ');
    
    $stmt->execute([$user_id, $title, $cuisine, $steps]);
    $recipe_id = $stmt->fetchColumn();
    
    if (!$recipe_id) {
        send_json(['error' => 'Failed to create recipe'], 500);
    }
    
    // Insert ingredients
    $ingredient_lines = explode("\n", $ingredients);
    $stmt = $db->prepare('
        INSERT INTO recipe_ingredient (recipe_id, line)
        VALUES (?, ?)
    ');
    
    foreach ($ingredient_lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $stmt->execute([$recipe_id, $line]);
        }
    }
    
    // Success!
    send_json([
        'success' => true,
        'message' => 'Recipe saved successfully to database',
        'recipe_id' => $recipe_id,
        'title' => $title,
        'mode' => 'db'
    ]);
    
} catch (PDOException $e) {
    error_log('Database error in save_ai_recipe: ' . $e->getMessage());
    send_json(['error' => 'Database error: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log('Error in save_ai_recipe: ' . $e->getMessage());
    send_json(['error' => 'Server error: ' . $e->getMessage()], 500);
}

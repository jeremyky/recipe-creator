<?php
/**
 * API endpoint to toggle recipe favorite status
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 */

// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', '0');

// Start output buffering
ob_start();

// Load dependencies
require __DIR__ . '/../lib/session.php';
require __DIR__ . '/../lib/util.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/auth.php';

// Function to send JSON response
function send_json($data, $code = 200) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit();
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['error' => 'Method not allowed'], 405);
}

// Check authentication (auto-login on localhost)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    if (!is_authenticated()) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Demo User';
    }
} else {
    if (!is_authenticated()) {
        send_json(['error' => 'Unauthorized'], 401);
    }
}

// Get JSON input
$json_input = file_get_contents('php://input');
$input = json_decode($json_input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_json(['error' => 'Invalid JSON'], 400);
}

// Get recipe ID
$recipe_id = isset($input['recipe_id']) ? $input['recipe_id'] : null;

if (!$recipe_id) {
    send_json(['error' => 'Recipe ID is required'], 400);
}

$user_id = user_id();

// Handle local recipes (stored in localStorage)
if (strpos($recipe_id, 'local_') === 0) {
    // For local recipes, just return success - JavaScript will handle localStorage
    send_json([
        'success' => true,
        'mode' => 'local',
        'recipe_id' => $recipe_id,
        'is_favorite' => $input['is_favorite'] ?? true
    ]);
}

// Database recipe
$recipe_id = intval($recipe_id);
if ($recipe_id <= 0) {
    send_json(['error' => 'Invalid recipe ID'], 400);
}

try {
    $db = db_connect();
    
    if (!$db) {
        // Local mode without database
        send_json([
            'success' => true,
            'mode' => 'local',
            'recipe_id' => $recipe_id,
            'is_favorite' => $input['is_favorite'] ?? true
        ]);
    }
    
    // Check if already favorited
    $stmt = $db->prepare('
        SELECT id FROM favorite 
        WHERE user_id = ? AND recipe_id = ?
    ');
    $stmt->execute([$user_id, $recipe_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remove favorite
        $stmt = $db->prepare('
            DELETE FROM favorite 
            WHERE user_id = ? AND recipe_id = ?
        ');
        $stmt->execute([$user_id, $recipe_id]);
        
        send_json([
            'success' => true,
            'is_favorite' => false,
            'recipe_id' => $recipe_id,
            'message' => 'Removed from favorites'
        ]);
    } else {
        // Add favorite
        $stmt = $db->prepare('
            INSERT INTO favorite (user_id, recipe_id)
            VALUES (?, ?)
        ');
        $stmt->execute([$user_id, $recipe_id]);
        
        send_json([
            'success' => true,
            'is_favorite' => true,
            'recipe_id' => $recipe_id,
            'message' => 'Added to favorites'
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Database error in toggle_favorite: ' . $e->getMessage());
    send_json(['error' => 'Database error'], 500);
} catch (Exception $e) {
    error_log('Error in toggle_favorite: ' . $e->getMessage());
    send_json(['error' => 'Server error'], 500);
}


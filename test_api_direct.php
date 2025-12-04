<?php
/**
 * Direct API test - bypasses all the includes to test JSON output
 */

header('Content-Type: application/json');

// Simulate the save recipe response
$response = [
    'success' => true,
    'message' => 'Test response',
    'recipe_id' => 'local_' . time(),
    'title' => 'Test Recipe',
    'mode' => 'local',
    'recipe' => [
        'title' => 'Test Recipe',
        'cuisine' => 'italian',
        'ingredients' => 'test ingredients',
        'steps' => 'test steps',
        'created_at' => date('Y-m-d H:i:s')
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
exit;


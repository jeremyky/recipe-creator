<?php
/**
 * Minimal save recipe test
 */

// Test 1: Basic JSON output
echo "TEST 1: Basic JSON\n";
header('Content-Type: application/json');
echo json_encode(['test' => 'basic', 'status' => 'ok']);
echo "\n\n";

// Test 2: Load dependencies
echo "TEST 2: Loading dependencies...\n";
try {
    require __DIR__ . '/lib/session.php';
    echo "✅ session.php loaded\n";
    
    require __DIR__ . '/lib/util.php';
    echo "✅ util.php loaded\n";
    
    require __DIR__ . '/lib/db.php';
    echo "✅ db.php loaded\n";
    
    require __DIR__ . '/lib/repo.php';
    echo "✅ repo.php loaded\n";
    
    require __DIR__ . '/lib/validate.php';
    echo "✅ validate.php loaded\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check authentication
echo "\nTEST 3: Check authentication\n";
echo "is_authenticated(): " . (is_authenticated() ? 'true' : 'false') . "\n";
echo "user_id(): " . user_id() . "\n";

// Test 4: Test database
echo "\nTEST 4: Database connection\n";
$db = db_connect();
echo "db_connect(): " . ($db ? 'connected' : 'null (local mode)') . "\n";

// Test 5: Simulate save
echo "\nTEST 5: Simulate save to localStorage\n";
$response = [
    'success' => true,
    'message' => 'Recipe saved to local storage',
    'recipe_id' => 'local_' . time(),
    'title' => 'Test Recipe',
    'mode' => 'local',
    'recipe' => [
        'title' => 'Test Recipe',
        'cuisine' => 'italian',
        'ingredients' => 'test',
        'steps' => 'test steps',
        'created_at' => date('Y-m-d H:i:s')
    ]
];
echo json_encode($response, JSON_PRETTY_PRINT);


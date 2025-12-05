<?php
/**
 * Fix User ID Sequence Script
 * This script resets the PostgreSQL sequence to match the current max ID in the table
 * Run this if you get "duplicate key value violates unique constraint" errors
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>Fix User Sequence</title>";
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;}</style>";
echo "</head><body><h1>Fix User ID Sequence</h1>";

// Support both direct access and router inclusion
if (!function_exists('db_connect')) {
    require __DIR__ . '/lib/db.php';
    load_env();
} elseif (!function_exists('load_env')) {
    load_env();
}

try {
    $pdo = db_connect();
    
    if (!$pdo) {
        echo "<p class='error'>[ERROR] Cannot connect to database.</p>";
        echo "</body></html>";
        exit;
    }
    
    echo "<p class='ok'>[OK] Connected to database</p>";
    
    // Get current max ID from table
    $stmt = $pdo->query("SELECT COALESCE(MAX(id), 0) as max_id FROM app_user");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_id = (int)$result['max_id'];
    
    echo "<p>Current maximum user ID in table: <strong>$max_id</strong></p>";
    
    // Get current sequence value
    $stmt = $pdo->query("SELECT last_value, is_called FROM app_user_id_seq");
    $seq_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_value = (int)$seq_result['last_value'];
    $is_called = $seq_result['is_called'] === 't';
    
    echo "<p>Current sequence value: <strong>$last_value</strong> (is_called: " . ($is_called ? 'true' : 'false') . ")</p>";
    
    // Calculate next ID (should be max_id + 1)
    $next_id = $max_id + 1;
    
    if ($last_value < $max_id || ($last_value == $max_id && $is_called)) {
        echo "<p class='error'>[WARNING] Sequence is out of sync! Resetting sequence...</p>";
        
        // Reset sequence to start from next_id
        // This ensures the next insert will use next_id
        $pdo->exec("SELECT setval('app_user_id_seq', $max_id, true)");
        
        // Verify the fix
        $stmt = $pdo->query("SELECT last_value, is_called FROM app_user_id_seq");
        $new_seq_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $new_last_value = (int)$new_seq_result['last_value'];
        $new_is_called = $new_seq_result['is_called'] === 't';
        
        echo "<p class='ok'>[OK] Sequence reset!</p>";
        echo "<p>New sequence value: <strong>$new_last_value</strong> (is_called: " . ($new_is_called ? 'true' : 'false') . ")</p>";
        echo "<p class='ok'>Next new user will get ID: <strong>$next_id</strong></p>";
    } else {
        echo "<p class='ok'>[OK] Sequence is already in sync!</p>";
        echo "<p>Next new user will get ID: <strong>$next_id</strong></p>";
    }
    
    // Show all existing users
    echo "<h2>Existing Users:</h2>";
    $stmt = $pdo->query("SELECT id, email, name, created_at FROM app_user ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<p>No users found in database.</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Created At</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2 class='ok'>[SUCCESS] Sequence fix complete!</h2>";
    echo "<p><a href='index.php?action=home'>Go to Home</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>


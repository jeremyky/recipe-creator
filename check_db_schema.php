<?php
/**
 * Check database schema for debugging
 */

require __DIR__ . '/lib/session.php';
require __DIR__ . '/lib/db.php';

echo "<!DOCTYPE html><html><head><title>DB Schema Check</title>";
echo "<style>body{font-family:monospace;padding:20px;max-width:1000px;margin:0 auto;} .ok{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow-x:auto;} table{border-collapse:collapse;width:100%;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f0f0f0;}</style>";
echo "</head><body><h1>🔍 Database Schema Check</h1>";

$pdo = db_connect();

if (!$pdo) {
    echo "<p class='error'>[ERROR] Cannot connect to database</p>";
    echo "<p>Check your .env file and database credentials.</p>";
    echo "</body></html>";
    exit;
}

echo "<p class='ok'>[OK] Database connected</p>";

// Check app_user table structure
echo "<h2>📋 app_user Table Schema</h2>";
try {
    $stmt = $pdo->query("
        SELECT column_name, data_type, is_nullable, column_default 
        FROM information_schema.columns 
        WHERE table_name = 'app_user' 
        ORDER BY ordinal_position
    ");
    $columns = $stmt->fetchAll();
    
    if (empty($columns)) {
        echo "<p class='error'>[ERROR] app_user table does not exist!</p>";
        echo "<p>Run init_db.php to create the table.</p>";
    } else {
        echo "<table>";
        echo "<tr><th>Column</th><th>Type</th><th>Nullable</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($col['column_name']) . "</td>";
            echo "<td>" . htmlspecialchars($col['data_type']) . "</td>";
            echo "<td>" . htmlspecialchars($col['is_nullable']) . "</td>";
            echo "<td>" . htmlspecialchars($col['column_default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for required columns
        $column_names = array_column($columns, 'column_name');
        $required = ['id', 'name', 'email', 'password_hash', 'created_at'];
        $missing = array_diff($required, $column_names);
        
        if (empty($missing)) {
            echo "<p class='ok'>[OK] All required columns present</p>";
        } else {
            echo "<p class='error'>[ERROR] Missing columns: " . implode(', ', $missing) . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] " . htmlspecialchars($e->getMessage()) . "</p>";
}

// List existing users
echo "<h2>👥 Existing Users</h2>";
try {
    $stmt = $pdo->query("SELECT id, name, email, created_at, (password_hash IS NOT NULL) as has_password FROM app_user ORDER BY id");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p>No users found</p>";
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Has Password</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . ($user['has_password'] ? '✅' : '❌') . "</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test insert query
echo "<h2>🧪 Test Registration Query</h2>";
try {
    $test_email = 'test_' . time() . '@example.com';
    $stmt = $pdo->prepare('
        INSERT INTO app_user (name, email, password_hash, created_at) 
        VALUES (:name, :email, :password_hash, NOW())
    ');
    
    echo "<pre>";
    echo "Query: INSERT INTO app_user (name, email, password_hash, created_at) VALUES (:name, :email, :password_hash, NOW())\n";
    echo "Test values:\n";
    echo "  name: Test User\n";
    echo "  email: $test_email\n";
    echo "  password_hash: (hashed)\n";
    echo "</pre>";
    
    $result = $stmt->execute([
        'name' => 'Test User',
        'email' => $test_email,
        'password_hash' => password_hash('testpass', PASSWORD_DEFAULT)
    ]);
    
    if ($result) {
        $test_user_id = $pdo->lastInsertId();
        echo "<p class='ok'>[OK] Test insert successful! User ID: $test_user_id</p>";
        
        // Clean up test user
        $pdo->prepare('DELETE FROM app_user WHERE id = :id')->execute(['id' => $test_user_id]);
        echo "<p class='ok'>[OK] Test user cleaned up</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] Test insert failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>✅ Summary</h2>";
echo "<p>If you see errors above, run: <a href='init_db.php'>init_db.php</a></p>";

echo "</body></html>";
?>


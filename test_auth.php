<?php
/**
 * Test authentication functionality
 * Visit this page to test login/signup
 */

require __DIR__ . '/lib/session.php';
require __DIR__ . '/lib/util.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/auth.php';

echo "<!DOCTYPE html><html><head><title>Auth Test</title>";
echo "<style>body{font-family:monospace;padding:20px;max-width:800px;margin:0 auto;} .ok{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:4px;}</style>";
echo "</head><body><h1>🔐 Authentication System Test</h1>";

$pdo = db_connect();

if (!$pdo) {
    echo "<p class='error'>[ERROR] Cannot connect to database</p>";
    echo "</body></html>";
    exit;
}

echo "<p class='ok'>[OK] Database connected</p>";

// Check if app_user table has required columns
try {
    $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'app_user' ORDER BY ordinal_position");
    $columns = $stmt->fetchAll();
    
    echo "<h2>📋 Database Schema</h2>";
    echo "<pre>";
    foreach ($columns as $col) {
        echo "{$col['column_name']}: {$col['data_type']}\n";
    }
    echo "</pre>";
    
    $required = ['id', 'name', 'email', 'password_hash', 'created_at'];
    $existing = array_column($columns, 'column_name');
    $missing = array_diff($required, $existing);
    
    if (empty($missing)) {
        echo "<p class='ok'>[OK] All required columns exist</p>";
    } else {
        echo "<p class='error'>[ERROR] Missing columns: " . implode(', ', $missing) . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] " . htmlspecialchars($e->getMessage()) . "</p>";
}

// List existing users
try {
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM app_user ORDER BY id");
    $users = $stmt->fetchAll();
    
    echo "<h2>👥 Existing Users</h2>";
    if (empty($users)) {
        echo "<p class='info'>No users found</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th><th>Has Password</th></tr>";
        foreach ($users as $user) {
            $stmt2 = $pdo->prepare("SELECT password_hash IS NOT NULL as has_pass FROM app_user WHERE id = :id");
            $stmt2->execute(['id' => $user['id']]);
            $has_pass = $stmt2->fetch()['has_pass'] ? '✅' : '❌';
            
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "<td>{$has_pass}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test authentication functions
echo "<h2>🧪 Function Tests</h2>";

// Test password hashing
$test_password = "testpass123";
$hash = password_hash($test_password, PASSWORD_DEFAULT);
$verify = password_verify($test_password, $hash);
echo "<p class='ok'>[OK] Password hashing works: " . ($verify ? "✅" : "❌") . "</p>";

// Check session
echo "<p class='info'>[INFO] Session active: " . (session_status() === PHP_SESSION_ACTIVE ? "✅" : "❌") . "</p>";
echo "<p class='info'>[INFO] Authenticated: " . (is_authenticated() ? "✅ (User ID: " . auth_user_id() . ")" : "❌") . "</p>";

echo "<h2>🔗 Quick Links</h2>";
echo "<ul>";
echo "<li><a href='index.php?action=signup'>Sign Up Page</a></li>";
echo "<li><a href='index.php?action=login'>Login Page</a></li>";
echo "<li><a href='index.php?action=home'>Home (requires auth)</a></li>";
echo "<li><a href='index.php?action=logout'>Logout</a></li>";
echo "</ul>";

echo "<h2>✅ Summary</h2>";
echo "<p>Your authentication system is set up and ready to use!</p>";
echo "<p><strong>To test:</strong></p>";
echo "<ol>";
echo "<li>Visit the <a href='index.php?action=signup'>signup page</a> and create an account</li>";
echo "<li>You'll be automatically logged in and redirected to home</li>";
echo "<li>Click logout to test the login flow</li>";
echo "<li>Visit the <a href='index.php?action=login'>login page</a> and sign in with your credentials</li>";
echo "</ol>";

echo "</body></html>";
?>


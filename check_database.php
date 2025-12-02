<?php
/**
 * Database Diagnostic Script
 * Upload this to your server and visit it to see what's wrong
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>Database Diagnostic</title>";
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} table{border-collapse:collapse;margin:10px 0;} th,td{border:1px solid #ccc;padding:8px;text-align:left;}</style>";
echo "</head><body><h1>Database Diagnostic Report</h1>";

// Test 1: Database Connection
echo "<h2>1. Database Connection</h2>";
require 'lib/db.php';

// Use shared helper (may return null in local dev)
try {
    $pdo = db_connect();
} catch (Exception $e) {
    $pdo = null;
}

if ($pdo === null) {
    echo "<p class='error'>[ERROR] Database connection FAILED.</p>";
    echo "<p>This diagnostic script is intended for the cs4640 server where PostgreSQL is running with your course credentials.</p>";
    echo "<p>On your personal machine, it's normal to see this if Postgres isn't running or isn't configured the same way.</p>";
    echo "</body></html>";
    exit;
}

echo "<p class='ok'>[OK] Database connected successfully</p>";

// Test 2: Check Required Tables
echo "<h2>2. Required Tables</h2>";
$required_tables = [
    'app_user' => ['id', 'email', 'created_at'],
    'recipe' => ['id', 'user_id', 'title', 'image_url', 'steps', 'created_at'],
    'pantry_item' => ['id', 'user_id', 'ingredient', 'quantity', 'unit', 'created_at'],
    'recipe_ingredient' => ['id', 'recipe_id', 'line']
];

$tables_exist = [];
foreach ($required_tables as $table => $columns) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        $tables_exist[$table] = true;
        echo "<p class='ok'>[OK] Table '$table' exists</p>";
        
        // Check columns
        $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$table' ORDER BY ordinal_position");
        $actual_columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        $missing = array_diff($columns, $actual_columns);
        $extra = array_diff($actual_columns, $columns);
        
        if (!empty($missing)) {
            echo "<p class='error'>   [ERROR] Missing columns: " . implode(', ', $missing) . "</p>";
        }
        if (!empty($extra)) {
            echo "<p class='warning'>   [WARNING] Extra columns: " . implode(', ', $extra) . "</p>";
        }
        if (empty($missing) && empty($extra)) {
            echo "<p class='ok'>   [OK] All required columns present</p>";
        }
    } catch (PDOException $e) {
        $tables_exist[$table] = false;
        echo "<p class='error'>[ERROR] Table '$table' MISSING: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Test 3: Check Foreign Keys
echo "<h2>3. Foreign Key Constraints</h2>";
$foreign_keys = [
    'recipe.user_id → app_user.id',
    'pantry_item.user_id → app_user.id',
    'recipe_ingredient.recipe_id → recipe.id'
];

foreach ($foreign_keys as $fk_desc) {
    // Check if foreign key exists (simplified check)
    echo "<p class='warning'>[WARNING] Manual check needed for: $fk_desc</p>";
}

// Test 4: Check Indexes
echo "<h2>4. Indexes</h2>";
try {
    $stmt = $pdo->query("SELECT indexname FROM pg_indexes WHERE tablename IN ('recipe', 'pantry_item', 'recipe_ingredient') AND schemaname = 'public'");
    $indexes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($indexes) > 0) {
        echo "<p class='ok'>[OK] Found " . count($indexes) . " index(es): " . implode(', ', $indexes) . "</p>";
    } else {
        echo "<p class='warning'>[WARNING] No indexes found (performance may be slower, but not required)</p>";
    }
} catch (Exception $e) {
    echo "<p class='warning'>[WARNING] Could not check indexes</p>";
}

// Test 5: Check Demo User
echo "<h2>5. Demo User</h2>";
try {
    $stmt = $pdo->query("SELECT id, email FROM app_user WHERE id = 1");
    $user = $stmt->fetch();
    if ($user) {
        echo "<p class='ok'>[OK] Demo user exists: ID={$user['id']}, Email={$user['email']}</p>";
    } else {
        echo "<p class='error'>[ERROR] Demo user (ID=1) NOT FOUND</p>";
        echo "<p>Run: INSERT INTO app_user (id, email) VALUES (1, 'demo@example.com');</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] Error checking user: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 6: Test Repository Functions
echo "<h2>6. Repository Functions</h2>";
try {
    require 'lib/repo.php';
    require 'lib/session.php';
    
    $userId = user_id();
    echo "<p class='ok'>[OK] user_id() returns: $userId</p>";
    
    $recipes = get_recipes($userId);
    echo "<p class='ok'>[OK] get_recipes() works: Found " . count($recipes) . " recipes</p>";
    
    $pantry = get_pantry($userId);
    echo "<p class='ok'>[OK] get_pantry() works: Found " . count($pantry) . " items</p>";
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] Repository function error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Test 7: Try a Test Insert
echo "<h2>7. Test Database Write</h2>";
try {
    $stmt = $pdo->prepare("INSERT INTO recipe (user_id, title, steps) VALUES (:user_id, 'Test Recipe', 'Test steps') RETURNING id");
    $stmt->execute(['user_id' => 1]);
    $testId = $stmt->fetchColumn();
    
    echo "<p class='ok'>[OK] Can INSERT into recipe table (test ID: $testId)</p>";
    
    // Clean up
    $pdo->prepare("DELETE FROM recipe WHERE id = :id")->execute(['id' => $testId]);
    echo "<p class='ok'>[OK] Can DELETE from recipe table</p>";
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] Database write test FAILED: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Summary
echo "<h2>Summary</h2>";
$all_tables_exist = !in_array(false, $tables_exist);
if ($all_tables_exist) {
    echo "<p class='ok'><strong>[OK] All required tables exist</strong></p>";
    echo "<p>Your database appears to be set up correctly. The 500 error might be from:</p>";
    echo "<ul>";
    echo "<li>PHP syntax error (check error logs)</li>";
    echo "<li>Missing files on server</li>";
    echo "<li>Permission issues</li>";
    echo "</ul>";
} else {
    echo "<p class='error'><strong>[ERROR] Missing tables detected</strong></p>";
    echo "<p>You need to create the missing tables. Use phpPgAdmin SQL tab and run:</p>";
    echo "<pre style='background:#f5f5f5;padding:10px;overflow:auto;'>";
    echo "-- Create missing tables\n";
    if (!$tables_exist['app_user']) {
        echo "CREATE TABLE app_user (\n  id SERIAL PRIMARY KEY,\n  email TEXT UNIQUE NOT NULL,\n  created_at TIMESTAMP DEFAULT NOW()\n);\n\n";
    }
    if (!$tables_exist['recipe']) {
        echo "CREATE TABLE recipe (\n  id SERIAL PRIMARY KEY,\n  user_id INT REFERENCES app_user(id) ON DELETE CASCADE,\n  title TEXT NOT NULL,\n  image_url TEXT,\n  steps TEXT NOT NULL,\n  created_at TIMESTAMP DEFAULT NOW()\n);\n\n";
    }
    if (!$tables_exist['pantry_item']) {
        echo "CREATE TABLE pantry_item (\n  id SERIAL PRIMARY KEY,\n  user_id INT REFERENCES app_user(id) ON DELETE CASCADE,\n  ingredient TEXT NOT NULL,\n  quantity NUMERIC(10,2) NOT NULL DEFAULT 0,\n  unit TEXT NOT NULL,\n  created_at TIMESTAMP DEFAULT NOW()\n);\n\n";
    }
    if (!$tables_exist['recipe_ingredient']) {
        echo "CREATE TABLE recipe_ingredient (\n  id SERIAL PRIMARY KEY,\n  recipe_id INT REFERENCES recipe(id) ON DELETE CASCADE,\n  line TEXT NOT NULL\n);\n\n";
    }
    echo "</pre>";
}

echo "</body></html>";
?>


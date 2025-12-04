<?php
/**
 * Database Initialization Script
 * Run this once to set up your database
 * Visit: https://cs4640.cs.virginia.edu/juh7hc/init_db.php
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>Database Init</title>";
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;}</style>";
echo "</head><body><h1>Database Initialization</h1>";

require 'lib/db.php';

// Try to connect using shared helper (may return null in local dev)
try {
    $pdo = db_connect();
} catch (Exception $e) {
    $pdo = null;
}

if ($pdo === null) {
    echo "<p class='error'>[ERROR] Cannot connect to database.</p>";
    echo "<p>This script is meant to run on the cs4640 server where PostgreSQL is available.</p>";
    echo "<p>On your laptop, it's normal to see this if Postgres isn't running with the same credentials.</p>";
    echo "</body></html>";
    exit;
}

echo "<p class='ok'>[OK] Connected to database</p>";

$sql_statements = [
    "CREATE TABLE IF NOT EXISTS app_user (
      id SERIAL PRIMARY KEY,
      name TEXT NOT NULL DEFAULT 'User',
      email TEXT UNIQUE NOT NULL,
      password_hash TEXT,
      created_at TIMESTAMP DEFAULT NOW()
    )",
    
    "CREATE TABLE IF NOT EXISTS recipe (
      id SERIAL PRIMARY KEY,
      user_id INT REFERENCES app_user(id) ON DELETE CASCADE,
      title TEXT NOT NULL,
      cuisine TEXT,
      image_url TEXT,
      steps TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT NOW()
    )",
    
    "CREATE TABLE IF NOT EXISTS pantry_item (
      id SERIAL PRIMARY KEY,
      user_id INT REFERENCES app_user(id) ON DELETE CASCADE,
      ingredient TEXT NOT NULL,
      quantity NUMERIC(10,2) NOT NULL DEFAULT 0,
      unit TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT NOW()
    )",
    
    "CREATE TABLE IF NOT EXISTS recipe_ingredient (
      id SERIAL PRIMARY KEY,
      recipe_id INT REFERENCES recipe(id) ON DELETE CASCADE,
      line TEXT NOT NULL
    )",
    
    "CREATE INDEX IF NOT EXISTS idx_recipe_user_id ON recipe(user_id)",
    "CREATE INDEX IF NOT EXISTS idx_pantry_user_id ON pantry_item(user_id)",
    "CREATE INDEX IF NOT EXISTS idx_recipe_ingredient_recipe_id ON recipe_ingredient(recipe_id)",
    
    "ALTER TABLE recipe ADD COLUMN IF NOT EXISTS cuisine TEXT",
    
    "INSERT INTO app_user (id, email) VALUES (1, 'demo@example.com') ON CONFLICT (id) DO NOTHING",
    
    "SELECT setval('app_user_id_seq', 1, true)"
];

echo "<h2>Creating Tables...</h2>";

foreach ($sql_statements as $sql) {
    try {
        $pdo->exec($sql);
        echo "<p class='ok'>[OK] Executed successfully</p>";
    } catch (PDOException $e) {
        // Some errors are OK (like "already exists")
        if (strpos($e->getMessage(), 'already exists') !== false || 
            strpos($e->getMessage(), 'duplicate key') !== false) {
            echo "<p class='ok'>[WARNING] " . htmlspecialchars($e->getMessage()) . " (OK - already exists)</p>";
        } else {
            echo "<p class='error'>[ERROR] Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

echo "<h2>Verifying Setup...</h2>";

$tables = ['app_user', 'recipe', 'pantry_item', 'recipe_ingredient'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "<p class='ok'>[OK] Table '$table' exists</p>";
    } catch (Exception $e) {
        echo "<p class='error'>[ERROR] Table '$table' missing: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Check demo user
try {
    $stmt = $pdo->query("SELECT id, email FROM app_user WHERE id = 1");
    $user = $stmt->fetch();
    if ($user) {
        echo "<p class='ok'>[OK] Demo user exists: ID={$user['id']}, Email={$user['email']}</p>";
    } else {
        echo "<p class='error'>[ERROR] Demo user not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] Error checking user: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>[OK] Database Initialization Complete!</h2>";
echo "<p><a href='check_database.php'>Verify with diagnostic script</a></p>";
echo "<p><a href='index.php?action=upload'>Test upload form</a></p>";
echo "</body></html>";
?>


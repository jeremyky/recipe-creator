<?php
/**
 * Database connection helper
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 3
 */

/**
 * Establish PDO connection to Postgres database
 * @return PDO
 */
function db_connect() {
    static $pdo = null;
    static $failed = false;
    
    // If we've already failed in local dev, return null immediately
    if ($failed) {
        return null;
    }
    
    if ($pdo === null) {
        // Load .env file if it exists
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue; // Skip comments
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
        
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'juh7hc';
        // For local development, use current system user; for production use juh7hc
        $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8000', '127.0.0.1:8000']);
        $username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: ($isLocal ? get_current_user() : 'juh7hc');
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
        
        // Try connection with port first
        $dsn = "pgsql:host=$host;port=5432;dbname=$dbname";
        
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback: try without port
            try {
                $dsn = "pgsql:host=$host;dbname=$dbname";
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                // For local development: allow graceful failure
                // Check if we're in a local environment (not production)
                $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8000', '127.0.0.1:8000']);
                
                if ($isLocal) {
                    error_log("Database connection failed (local dev): " . $e2->getMessage());
                    error_log("Attempted: host=$host, dbname=$dbname, user=$username");
                    error_log("Returning null for local development - database features will be disabled");
                    $failed = true; // Mark as failed to avoid retrying
                    return null; // Return null instead of throwing for local dev
                } else {
                    error_log("Database connection failed: " . $e2->getMessage());
                    error_log("Attempted: host=$host, dbname=$dbname, user=$username");
                    throw new Exception("Database connection failed: " . $e2->getMessage());
                }
            }
        }
    }
    
    return $pdo;
}

<?php
/**
 * Debug version of save_ai_recipe.php
 * Shows exactly where it fails
 */

// Logging function
function debug_log($message) {
    $log = sys_get_temp_dir() . '/recipe_debug_trace.log';
    file_put_contents($log, date('H:i:s') . " " . $message . "\n", FILE_APPEND);
}

debug_log("=== START ===");

// Step 1: Start output buffering
ob_start();
debug_log("Output buffering started");

// Step 2: Suppress errors
error_reporting(0);
ini_set('display_errors', '0');
debug_log("Errors suppressed");

// Step 3: Set header
header('Content-Type: application/json; charset=utf-8');
debug_log("Header set");

// Step 4: Load dependencies ONE BY ONE
try {
    debug_log("Loading session.php...");
    require __DIR__ . '/../lib/session.php';
    debug_log("✅ session.php loaded");
    
    debug_log("Loading util.php...");
    require __DIR__ . '/../lib/util.php';
    debug_log("✅ util.php loaded");
    
    debug_log("Loading db.php...");
    require __DIR__ . '/../lib/db.php';
    debug_log("✅ db.php loaded");
    
    debug_log("Loading repo.php...");
    require __DIR__ . '/../lib/repo.php';
    debug_log("✅ repo.php loaded");
    
    debug_log("Loading auth.php...");
    require __DIR__ . '/../lib/auth.php';
    debug_log("✅ auth.php loaded");
    
} catch (Exception $e) {
    debug_log("❌ ERROR loading dependencies: " . $e->getMessage());
    
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load: ' . $e->getMessage()]);
    exit;
}

debug_log("All dependencies loaded successfully");

// Step 5: Check request method
debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Not POST, returning error");
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed', 'method' => $_SERVER['REQUEST_METHOD']]);
    exit;
}

// Step 6: Check authentication
debug_log("Checking authentication...");
debug_log("SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'not set'));

// Auto-login for localhost
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    debug_log("Localhost detected - auto-login enabled");
    if (!is_authenticated()) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Demo User';
        debug_log("Auto-logged in as demo user");
    }
}

$is_auth = is_authenticated();
debug_log("is_authenticated() = " . ($is_auth ? 'true' : 'false'));
debug_log("user_id = " . (user_id() ?? 'null'));

if (!$is_auth) {
    debug_log("Not authenticated, returning 401");
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated', 'user_id' => user_id()]);
    exit;
}

// Step 7: Read input
debug_log("Reading JSON input...");
$json_input = file_get_contents('php://input');
debug_log("Input length: " . strlen($json_input));
debug_log("Input preview: " . substr($json_input, 0, 100));

$input = json_decode($json_input, true);
debug_log("JSON decode result: " . (is_array($input) ? 'success' : 'failed'));

if (json_last_error() !== JSON_ERROR_NONE) {
    debug_log("JSON error: " . json_last_error_msg());
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
}

// Step 8: Send success response
debug_log("Sending success response...");

if (ob_get_level() > 0) ob_end_clean();
http_response_code(200);

$response = [
    'success' => true,
    'message' => 'Debug test successful!',
    'received' => $input,
    'timestamp' => date('Y-m-d H:i:s')
];

$json = json_encode($response);
debug_log("Response JSON length: " . strlen($json));
debug_log("Response preview: " . substr($json, 0, 100));

echo $json;
debug_log("=== END ===");
exit;


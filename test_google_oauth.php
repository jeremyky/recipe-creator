<?php
/**
 * Test Google OAuth Configuration
 */

require __DIR__ . '/lib/session.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/auth.php';

// Load environment variables
load_env();

echo "<!DOCTYPE html><html><head><title>Google OAuth Test</title>";
echo "<style>body{font-family:monospace;padding:20px;max-width:800px;margin:0 auto;} .ok{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow-x:auto;}</style>";
echo "</head><body><h1>🔐 Google OAuth Configuration Test</h1>";

// Check environment variables
$client_id = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$client_secret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

echo "<h2>📋 Configuration</h2>";

if ($client_id) {
    echo "<p class='ok'>[OK] GOOGLE_CLIENT_ID found</p>";
    echo "<pre>Client ID: " . htmlspecialchars($client_id) . "</pre>";
} else {
    echo "<p class='error'>[ERROR] GOOGLE_CLIENT_ID not found</p>";
}

if ($client_secret) {
    echo "<p class='ok'>[OK] GOOGLE_CLIENT_SECRET found</p>";
    $masked = substr($client_secret, 0, 10) . '***' . substr($client_secret, -5);
    echo "<pre>Client Secret: " . htmlspecialchars($masked) . "</pre>";
} else {
    echo "<p class='error'>[ERROR] GOOGLE_CLIENT_SECRET not found</p>";
}

if ($redirect_uri) {
    echo "<p class='ok'>[OK] GOOGLE_REDIRECT_URI found</p>";
    echo "<pre>Redirect URI: " . htmlspecialchars($redirect_uri) . "</pre>";
} else {
    echo "<p class='error'>[ERROR] GOOGLE_REDIRECT_URI not found</p>";
}

// Generate auth URL
echo "<h2>🔗 Authorization URL</h2>";
try {
    $auth_url = google_auth_url();
    echo "<p class='ok'>[OK] Authorization URL generated</p>";
    echo "<pre>" . htmlspecialchars($auth_url) . "</pre>";
    echo "<p><a href='" . htmlspecialchars($auth_url) . "' target='_blank' style='padding:10px 20px;background:#4285f4;color:white;text-decoration:none;border-radius:4px;display:inline-block;margin:10px 0;'>Test Google Sign In</a></p>";
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] Failed to generate auth URL: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>⚠️ Important Setup Steps</h2>";
echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107;margin:20px 0;'>";
echo "<p><strong>Make sure you've configured this in Google Cloud Console:</strong></p>";
echo "<ol>";
echo "<li>Go to <a href='https://console.cloud.google.com/apis/credentials' target='_blank'>Google Cloud Console</a></li>";
echo "<li>Select your OAuth 2.0 Client ID</li>";
echo "<li>Under \"Authorized redirect URIs\", add EXACTLY:</li>";
echo "<pre style='margin:10px 0;padding:10px;background:white;'>" . htmlspecialchars($redirect_uri) . "</pre>";
echo "<li>Save changes and wait a few minutes for them to propagate</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🧪 Testing Steps</h2>";
echo "<ol>";
echo "<li>Click the \"Test Google Sign In\" button above</li>";
echo "<li>If you see an error from Google, verify the redirect URI in Google Cloud Console matches exactly</li>";
echo "<li>If successful, you'll be redirected back to this app</li>";
echo "</ol>";

echo "</body></html>";
?>


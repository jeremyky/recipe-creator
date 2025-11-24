<?php
/**
 * Chat API endpoint for LLM integration with security features
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

require __DIR__ . '/../lib/session.php';
require __DIR__ . '/../lib/util.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/repo.php';

// Load .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue; // Skip invalid lines
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'Method not allowed'], 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$prompt = trim($input['prompt'] ?? '');

if (empty($prompt)) {
    json_out(['error' => 'Prompt is required'], 400);
}

// SECURITY: Password protection via session (password never sent from client)
// Authentication is handled server-side when chat page is visited
// Password is stored in .env file only, never in client code
$chat_authenticated = $_SESSION['chat_authenticated'] ?? false;

if (!$chat_authenticated) {
    json_out(['error' => 'Unauthorized: Please visit the chat page first to authenticate'], 401);
}

// SECURITY: Rate limiting and spam prevention
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rate_limit_file = sys_get_temp_dir() . '/recipe_chat_rate_' . md5($ip) . '.json';

// Rate limit: 10 requests per 5 minutes per IP
$rate_limit = [
    'max_requests' => 10,
    'time_window' => 300, // 5 minutes in seconds
    'block_duration' => 1800 // 30 minutes block if exceeded
];

$rate_data = [];
if (file_exists($rate_limit_file)) {
    $rate_data = json_decode(file_get_contents($rate_limit_file), true) ?: [];
}

$now = time();

// Check if IP is blocked
if (isset($rate_data['blocked_until']) && $rate_data['blocked_until'] > $now) {
    $remaining = $rate_data['blocked_until'] - $now;
    json_out([
        'error' => 'Rate limit exceeded. Please try again in ' . ceil($remaining / 60) . ' minutes.',
        'blocked_until' => $rate_data['blocked_until']
    ], 429);
}

// Clean old requests outside time window
if (isset($rate_data['requests'])) {
    $rate_data['requests'] = array_filter($rate_data['requests'], function($timestamp) use ($now, $rate_limit) {
        return ($now - $timestamp) < $rate_limit['time_window'];
    });
    $rate_data['requests'] = array_values($rate_data['requests']);
} else {
    $rate_data['requests'] = [];
}

// Check if limit exceeded
if (count($rate_data['requests']) >= $rate_limit['max_requests']) {
    // Block this IP
    $rate_data['blocked_until'] = $now + $rate_limit['block_duration'];
    $rate_data['block_count'] = ($rate_data['block_count'] ?? 0) + 1;
    file_put_contents($rate_limit_file, json_encode($rate_data));
    
    json_out([
        'error' => 'Rate limit exceeded. Too many requests. Please try again later.',
        'retry_after' => $rate_limit['block_duration']
    ], 429);
}

// Add current request
$rate_data['requests'][] = $now;
$rate_data['last_request'] = $now;
file_put_contents($rate_limit_file, json_encode($rate_data));

// Get user's pantry items for context
$pantry_items = get_pantry(user_id());
$pantry_context = '';
if (!empty($pantry_items)) {
    $ingredients = array_map(function($item) {
        return $item['ingredient'] . ' (' . $item['quantity'] . ' ' . $item['unit'] . ')';
    }, $pantry_items);
    $pantry_context = "\n\nUser's current pantry ingredients: " . implode(', ', $ingredients);
}

// Get available recipes for context
$recipes = get_recipes(user_id());
$recipe_titles = array_map(function($recipe) {
    return $recipe['title'];
}, array_slice($recipes, 0, 10)); // Limit to 10 for context
$recipes_context = '';
if (!empty($recipe_titles)) {
    $recipes_context = "\n\nAvailable recipes in user's collection: " . implode(', ', $recipe_titles);
}

// Build system prompt
$system_prompt = "You are a helpful cooking assistant for Recipe Creator. Help users find recipes, get cooking tips, and plan meals. " .
    "Be friendly, concise, and practical. If the user asks about recipes, suggest specific dishes from their collection when relevant. " .
    "If they ask about ingredients, reference their pantry when helpful.";

// Build user prompt with context
$user_prompt = $prompt . $pantry_context . $recipes_context;

// Get API key from environment
$api_key = $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?? '';

if (empty($api_key)) {
    // Fallback: return a helpful response without API
    $response = generate_fallback_response($prompt, $pantry_items, $recipes);
    json_out(['success' => true, 'response' => $response, 'source' => 'fallback']);
}

// Call OpenAI API
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => $system_prompt],
            ['role' => 'user', 'content' => $user_prompt]
        ],
        'temperature' => 0.7,
        'max_tokens' => 500
    ]),
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    error_log("OpenAI API Error: " . $error);
    $fallback = generate_fallback_response($prompt, $pantry_items, $recipes);
    json_out(['success' => true, 'response' => $fallback, 'source' => 'fallback', 'error' => $error]);
}

if ($http_code !== 200) {
    error_log("OpenAI API HTTP Error: " . $http_code . " - " . $response);
    $fallback = generate_fallback_response($prompt, $pantry_items, $recipes);
    json_out(['success' => true, 'response' => $fallback, 'source' => 'fallback']);
}

$data = json_decode($response, true);

if (!isset($data['choices'][0]['message']['content'])) {
    $fallback = generate_fallback_response($prompt, $pantry_items, $recipes);
    json_out(['success' => true, 'response' => $fallback, 'source' => 'fallback']);
}

$ai_response = trim($data['choices'][0]['message']['content']);

json_out([
    'success' => true,
    'response' => $ai_response,
    'source' => 'openai'
]);

/**
 * Generate a fallback response when API is not available
 */
function generate_fallback_response($prompt, $pantry_items, $recipes) {
    $prompt_lower = strtolower($prompt);
    
    // Check if asking about ingredients
    if (preg_match('/\b(chicken|beef|pork|fish|vegetable|pasta|rice|tomato|onion|garlic)\b/i', $prompt)) {
        $suggestions = [];
        foreach ($recipes as $recipe) {
            if (count($suggestions) >= 3) break;
            $suggestions[] = $recipe['title'];
        }
        
        if (!empty($suggestions)) {
            return "Based on your question, here are some recipe suggestions from your collection:\n\n" .
                   "• " . implode("\n• ", $suggestions) . "\n\n" .
                   "You can also try the <a href='index.php?action=match'>Recipe Matcher</a> to find recipes based on your pantry ingredients!";
        }
    }
    
    // Check if asking about recipes
    if (preg_match('/\b(recipe|dish|meal|cook|make|prepare)\b/i', $prompt)) {
        if (!empty($recipes)) {
            $count = count($recipes);
            return "You have {$count} recipes in your collection! Try browsing your <a href='index.php?action=recipes'>recipes</a> or use the <a href='index.php?action=match'>Recipe Matcher</a> to find dishes based on your pantry.";
        }
    }
    
    // Default response
    return "I'd be happy to help you with cooking questions! For now, try:\n\n" .
           "• Browse your <a href='index.php?action=recipes'>recipes</a>\n" .
           "• Use the <a href='index.php?action=match'>Recipe Matcher</a> to find recipes based on your pantry\n" .
           "• <a href='index.php?action=upload'>Upload new recipes</a> to your collection\n\n" .
           "Full AI chat functionality will be available soon!";
}


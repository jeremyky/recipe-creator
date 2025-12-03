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
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue; // Skip comments and empty lines
        if (strpos($line, '=') === false) continue; // Skip invalid lines
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Remove surrounding quotes if present
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        
        $_ENV[$key] = $value;
        putenv("$key=$value");
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

// SECURITY: Rate limiting and spam prevention (per user + per IP)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$user_id = user_id();
$rate_limit_key = md5($ip . '_' . $user_id);
$rate_limit_file = sys_get_temp_dir() . '/recipe_chat_rate_' . $rate_limit_key . '.json';

// Rate limit: 5 requests per 2 minutes per user (stricter to prevent abuse)
$rate_limit = [
    'max_requests' => 5,        // Reduced from 10
    'time_window' => 120,       // 2 minutes (reduced from 5)
    'block_duration' => 3600    // 1 hour block if exceeded (increased from 30 min)
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
        return '- ' . $item['quantity'] . ' ' . $item['unit'] . ' ' . $item['ingredient'];
    }, $pantry_items);
    $pantry_context = "\n\n=== USER'S PANTRY ===\n" . implode("\n", $ingredients);
} else {
    $pantry_context = "\n\n=== USER'S PANTRY ===\n(Empty - user hasn't added ingredients yet)";
}

// Get available recipes for context
$recipes = get_recipes(user_id());
$recipe_context = '';
if (!empty($recipes)) {
    $recipe_list = array_map(function($recipe) {
        $cuisine = isset($recipe['cuisine']) ? " [{$recipe['cuisine']}]" : '';
        $ing_count = isset($recipe['ingredient_count']) ? " ({$recipe['ingredient_count']} ingredients)" : '';
        return '- ' . $recipe['title'] . $cuisine . $ing_count;
    }, array_slice($recipes, 0, 15)); // Show up to 15 recipes
    $recipe_context = "\n\n=== USER'S SAVED RECIPES ===\n" . implode("\n", $recipe_list);
} else {
    $recipe_context = "\n\n=== USER'S SAVED RECIPES ===\n(None - user hasn't uploaded any recipes yet)";
}

// Build system prompt with app-specific knowledge
$system_prompt = "You are an AI cooking assistant integrated into the Recipe Creator app. You have access to the user's pantry inventory and their saved recipes.\n\n" .
    
    "YOUR KNOWLEDGE:\n" .
    "- You can see what ingredients the user currently has in their pantry\n" .
    "- You know which recipes they've already saved in their collection\n" .
    "- You understand the Recipe Creator app has features like: Recipe Matcher (finds recipes based on pantry), Pantry Management, Recipe Upload, and Cooking Mode\n\n" .
    
    "WHEN PROVIDING RECIPES:\n" .
    "If the user asks for a recipe or you want to suggest one, format it EXACTLY like this:\n\n" .
    "=== RECIPE START ===\n" .
    "TITLE: [Recipe Name]\n" .
    "CUISINE: [italian/chinese/mexican/indian/thai/greek/american]\n" .
    "INGREDIENTS:\n" .
    "- [quantity] [unit] [ingredient name]\n" .
    "- [quantity] [unit] [ingredient name]\n" .
    "(continue list)\n\n" .
    "STEPS:\n" .
    "1. [First step instruction]\n" .
    "2. [Second step instruction]\n" .
    "(continue numbered steps)\n" .
    "=== RECIPE END ===\n\n" .
    
    "GUIDELINES:\n" .
    "- Be concise and practical (keep responses under 400 words)\n" .
    "- When suggesting recipes, prioritize ingredients from their pantry\n" .
    "- If they already have a similar recipe, mention it by name\n" .
    "- Use the structured recipe format above so they can easily save it to the app\n" .
    "- For general cooking questions, be helpful but brief\n" .
    "- Suggest using app features when relevant (e.g., 'Try the Recipe Matcher to find more options!')";

// Build user prompt with context
$user_prompt = $prompt . $pantry_context . $recipe_context;

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
        'max_tokens' => 400  // Reduced to save costs while keeping responses concise
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
    
    // Check if asking for a recipe with specific ingredients
    if (preg_match('/\b(chicken|beef|pork|fish|vegetable|pasta|rice|tomato|onion|garlic|recipe)\b/i', $prompt)) {
        // Try to find matching recipes
        $matching_recipes = [];
        foreach ($recipes as $recipe) {
            if (count($matching_recipes) >= 3) break;
            $matching_recipes[] = $recipe['title'];
        }
        
        if (!empty($matching_recipes)) {
            $response = "Based on your ingredients, here are some recipes from your collection:\n\n";
            foreach ($matching_recipes as $title) {
                $response .= "• {$title}\n";
            }
            $response .= "\nTry the <a href='index.php?action=match'>Recipe Matcher</a> to find more recipes based on your pantry!";
            return $response;
        }
        
        // Provide a sample recipe in structured format
        return "Here's a simple recipe you can make:\n\n" .
               "=== RECIPE START ===\n" .
               "TITLE: Quick Chicken Stir-Fry\n" .
               "CUISINE: chinese\n" .
               "INGREDIENTS:\n" .
               "- 1 lb chicken breast\n" .
               "- 2 tbsp vegetable oil\n" .
               "- 2 cups mixed vegetables\n" .
               "- 3 tbsp soy sauce\n" .
               "- 1 tsp garlic minced\n" .
               "- 1 tsp ginger minced\n\n" .
               "STEPS:\n" .
               "1. Cut chicken into bite-sized pieces\n" .
               "2. Heat oil in a large pan or wok over high heat\n" .
               "3. Add chicken and cook until golden, about 5-7 minutes\n" .
               "4. Add garlic and ginger, stir for 30 seconds\n" .
               "5. Add vegetables and stir-fry for 3-4 minutes\n" .
               "6. Add soy sauce, toss everything together\n" .
               "7. Serve hot with rice\n" .
               "=== RECIPE END ===\n\n" .
               "You can copy this recipe format to save it to your collection!";
    }
    
    // Check if asking about their recipes/pantry
    if (preg_match('/\b(my recipes|my pantry|what do i have|my collection)\b/i', $prompt)) {
        $response = "";
        if (!empty($pantry_items)) {
            $response .= "Your pantry currently has:\n";
            foreach (array_slice($pantry_items, 0, 10) as $item) {
                $response .= "• {$item['quantity']} {$item['unit']} {$item['ingredient']}\n";
            }
            $response .= "\n";
        }
        
        if (!empty($recipes)) {
            $count = count($recipes);
            $response .= "You have {$count} recipes saved. ";
        }
        
        $response .= "Try the <a href='index.php?action=match'>Recipe Matcher</a> to find recipes you can make with your ingredients!";
        return $response;
    }
    
    // Default helpful response
    return "I'm here to help with cooking questions! Try asking:\n\n" .
           "• \"What can I make with chicken and rice?\"\n" .
           "• \"Give me a quick dinner recipe\"\n" .
           "• \"What's in my pantry?\"\n\n" .
           "You can also:\n" .
           "• Browse your <a href='index.php?action=recipes'>recipes</a>\n" .
           "• Use the <a href='index.php?action=match'>Recipe Matcher</a>\n" .
           "• <a href='index.php?action=upload'>Upload new recipes</a>\n\n" .
           "Note: For full AI functionality, ensure your OpenAI API has credits.";
}


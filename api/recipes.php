<?php
/**
 * JSON API endpoint for recipes
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 3
 */

require __DIR__ . '/../lib/session.php';
require __DIR__ . '/../lib/util.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/repo.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_out(['error' => 'Method not allowed'], 405);
}

// Get query parameters
$search = $_GET['q'] ?? '';
$cuisine = $_GET['cuisine'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;

// Build filters
$filters = [];
if (!empty($search)) {
    $filters['q'] = $search;
}
if (!empty($cuisine)) {
    $filters['cuisine'] = $cuisine;
}

// Get recipes (will return empty array if no database connection)
$recipes = get_recipes(user_id(), $filters);

// Paginate results
$total = count($recipes);
$offset = ($page - 1) * $per_page;
$paginated_recipes = array_slice($recipes, $offset, $per_page);

// Format response
$response = [
    'success' => true,
    'data' => array_map(function($recipe) {
        return [
            'id' => intval($recipe['id']),
            'title' => $recipe['title'],
            'image_url' => $recipe['image_url'],
            'created_at' => $recipe['created_at'],
            'ingredient_count' => intval($recipe['ingredient_count'])
        ];
    }, $paginated_recipes),
    'pagination' => [
        'page' => $page,
        'per_page' => $per_page,
        'total' => $total,
        'total_pages' => ceil($total / $per_page)
    ]
];

json_out($response);


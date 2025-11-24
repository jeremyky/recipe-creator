<?php
/**
 * Populate sample data for Recipe Creator
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

require __DIR__ . '/lib/session.php';
require __DIR__ . '/lib/util.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/repo.php';

$pdo = db_connect();
if ($pdo === null) {
    die("Database connection failed. Please ensure PostgreSQL is running.");
}

$userId = 1; // Demo user

echo "<!DOCTYPE html><html><head><title>Populate Sample Data</title>";
echo "<style>body{font-family:system-ui;padding:2rem;max-width:800px;margin:0 auto;} .ok{color:#10b981;} .error{color:#b91c1c;} pre{background:#f5f5f5;padding:1rem;border-radius:8px;}</style>";
echo "</head><body><h1>Populating Sample Data</h1>";

// Sample recipes
$sampleRecipes = [
    [
        'title' => 'Classic Spaghetti Carbonara',
        'image_url' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800',
        'steps' => "1. Boil water and cook spaghetti until al dente\n2. Cook pancetta in a large pan until crispy\n3. Whisk eggs and parmesan cheese together\n4. Drain pasta, reserving some pasta water\n5. Toss hot pasta with pancetta, then mix in egg mixture\n6. Add pasta water if needed for creaminess\n7. Season with black pepper and serve immediately",
        'ingredients' => [
            "1 lb spaghetti",
            "8 oz pancetta, diced",
            "4 large eggs",
            "1 cup grated parmesan cheese",
            "Black pepper to taste",
            "Salt for pasta water"
        ]
    ],
    [
        'title' => 'Chicken Tikka Masala',
        'image_url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=800',
        'steps' => "1. Marinate chicken in yogurt, lemon, and spices for 30 minutes\n2. Grill or pan-fry chicken until cooked through\n3. In a separate pan, sauté onions and garlic\n4. Add tomato sauce, cream, and spices\n5. Simmer sauce until thickened\n6. Add cooked chicken to sauce\n7. Serve over basmati rice with naan bread",
        'ingredients' => [
            "2 lbs chicken breast, cubed",
            "1 cup plain yogurt",
            "1 can tomato sauce",
            "1 cup heavy cream",
            "1 large onion, diced",
            "4 cloves garlic, minced",
            "2 tbsp garam masala",
            "1 tbsp turmeric",
            "1 tsp cumin",
            "Basmati rice for serving"
        ]
    ],
    [
        'title' => 'Greek Salad',
        'image_url' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800',
        'steps' => "1. Chop tomatoes, cucumber, and red onion\n2. Slice kalamata olives in half\n3. Crumble feta cheese\n4. Mix all vegetables in a large bowl\n5. Drizzle with olive oil and red wine vinegar\n6. Add oregano, salt, and pepper\n7. Toss gently and serve chilled",
        'ingredients' => [
            "4 large tomatoes, chopped",
            "1 large cucumber, chopped",
            "1 red onion, sliced",
            "1 cup kalamata olives",
            "8 oz feta cheese",
            "1/4 cup olive oil",
            "2 tbsp red wine vinegar",
            "1 tsp dried oregano",
            "Salt and pepper to taste"
        ]
    ],
    [
        'title' => 'Pad Thai',
        'image_url' => 'https://images.unsplash.com/photo-1559314809-0d155014e29e?w=800',
        'steps' => "1. Soak rice noodles in warm water for 30 minutes\n2. Heat oil in a wok or large pan\n3. Scramble eggs and set aside\n4. Cook shrimp until pink\n5. Add noodles, tamarind paste, fish sauce, and sugar\n6. Toss everything together\n7. Add bean sprouts and peanuts\n8. Serve with lime wedges",
        'ingredients' => [
            "8 oz rice noodles",
            "1/2 lb shrimp, peeled",
            "2 eggs",
            "3 tbsp tamarind paste",
            "2 tbsp fish sauce",
            "2 tbsp brown sugar",
            "1 cup bean sprouts",
            "1/4 cup chopped peanuts",
            "2 green onions, chopped",
            "Lime wedges for serving"
        ]
    ],
    [
        'title' => 'Beef Tacos',
        'image_url' => 'https://images.unsplash.com/photo-1565299585323-38174c3b18c8?w=800',
        'steps' => "1. Brown ground beef in a large pan\n2. Add taco seasoning and water\n3. Simmer until liquid is absorbed\n4. Warm taco shells in oven\n5. Prepare toppings: lettuce, tomatoes, cheese, sour cream\n6. Fill shells with beef\n7. Top with desired toppings and serve",
        'ingredients' => [
            "1 lb ground beef",
            "1 packet taco seasoning",
            "8-10 taco shells",
            "2 cups shredded lettuce",
            "2 tomatoes, diced",
            "1 cup shredded cheddar cheese",
            "1/2 cup sour cream",
            "Salsa for serving"
        ]
    ],
    [
        'title' => 'Chocolate Chip Cookies',
        'image_url' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=800',
        'steps' => "1. Cream butter and sugars together\n2. Beat in eggs and vanilla\n3. Mix in flour, baking soda, and salt\n4. Stir in chocolate chips\n5. Drop rounded tablespoons onto baking sheet\n6. Bake at 375°F for 9-11 minutes\n7. Cool on baking sheet for 2 minutes, then transfer",
        'ingredients' => [
            "2 1/4 cups all-purpose flour",
            "1 tsp baking soda",
            "1 tsp salt",
            "1 cup butter, softened",
            "3/4 cup granulated sugar",
            "3/4 cup brown sugar",
            "2 large eggs",
            "2 tsp vanilla extract",
            "2 cups chocolate chips"
        ]
    ]
];

// Sample pantry items
$samplePantryItems = [
    ['ingredient' => 'Chicken Breast', 'quantity' => 2, 'unit' => 'lb'],
    ['ingredient' => 'Ground Beef', 'quantity' => 1.5, 'unit' => 'lb'],
    ['ingredient' => 'Spaghetti', 'quantity' => 1, 'unit' => 'lb'],
    ['ingredient' => 'Tomatoes', 'quantity' => 6, 'unit' => 'piece'],
    ['ingredient' => 'Onions', 'quantity' => 3, 'unit' => 'piece'],
    ['ingredient' => 'Garlic', 'quantity' => 1, 'unit' => 'head'],
    ['ingredient' => 'Olive Oil', 'quantity' => 16, 'unit' => 'oz'],
    ['ingredient' => 'Parmesan Cheese', 'quantity' => 8, 'unit' => 'oz'],
    ['ingredient' => 'Eggs', 'quantity' => 12, 'unit' => 'piece'],
    ['ingredient' => 'Rice', 'quantity' => 2, 'unit' => 'cup'],
    ['ingredient' => 'Flour', 'quantity' => 5, 'unit' => 'lb'],
    ['ingredient' => 'Sugar', 'quantity' => 4, 'unit' => 'lb'],
    ['ingredient' => 'Butter', 'quantity' => 1, 'unit' => 'lb'],
    ['ingredient' => 'Milk', 'quantity' => 1, 'unit' => 'gallon'],
    ['ingredient' => 'Salt', 'quantity' => 1, 'unit' => 'lb'],
    ['ingredient' => 'Black Pepper', 'quantity' => 4, 'unit' => 'oz'],
    ['ingredient' => 'Cucumber', 'quantity' => 2, 'unit' => 'piece'],
    ['ingredient' => 'Feta Cheese', 'quantity' => 8, 'unit' => 'oz'],
    ['ingredient' => 'Lettuce', 'quantity' => 1, 'unit' => 'head'],
    ['ingredient' => 'Shrimp', 'quantity' => 1, 'unit' => 'lb']
];

// Clear existing data (optional - comment out if you want to keep existing data)
echo "<h2>Clearing existing data...</h2>";
try {
    $pdo->exec("DELETE FROM recipe_ingredient");
    $pdo->exec("DELETE FROM recipe");
    $pdo->exec("DELETE FROM pantry_item");
    echo "<p class='ok'>[OK] Cleared existing recipes and pantry items</p>";
} catch (Exception $e) {
    echo "<p class='error'>[ERROR] " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Insert sample recipes
echo "<h2>Inserting Sample Recipes...</h2>";
$recipeCount = 0;
foreach ($sampleRecipes as $recipe) {
    try {
        $recipeId = save_recipe($userId, [
            'title' => $recipe['title'],
            'image_url' => $recipe['image_url'],
            'steps' => $recipe['steps']
        ]);
        
        if ($recipeId > 0) {
            save_recipe_ingredients($recipeId, $recipe['ingredients']);
            $recipeCount++;
            echo "<p class='ok'>[OK] Added recipe: " . htmlspecialchars($recipe['title']) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>[ERROR] Failed to add " . htmlspecialchars($recipe['title']) . ": " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Insert sample pantry items
echo "<h2>Inserting Sample Pantry Items...</h2>";
$pantryCount = 0;
foreach ($samplePantryItems as $item) {
    try {
        $itemId = add_pantry_item($userId, [
            'ingredient' => $item['ingredient'],
            'quantity' => $item['quantity'],
            'unit' => $item['unit']
        ]);
        
        if ($itemId > 0) {
            $pantryCount++;
            echo "<p class='ok'>[OK] Added pantry item: " . htmlspecialchars($item['ingredient']) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>[ERROR] Failed to add " . htmlspecialchars($item['ingredient']) . ": " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<h2>Summary</h2>";
echo "<p class='ok'><strong>Successfully added:</strong></p>";
echo "<ul>";
echo "<li>$recipeCount recipes</li>";
echo "<li>$pantryCount pantry items</li>";
echo "</ul>";

echo "<p><a href='index.php'>Go to Home</a> | <a href='index.php?action=recipes'>View Recipes</a> | <a href='index.php?action=pantry'>View Pantry</a></p>";
echo "</body></html>";
?>

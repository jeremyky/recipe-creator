<?php
/**
 * Sample Data Population Script
 * Run this to add sample recipes and pantry items for testing
 * Usage: php populate_sample_data.php
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "🌱 Populating Sample Data...\n\n";

require __DIR__ . '/lib/db.php';

try {
    $pdo = db_connect();
} catch (Exception $e) {
    $pdo = null;
}

if ($pdo === null) {
    echo "❌ Cannot connect to database.\n";
    echo "💡 Make sure PostgreSQL is running and credentials are configured.\n";
    exit(1);
}

echo "✅ Connected to database\n\n";

// Demo user ID (created by init_db.php)
$userId = 1;

// Sample recipes
$sampleRecipes = [
    [
        'title' => 'Classic Spaghetti Carbonara',
        'cuisine' => 'italian',
        'image_url' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800',
        'steps' => "1. Bring a large pot of salted water to boil and cook spaghetti until al dente\n2. While pasta cooks, fry diced pancetta until crispy\n3. Beat eggs with grated Parmesan cheese and black pepper\n4. Drain pasta, reserving 1 cup pasta water\n5. Toss hot pasta with pancetta, then remove from heat\n6. Quickly stir in egg mixture, adding pasta water to create creamy sauce\n7. Serve immediately with extra Parmesan",
        'ingredients' => [
            '1 lb spaghetti',
            '6 oz pancetta or guanciale, diced',
            '4 large eggs',
            '1 cup grated Parmesan cheese',
            'Freshly ground black pepper',
            'Salt for pasta water'
        ]
    ],
    [
        'title' => 'Thai Chicken Pad Thai',
        'cuisine' => 'thai',
        'image_url' => 'https://images.unsplash.com/photo-1559314809-0d155014e29e?w=800',
        'steps' => "1. Soak rice noodles in warm water for 30 minutes\n2. Make sauce by mixing tamarind paste, fish sauce, sugar, and sriracha\n3. Heat wok over high heat with oil\n4. Stir-fry chicken until cooked through\n5. Push chicken aside, scramble eggs in wok\n6. Add drained noodles and sauce, toss everything together\n7. Add bean sprouts, peanuts, and green onions\n8. Serve with lime wedges",
        'ingredients' => [
            '8 oz rice noodles',
            '1 lb chicken breast, sliced thin',
            '3 eggs, beaten',
            '3 tbsp tamarind paste',
            '3 tbsp fish sauce',
            '2 tbsp brown sugar',
            '1 tsp sriracha',
            '1 cup bean sprouts',
            '1/2 cup roasted peanuts, crushed',
            '3 green onions, chopped',
            '2 limes, cut into wedges'
        ]
    ],
    [
        'title' => 'Homemade Margherita Pizza',
        'cuisine' => 'italian',
        'image_url' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800',
        'steps' => "1. Preheat oven to 500°F with pizza stone inside\n2. Roll out pizza dough into 12-inch circle\n3. Brush dough with olive oil\n4. Spread crushed tomatoes evenly, leaving 1-inch border\n5. Tear fresh mozzarella and distribute over sauce\n6. Sprinkle with salt and Italian herbs\n7. Transfer to hot pizza stone and bake 10-12 minutes\n8. Top with fresh basil leaves and drizzle with olive oil",
        'ingredients' => [
            '1 lb pizza dough',
            '1 cup crushed San Marzano tomatoes',
            '8 oz fresh mozzarella',
            '3 tbsp extra virgin olive oil',
            'Fresh basil leaves',
            '1 tsp dried oregano',
            'Salt to taste'
        ]
    ],
    [
        'title' => 'Beef Tacos with Guacamole',
        'cuisine' => 'mexican',
        'image_url' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800',
        'steps' => "1. Brown ground beef in skillet over medium-high heat\n2. Add taco seasoning and water, simmer until thickened\n3. Make guacamole: mash avocados with lime, salt, cilantro, and diced onion\n4. Warm tortillas in dry skillet\n5. Assemble tacos with beef, lettuce, tomatoes, cheese, sour cream\n6. Top with guacamole and salsa\n7. Serve with lime wedges",
        'ingredients' => [
            '1 lb ground beef',
            '1 packet taco seasoning',
            '8 taco shells or tortillas',
            '3 ripe avocados',
            '1 lime, juiced',
            '1/4 cup diced red onion',
            '2 tbsp fresh cilantro, chopped',
            '1 cup shredded lettuce',
            '2 tomatoes, diced',
            '1 cup shredded cheddar cheese',
            '1/2 cup sour cream',
            'Salsa for serving'
        ]
    ],
    [
        'title' => 'Classic Caesar Salad',
        'cuisine' => 'american',
        'image_url' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800',
        'steps' => "1. Make dressing: blend garlic, anchovies, lemon juice, Dijon, Worcestershire\n2. Slowly drizzle in olive oil while blending\n3. Stir in grated Parmesan cheese\n4. Tear romaine lettuce into bite-sized pieces\n5. Toss lettuce with dressing until well coated\n6. Top with homemade croutons and extra Parmesan\n7. Garnish with cracked black pepper",
        'ingredients' => [
            '2 heads romaine lettuce',
            '2 cloves garlic',
            '4 anchovy fillets',
            '1 lemon, juiced',
            '1 tsp Dijon mustard',
            '1 tsp Worcestershire sauce',
            '1/2 cup extra virgin olive oil',
            '1 cup grated Parmesan cheese',
            '2 cups homemade croutons',
            'Black pepper to taste'
        ]
    ],
    [
        'title' => 'Butter Chicken (Murgh Makhani)',
        'cuisine' => 'indian',
        'image_url' => 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=800',
        'steps' => "1. Marinate chicken in yogurt, garam masala, and ginger-garlic paste for 2 hours\n2. Grill or pan-fry marinated chicken until charred\n3. Make sauce: sauté onions, add tomato puree, spices, and cook 10 minutes\n4. Blend sauce until smooth and strain\n5. Return sauce to pan, add cream and butter\n6. Add cooked chicken and simmer 15 minutes\n7. Garnish with cream and cilantro\n8. Serve with naan bread and basmati rice",
        'ingredients' => [
            '2 lbs boneless chicken thighs',
            '1 cup plain yogurt',
            '2 tbsp garam masala',
            '2 tbsp ginger-garlic paste',
            '2 onions, chopped',
            '1 can (28 oz) crushed tomatoes',
            '1 cup heavy cream',
            '4 tbsp butter',
            '1 tsp turmeric',
            '1 tsp cumin',
            '1 tsp paprika',
            'Fresh cilantro for garnish',
            'Salt to taste'
        ]
    ]
];

// Sample pantry items
$samplePantry = [
    ['ingredient' => 'All-Purpose Flour', 'quantity' => 5, 'unit' => 'lbs'],
    ['ingredient' => 'White Sugar', 'quantity' => 2, 'unit' => 'lbs'],
    ['ingredient' => 'Brown Sugar', 'quantity' => 1, 'unit' => 'lb'],
    ['ingredient' => 'Salt', 'quantity' => 26, 'unit' => 'oz'],
    ['ingredient' => 'Black Pepper', 'quantity' => 4, 'unit' => 'oz'],
    ['ingredient' => 'Olive Oil', 'quantity' => 32, 'unit' => 'fl oz'],
    ['ingredient' => 'Vegetable Oil', 'quantity' => 48, 'unit' => 'fl oz'],
    ['ingredient' => 'Garlic', 'quantity' => 12, 'unit' => 'cloves'],
    ['ingredient' => 'Onions', 'quantity' => 3, 'unit' => 'whole'],
    ['ingredient' => 'Pasta (Spaghetti)', 'quantity' => 2, 'unit' => 'lbs'],
    ['ingredient' => 'Rice', 'quantity' => 5, 'unit' => 'lbs'],
    ['ingredient' => 'Canned Tomatoes', 'quantity' => 4, 'unit' => 'cans'],
    ['ingredient' => 'Chicken Broth', 'quantity' => 2, 'unit' => 'cartons'],
    ['ingredient' => 'Soy Sauce', 'quantity' => 16, 'unit' => 'fl oz'],
    ['ingredient' => 'Parmesan Cheese', 'quantity' => 8, 'unit' => 'oz']
];

echo "📖 Adding Sample Recipes...\n";

foreach ($sampleRecipes as $recipe) {
    try {
        // Insert recipe
        $stmt = $pdo->prepare("
            INSERT INTO recipe (user_id, title, cuisine, image_url, steps)
            VALUES (:user_id, :title, :cuisine, :image_url, :steps)
            RETURNING id
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'title' => $recipe['title'],
            'cuisine' => $recipe['cuisine'] ?? null,
            'image_url' => $recipe['image_url'],
            'steps' => $recipe['steps']
        ]);
        
        $recipeId = $stmt->fetchColumn();
        
        // Insert ingredients
        $stmt = $pdo->prepare("
            INSERT INTO recipe_ingredient (recipe_id, line)
            VALUES (:recipe_id, :line)
        ");
        
        foreach ($recipe['ingredients'] as $ingredient) {
            $stmt->execute([
                'recipe_id' => $recipeId,
                'line' => $ingredient
            ]);
        }
        
        echo "   ✅ Added: {$recipe['title']}\n";
        
    } catch (PDOException $e) {
        echo "   ❌ Error adding {$recipe['title']}: " . $e->getMessage() . "\n";
    }
}

echo "\n🥫 Adding Sample Pantry Items...\n";

foreach ($samplePantry as $item) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO pantry_item (user_id, ingredient, quantity, unit)
            VALUES (:user_id, :ingredient, :quantity, :unit)
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'ingredient' => $item['ingredient'],
            'quantity' => $item['quantity'],
            'unit' => $item['unit']
        ]);
        
        echo "   ✅ Added: {$item['quantity']} {$item['unit']} {$item['ingredient']}\n";
        
    } catch (PDOException $e) {
        echo "   ❌ Error adding {$item['ingredient']}: " . $e->getMessage() . "\n";
    }
}

echo "\n✨ Sample Data Population Complete!\n";
echo "🎉 You now have 6 recipes and 15 pantry items to test with.\n";
echo "\n💡 Access the app at: http://localhost:8000\n";
echo "   (Demo user auto-login is enabled for local testing)\n\n";
?>

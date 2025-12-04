<?php
$page_title = 'Browse Recipes - Recipe Creator';
$current_page = 'recipes';
$recipes = $recipes ?? [];
$last_cuisine = $last_cuisine ?? '';
?>

<div class="section-header">
  <h1>Browse Recipes</h1>
  <p>Discover recipes by cuisine, search by name, or explore our collection</p>
</div>

<section aria-labelledby="filters-heading">
  <h2 class="sr-only" id="filters-heading">Filter recipes</h2>
  <div class="card">
    <form method="get" action="index.php" style="display: flex; flex-direction: column; gap: var(--space-l);">
      <input type="hidden" name="action" value="recipes">
      <div style="display: grid; grid-template-columns: 1fr 200px auto; gap: var(--space-l); align-items: end;">
        <div>
          <label for="search-recipes">Search recipes</label>
          <input type="search" id="search-recipes" name="search" 
                 value="<?= h($_GET['search'] ?? '') ?>" 
                 placeholder="pasta, chicken, curry...">
        </div>
        <div>
          <label for="cuisine-filter">Cuisine</label>
          <select id="cuisine-filter" name="cuisine">
            <option value="">All cuisines</option>
            <?php
            $cuisines = ['italian', 'chinese', 'mexican', 'indian', 'thai', 'greek', 'american'];
            foreach ($cuisines as $cuisine):
            ?>
              <option value="<?= h($cuisine) ?>" 
                      <?= ($_GET['cuisine'] ?? $last_cuisine) === $cuisine ? 'selected' : '' ?>>
                <?= ucfirst(h($cuisine)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn-primary">Search</button>
      </div>
    </form>
  </div>
</section>

<section aria-labelledby="results-heading">
  <div class="section-header">
    <h2 id="results-heading">All Recipes</h2>
    <p class="text-muted"><?= count($recipes) ?> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found</p>
  </div>
  <?php if (empty($recipes)): ?>
    <div class="card" id="no-recipes-message">
      <p>No recipes found. <a href="index.php?action=upload">Upload a recipe</a> or <a href="index.php?action=chat">ask the AI for recipes</a> to get started!</p>
    </div>
  <?php else: ?>
    <div class="grid grid-3" id="recipes-grid">
      <?php foreach ($recipes as $recipe): ?>
        <article class="card recipe-card">
          <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" style="text-decoration: none; color: inherit; display: block;">
            <div class="card-image recipe-img">
              <?php if (!empty($recipe['image_url'])): ?>
                <img src="<?= h($recipe['image_url']) ?>" 
                     alt="<?= h($recipe['title']) ?>">
              <?php else: ?>
                <span style="font-size: 64px; opacity: 0.3;">🍳</span>
              <?php endif; ?>
            </div>
            <div class="card-header">
              <h3 class="recipe-title"><?= h($recipe['title']) ?></h3>
              <p class="text-small">
                <?= date('M j, Y', strtotime($recipe['created_at'])) ?> • 
                <span class="chip chip-primary" style="margin-left: 8px;"><?= intval($recipe['ingredient_count']) ?> ingredients</span>
              </p>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  
  <!-- Container for local storage recipes -->
  <div id="local-recipes-container"></div>
  
  <script>
  // Load and display localStorage recipes (for local testing)
  (function() {
    let localRecipes = JSON.parse(localStorage.getItem('local_recipes') || '[]');
    
    // Clean up malformed recipes (titles that contain full recipe text)
    let needsCleanup = false;
    localRecipes = localRecipes.map(recipe => {
      // If title is suspiciously long or contains CUISINE/INGREDIENTS/STEPS, extract just the title
      if (recipe.title && (recipe.title.length > 200 || recipe.title.includes('CUISINE:') || recipe.title.includes('INGREDIENTS:'))) {
        needsCleanup = true;
        
        // Try to extract just the title part
        const titleMatch = recipe.title.match(/^([^C]+?)(?:CUISINE:|INGREDIENTS:|STEPS:|$)/);
        if (titleMatch) {
          const cleanTitle = titleMatch[1].trim();
          console.log(`🧹 Cleaning recipe title: "${recipe.title.substring(0, 50)}..." → "${cleanTitle}"`);
          return { ...recipe, title: cleanTitle };
        }
      }
      return recipe;
    });
    
    // Save cleaned recipes back to localStorage
    if (needsCleanup) {
      localStorage.setItem('local_recipes', JSON.stringify(localRecipes));
      console.log('✅ Cleaned up malformed recipe titles in localStorage');
    }
    
    if (localRecipes.length > 0) {
      const container = document.getElementById('local-recipes-container');
      const recipesGrid = document.getElementById('recipes-grid');
      const noRecipesMsg = document.getElementById('no-recipes-message');
      
      // Hide "no recipes" message if we have local recipes
      if (noRecipesMsg && localRecipes.length > 0) {
        noRecipesMsg.style.display = 'none';
      }
      
      // Create or get grid
      let grid = recipesGrid;
      if (!grid) {
        grid = document.createElement('div');
        grid.className = 'grid grid-3';
        grid.id = 'recipes-grid';
        container.parentElement.insertBefore(grid, container);
      }
      
      // Add section header for local recipes
      const header = document.createElement('div');
      header.style.gridColumn = '1 / -1';
      header.innerHTML = '<p class="chip chip-info" style="display: inline-block; margin-bottom: 1rem;">📦 Local Testing Mode - Recipes stored in browser</p>';
      grid.appendChild(header);
      
      // Add each local recipe
      localRecipes.forEach(recipe => {
        const ingredientCount = recipe.ingredients ? recipe.ingredients.split('\n').filter(l => l.trim()).length : 0;
        const createdDate = recipe.created_at ? new Date(recipe.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Recently';
        
        const card = document.createElement('article');
        card.className = 'card recipe-card';
        card.innerHTML = `
          <div style="display: block; text-decoration: none; color: inherit; cursor: pointer;" onclick="viewLocalRecipe('${recipe.id}')">
            <div class="card-image recipe-img">
              <span style="font-size: 64px; opacity: 0.3;">🍳</span>
            </div>
            <div class="card-header">
              <h3 class="recipe-title">${escapeHtml(recipe.title)}</h3>
              <p class="text-small">
                ${createdDate} • 
                <span class="chip chip-primary" style="margin-left: 8px;">${ingredientCount} ingredients</span>
              </p>
            </div>
          </div>
        `;
        grid.appendChild(card);
      });
      
      // Update recipe count
      const countElement = document.querySelector('.section-header .text-muted');
      if (countElement) {
        const dbCount = <?= count($recipes) ?>;
        const totalCount = dbCount + localRecipes.length;
        countElement.textContent = `${totalCount} recipe${totalCount !== 1 ? 's' : ''} found (${localRecipes.length} local)`;
      }
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    // Function to view local recipe details
    window.viewLocalRecipe = function(recipeId) {
      const localRecipes = JSON.parse(localStorage.getItem('local_recipes') || '[]');
      const recipe = localRecipes.find(r => r.id === recipeId);
      
      if (recipe) {
        // Create a modal or detailed view
        const ingredients = recipe.ingredients.split('\n').map(i => `• ${i}`).join('\n');
        const steps = recipe.steps.split('\n').map((s, i) => `${i + 1}. ${s}`).join('\n');
        
        const message = `📖 ${recipe.title}\n\n` +
              `🍽️ Cuisine: ${recipe.cuisine || 'Not specified'}\n\n` +
              `📝 INGREDIENTS:\n${ingredients}\n\n` +
              `👨‍🍳 STEPS:\n${steps}\n\n` +
              `💡 This recipe is stored locally in your browser.\n\n` +
              `Click OK to edit this recipe.`;
        
        if (confirm(message)) {
          // Redirect to edit page with local recipe data
          sessionStorage.setItem('editLocalRecipe', JSON.stringify(recipe));
          window.location.href = `index.php?action=recipe_edit&id=${recipeId}`;
        }
      }
    };
  })();
  </script>
</section>


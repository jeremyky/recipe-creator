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
    <form method="get" action="index.php" id="recipe-filters-form">
      <input type="hidden" name="action" value="recipes">
      
      <!-- Search and Sort Row -->
      <div style="display: grid; grid-template-columns: 1fr 200px 200px; gap: var(--space-l); margin-bottom: var(--space-l);">
        <div>
          <label for="search-recipes">Search recipes</label>
          <input type="search" id="search-recipes" name="search" 
                 value="<?= h($_GET['search'] ?? '') ?>" 
                 placeholder="Search by name or ingredients..."
                 style="width: 100%;">
        </div>
        <div>
          <label for="cuisine-filter">Cuisine Type</label>
          <select id="cuisine-filter" name="cuisine" onchange="this.form.submit()">
            <option value="">All cuisines</option>
            <?php
            $cuisines = ['italian', 'chinese', 'mexican', 'indian', 'thai', 'greek', 'american', 'other'];
            foreach ($cuisines as $cuisine):
            ?>
              <option value="<?= h($cuisine) ?>" 
                      <?= ($_GET['cuisine'] ?? '') === $cuisine ? 'selected' : '' ?>>
                <?= ucfirst(h($cuisine)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="sort-by">Sort By</label>
          <select id="sort-by" name="sort" onchange="this.form.submit()">
            <option value="date_desc" <?= ($_GET['sort'] ?? 'date_desc') === 'date_desc' ? 'selected' : '' ?>>
              📅 Newest First
            </option>
            <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>
              📅 Oldest First
            </option>
            <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>
              🔤 A → Z
            </option>
            <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>
              🔤 Z → A
            </option>
            <option value="ingredients_asc" <?= ($_GET['sort'] ?? '') === 'ingredients_asc' ? 'selected' : '' ?>>
              📊 Fewest Ingredients
            </option>
            <option value="ingredients_desc" <?= ($_GET['sort'] ?? '') === 'ingredients_desc' ? 'selected' : '' ?>>
              📊 Most Ingredients
            </option>
          </select>
        </div>
      </div>
      
      <!-- Filter Checkboxes Row -->
      <div style="display: flex; gap: var(--space-xl); align-items: center; flex-wrap: wrap; padding: var(--space-m) 0; border-top: 1px solid var(--color-border-subtle);">
        <label style="display: flex; align-items: center; gap: var(--space-s); cursor: pointer; user-select: none;">
          <input type="checkbox" id="favorites-only" name="favorites_only" value="1" 
                 <?= !empty($_GET['favorites_only']) ? 'checked' : '' ?>
                 onchange="this.form.submit()"
                 style="width: 18px; height: 18px; cursor: pointer;">
          <span style="font-weight: 500;">⭐ Favorites only</span>
        </label>
        
        <button type="submit" class="btn-primary" style="margin-left: auto;">
          🔍 Apply Filters
        </button>
        
        <?php if (!empty($_GET['search']) || !empty($_GET['cuisine']) || !empty($_GET['favorites_only'])): ?>
          <a href="index.php?action=recipes<?= !empty($_GET['sort']) ? '&sort=' . h($_GET['sort']) : '' ?>" 
             class="btn-ghost" 
             style="padding: var(--space-s) var(--space-m);">
            ✖️ Clear Filters
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</section>

<section aria-labelledby="results-heading">
  <div class="section-header">
    <div>
      <h2 id="results-heading">
        <?= !empty($_GET['favorites_only']) ? '⭐ Favorite Recipes' : 'All Recipes' ?>
      </h2>
      <p class="text-muted">
        <?= count($recipes) ?> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found
        <?php if (!empty($_GET['search']) || !empty($_GET['cuisine'])): ?>
          <span style="margin-left: var(--space-s);">
            • Filtered
            <?php if (!empty($_GET['search'])): ?>
              by <strong>"<?= h($_GET['search']) ?>"</strong>
            <?php endif; ?>
            <?php if (!empty($_GET['cuisine'])): ?>
              <span class="chip chip-primary" style="margin-left: var(--space-xs);">
                <?= ucfirst(h($_GET['cuisine'])) ?>
              </span>
            <?php endif; ?>
          </span>
        <?php endif; ?>
      </p>
    </div>
  </div>
  <?php if (empty($recipes)): ?>
    <div class="card" id="no-recipes-message">
      <p>No recipes found. <a href="index.php?action=upload">Upload a recipe</a> or <a href="index.php?action=chat">ask the AI for recipes</a> to get started!</p>
    </div>
  <?php else: ?>
    <div class="grid grid-3" id="recipes-grid">
      <?php foreach ($recipes as $recipe): 
        // Convert to boolean (handles 1, '1', true, 't')
        $is_fav = !empty($recipe['is_favorite']) && $recipe['is_favorite'] !== '0' && $recipe['is_favorite'] !== 0;
      ?>
        <article class="card recipe-card" style="position: relative;">
          <button 
            class="favorite-btn <?= $is_fav ? 'is-favorite' : '' ?>" 
            data-recipe-id="<?= $recipe['id'] ?>"
            onclick="toggleFavorite(event, '<?= $recipe['id'] ?>')"
            title="<?= $is_fav ? 'Remove from favorites' : 'Add to favorites' ?>"
            style="position: absolute; top: 12px; right: 12px; z-index: 10; background: rgba(255,255,255,0.9); border: 2px solid var(--color-border); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 20px; transition: all 0.2s ease;">
            <span class="star-icon"><?= $is_fav ? '⭐' : '☆' ?></span>
          </button>
          
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
    
    // Apply sorting to local recipes
    const urlParams = new URLSearchParams(window.location.search);
    const sortBy = urlParams.get('sort') || 'date_desc';
    const localFavorites = JSON.parse(localStorage.getItem('local_favorites') || '[]');
    
    // Add is_favorite flag to each recipe
    localRecipes = localRecipes.map(recipe => ({
      ...recipe,
      is_favorite: localFavorites.includes(recipe.id)
    }));
    
    // Sort function
    localRecipes.sort((a, b) => {
      // Favorites always first
      if (a.is_favorite !== b.is_favorite) {
        return b.is_favorite ? 1 : -1;
      }
      
      // Then apply chosen sort
      switch (sortBy) {
        case 'name_asc':
          return a.title.localeCompare(b.title);
        case 'name_desc':
          return b.title.localeCompare(a.title);
        case 'date_asc':
          return new Date(a.created_at) - new Date(b.created_at);
        case 'date_desc':
          return new Date(b.created_at) - new Date(a.created_at);
        case 'ingredients_asc':
          const aCount = a.ingredients ? a.ingredients.split('\n').filter(l => l.trim()).length : 0;
          const bCount = b.ingredients ? b.ingredients.split('\n').filter(l => l.trim()).length : 0;
          return aCount - bCount;
        case 'ingredients_desc':
          const aCount2 = a.ingredients ? a.ingredients.split('\n').filter(l => l.trim()).length : 0;
          const bCount2 = b.ingredients ? b.ingredients.split('\n').filter(l => l.trim()).length : 0;
          return bCount2 - aCount2;
        default:
          return new Date(b.created_at) - new Date(a.created_at);
      }
    });
    
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
      
      // Add each local recipe (already sorted with favorites first)
      localRecipes.forEach(recipe => {
        const ingredientCount = recipe.ingredients ? recipe.ingredients.split('\n').filter(l => l.trim()).length : 0;
        const createdDate = recipe.created_at ? new Date(recipe.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Recently';
        const isFavorite = recipe.is_favorite; // Use from recipe object (set during sorting)
        
        const card = document.createElement('article');
        card.className = 'card recipe-card';
        card.style.position = 'relative';
        card.innerHTML = `
          <button 
            class="favorite-btn ${isFavorite ? 'is-favorite' : ''}" 
            data-recipe-id="${recipe.id}"
            onclick="toggleFavorite(event, '${recipe.id}')"
            title="${isFavorite ? 'Remove from favorites' : 'Add to favorites'}"
            style="position: absolute; top: 12px; right: 12px; z-index: 10; background: rgba(255,255,255,0.9); border: 2px solid var(--color-border); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 20px; transition: all 0.2s ease;">
            <span class="star-icon">${isFavorite ? '⭐' : '☆'}</span>
          </button>
          
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
  
  // Favorite toggle functionality
  async function toggleFavorite(event, recipeId) {
    event.preventDefault();
    event.stopPropagation();
    
    const button = event.currentTarget;
    const starIcon = button.querySelector('.star-icon');
    const isFavorite = button.classList.contains('is-favorite');
    
    // Optimistic UI update
    button.disabled = true;
    button.style.opacity = '0.6';
    
    try {
      // Check if local recipe
      if (recipeId.startsWith('local_')) {
        // Handle localStorage favorites
        const localFavorites = JSON.parse(localStorage.getItem('local_favorites') || '[]');
        
        if (isFavorite) {
          // Remove from favorites
          const filtered = localFavorites.filter(id => id !== recipeId);
          localStorage.setItem('local_favorites', JSON.stringify(filtered));
          button.classList.remove('is-favorite');
          starIcon.textContent = '☆';
          button.title = 'Add to favorites';
        } else {
          // Add to favorites
          localFavorites.push(recipeId);
          localStorage.setItem('local_favorites', JSON.stringify(localFavorites));
          button.classList.add('is-favorite');
          starIcon.textContent = '⭐';
          button.title = 'Remove from favorites';
        }
        
        button.disabled = false;
        button.style.opacity = '1';
        return;
      }
      
      // Database recipe - call API
      const response = await fetch('api/toggle_favorite.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ recipe_id: recipeId })
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Update UI based on response
        if (data.is_favorite) {
          button.classList.add('is-favorite');
          starIcon.textContent = '⭐';
          button.title = 'Remove from favorites';
        } else {
          button.classList.remove('is-favorite');
          starIcon.textContent = '☆';
          button.title = 'Add to favorites';
        }
        
        // If favorites filter is active, remove card from view
        const favoritesOnly = new URLSearchParams(window.location.search).get('favorites_only');
        if (favoritesOnly && !data.is_favorite) {
          button.closest('.recipe-card').style.animation = 'fadeOut 0.3s ease-out';
          setTimeout(() => {
            button.closest('.recipe-card').remove();
            // Update count
            const remaining = document.querySelectorAll('.recipe-card').length;
            document.querySelector('.text-muted').textContent = remaining + ' recipe' + (remaining !== 1 ? 's' : '') + ' found';
          }, 300);
        }
      } else {
        throw new Error(data.error || 'Failed to toggle favorite');
      }
      
    } catch (error) {
      console.error('Error toggling favorite:', error);
      alert('Failed to update favorite: ' + error.message);
    } finally {
      button.disabled = false;
      button.style.opacity = '1';
    }
  }
  </script>
</section>


<?php
$page_title = 'Browse Recipes - Recipe Creator';
$current_page = 'recipes';
$recipes = $recipes ?? [];

// Get current filters
$currentSearch = $_GET['search'] ?? '';
$currentCuisine = $_GET['cuisine'] ?? '';
$currentSort = $_GET['sort'] ?? 'date_desc';
$favoritesOnly = !empty($_GET['favorites_only']);

// Sort options mapping
$sortOptions = [
    'date_desc' => '📅 Newest First',
    'date_asc' => '📅 Oldest First',
    'name_asc' => '🔤 A → Z',
    'name_desc' => '🔤 Z → A',
    'ingredients_asc' => '📊 Fewest Ingredients',
    'ingredients_desc' => '📊 Most Ingredients'
];
$currentSortLabel = $sortOptions[$currentSort] ?? $sortOptions['date_desc'];

// Cuisine options
$cuisines = ['italian', 'chinese', 'mexican', 'indian', 'thai', 'greek', 'american', 'other'];
$currentCuisineLabel = $currentCuisine ? ucfirst($currentCuisine) : 'All Cuisines';
?>

<!-- Include filter CSS -->
<link rel="stylesheet" href="assets/filters.css">

<!-- Modern Filter Bar -->
<div class="filter-bar-container">
  <div class="filter-bar">
    <form method="get" action="index.php" id="filter-form" role="search" aria-label="Recipe filters">
      <input type="hidden" name="action" value="recipes">
      
      <!-- Main Filter Controls -->
      <div class="filter-controls">
        <!-- Search (Pill-shaped with icon) -->
        <div class="filter-search">
          <input 
            type="search" 
            name="search" 
            id="search-input"
            value="<?= h($currentSearch) ?>"
            placeholder="Search recipes or ingredients..."
            aria-label="Search recipes"
            autocomplete="off">
          <span class="filter-search-icon" aria-hidden="true">🔍</span>
        </div>
        
        <!-- Cuisine Dropdown Pill -->
        <div style="position: relative;">
          <button 
            type="button"
            class="filter-pill <?= $currentCuisine ? 'active' : '' ?>"
            id="cuisine-pill"
            data-dropdown="cuisine-dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            tabindex="0">
            <span class="icon" aria-hidden="true">🍽️</span>
            <span class="filter-value"><?= h($currentCuisineLabel) ?></span>
            <span class="chevron" aria-hidden="true">▼</span>
          </button>
          
          <div class="filter-dropdown" id="cuisine-dropdown" data-for-pill="cuisine-pill" role="menu">
            <div class="filter-dropdown-header">Cuisine Type</div>
            <div class="filter-dropdown-option <?= !$currentCuisine ? 'selected' : '' ?>" 
                 role="menuitem"
                 data-value="">
              All Cuisines
              <span class="check-icon" aria-hidden="true">✓</span>
            </div>
            <?php foreach ($cuisines as $cuisine): ?>
              <div class="filter-dropdown-option <?= $currentCuisine === $cuisine ? 'selected' : '' ?>" 
                   role="menuitem"
                   data-value="<?= h($cuisine) ?>">
                <?= ucfirst(h($cuisine)) ?>
                <span class="check-icon" aria-hidden="true">✓</span>
              </div>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="cuisine" id="cuisine-input" value="<?= h($currentCuisine) ?>">
        </div>
        
        <!-- Sort Dropdown Pill -->
        <div style="position: relative;">
          <button 
            type="button"
            class="filter-pill sort-button <?= $currentSort !== 'date_desc' ? 'active' : '' ?>"
            id="sort-pill"
            data-dropdown="sort-dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            tabindex="0">
            <span class="icon" aria-hidden="true">⇅</span>
            <span class="sort-label">Sort:</span>
            <span class="sort-value"><?= h($currentSortLabel) ?></span>
            <span class="chevron" aria-hidden="true">▼</span>
          </button>
          
          <div class="filter-dropdown" id="sort-dropdown" data-for-pill="sort-pill" role="menu">
            <div class="filter-dropdown-header">Sort Order</div>
            <?php foreach ($sortOptions as $value => $label): ?>
              <div class="filter-dropdown-option <?= $currentSort === $value ? 'selected' : '' ?>" 
                   role="menuitem"
                   data-value="<?= h($value) ?>">
                <?= h($label) ?>
                <span class="check-icon" aria-hidden="true">✓</span>
              </div>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="sort" id="sort-input" value="<?= h($currentSort) ?>">
        </div>
        
        <!-- Favorites Toggle -->
        <button 
          type="button"
          class="favorites-toggle <?= $favoritesOnly ? 'active' : '' ?>"
          onclick="this.classList.toggle('active'); document.getElementById('favorites-input').value = this.classList.contains('active') ? '1' : ''; document.getElementById('filter-form').submit();"
          aria-pressed="<?= $favoritesOnly ? 'true' : 'false' ?>"
          aria-label="<?= $favoritesOnly ? 'Show all recipes' : 'Show only favorites' ?>">
          <span class="star" aria-hidden="true"><?= $favoritesOnly ? '⭐' : '☆' ?></span>
          <span><?= $favoritesOnly ? 'Favorites Only' : 'Show Favorites' ?></span>
        </button>
        <input type="hidden" name="favorites_only" id="favorites-input" value="<?= $favoritesOnly ? '1' : '' ?>">
        
        <!-- Results Summary -->
        <div class="results-summary">
          <span class="results-count"><?= count($recipes) ?></span>
          <span>recipe<?= count($recipes) !== 1 ? 's' : '' ?></span>
        </div>
      </div>
      
      <!-- Active Filter Chips -->
      <?php if ($currentSearch || $currentCuisine || $favoritesOnly): ?>
        <div class="filter-chips" role="status" aria-live="polite">
          <?php if ($currentSearch): ?>
            <div class="filter-chip" data-filter-type="search">
              <span class="chip-label">Search:</span>
              <span class="chip-value"><?= h($currentSearch) ?></span>
              <button 
                type="button" 
                class="filter-chip-remove" 
                onclick="document.getElementById('search-input').value=''; document.getElementById('filter-form').submit();"
                aria-label="Remove search filter">
                ×
              </button>
            </div>
          <?php endif; ?>
          
          <?php if ($currentCuisine): ?>
            <div class="filter-chip" data-filter-type="cuisine">
              <span class="chip-label">Cuisine:</span>
              <span class="chip-value"><?= h(ucfirst($currentCuisine)) ?></span>
              <button 
                type="button" 
                class="filter-chip-remove" 
                onclick="document.getElementById('cuisine-input').value=''; document.getElementById('filter-form').submit();"
                aria-label="Remove cuisine filter">
                ×
              </button>
            </div>
          <?php endif; ?>
          
          <?php if ($favoritesOnly): ?>
            <div class="filter-chip" data-filter-type="favorites_only">
              <span class="chip-value">⭐ Favorites Only</span>
              <button 
                type="button" 
                class="filter-chip-remove" 
                onclick="document.getElementById('favorites-input').value=''; document.getElementById('filter-form').submit();"
                aria-label="Remove favorites filter">
                ×
              </button>
            </div>
          <?php endif; ?>
          
          <!-- Clear All -->
          <button 
            type="button" 
            class="clear-filters-btn"
            onclick="window.location.href='index.php?action=recipes&sort=<?= h($currentSort) ?>'"
            aria-label="Clear all filters">
            Clear all
          </button>
        </div>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Include filter JavaScript -->
<script src="assets/js/filters.js"></script>

<section aria-labelledby="results-heading">

<section aria-labelledby="results-heading" style="margin-top: var(--space-xl);">
  <div class="section-header" style="margin-bottom: var(--space-xl);">
    <h2 id="results-heading" style="font-size: 1.5rem; margin-bottom: var(--space-xs);">
      <?= $favoritesOnly ? '⭐ Favorite Recipes' : 'All Recipes' ?>
    </h2>
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
          
          <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" style="text-decoration: none; color: inherit; display: block; cursor: pointer;">
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


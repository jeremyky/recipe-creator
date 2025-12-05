<?php
$page_title = 'Cook & History - Recipe Creator';
$current_page = 'cook';
$recipes = $recipes ?? [];
?>

<div class="section-header">
  <h1>Start Cooking</h1>
  <p>Select a recipe to start a step-by-step cooking session</p>
</div>

<section aria-labelledby="session-heading">
  <div class="section-header">
    <h2 id="session-heading">Available Recipes</h2>
    <p class="text-muted">Choose a recipe to begin</p>
  </div>
  <?php if (empty($recipes)): ?>
    <div class="card">
      <p>No recipes available. <a href="index.php?action=upload">Upload a recipe</a> to get started!</p>
    </div>
  <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($recipes as $recipe): ?>
        <article class="card recipe-card" style="padding: 0; overflow: hidden;">
          <div class="card-image" style="margin: 0; border-radius: 0;">
            <?php if (!empty($recipe['image_url'])): ?>
              <img src="<?= h($recipe['image_url']) ?>" 
                   alt="<?= h($recipe['title']) ?>" 
                   style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <span style="font-size: 64px; opacity: 0.3;">🍳</span>
            <?php endif; ?>
          </div>
          <div style="padding: var(--space-l) var(--space-xl);">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 var(--space-s); color: var(--color-text-main);"><?= h($recipe['title']) ?></h3>
            <p style="color: var(--color-text-muted); font-size: 0.875rem; margin: 0 0 var(--space-l);">
              Created <?= date('M j, Y', strtotime($recipe['created_at'])) ?> • 
              <?= intval($recipe['ingredient_count']) ?> ingredients
            </p>
            <div style="display: flex; gap: var(--space-s);">
              <a href="index.php?action=cook_session&id=<?= $recipe['id'] ?>" 
                 style="flex: 1; height: 44px; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); color: white; border: none; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s ease;"
                 onmouseover="this.style.background='linear-gradient(135deg, #4f46e5, #4338ca)';"
                 onmouseout="this.style.background='linear-gradient(135deg, var(--color-primary), var(--color-primary-dark))';">
                👨‍🍳 Start Cooking
              </a>
              <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" 
                 style="height: 44px; padding: 0 var(--space-l); display: flex; align-items: center; justify-content: center; background: white; color: var(--color-primary); border: 1.5px solid rgba(99, 102, 241, 0.4); border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem; white-space: nowrap; transition: all 0.2s ease;"
                 onmouseover="this.style.background='rgba(99, 102, 241, 0.08)'; this.style.borderColor='var(--color-primary)';"
                 onmouseout="this.style.background='white'; this.style.borderColor='rgba(99, 102, 241, 0.4)';">
                View Recipe
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section aria-labelledby="history-heading">
  <h2 id="history-heading">Cooking History</h2>
  <div class="card">
    <ul class="history-list">
      <li class="history-item">
        <div class="history-header">
          <h4>Spaghetti Carbonara</h4>
          <span class="rating">Rated 5/5</span>
        </div>
        <p class="history-meta">October 8, 2025 at 6:30 PM • 4 servings</p>
      </li>
      
      <li class="history-item">
        <div class="history-header">
          <h4>Chicken Tikka Masala</h4>
          <span class="rating">Rated 4/5</span>
        </div>
        <p class="history-meta">October 5, 2025 at 7:00 PM • 6 servings</p>
      </li>
      
      <li class="history-item">
        <div class="history-header">
          <h4>Greek Salad</h4>
          <span class="rating">Rated 5/5</span>
        </div>
        <p class="history-meta">October 3, 2025 at 12:30 PM • 4 servings</p>
      </li>
      
      <li class="history-item">
        <div class="history-header">
          <h4>Pad Thai</h4>
          <span class="rating">Rated 4/5</span>
        </div>
        <p class="history-meta">September 30, 2025 at 6:45 PM • 4 servings</p>
      </li>
    </ul>
    <p style="color: var(--muted); font-size: 0.9rem; margin-top: 1rem;">
      <em>Note: Cooking history tracking will be fully implemented in future sprints.</em>
    </p>
  </div>
</section>


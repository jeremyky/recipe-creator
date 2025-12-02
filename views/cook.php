<?php
$page_title = 'Cook & History - Recipe Creator';
$current_page = 'cook';
$recipes = $recipes ?? [];
?>

<h1>Cook & History</h1>
<p class="lead">Select a recipe to start a step-by-step cooking session.</p>

<section aria-labelledby="session-heading">
  <h2 id="session-heading">Select Recipe to Cook</h2>
  <?php if (empty($recipes)): ?>
    <div class="card">
      <p>No recipes available. <a href="index.php?action=upload">Upload a recipe</a> to get started!</p>
    </div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($recipes as $recipe): ?>
        <article class="recipe-card">
          <div class="recipe-img" role="img" 
               aria-label="<?= h('Placeholder for ' . $recipe['title']) ?>">
            <?php if (!empty($recipe['image_url'])): ?>
              <img src="<?= h($recipe['image_url']) ?>" 
                   alt="<?= h($recipe['title']) ?>" 
                   style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              [Image]
            <?php endif; ?>
          </div>
          <div class="recipe-content">
            <h3 class="recipe-title"><?= h($recipe['title']) ?></h3>
            <p class="recipe-meta">
              Created <?= date('M j, Y', strtotime($recipe['created_at'])) ?> • 
              <?= intval($recipe['ingredient_count']) ?> ingredients
            </p>
            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
              <a href="index.php?action=cook_session&id=<?= $recipe['id'] ?>" 
                 class="btn btn--primary" 
                 style="flex: 1; text-align: center; text-decoration: none;">
                Start Cooking
              </a>
              <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" 
                 class="btn" 
                 style="text-decoration: none;">
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


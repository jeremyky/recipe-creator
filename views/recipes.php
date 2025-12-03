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
    <div class="card">
      <p>No recipes found. <a href="index.php?action=upload">Upload a recipe</a> to get started!</p>
    </div>
  <?php else: ?>
    <div class="grid grid-3">
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
              <h3><?= h($recipe['title']) ?></h3>
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
</section>


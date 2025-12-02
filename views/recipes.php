<?php
$page_title = 'Browse Recipes - Recipe Creator';
$current_page = 'recipes';
$recipes = $recipes ?? [];
$last_cuisine = $last_cuisine ?? '';
?>

<h1>Browse Recipes</h1>
<p class="lead">Filter by cuisine, search by name, or explore our full collection.</p>

<section aria-labelledby="filters-heading">
  <h2 class="sr-only" id="filters-heading">Filter recipes</h2>
  <div class="card m-b-1-5">
    <form method="get" action="index.php">
      <input type="hidden" name="action" value="recipes">
      <div class="form-row">
        <div>
          <label for="search-recipes">Search recipes</label>
          <input type="search" id="search-recipes" name="search" 
                 value="<?= h($_GET['search'] ?? '') ?>" 
                 placeholder="pasta, chicken, etc">
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
        <div style="display: flex; align-items: flex-end;">
          <button type="submit" class="btn btn--primary">Filter</button>
        </div>
      </div>
    </form>
  </div>
</section>

<section aria-labelledby="results-heading">
  <h2 id="results-heading">All Recipes</h2>
  <?php if (empty($recipes)): ?>
    <div class="card">
      <p>No recipes found. <a href="index.php?action=upload">Upload a recipe</a> to get started!</p>
    </div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($recipes as $recipe): ?>
        <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" style="text-decoration: none; color: inherit;">
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
            </div>
          </article>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>


<?php
$page_title = 'Match from Fridge - Recipe Creator';
$current_page = 'match';
$recipes = $recipes ?? [];
$max_missing = $max_missing ?? 3;
?>

<div class="section-header">
  <h1>Recipe Matcher</h1>
  <p>Find recipes based on ingredients you already have</p>
</div>

<section aria-labelledby="filter-heading">
  <h2 class="sr-only" id="filter-heading">Filter options</h2>
  <div class="card">
    <form method="get" action="index.php">
      <input type="hidden" name="action" value="match">
      <div style="margin-bottom: var(--space-xl);">
        <label for="max-missing" style="display: block; margin-bottom: var(--space-l);">
          Maximum missing ingredients: 
          <span class="chip chip-primary" id="max-missing-value"><?= h($max_missing) ?></span>
        </label>
        <input type="range" id="max-missing" name="max-missing" min="0" max="5" 
               value="<?= h($max_missing) ?>" step="1" 
               oninput="document.getElementById('max-missing-value').textContent = this.value"
               aria-valuenow="<?= h($max_missing) ?>"
               style="width: 100%;">
      </div>
      <button type="submit" class="btn-primary">Find Matches</button>
    </form>
  </div>
</section>

<section aria-labelledby="matches-heading">
  <div class="section-header">
    <h2 id="matches-heading">Recipe Matches</h2>
    <p class="text-muted"><?= count($recipes) ?> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found</p>
  </div>
  <?php if (empty($recipes)): ?>
    <div class="card">
      <p>No matching recipes found. Try adjusting the maximum missing ingredients or <a href="index.php?action=pantry">add more ingredients to your pantry</a>.</p>
    </div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($recipes as $recipe): ?>
        <article class="card">
          <div class="card-toolbar">
            <h3 style="margin: 0;"><?= h($recipe['title']) ?></h3>
            <?php if (intval($recipe['missing_count']) === 0): ?>
              <span class="badge badge--success">100% match</span>
            <?php else: ?>
              <span class="badge badge--warning">
                <span style="display: block; text-align: center; line-height: 1.2;">MISSING</span>
                <span style="display: block; text-align: center; font-size: 1.1em; font-weight: 700; margin-top: 2px;"><?= h($recipe['missing_count']) ?></span>
              </span>
            <?php endif; ?>
          </div>
          <p class="recipe-meta">
            Created <?= date('M j, Y', strtotime($recipe['created_at'])) ?> • 
            <?= intval($recipe['ingredient_count']) ?> ingredients
          </p>
          <?php if (intval($recipe['missing_count']) === 0): ?>
            <p>You have all the ingredients needed for this recipe!</p>
          <?php endif; ?>
          <a href="index.php?action=recipes" class="btn btn--primary">View Recipe</a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>


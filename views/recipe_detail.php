<?php
$page_title = ($recipe['title'] ?? 'Recipe') . ' - Recipe Creator';
$current_page = 'recipes';
$recipe = $recipe ?? [];
$ingredients = $ingredients ?? [];
?>

<div style="margin-bottom: var(--space-xl);">
  <a href="index.php?action=recipes" class="btn-ghost" style="padding: var(--space-s) var(--space-l);">
    ← Back to Recipes
  </a>
</div>

<article class="recipe-detail">
  <header class="recipe-header card">
    <?php if (!empty($recipe['image_url'])): ?>
      <div class="card-image" style="margin: calc(var(--space-xl) * -1) calc(var(--space-xl) * -1) var(--space-xl);">
        <img src="<?= h($recipe['image_url']) ?>" alt="<?= h($recipe['title']) ?>">
      </div>
    <?php endif; ?>
    
    <div class="section-header">
      <h1><?= h($recipe['title']) ?></h1>
      <p>
        Created <?= date('M j, Y', strtotime($recipe['created_at'])) ?> • 
        <span class="chip chip-primary"><?= count($ingredients) ?> ingredients</span>
      </p>
    </div>
    
    <div style="display: flex; gap: var(--space-m); margin-top: var(--space-xl);">
      <a href="index.php?action=cook_session&id=<?= $recipe['id'] ?>" class="btn-primary">
        Start Cooking
      </a>
    </div>
  </header>

  <div class="recipe-body">
    <section class="card" style="margin-bottom: 2rem;">
      <h2>Ingredients</h2>
      <?php if (empty($ingredients)): ?>
        <p style="color: var(--muted);">No ingredients listed.</p>
      <?php else: ?>
        <ul class="ingredient-list">
          <?php foreach ($ingredients as $ingredient): ?>
            <li><?= h($ingredient) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <section class="card">
      <h2>Instructions</h2>
      <div class="recipe-steps">
        <?php
        // Split steps by newlines and display as numbered list
        $steps = explode("\n", $recipe['steps']);
        $stepNumber = 1;
        ?>
        <ol class="steps-list">
          <?php foreach ($steps as $step): ?>
            <?php $step = trim($step); ?>
            <?php if (!empty($step)): ?>
              <li>
                <div class="step-content">
                  <?= nl2br(h($step)) ?>
                </div>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </div>
    </section>
  </div>
</article>

<style>
.recipe-hero-img {
  width: 100%;
  max-height: 400px;
  overflow: hidden;
  border-radius: var(--radius-lg);
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-lg);
}

.recipe-hero-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.ingredient-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0 0;
}

.ingredient-list li {
  padding: 0.75rem 1rem;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  margin-bottom: 0.5rem;
  transition: all 0.2s ease;
}

.ingredient-list li:hover {
  background: var(--panel);
  border-color: var(--accent);
  transform: translateX(4px);
}

.steps-list {
  counter-reset: step-counter;
  list-style: none;
  padding: 0;
  margin: 1rem 0 0;
}

.steps-list li {
  counter-increment: step-counter;
  padding: 1.25rem;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  margin-bottom: 1rem;
  position: relative;
  padding-left: 4rem;
  transition: all 0.2s ease;
}

.steps-list li::before {
  content: counter(step-counter);
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 2rem;
  height: 2rem;
  background: var(--accent);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1rem;
}

.steps-list li:hover {
  background: var(--panel);
  border-color: var(--accent);
  box-shadow: var(--shadow-md);
}

.step-content {
  line-height: 1.6;
}
</style>


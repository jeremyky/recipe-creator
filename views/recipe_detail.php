<?php
$page_title = ($recipe['title'] ?? 'Recipe') . ' - Pantry Pilot';
$current_page = 'recipes';
$recipe = $recipe ?? [];
$ingredients = $ingredients ?? [];
?>

<div style="margin-bottom: var(--space-2xl);">
  <a href="index.php?action=recipes" class="btn-ghost" style="padding: var(--space-m) var(--space-xl); font-size: 1rem; font-weight: 600; display: inline-flex; align-items: center; gap: var(--space-s);">
    <span style="font-size: 1.2rem;">←</span>
    <span>Back to Recipes</span>
  </a>
</div>

<article class="recipe-detail" style="max-width: 1000px; margin: 0 auto;">
  <header class="recipe-header card" style="padding: 0; overflow: hidden;">
    <?php if (!empty($recipe['image_url'])): ?>
      <div style="width: 100%; height: 400px; overflow: hidden; border-radius: var(--radius-l) var(--radius-l) 0 0;">
        <img src="<?= h($recipe['image_url']) ?>" alt="<?= h($recipe['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
    <?php endif; ?>
    
    <div style="padding: var(--space-2xl);">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-l);">
        <div>
          <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 var(--space-m); letter-spacing: -0.02em;"><?= h($recipe['title']) ?></h1>
          <div style="display: flex; align-items: center; gap: var(--space-l); color: var(--color-text-muted); font-size: 0.95rem;">
            <span>📅 <?= date('M j, Y', strtotime($recipe['created_at'])) ?></span>
            <span>•</span>
            <span>📊 <?= count($ingredients) ?> ingredients</span>
            <?php if (!empty($recipe['cuisine'])): ?>
              <span>•</span>
              <span>🍽️ <?= ucfirst(h($recipe['cuisine'])) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: var(--space-xl);">
        <a href="index.php?action=cook_session&id=<?= $recipe['id'] ?>" 
           style="height: 54px; padding: 0 1.4rem; font-size: 16px; font-weight: 600; letter-spacing: 0.2px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; border: none; border-radius: 12px; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);"
           onmouseover="this.style.background='linear-gradient(135deg, #4f46e5, #4338ca)'; this.style.boxShadow='0 6px 16px rgba(99, 102, 241, 0.35)';"
           onmouseout="this.style.background='linear-gradient(135deg, #6366f1, #4f46e5)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.25)';">
          <span style="font-size: 20px; line-height: 1;">👨‍🍳</span>
          <span>Start Cooking</span>
        </a>
        <a href="index.php?action=recipe_edit&id=<?= $recipe['id'] ?>" 
           class="btn-edit-recipe"
           style="height: 54px; padding: 0 1.4rem; font-size: 16px; font-weight: 600; letter-spacing: 0.2px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; background: var(--color-bg-elevated); color: var(--color-primary); border: 1.5px solid rgba(99, 102, 241, 0.4); border-radius: 12px; text-decoration: none; transition: all 0.2s ease;">
          <span style="font-size: 20px; line-height: 1;">✏️</span>
          <span>Edit</span>
        </a>
        <button onclick="confirmDeleteRecipe('<?= $recipe['id'] ?>', '<?= h(addslashes($recipe['title'])) ?>')" 
                class="btn-delete-recipe"
                style="height: 54px; padding: 0 1.4rem; font-size: 16px; font-weight: 600; letter-spacing: 0.2px; color: var(--color-danger); border: 1.5px solid rgba(239, 68, 68, 0.4); background: var(--color-bg-elevated); border-radius: 12px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.2s ease; font-family: inherit;">
          <span style="font-size: 20px; line-height: 1;">🗑️</span>
          <span>Delete</span>
        </button>
      </div>
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

/* Edit button - dark mode compatible */
.btn-edit-recipe {
  color: var(--color-primary) !important;
  background: var(--color-bg-elevated) !important;
  border: 1.5px solid rgba(99, 102, 241, 0.4) !important;
}

.btn-edit-recipe:hover {
  background: var(--color-primary-soft) !important;
  border-color: var(--color-primary) !important;
  color: var(--color-primary) !important;
}

/* Delete button - dark mode compatible */
.btn-delete-recipe {
  color: var(--color-danger) !important;
  background: var(--color-bg-elevated) !important;
  border: 1.5px solid rgba(239, 68, 68, 0.4) !important;
}

.btn-delete-recipe:hover {
  background: var(--color-danger-soft) !important;
  border-color: var(--color-danger) !important;
  color: var(--color-danger) !important;
}

/* Ensure text and icons are visible in dark mode - brighter colors */
body.dark-mode .btn-edit-recipe,
body.dark-mode .btn-edit-recipe span {
  color: #a5b4fc !important;
}

body.dark-mode .btn-edit-recipe:hover {
  background: var(--color-primary-soft) !important;
  border-color: #a5b4fc !important;
  color: #a5b4fc !important;
}

body.dark-mode .btn-delete-recipe,
body.dark-mode .btn-delete-recipe span {
  color: #f87171 !important;
}

body.dark-mode .btn-delete-recipe:hover {
  background: var(--color-danger-soft) !important;
  border-color: #f87171 !important;
  color: #f87171 !important;
}
</style>

<script>
function confirmDeleteRecipe(recipeId, recipeTitle) {
  if (!confirm(`Are you sure you want to delete "${recipeTitle}"?\n\nThis action cannot be undone.`)) {
    return;
  }
  
  // Check if local recipe
  if (recipeId.startsWith('local_')) {
    // Delete from localStorage
    const localRecipes = JSON.parse(localStorage.getItem('local_recipes') || '[]');
    const filtered = localRecipes.filter(r => r.id !== recipeId);
    localStorage.setItem('local_recipes', JSON.stringify(filtered));
    
    alert('✅ Recipe deleted successfully!');
    window.location.href = 'index.php?action=recipes';
    return;
  }
  
  // Delete from database
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'index.php?action=recipe_delete&id=' + recipeId;
  
  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = 'csrf';
  csrfInput.value = '<?= h(csrf_token()) ?>';
  
  form.appendChild(csrfInput);
  document.body.appendChild(form);
  form.submit();
}
</script>


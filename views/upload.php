<?php
$page_title = 'Upload Recipe - Pantry Pilot';
$current_page = 'upload';
$old = $old ?? [];
$errors = $flash['errors'] ?? [];
?>

<div class="section-header">
  <h1>Upload Recipe</h1>
  <p>Add recipes from a URL or enter them manually</p>
</div>

<?php if (!empty($errors)): ?>
  <div class="card" style="background: var(--danger); color: white; margin-bottom: 1rem;">
    <p><strong>Please fix the following errors:</strong></p>
    <ul>
      <?php foreach ($errors as $field => $error): ?>
        <li><?= h($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<section>
  <h2 id="url-heading">Import from URL</h2>
  <details>
    <summary>Import from URL</summary>
    <form method="post" action="index.php?action=upload_submit&mode=url" style="margin-top: 1rem;">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <div class="form-row">
        <label for="recipe-url">Recipe URL</label>
        <input type="url" id="recipe-url" name="url" 
               value="<?= h($old['url'] ?? '') ?>" 
               placeholder="https://example.com/recipe" 
               class="<?= !empty($errors['url']) ? 'error' : '' ?>" 
               required>
      </div>
      <button type="submit" class="btn btn--primary">Parse Recipe</button>
    </form>
  </details>
</section>

<section style="margin-top: 1rem;">
  <h2 id="manual-heading">Enter Manually</h2>
  <details open>
    <summary>Enter Manually</summary>
    <form method="post" action="index.php?action=upload_submit&mode=manual" style="margin-top: 1rem;">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      
      <div class="form-row">
        <label for="recipe-title">Recipe Title</label>
        <input type="text" id="recipe-title" name="title" 
               value="<?= h($old['title'] ?? '') ?>" 
               placeholder="e.g. Garlic Butter Shrimp" 
               class="<?= !empty($errors['title']) ? 'error' : '' ?>" 
               required>
      </div>

      <div class="form-row">
        <label for="recipe-image">Image URL</label>
        <input type="url" id="recipe-image" name="image" 
               value="<?= h($old['image'] ?? '') ?>" 
               placeholder="https://example.com/image.jpg"
               class="<?= !empty($errors['image']) ? 'error' : '' ?>">
      </div>

      <div class="form-row">
        <label for="recipe-ingredients">Ingredients (one per line)</label>
        <textarea id="recipe-ingredients" name="ingredients" rows="6" 
                  placeholder="1 lb shrimp, peeled and deveined&#10;4 cloves garlic, minced&#10;4 tbsp butter&#10;Salt and pepper to taste"
                  class="<?= !empty($errors['ingredients']) ? 'error' : '' ?>" 
                  required><?= h($old['ingredients'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <label for="recipe-steps">Steps (one per line)</label>
        <textarea id="recipe-steps" name="steps" rows="6" 
                  placeholder="1. Melt butter in a large pan over medium heat&#10;2. Add garlic and cook until fragrant, about 1 minute&#10;3. Add shrimp and cook until pink, 3-4 minutes per side&#10;4. Season with salt and pepper, serve immediately"
                  class="<?= !empty($errors['steps']) ? 'error' : '' ?>" 
                  required><?= h($old['steps'] ?? '') ?></textarea>
      </div>

      <div style="display: flex; gap: 0.5rem;">
        <button type="submit" class="btn btn--primary">Save Recipe</button>
        <button type="reset" class="btn">Clear Form</button>
      </div>
    </form>
  </details>
</section>


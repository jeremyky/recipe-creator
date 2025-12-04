<?php
$page_title = 'Edit Recipe - Recipe Creator';
$current_page = 'recipes';
$recipe = $recipe ?? [];
$old = $old ?? [];
$errors = $flash['errors'] ?? [];

// Pre-fill with recipe data if not from error redirect
if (empty($old) && !empty($recipe)) {
    $old = [
        'title' => $recipe['title'] ?? '',
        'image' => $recipe['image_url'] ?? '',
        'cuisine' => $recipe['cuisine'] ?? '',
        'ingredients' => $recipe['ingredients_text'] ?? '',
        'steps' => $recipe['steps'] ?? ''
    ];
}
?>

<div style="margin-bottom: var(--space-xl);">
  <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" class="btn-ghost" style="padding: var(--space-s) var(--space-l);">
    ← Back to Recipe
  </a>
</div>

<div class="section-header">
  <h1>Edit Recipe</h1>
  <p>Update your recipe details</p>
</div>

<?php if (!empty($errors)): ?>
  <div class="card" style="background: var(--color-danger); color: white; margin-bottom: 1rem;">
    <p><strong>Please fix the following errors:</strong></p>
    <ul>
      <?php foreach ($errors as $field => $error): ?>
        <li><?= h($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <form method="post" action="index.php?action=recipe_update&id=<?= $recipe['id'] ?>">
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
      <label for="recipe-cuisine">Cuisine Type</label>
      <select id="recipe-cuisine" name="cuisine" 
              class="<?= !empty($errors['cuisine']) ? 'error' : '' ?>">
        <option value="">Select cuisine</option>
        <?php
        $cuisines = ['italian', 'chinese', 'mexican', 'indian', 'thai', 'greek', 'american', 'other'];
        foreach ($cuisines as $cuisine):
        ?>
          <option value="<?= h($cuisine) ?>" 
                  <?= ($old['cuisine'] ?? '') === $cuisine ? 'selected' : '' ?>>
            <?= ucfirst(h($cuisine)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-row">
      <label for="recipe-image">Image URL (optional)</label>
      <input type="url" id="recipe-image" name="image" 
             value="<?= h($old['image'] ?? '') ?>" 
             placeholder="https://example.com/image.jpg"
             class="<?= !empty($errors['image']) ? 'error' : '' ?>">
      <small class="text-muted">Leave blank to use default placeholder</small>
    </div>

    <div class="form-row">
      <label for="recipe-ingredients">Ingredients (one per line)</label>
      <textarea id="recipe-ingredients" name="ingredients" rows="8" 
                placeholder="1 lb shrimp, peeled and deveined&#10;4 cloves garlic, minced&#10;4 tbsp butter&#10;Salt and pepper to taste"
                class="<?= !empty($errors['ingredients']) ? 'error' : '' ?>" 
                required><?= h($old['ingredients'] ?? '') ?></textarea>
      <small class="text-muted">Enter each ingredient on a new line</small>
    </div>

    <div class="form-row">
      <label for="recipe-steps">Steps (one per line)</label>
      <textarea id="recipe-steps" name="steps" rows="8" 
                placeholder="1. Melt butter in a large pan over medium heat&#10;2. Add garlic and cook until fragrant&#10;3. Add shrimp and cook until pink&#10;4. Season with salt and pepper"
                class="<?= !empty($errors['steps']) ? 'error' : '' ?>" 
                required><?= h($old['steps'] ?? '') ?></textarea>
      <small class="text-muted">Number your steps or separate with line breaks</small>
    </div>

    <div style="display: flex; gap: var(--space-m); margin-top: var(--space-xl);">
      <button type="submit" class="btn-primary">💾 Save Changes</button>
      <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" class="btn-ghost">Cancel</a>
    </div>
  </form>
</div>

<script>
// Handle local recipe editing (for local testing without database)
(function() {
  const recipeId = '<?= $recipe['id'] ?>';
  
  // Check if this is a local recipe being edited
  const localRecipeData = sessionStorage.getItem('editLocalRecipe');
  if (localRecipeData && recipeId.startsWith('local_')) {
    const recipe = JSON.parse(localRecipeData);
    
    // Pre-fill form with local recipe data
    document.getElementById('recipe-title').value = recipe.title || '';
    document.getElementById('recipe-cuisine').value = recipe.cuisine || '';
    document.getElementById('recipe-image').value = recipe.image_url || '';
    document.getElementById('recipe-ingredients').value = recipe.ingredients || '';
    document.getElementById('recipe-steps').value = recipe.steps || '';
    
    // Clear session storage
    sessionStorage.removeItem('editLocalRecipe');
    
    // Intercept form submission for local recipes
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
      event.preventDefault();
      
      // Get form data
      const updatedRecipe = {
        id: recipeId,
        title: document.getElementById('recipe-title').value.trim(),
        cuisine: document.getElementById('recipe-cuisine').value,
        image_url: document.getElementById('recipe-image').value.trim(),
        ingredients: document.getElementById('recipe-ingredients').value.trim(),
        steps: document.getElementById('recipe-steps').value.trim(),
        created_at: recipe.created_at || new Date().toISOString()
      };
      
      // Update in localStorage
      const localRecipes = JSON.parse(localStorage.getItem('local_recipes') || '[]');
      const index = localRecipes.findIndex(r => r.id === recipeId);
      
      if (index !== -1) {
        localRecipes[index] = updatedRecipe;
        localStorage.setItem('local_recipes', JSON.stringify(localRecipes));
        
        // Show success message and redirect
        alert('✅ Recipe updated successfully!\n\n(Saved to local storage)');
        window.location.href = 'index.php?action=recipes';
      } else {
        alert('❌ Recipe not found in local storage');
      }
    });
  }
})();
</script>



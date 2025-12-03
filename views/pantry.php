<?php
$page_title = 'Pantry - Recipe Creator';
$current_page = 'pantry';
$items = $items ?? [];
$old = $old ?? [];
$errors = $flash['errors'] ?? [];
?>

<div class="section-header">
  <h1>My Pantry</h1>
  <p>Keep track of ingredients you have at home</p>
</div>

<section aria-labelledby="add-heading">
  <div class="section-header">
    <h2 id="add-heading">Add Ingredient</h2>
  </div>
  <div class="card">
    <?php if (!empty($errors)): ?>
      <div style="background: var(--danger); color: white; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
        <p><strong>Please fix the following errors:</strong></p>
        <ul>
          <?php foreach ($errors as $field => $error): ?>
            <li><?= h($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form method="post" action="index.php?action=pantry_add">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      
      <div class="form-row">
        <div>
          <label for="ingredient-name">Ingredient Name</label>
          <input type="text" id="ingredient-name" name="name" 
                 value="<?= h($old['name'] ?? '') ?>" 
                 placeholder="Chicken Breast" 
                 class="<?= !empty($errors['name']) ? 'error' : '' ?>" 
                 required>
        </div>
        <div>
          <label for="ingredient-quantity">Quantity</label>
          <input type="number" id="ingredient-quantity" name="quantity" 
                 value="<?= h($old['quantity'] ?? '') ?>" 
                 min="0" step="0.1" 
                 placeholder="2" 
                 class="<?= !empty($errors['quantity']) ? 'error' : '' ?>" 
                 required>
        </div>
        <div>
          <label for="ingredient-unit">Unit</label>
          <select id="ingredient-unit" name="unit" 
                  class="<?= !empty($errors['unit']) ? 'error' : '' ?>" 
                  required>
            <option value="">Select unit</option>
            <?php
            $units = ['lb', 'oz', 'g', 'kg', 'cup', 'tbsp', 'tsp', 'piece', 'ml'];
            foreach ($units as $unit):
            ?>
              <option value="<?= h($unit) ?>" <?= ($old['unit'] ?? '') === $unit ? 'selected' : '' ?>>
                <?= h($unit) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <button type="submit" class="btn-primary">Add to Pantry</button>
    </form>
  </div>
</section>

<section aria-labelledby="list-heading">
  <div class="section-header">
    <h2 id="list-heading">Current Inventory</h2>
    <p class="text-muted"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> in pantry</p>
  </div>
  <div class="card">
    <?php if (empty($items)): ?>
      <p>Your pantry is empty. Add some ingredients to get started!</p>
    <?php else: ?>
      <table class="pantry-table">
        <thead>
          <tr>
            <th scope="col">Ingredient</th>
            <th scope="col">Update Quantity & Unit</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= h($item['ingredient']) ?></td>
              <td>
                <form method="post" action="index.php?action=pantry_update" style="display: flex; gap: 0.5rem; align-items: center;">
                  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="item_id" value="<?= h($item['id']) ?>">
                  <input type="number" name="quantity" value="<?= h($item['quantity']) ?>" 
                         min="0" step="0.1" style="width: 80px; padding: 6px;" required>
                  <select name="unit" style="padding: 6px;" required>
                    <?php
                    $units = ['lb', 'oz', 'g', 'kg', 'cup', 'tbsp', 'tsp', 'piece', 'ml'];
                    foreach ($units as $unit):
                    ?>
                      <option value="<?= h($unit) ?>" <?= $item['unit'] === $unit ? 'selected' : '' ?>>
                        <?= h($unit) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn" style="padding: 6px 12px;">Update</button>
                </form>
              </td>
              <td>
                <form method="post" action="index.php?action=pantry_delete" style="display: inline;">
                  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="item_id" value="<?= h($item['id']) ?>">
                  <button type="submit" class="btn btn--danger">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</section>


<?php
$page_title = 'Pantry - Recipe Creator';
$current_page = 'pantry';
$items = $items ?? [];
$old = $old ?? [];
$errors = $flash['errors'] ?? [];
?>

<div style="max-width: 1200px; margin: 0 auto;">
  <div style="display: flex; align-items: center; gap: var(--space-m); margin-bottom: var(--space-xl);">
    <span style="font-size: 2rem;">📦</span>
    <h1 style="font-size: 2rem; font-weight: 700; margin: 0; color: var(--color-text-main);">My Pantry</h1>
  </div>
  <p style="color: var(--color-text-muted); margin-bottom: var(--space-2xl);">Keep track of ingredients you have at home</p>

  <!-- Add Ingredient Card -->
  <section aria-labelledby="add-heading" style="margin-bottom: var(--space-2xl);">
    <div class="card" style="padding: var(--space-xl); border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
      <div style="display: flex; align-items: center; gap: var(--space-s); margin-bottom: var(--space-l);">
        <span style="font-size: 1.25rem;">➕</span>
        <h2 id="add-heading" style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--color-text-main);">Add Ingredient</h2>
      </div>
      
      <?php if (!empty($errors)): ?>
        <div id="error-toast" style="background: #ef4444; color: white; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: var(--space-l); display: flex; align-items: center; gap: var(--space-s);">
          <span>⚠️</span>
          <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
              <?php foreach ($errors as $field => $error): ?>
                <li><?= h($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>
      
      <form method="post" action="index.php?action=pantry_add" id="add-ingredient-form">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: var(--space-m); margin-bottom: var(--space-l);">
          <div>
            <label for="ingredient-name" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: var(--color-text-main);">
              Ingredient Name
            </label>
            <input type="text" id="ingredient-name" name="name" 
                   value="<?= h($old['name'] ?? '') ?>" 
                   placeholder="e.g., Chicken Breast" 
                   class="<?= !empty($errors['name']) ? 'error' : '' ?>" 
                   style="width: 100%; height: 44px; padding: 0 var(--space-m); border: 1.5px solid var(--color-border-subtle); border-radius: 8px; font-size: 0.95rem; background: var(--color-bg-elevated); color: var(--color-text-main); transition: all 0.2s ease;"
                   onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';"
                   onblur="this.style.borderColor='var(--color-border-subtle)'; this.style.boxShadow='none';"
                   required
                   autocomplete="off"
                   list="ingredient-suggestions">
            <datalist id="ingredient-suggestions">
              <?php
              $common_ingredients = ['Chicken Breast', 'Ground Beef', 'Salmon', 'Eggs', 'Milk', 'Butter', 'Olive Oil', 'Garlic', 'Onions', 'Tomatoes', 'Pasta', 'Rice', 'Flour', 'Sugar', 'Salt', 'Black Pepper', 'Parmesan Cheese', 'Mozzarella', 'Bread', 'Lettuce'];
              foreach ($common_ingredients as $ing): ?>
                <option value="<?= h($ing) ?>">
              <?php endforeach; ?>
            </datalist>
            <?php if (!empty($errors['name'])): ?>
              <span style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: block;"><?= h($errors['name']) ?></span>
            <?php endif; ?>
          </div>
          
          <div>
            <label for="ingredient-quantity" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: var(--color-text-main);">
              Quantity
            </label>
            <input type="number" id="ingredient-quantity" name="quantity" 
                   value="<?= h($old['quantity'] ?? '') ?>" 
                   min="0" step="0.1" 
                   placeholder="2" 
                   class="<?= !empty($errors['quantity']) ? 'error' : '' ?>" 
                   style="width: 100%; height: 44px; padding: 0 var(--space-m); border: 1.5px solid var(--color-border-subtle); border-radius: 8px; font-size: 0.95rem; background: var(--color-bg-elevated); color: var(--color-text-main); transition: all 0.2s ease;"
                   onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';"
                   onblur="this.style.borderColor='var(--color-border-subtle)'; this.style.boxShadow='none';"
                   required>
            <?php if (!empty($errors['quantity'])): ?>
              <span style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: block;"><?= h($errors['quantity']) ?></span>
            <?php endif; ?>
          </div>
          
          <div>
            <label for="ingredient-unit" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: var(--color-text-main);">
              Unit
            </label>
            <select id="ingredient-unit" name="unit" 
                    class="<?= !empty($errors['unit']) ? 'error' : '' ?>" 
                    style="width: 100%; height: 44px; padding: 0 var(--space-m); border: 1.5px solid var(--color-border-subtle); border-radius: 8px; font-size: 0.95rem; background: var(--color-bg-elevated); color: var(--color-text-main); cursor: pointer; transition: all 0.2s ease;"
                    onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';"
                    onblur="this.style.borderColor='var(--color-border-subtle)'; this.style.boxShadow='none';"
                    required>
              <option value="">Select unit</option>
              <?php
              $units = ['lb', 'oz', 'g', 'kg', 'cup', 'tbsp', 'tsp', 'piece', 'ml', 'l', 'fl oz'];
              foreach ($units as $unit):
              ?>
                <option value="<?= h($unit) ?>" <?= ($old['unit'] ?? '') === $unit ? 'selected' : '' ?>>
                  <?= h($unit) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['unit'])): ?>
              <span style="color: #ef4444; font-size: 0.75rem; margin-top: 4px; display: block;"><?= h($errors['unit']) ?></span>
            <?php endif; ?>
          </div>
        </div>
        
        <button type="submit" 
                style="width: 100%; height: 48px; font-weight: 600; font-size: 0.95rem; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); color: white; border: none; border-radius: 10px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);"
                onmouseover="this.style.background='linear-gradient(135deg, #4f46e5, #4338ca)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.35)';"
                onmouseout="this.style.background='linear-gradient(135deg, var(--color-primary), var(--color-primary-dark))'; this.style.boxShadow='0 2px 8px rgba(99, 102, 241, 0.25)';">
          ➕ Add to Pantry
        </button>
      </form>
    </div>
  </section>

  <!-- Pantry Summary Chips -->
  <?php if (!empty($items)): ?>
    <div style="margin-bottom: var(--space-l);">
      <div style="display: flex; flex-wrap: wrap; gap: var(--space-s);">
        <?php foreach ($items as $item): ?>
          <div class="pantry-chip" 
               data-item-id="<?= $item['id'] ?>"
               style="display: inline-flex; align-items: center; gap: var(--space-xs); padding: 8px 14px; background: var(--color-bg-elevated); border: 1.5px solid var(--color-border-subtle); border-radius: 20px; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;"
               onmouseover="this.style.borderColor='var(--color-primary)'; this.style.background='var(--color-primary-soft)';"
               onmouseout="this.style.borderColor='var(--color-border-subtle)'; this.style.background='var(--color-bg-elevated)';"
               onclick="highlightRow(<?= $item['id'] ?>)">
            <span style="font-weight: 600; color: var(--color-text-main);"><?= h($item['ingredient']) ?></span>
            <span style="color: var(--color-text-muted);">(<?= h($item['quantity']) ?> <?= h($item['unit']) ?>)</span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Filters and Search -->
  <section aria-labelledby="list-heading" style="margin-bottom: var(--space-l);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-m); flex-wrap: wrap; gap: var(--space-m);">
      <div>
        <h2 id="list-heading" style="font-size: 1.5rem; font-weight: 700; margin: 0 0 var(--space-xs); color: var(--color-text-main);">Current Inventory</h2>
        <p style="color: var(--color-text-muted); font-size: 0.875rem; margin: 0;"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> in pantry</p>
      </div>
      
      <div style="display: flex; gap: var(--space-s); flex-wrap: wrap;">
        <input type="text" id="pantry-search" 
               placeholder="🔍 Search ingredients..." 
               style="height: 40px; padding: 0 var(--space-m); border: 1.5px solid var(--color-border-subtle); border-radius: 8px; font-size: 0.9rem; min-width: 200px; background: var(--color-bg-elevated); color: var(--color-text-main);"
               oninput="filterTable(this.value)">
        
        <select id="sort-select" 
                style="height: 40px; padding: 0 var(--space-m); border: 1.5px solid var(--color-border-subtle); border-radius: 8px; font-size: 0.9rem; background: var(--color-bg-elevated); color: var(--color-text-main); cursor: pointer;"
                onchange="sortTable(this.value)">
          <option value="name">Sort by Name</option>
          <option value="recent">Sort by Recently Added</option>
          <option value="quantity">Sort by Quantity</option>
        </select>
        
        <label style="display: flex; align-items: center; gap: 8px; padding: 0 16px; height: 40px; background: var(--color-bg-elevated); border: 1.5px solid var(--color-border-subtle); border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 500; color: var(--color-text-main); transition: all 0.2s ease;"
               onmouseover="this.style.borderColor='var(--color-warning)'; this.style.background='var(--color-warning-soft)';"
               onmouseout="this.style.borderColor='var(--color-border-subtle)'; this.style.background='var(--color-bg-elevated)';">
          <input type="checkbox" id="low-stock-toggle" onchange="filterLowStock(this.checked)" 
                 style="cursor: pointer; width: 18px; height: 18px; accent-color: #f59e0b; margin: 0;">
          <span>⚠️ Low Stock</span>
        </label>
      </div>
    </div>
  </section>

  <!-- Inventory Table -->
  <div class="card" style="padding: 0; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); overflow: hidden;">
    <?php if (empty($items)): ?>
      <div style="padding: var(--space-3xl); text-align: center;">
        <div style="font-size: 64px; opacity: 0.3; margin-bottom: var(--space-l);">📦</div>
        <h3 style="color: var(--color-text-main); margin-bottom: var(--space-s);">Your pantry is empty</h3>
        <p style="color: var(--color-text-muted);">Add some ingredients to get started!</p>
      </div>
    <?php else: ?>
      <table class="pantry-table" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background: var(--color-bg); border-bottom: 2px solid var(--color-border-subtle);">
            <th scope="col" style="padding: var(--space-m) var(--space-l); text-align: left; font-weight: 600; font-size: 0.875rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Ingredient</th>
            <th scope="col" style="padding: var(--space-m) var(--space-l); text-align: left; font-weight: 600; font-size: 0.875rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; width: 200px;">Quantity & Unit</th>
            <th scope="col" style="padding: var(--space-m) var(--space-l); text-align: left; font-weight: 600; font-size: 0.875rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr class="pantry-row" data-item-id="<?= $item['id'] ?>" data-ingredient="<?= strtolower(h($item['ingredient'])) ?>" data-quantity="<?= floatval($item['quantity']) ?>"
                style="border-bottom: 1px solid #eaeaea; transition: all 0.2s ease;">
              <td style="padding: var(--space-l);">
                <div style="font-weight: 600; color: var(--color-text-main);"><?= h($item['ingredient']) ?></div>
              </td>
              <td style="padding: var(--space-l);">
                <form method="post" action="index.php?action=pantry_update" class="update-form" style="display: flex; gap: var(--space-xs); align-items: center;">
                  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="item_id" value="<?= h($item['id']) ?>">
                  <input type="number" name="quantity" value="<?= h($item['quantity']) ?>" 
                         min="0" step="0.1" 
                         style="width: 70px; height: 36px; padding: 0 var(--space-s); border: 1.5px solid var(--color-border-subtle); border-radius: 6px; font-size: 0.9rem; text-align: center; background: var(--color-bg-elevated); color: var(--color-text-main);"
                         required>
                  <select name="unit" 
                          style="width: 80px; height: 36px; padding: 0 var(--space-s); border: 1.5px solid var(--color-border-subtle); border-radius: 6px; font-size: 0.9rem; background: var(--color-bg-elevated); color: var(--color-text-main); cursor: pointer;"
                          required>
                    <?php
                    $units = ['lb', 'oz', 'g', 'kg', 'cup', 'tbsp', 'tsp', 'piece', 'ml', 'l', 'fl oz'];
                    foreach ($units as $unit):
                    ?>
                      <option value="<?= h($unit) ?>" <?= $item['unit'] === $unit ? 'selected' : '' ?>>
                        <?= h($unit) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" 
                          style="height: 36px; padding: 0 var(--space-m); background: var(--color-primary); color: white; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; white-space: nowrap;"
                          onmouseover="this.style.background='var(--color-primary-dark)';"
                          onmouseout="this.style.background='var(--color-primary)';">
                    Update
                  </button>
                </form>
              </td>
              <td style="padding: var(--space-l);">
                <form method="post" action="index.php?action=pantry_delete" class="delete-form" style="display: inline;">
                  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="item_id" value="<?= h($item['id']) ?>">
                  <button type="submit" 
                          class="remove-btn"
                          onclick="return confirm('Remove <?= h(addslashes($item['ingredient'])) ?> from pantry?');">
                    <span>🗑️</span>
                    <span>Remove</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; flex-direction: column; gap: var(--space-s);"></div>

<script>
// Toast notification system
function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container');
  const toast = document.createElement('div');
  toast.style.cssText = `
    padding: 14px 20px;
    background: ${type === 'success' ? '#10b981' : '#ef4444'};
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-weight: 500;
    font-size: 0.9rem;
    animation: slideIn 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 250px;
  `;
  toast.innerHTML = `${type === 'success' ? '✓' : '⚠️'} ${message}`;
  container.appendChild(toast);
  
  setTimeout(() => {
    toast.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Highlight row when chip is clicked
function highlightRow(itemId) {
  document.querySelectorAll('.pantry-row').forEach(row => {
    row.style.background = '';
  });
  const row = document.querySelector(`.pantry-row[data-item-id="${itemId}"]`);
  if (row) {
    row.style.background = 'rgba(99, 102, 241, 0.1)';
    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => {
      row.style.background = '';
    }, 2000);
  }
}

// Filter table by search
function filterTable(searchTerm) {
  const rows = document.querySelectorAll('.pantry-row');
  const term = searchTerm.toLowerCase();
  rows.forEach(row => {
    const ingredient = row.getAttribute('data-ingredient');
    if (ingredient.includes(term)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

// Sort table
function sortTable(sortBy) {
  const tbody = document.querySelector('.pantry-table tbody');
  const rows = Array.from(tbody.querySelectorAll('.pantry-row'));
  
  rows.sort((a, b) => {
    if (sortBy === 'name') {
      return a.getAttribute('data-ingredient').localeCompare(b.getAttribute('data-ingredient'));
    } else if (sortBy === 'quantity') {
      return parseFloat(b.getAttribute('data-quantity')) - parseFloat(a.getAttribute('data-quantity'));
    } else {
      return 0; // Recent - keep original order
    }
  });
  
  rows.forEach(row => tbody.appendChild(row));
}

// Filter low stock items
function filterLowStock(showOnly) {
  const rows = document.querySelectorAll('.pantry-row');
  const toggle = document.getElementById('low-stock-toggle');
  const label = toggle.closest('label');
  
  rows.forEach(row => {
    const quantity = parseFloat(row.getAttribute('data-quantity'));
    if (showOnly) {
      row.style.display = quantity < 1 ? '' : 'none';
      // Highlight low stock rows
      if (quantity < 1) {
        row.style.background = 'var(--color-warning-soft)';
        row.style.borderLeft = '4px solid var(--color-warning)';
      }
    } else {
      row.style.display = '';
      row.style.background = '';
      row.style.borderLeft = '';
    }
  });
  
  // Update toggle styling
  if (showOnly) {
    label.style.background = 'var(--color-warning-soft)';
    label.style.borderColor = 'var(--color-warning)';
    label.style.color = 'var(--color-text-main)';
  } else {
    label.style.background = 'var(--color-bg-elevated)';
    label.style.borderColor = 'var(--color-border-subtle)';
    label.style.color = 'var(--color-text-main)';
  }
}

// Auto-fill unit based on ingredient name
document.getElementById('ingredient-name')?.addEventListener('input', function(e) {
  const name = e.target.value.toLowerCase();
  const unitSelect = document.getElementById('ingredient-unit');
  
  const unitMap = {
    'chicken': 'lb',
    'beef': 'lb',
    'pork': 'lb',
    'fish': 'lb',
    'milk': 'cup',
    'water': 'cup',
    'oil': 'tbsp',
    'flour': 'cup',
    'sugar': 'cup',
    'salt': 'tsp',
    'pepper': 'tsp',
    'egg': 'piece',
    'onion': 'piece',
    'garlic': 'piece'
  };
  
  for (const [key, unit] of Object.entries(unitMap)) {
    if (name.includes(key)) {
      unitSelect.value = unit;
      break;
    }
  }
});

// Show success toast on form submission
document.getElementById('add-ingredient-form')?.addEventListener('submit', function(e) {
  const form = this;
  const formData = new FormData(form);
  
  fetch(form.action, {
    method: 'POST',
    body: formData
  }).then(response => {
    if (response.ok) {
      showToast('Ingredient added successfully!', 'success');
    }
  }).catch(() => {
    // Form will submit normally if fetch fails
  });
});

// Show toast on update/delete
document.querySelectorAll('.update-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    const formData = new FormData(form);
    e.preventDefault();
    
    fetch(form.action, {
      method: 'POST',
      body: formData
    }).then(response => {
      if (response.ok) {
        showToast('Quantity updated!', 'success');
        setTimeout(() => location.reload(), 500);
      }
    });
  });
});

document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    const formData = new FormData(form);
    e.preventDefault();
    
    if (confirm('Remove this ingredient from pantry?')) {
      fetch(form.action, {
        method: 'POST',
        body: formData
      }).then(response => {
        if (response.ok) {
          showToast('Ingredient removed', 'success');
          setTimeout(() => location.reload(), 500);
        }
      });
    }
  });
});

// Add CSS animations and dark mode fixes
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
  .pantry-row:hover {
    background: var(--color-bg) !important;
  }
  
  /* Remove button - red outline block with lighter red fill */
  .remove-btn {
    color: #dc2626;
    background-color: #fee2e2;
    border: 1.5px solid #dc2626;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    height: 36px;
    min-width: 90px;
    font-family: inherit;
  }
  
  .remove-btn span:first-child {
    font-size: 1rem;
    line-height: 1;
  }
  
  .remove-btn:hover {
    background-color: #fecaca;
    color: #dc2626;
    border-color: #dc2626;
    transform: translateY(-1px);
  }
  
  .remove-btn:active {
    transform: translateY(0);
  }
  
  /* Dark mode adjustments */
  body.dark-mode .remove-btn,
  html[data-theme="dark"] .remove-btn,
  @media (prefers-color-scheme: dark) {
    .remove-btn {
      color: #fca5a5;
      background-color: rgba(220, 38, 38, 0.15);
      border-color: #fca5a5;
    }
    
    .remove-btn:hover {
      background-color: rgba(220, 38, 38, 0.25);
      color: #fca5a5;
      border-color: #fca5a5;
    }
  }
`;
document.head.appendChild(style);
</script>

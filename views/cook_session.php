<?php
$page_title = 'Cooking: ' . ($recipe['title'] ?? 'Recipe') . ' - Recipe Creator';
$current_page = 'cook';
$recipe = $recipe ?? [];
$ingredients = $ingredients ?? [];
?>

<div style="margin-bottom: 1rem;">
  <a href="index.php?action=cook" style="color: var(--accent); text-decoration: none;">
    ← Back to Cook
  </a>
</div>

<article class="cooking-session">
  <header class="session-header card" style="margin-bottom: 2rem;">
    <h1>Cooking: <?= h($recipe['title']) ?></h1>
    <p style="color: var(--text-secondary); margin-bottom: 1rem;">
      Follow along step-by-step. Check off each step as you complete it.
    </p>
    <div id="progress-bar" style="background: var(--border); height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 1rem;">
      <div id="progress-fill" style="background: var(--accent); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
    </div>
    <p id="progress-text" style="color: var(--muted); font-size: 0.9rem;">0% complete</p>
  </header>

  <div class="session-content">
    <section class="card" style="margin-bottom: 2rem;">
      <h2>Ingredients Checklist</h2>
      <?php if (empty($ingredients)): ?>
        <p style="color: var(--muted);">No ingredients listed.</p>
      <?php else: ?>
        <div class="ingredients-checklist">
          <?php foreach ($ingredients as $index => $ingredient): ?>
            <div class="checkbox-item">
              <input type="checkbox" id="ingredient-<?= $index ?>" class="ingredient-check">
              <label for="ingredient-<?= $index ?>"><?= h($ingredient) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="card">
      <h2>Cooking Steps</h2>
      <div class="cooking-steps">
        <?php
        $steps = explode("\n", $recipe['steps']);
        $stepNumber = 0;
        ?>
        <div class="steps-checklist">
          <?php foreach ($steps as $step): ?>
            <?php $step = trim($step); ?>
            <?php if (!empty($step)): ?>
              <?php $stepNumber++; ?>
              <div class="step-item" data-step="<?= $stepNumber ?>">
                <div class="step-checkbox">
                  <input type="checkbox" id="step-<?= $stepNumber ?>" class="step-check">
                </div>
                <div class="step-body">
                  <label for="step-<?= $stepNumber ?>" class="step-label">
                    <span class="step-number">Step <?= $stepNumber ?></span>
                    <div class="step-text"><?= nl2br(h($step)) ?></div>
                  </label>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <div class="card" style="margin-top: 2rem; text-align: center;">
      <button id="finish-cooking" class="btn btn--primary" style="font-size: 1.1rem; padding: 1rem 2rem;" disabled>
        Finish Cooking Session
      </button>
    </div>
  </div>
</article>

<style>
.ingredients-checklist {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 1rem;
}

.checkbox-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  transition: all 0.2s ease;
}

.checkbox-item:hover {
  background: var(--panel);
  border-color: var(--accent);
}

.checkbox-item input[type="checkbox"] {
  width: 1.25rem;
  height: 1.25rem;
  margin-right: 1rem;
  cursor: pointer;
  accent-color: var(--accent);
}

.checkbox-item label {
  cursor: pointer;
  flex: 1;
  user-select: none;
}

.checkbox-item input[type="checkbox"]:checked + label {
  text-decoration: line-through;
  opacity: 0.6;
}

.steps-checklist {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.step-item {
  display: flex;
  gap: 1rem;
  padding: 1.25rem;
  background: var(--bg);
  border: 2px solid var(--border);
  border-radius: var(--radius-lg);
  transition: all 0.3s ease;
}

.step-item:hover {
  background: var(--panel);
  border-color: var(--accent);
  box-shadow: var(--shadow-md);
}

.step-item.completed {
  opacity: 0.6;
  background: var(--panel);
}

.step-checkbox input[type="checkbox"] {
  width: 1.5rem;
  height: 1.5rem;
  cursor: pointer;
  accent-color: var(--accent);
  margin-top: 0.25rem;
}

.step-body {
  flex: 1;
}

.step-label {
  cursor: pointer;
  display: block;
}

.step-number {
  display: inline-block;
  background: var(--accent);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius);
  font-size: 0.85rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.step-text {
  line-height: 1.6;
  margin-top: 0.5rem;
}

.step-item.completed .step-text {
  text-decoration: line-through;
}

#finish-cooking:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const ingredientChecks = document.querySelectorAll('.ingredient-check');
  const stepChecks = document.querySelectorAll('.step-check');
  const allChecks = [...ingredientChecks, ...stepChecks];
  const progressBar = document.getElementById('progress-fill');
  const progressText = document.getElementById('progress-text');
  const finishButton = document.getElementById('finish-cooking');
  
  function updateProgress() {
    const total = allChecks.length;
    const checked = allChecks.filter(cb => cb.checked).length;
    const percentage = total > 0 ? Math.round((checked / total) * 100) : 0;
    
    progressBar.style.width = percentage + '%';
    progressText.textContent = percentage + '% complete';
    
    // Enable finish button when all steps are complete
    if (percentage === 100) {
      finishButton.disabled = false;
    } else {
      finishButton.disabled = true;
    }
  }
  
  // Add event listeners to all checkboxes
  allChecks.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      updateProgress();
      
      // Add completed class to step items
      if (this.classList.contains('step-check')) {
        const stepItem = this.closest('.step-item');
        if (this.checked) {
          stepItem.classList.add('completed');
        } else {
          stepItem.classList.remove('completed');
        }
      }
    });
  });
  
  // Finish cooking button
  finishButton.addEventListener('click', function() {
    if (confirm('Mark this cooking session as complete?')) {
      alert('Cooking session completed! Great job! 🎉\n\nNote: Cooking history tracking will be fully implemented in future updates.');
      window.location.href = 'index.php?action=cook';
    }
  });
  
  // Initialize progress
  updateProgress();
});
</script>


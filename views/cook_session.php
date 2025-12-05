<?php
$page_title = 'Cooking: ' . ($recipe['title'] ?? 'Recipe') . ' - Recipe Creator';
$current_page = 'cook';
$recipe = $recipe ?? [];
$ingredients = $ingredients ?? [];
?>

<div style="margin-bottom: var(--space-xl);">
  <a href="index.php?action=cook" class="btn-ghost" style="padding: var(--space-s) var(--space-l);">
    ← Back to Recipes
  </a>
</div>

<article class="cooking-session">
  <header class="session-header card">
    <div class="section-header" style="margin-bottom: var(--space-l);">
      <h1><?= h($recipe['title']) ?></h1>
      <p>Follow along step-by-step and check off each step as you complete it</p>
    </div>
    <div id="progress-bar" style="background: var(--color-bg); height: 12px; border-radius: var(--radius-s); overflow: hidden; margin-bottom: var(--space-m); border: 1px solid var(--color-border-subtle);">
      <div id="progress-fill" style="background: linear-gradient(90deg, var(--color-primary), var(--color-accent)); height: 100%; width: 0%; transition: width 0.3s ease;"></div>
    </div>
    <p id="progress-text" class="text-muted" style="font-size: 14px; font-weight: 600;">0% complete</p>
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
  padding: 0.875rem 1rem;
  background: var(--color-bg);
  border: 1.5px solid var(--color-border-subtle);
  border-radius: 10px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.checkbox-item:hover {
  background: var(--color-bg-elevated);
  border-color: var(--color-primary);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.checkbox-item input[type="checkbox"] {
  position: relative;
  width: 22px;
  height: 22px;
  margin-right: 1rem;
  cursor: pointer;
  appearance: none;
  border: 2px solid var(--color-border-strong);
  border-radius: 6px;
  background: white;
  transition: all 0.2s ease;
}

.checkbox-item input[type="checkbox"]:hover {
  border-color: var(--color-primary);
}

.checkbox-item input[type="checkbox"]:checked {
  background: var(--color-primary);
  border-color: var(--color-primary);
}

.checkbox-item input[type="checkbox"]:checked::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 14px;
  font-weight: 700;
}

.checkbox-item label {
  cursor: pointer;
  flex: 1;
  user-select: none;
  transition: all 0.2s ease;
}

.checkbox-item input[type="checkbox"]:checked + label {
  color: var(--color-text-muted);
  opacity: 0.7;
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
  padding: 1.25rem 1.5rem;
  background: var(--color-bg-elevated);
  border: 1.5px solid var(--color-border-subtle);
  border-radius: 12px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.step-item:hover {
  background: var(--color-bg-elevated);
  border-color: var(--color-primary);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}

.step-item.completed {
  background: var(--color-success-soft);
  border-color: var(--color-success);
}

.step-checkbox input[type="checkbox"] {
  position: relative;
  width: 22px;
  height: 22px;
  min-width: 22px;
  cursor: pointer;
  appearance: none;
  border: 2px solid var(--color-border-strong);
  border-radius: 6px;
  background: white;
  transition: all 0.2s ease;
  margin-top: 0.25rem;
}

.step-checkbox input[type="checkbox"]:hover {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.step-checkbox input[type="checkbox"]:checked {
  background: var(--color-success);
  border-color: var(--color-success);
}

.step-checkbox input[type="checkbox"]:checked::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 14px;
  font-weight: 700;
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
  background: var(--color-primary);
  color: white;
  padding: 4px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  letter-spacing: 0.3px;
}

.step-text {
  line-height: 1.7;
  margin-top: 0.5rem;
  color: var(--color-text-main);
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.step-item.completed .step-text {
  color: var(--color-text-muted);
  opacity: 0.75;
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


<?php
$page_title = 'Match from Fridge - Pantry Pilot';
$current_page = 'match';
$recipes = $recipes ?? [];
$max_missing = $max_missing ?? 3;
?>

<div style="max-width: 900px; margin: var(--space-2xl) auto;">
  <div class="card" style="padding: var(--space-2xl);">
    <div style="text-align: center; margin-bottom: var(--space-2xl);">
      <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 var(--space-s); color: var(--color-text-main);">🎯 Recipe Matcher</h1>
      <p style="color: var(--color-text-muted); font-size: 1rem;">Find recipes based on what you have in your pantry</p>
    </div>
    
    <form method="get" action="index.php" id="match-form">
      <input type="hidden" name="action" value="match">
      
      <div style="background: var(--color-bg); padding: var(--space-xl); border-radius: var(--radius-m); margin-bottom: var(--space-xl);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-m);">
          <label for="max-missing" style="font-weight: 600; font-size: 1rem; color: var(--color-text-main);">
            Max missing ingredients
          </label>
          <div style="display: flex; align-items: center; gap: var(--space-m);">
            <button type="button" onclick="adjustMissing(-1)" 
                    style="width: 36px; height: 36px; border-radius: 8px; border: 1.5px solid var(--color-border-strong); background: white; color: var(--color-text-main); font-size: 18px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                    onmouseover="this.style.background='var(--color-bg)'; this.style.borderColor='var(--color-primary)';"
                    onmouseout="this.style.background='white'; this.style.borderColor='var(--color-border-strong)';">
              −
            </button>
            <input type="number" id="max-missing" name="max-missing" min="0" max="10" 
                   value="<?= h($max_missing) ?>"
                   style="width: 70px; height: 36px; text-align: center; font-size: 1.2rem; font-weight: 700; border: 1.5px solid var(--color-border-strong); border-radius: 8px; color: var(--color-primary); background: white; padding: 0 4px;"
                   readonly>
            <button type="button" onclick="adjustMissing(1)" 
                    style="width: 36px; height: 36px; border-radius: 8px; border: 1.5px solid var(--color-border-strong); background: white; color: var(--color-text-main); font-size: 18px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                    onmouseover="this.style.background='var(--color-bg)'; this.style.borderColor='var(--color-primary)';"
                    onmouseout="this.style.background='white'; this.style.borderColor='var(--color-border-strong)';">
              +
            </button>
          </div>
        </div>
        <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0;">
          Show recipes that need up to <strong style="color: var(--color-primary);" id="missing-text"><?= h($max_missing) ?></strong> additional ingredients
        </p>
      </div>
      
      <button type="submit" 
              style="width: 100%; height: 54px; font-size: 16px; font-weight: 600; letter-spacing: 0.2px; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); color: white; border: none; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);"
              onmouseover="this.style.background='linear-gradient(135deg, #4f46e5, #4338ca)'; this.style.boxShadow='0 6px 16px rgba(99, 102, 241, 0.35)'; this.style.transform='translateY(-1px)';"
              onmouseout="this.style.background='linear-gradient(135deg, var(--color-primary), var(--color-primary-dark))'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.25)'; this.style.transform='translateY(0)';">
        🔍 Find Matching Recipes
      </button>
    </form>
  </div>
</div>

<script>
function adjustMissing(delta) {
  const input = document.getElementById('max-missing');
  const text = document.getElementById('missing-text');
  let value = parseInt(input.value) + delta;
  value = Math.max(0, Math.min(10, value));
  input.value = value;
  text.textContent = value;
}
</script>

<section aria-labelledby="matches-heading" style="max-width: 900px; margin: var(--space-2xl) auto;">
  <div style="margin-bottom: var(--space-xl);">
    <h2 id="matches-heading" style="font-size: 1.5rem; font-weight: 700; margin: 0 0 var(--space-xs);">🍳 Recipe Matches</h2>
    <p style="color: var(--color-text-muted);"><?= count($recipes) ?> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found</p>
  </div>
  
  <?php if (empty($recipes)): ?>
    <div class="card" style="text-align: center; padding: var(--space-3xl);">
      <div style="font-size: 64px; opacity: 0.3; margin-bottom: var(--space-l);">🔍</div>
      <h3 style="color: var(--color-text-main); margin-bottom: var(--space-m);">No matching recipes found</h3>
      <p style="color: var(--color-text-muted); margin-bottom: var(--space-xl);">
        Try increasing the maximum missing ingredients or add more items to your pantry
      </p>
      <a href="index.php?action=pantry" class="btn-primary" style="display: inline-block;">
        📦 Manage Pantry
      </a>
    </div>
  <?php else: ?>
    <div style="display: grid; gap: var(--space-l);">
      <?php foreach ($recipes as $recipe): 
        $missing = intval($recipe['missing_count']);
        $total = intval($recipe['ingredient_count']);
        $have = $total - $missing;
        $matchPercent = $total > 0 ? round(($have / $total) * 100) : 0;
      ?>
        <article class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: row; align-items: stretch; cursor: pointer;" onclick="window.location.href='index.php?action=recipe_detail&id=<?= $recipe['id'] ?>'">
          <?php if (!empty($recipe['image_url'])): ?>
            <div style="width: 140px; min-width: 140px; height: 140px; overflow: hidden; background: var(--color-bg);">
              <img src="<?= h($recipe['image_url']) ?>" alt="<?= h($recipe['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
          <?php else: ?>
            <div style="width: 140px; min-width: 140px; height: 140px; background: linear-gradient(135deg, var(--color-primary-soft), var(--color-accent-soft)); display: flex; align-items: center; justify-content: center; font-size: 48px; opacity: 0.5;">
              🍳
            </div>
          <?php endif; ?>
          
          <div style="flex: 1; padding: var(--space-l) var(--space-xl); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-s);">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--color-text-main);"><?= h($recipe['title']) ?></h3>
                <?php if ($missing === 0): ?>
                  <span style="padding: 4px 12px; background: var(--color-success-soft); color: var(--color-success); border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;">
                    ✓ Perfect Match
                  </span>
                <?php else: ?>
                  <span style="padding: 4px 12px; background: var(--color-warning-soft); color: #d97706; border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;">
                    Missing <?= $missing ?>
                  </span>
                <?php endif; ?>
              </div>
              
              <div style="display: flex; align-items: center; gap: var(--space-m); color: var(--color-text-muted); font-size: 0.875rem; margin-bottom: var(--space-m);">
                <span>🍽️ <?= ucfirst(h($recipe['cuisine'] ?? 'Other')) ?></span>
                <span>•</span>
                <span><?= $total ?> ingredients</span>
              </div>
              
              <div style="margin-bottom: var(--space-m);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                  <span style="font-size: 0.875rem; font-weight: 600; color: var(--color-text-main);">Match Score</span>
                  <span style="font-size: 0.875rem; font-weight: 700; color: var(--color-primary);"><?= $matchPercent ?>%</span>
                </div>
                <div style="height: 8px; background: var(--color-bg); border-radius: 4px; overflow: hidden;">
                  <div style="height: 100%; background: linear-gradient(90deg, var(--color-success), var(--color-primary)); width: <?= $matchPercent ?>%; transition: width 0.3s ease; border-radius: 4px;"></div>
                </div>
                <p style="font-size: 0.75rem; color: var(--color-text-muted); margin: 6px 0 0;">
                  <?= $have ?>/<?= $total ?> ingredients in your pantry
                </p>
              </div>
            </div>
            
            <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" 
               style="display: inline-block; padding: 10px 24px; background: var(--color-primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; text-align: center; transition: all 0.2s ease;"
               onmouseover="this.style.background='var(--color-primary-dark)';"
               onmouseout="this.style.background='var(--color-primary)';"
               onclick="event.stopPropagation();">
              View Recipe →
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>


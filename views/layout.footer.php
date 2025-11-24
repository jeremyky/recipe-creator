  </main>

  <footer class="site-footer">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-bottom: 2rem; text-align: left;">
        <div>
          <h4 style="margin: 0 0 1rem; color: var(--text); font-size: 1.1rem;">Recipe Creator</h4>
          <p style="color: var(--muted); font-size: 0.9rem; line-height: 1.6;">
            Your intelligent kitchen companion for reducing food waste and discovering delicious recipes.
          </p>
        </div>
        <div>
          <h4 style="margin: 0 0 1rem; color: var(--text); font-size: 1.1rem;">Quick Links</h4>
          <nav style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="index.php" style="color: var(--muted); text-decoration: none; font-size: 0.9rem; transition: color 0.2s;">Home</a>
            <a href="index.php?action=recipes" style="color: var(--muted); text-decoration: none; font-size: 0.9rem; transition: color 0.2s;">Recipes</a>
            <a href="index.php?action=pantry" style="color: var(--muted); text-decoration: none; font-size: 0.9rem; transition: color 0.2s;">Pantry</a>
            <a href="index.php?action=about" style="color: var(--muted); text-decoration: none; font-size: 0.9rem; transition: color 0.2s;">About</a>
          </nav>
        </div>
        <div>
          <h4 style="margin: 0 0 1rem; color: var(--text); font-size: 1.1rem;">Team</h4>
          <p style="color: var(--muted); font-size: 0.9rem; line-height: 1.8;">
            Jeremy Ky<br>
            Ashley Wu<br>
            Shaunak Sinha
          </p>
        </div>
      </div>
      <div style="border-top: 1px solid var(--border-light); padding-top: 1.5rem; text-align: center;">
        <p style="color: var(--muted); font-size: 0.9rem; margin: 0 0 0.5rem;">
          Recipe Creator • CS 4640 Sprint 4
        </p>
        <p style="color: var(--muted); font-size: 0.85rem; margin: 0;">
          <a href="#main" style="color: var(--accent); text-decoration: none;">Back to top</a> • 
          <a href="index.php?action=about" style="color: var(--accent); text-decoration: none;">About Us</a>
        </p>
      </div>
    </div>
  </footer>

  <!-- jQuery (only for recipes page) -->
  <?php if (($current_page ?? '') === 'recipes'): ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
  <?php endif; ?>

  <!-- Page-specific JavaScript -->
  <?php
  $js_file = 'assets/js/' . ($current_page ?? 'home') . '.js';
  if (file_exists(__DIR__ . '/../' . $js_file)):
  ?>
    <script src="<?= h($js_file) ?>"></script>
  <?php endif; ?>
</body>
</html>


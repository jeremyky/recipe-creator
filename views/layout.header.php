<!--
Deployed URL: https://cs4640.cs.virginia.edu/juh7hc/
Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
CS 4640 Sprint 4
-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <meta name="author" content="Jeremy Ky, Ashley Wu, Shaunak Sinha">
  <meta name="description" content="Create, manage, and cook recipes with AI-powered assistance">
  <meta name="theme-color" content="#6366f1">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Recipe Creator">
  <title><?= h($page_title ?? 'Recipe Creator') ?></title>
  <link rel="stylesheet" href="assets/styles.css">
  <link rel="manifest" href="manifest.json">
  <link rel="apple-touch-icon" href="assets/icon-192.png">
  <script>
    // Set initial theme before page loads to prevent flash
    (function() {
      const savedTheme = localStorage.getItem('app-theme') || 'dark';
      const themeClass = savedTheme === 'light' ? 'light-mode' : 'dark-mode';
      document.documentElement.className = themeClass;
    })();
  </script>
</head>
<body>
  <a href="#main" class="sr-only">Skip to main content</a>

  <header class="navbar">
    <div class="navbar-inner">
      <a href="index.php" class="brand">
        <span class="brand-icon">🍳</span>
        <span class="brand-text">Recipe Creator</span>
      </a>
      
      <!-- Mobile Menu Toggle -->
      <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <nav id="main-nav" aria-label="Primary navigation">
        <div class="mobile-nav-links">
          <a href="index.php" <?= ($current_page ?? '') === 'home' ? 'aria-current="page"' : '' ?>>Home</a>
          <a href="index.php?action=recipes" <?= ($current_page ?? '') === 'recipes' ? 'aria-current="page"' : '' ?>>Recipes</a>
          <a href="index.php?action=chat" <?= ($current_page ?? '') === 'chat' ? 'aria-current="page"' : '' ?>>Chat</a>
          <a href="index.php?action=upload" <?= ($current_page ?? '') === 'upload' ? 'aria-current="page"' : '' ?>>Upload</a>
          <a href="index.php?action=pantry" <?= ($current_page ?? '') === 'pantry' ? 'aria-current="page"' : '' ?>>Pantry</a>
          <a href="index.php?action=match" <?= ($current_page ?? '') === 'match' ? 'aria-current="page"' : '' ?>>Match</a>
          <a href="index.php?action=cook" <?= ($current_page ?? '') === 'cook' ? 'aria-current="page"' : '' ?>>Cook</a>
          <a href="index.php?action=about" <?= ($current_page ?? '') === 'about' ? 'aria-current="page"' : '' ?>>About</a>
        </div>
        
        <!-- Mobile-only user section in menu (fixed at bottom) -->
        <div class="navbar-user mobile-only">
          <?php if (function_exists('is_authenticated') && is_authenticated()): ?>
            <?php $user = auth_user(); ?>
            <div class="user-info">
              <span class="user-greeting">👋 <?= h($user['name'] ?? 'User') ?></span>
            </div>
            <a href="index.php?action=logout" class="btn btn-logout-mobile">Logout</a>
          <?php endif; ?>
        </div>
      </nav>
      
      <div class="navbar-actions">
        <button id="app-theme-toggle" class="theme-toggle-btn" aria-label="Toggle dark mode" title="Toggle light/dark mode">
          <span class="theme-icon">🌙</span>
        </button>
        <div class="navbar-user desktop-only">
          <?php if (function_exists('is_authenticated') && is_authenticated()): ?>
            <?php $user = auth_user(); ?>
            <span class="user-greeting">👋 <?= h($user['name'] ?? 'User') ?></span>
            <a href="index.php?action=logout" class="btn btn-logout">Logout</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <main id="main" class="container">
    <?php if (!empty($flash['error'])): ?>
      <div class="card" style="background: var(--danger); color: white; margin-bottom: 1rem;">
        <p><strong>Error:</strong> <?= h($flash['error']) ?></p>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($flash['success'])): ?>
      <div class="card" style="background: var(--success); color: white; margin-bottom: 1rem;">
        <p><strong>Success:</strong> <?= h($flash['success']) ?></p>
      </div>
    <?php endif; ?>

    <div class="hero-logo">
      <span class="hero-logo-emoji">🍳</span>
    </div>


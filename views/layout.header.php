<!--
Deployed URL: https://cs4640.cs.virginia.edu/juh7hc/
Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
CS 4640 Sprint 4
-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Jeremy Ky, Ashley Wu, Shaunak Sinha">
  <title><?= h($page_title ?? 'Recipe Creator') ?></title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <a href="#main" class="sr-only">Skip to main content</a>

  <header class="navbar">
    <div class="navbar-inner">
      <a href="index.php" class="brand">Recipe Creator</a>
      <nav aria-label="Primary navigation">
        <a href="index.php" <?= ($current_page ?? '') === 'home' ? 'aria-current="page"' : '' ?>>Home</a>
        <a href="index.php?action=recipes" <?= ($current_page ?? '') === 'recipes' ? 'aria-current="page"' : '' ?>>Recipes</a>
        <a href="index.php?action=chat" <?= ($current_page ?? '') === 'chat' ? 'aria-current="page"' : '' ?>>Chat</a>
        <a href="index.php?action=upload" <?= ($current_page ?? '') === 'upload' ? 'aria-current="page"' : '' ?>>Upload</a>
        <a href="index.php?action=pantry" <?= ($current_page ?? '') === 'pantry' ? 'aria-current="page"' : '' ?>>Pantry</a>
        <a href="index.php?action=match" <?= ($current_page ?? '') === 'match' ? 'aria-current="page"' : '' ?>>Match</a>
        <a href="index.php?action=cook" <?= ($current_page ?? '') === 'cook' ? 'aria-current="page"' : '' ?>>Cook</a>
        <a href="index.php?action=about" <?= ($current_page ?? '') === 'about' ? 'aria-current="page"' : '' ?>>About</a>
      </nav>
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
      <img src="assets/uva.jpg" alt="University of Virginia logo">
    </div>


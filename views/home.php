<?php
$page_title = 'Pantry Pilot - Landing';
$current_page = 'home';
?>

<div class="section-header">
  <h1 id="hero-heading">Welcome to Pantry Pilot</h1>
  <p>Keep track of what's in your kitchen and find recipes that work with your ingredients. No more wasted food or last-minute grocery runs.</p>
</div>

<section>
  <div class="section-header">
    <h2 id="features-heading">Quick Actions</h2>
    <p>Choose what you'd like to do today</p>
  </div>
  <div class="grid grid-3">
    <div class="card">
      <div class="card-header">
        <h3>Browse Recipes</h3>
        <p>Search our collection by cuisine, time, or skill level</p>
      </div>
      <button onclick="window.location.href='index.php?action=recipes'" class="btn-primary">Browse Recipes</button>
    </div>
    <div class="card">
      <div class="card-header">
        <h3>AI Chat Assistant</h3>
        <p>Get personalized recipe suggestions and cooking tips</p>
      </div>
      <button onclick="window.location.href='index.php?action=chat'" class="btn-primary">Start Chatting</button>
    </div>
    <div class="card">
      <div class="card-header">
        <h3>Recipe Matcher</h3>
        <p>Find recipes based on your current ingredients</p>
      </div>
      <button onclick="window.location.href='index.php?action=match'" class="btn-primary">Match Recipes</button>
    </div>
  </div>
</section>

<section>
  <div class="section-header">
    <h2 id="get-started">How It Works</h2>
    <p>Get started in three simple steps</p>
  </div>
  <div class="grid grid-3">
    <div class="card">
      <div class="card-header">
        <h3>1. Add Ingredients</h3>
        <p>Tell us what you have in your kitchen</p>
      </div>
      <button onclick="window.location.href='index.php?action=pantry'" class="btn-secondary">Manage Pantry</button>
    </div>
    <div class="card">
      <div class="card-header">
        <h3>2. Upload Recipes</h3>
        <p>Save recipes from websites or add your own</p>
      </div>
      <button onclick="window.location.href='index.php?action=upload'" class="btn-secondary">Add Recipe</button>
    </div>
    <div class="card">
      <div class="card-header">
        <h3>3. Start Cooking</h3>
        <p>Follow step-by-step instructions with progress tracking</p>
      </div>
      <button onclick="window.location.href='index.php?action=cook'" class="btn-secondary">Start Cooking</button>
    </div>
  </div>
</section>


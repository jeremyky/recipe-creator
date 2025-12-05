<?php
// Load utility functions
if (!function_exists('h')) {
  require_once __DIR__ . '/../lib/util.php';
}
if (!function_exists('csrf_token')) {
  require_once __DIR__ . '/../lib/session.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="authors" content="Jeremy Ky, Ashley Wu, Shaunak Sinha">
  <title>Sign In - Pantry Pilot</title>
  <link rel="stylesheet" href="assets/auth.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <a href="index.php" class="back-link">← Back to home</a>
        <h1>Welcome back</h1>
        <p>Sign in to your Pantry Pilot account</p>
      </div>
      
      <?php if (isset($flash['error'])): ?>
        <div class="alert alert-error">
          <?= h($flash['error']) ?>
        </div>
      <?php endif; ?>
      
      <div class="auth-providers">
        <button type="button" class="btn-provider btn-google" id="google-signin">
          <svg width="18" height="18" viewBox="0 0 18 18">
            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
            <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
            <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.039l3.007-2.332z"/>
            <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.163 6.656 3.58 9 3.58z"/>
          </svg>
          Continue with Google
        </button>
      </div>
      
      <div class="divider">
        <span>or</span>
      </div>
      
      <form method="POST" action="index.php?action=login_submit" class="auth-form">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        
        <div class="form-group">
          <label for="email">Email address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            placeholder="you@example.com"
            value="<?= h($old['email'] ?? '') ?>"
            autocomplete="email"
          >
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            required 
            placeholder="Enter your password"
            autocomplete="current-password"
          >
        </div>
        
        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="remember" value="1">
            <span>Remember me</span>
          </label>
          <a href="index.php?action=forgot_password" class="forgot-link">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full">Sign In</button>
      </form>
      
      <?php if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1'): ?>
      <div style="margin-top: 1.5rem; padding: 1rem; background: #f0f9ff; border: 2px solid #3b82f6; border-radius: 8px; text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 600; color: #1e40af;">🔧 Local Testing Mode</p>
        <p style="margin: 0 0 1rem; font-size: 0.875rem; color: #1e40af;">Database not required for local testing</p>
        <a href="index.php?action=home" class="btn btn-primary" style="display: inline-block;">Continue to App (Demo Mode)</a>
      </div>
      <?php endif; ?>
      
      <div class="auth-footer">
        <p>Don't have an account? <a href="index.php?action=signup">Sign up</a></p>
      </div>
    </div>
    
    <div class="auth-info">
      <div class="info-content">
        <h2>🍳 Cook Smarter, Waste Less</h2>
        <p>Join thousands of home cooks using Pantry Pilot to:</p>
        <ul>
          <li>✨ Match recipes with your pantry ingredients</li>
          <li>📦 Track your food inventory</li>
          <li>♻️ Reduce food waste by 50%</li>
          <li>👨‍🍳 Get AI-powered cooking assistance</li>
        </ul>
      </div>
    </div>
  </div>
  
  <script src="assets/js/auth.js"></script>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</body>
</html>


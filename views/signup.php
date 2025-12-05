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
  <title>Sign Up - Pantry Pilot</title>
  <link rel="stylesheet" href="assets/auth.css">
  <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
  <link rel="apple-touch-icon" href="assets/icon-192.png">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <a href="index.php" class="back-link">← Back to home</a>
        <h1>Get started free</h1>
        <p>Create your Pantry Pilot account</p>
      </div>
      
      <?php if (isset($flash['error'])): ?>
        <div class="alert alert-error">
          <?= h($flash['error']) ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($flash['errors'])): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($flash['errors'] as $error): ?>
              <li><?= h($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <div class="auth-providers">
        <button type="button" class="btn-provider btn-google" id="google-signup">
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
      
      <form method="POST" action="index.php?action=signup_submit" class="auth-form">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        
        <div class="form-group">
          <label for="name">Full name</label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            required 
            placeholder="John Doe"
            value="<?= h($old['name'] ?? '') ?>"
            autocomplete="name"
          >
        </div>
        
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
            placeholder="At least 8 characters"
            autocomplete="new-password"
          >
          <div class="password-strength" id="password-strength"></div>
        </div>
        
        <div class="form-group">
          <label for="password_confirm">Confirm password</label>
          <input 
            type="password" 
            id="password_confirm" 
            name="password_confirm" 
            required 
            placeholder="Re-enter your password"
            autocomplete="new-password"
          >
        </div>
        
        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="terms" value="1" required>
            <span>I agree to the <a href="#terms" target="_blank">Terms</a> and <a href="#privacy" target="_blank">Privacy Policy</a></span>
          </label>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full">Create Account</button>
      </form>
      
      <?php if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1'): ?>
      <div style="margin-top: 1.5rem; padding: 1rem; background: #f0f9ff; border: 2px solid #3b82f6; border-radius: 8px; text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 600; color: #1e40af;">🔧 Local Testing Mode</p>
        <p style="margin: 0 0 1rem; font-size: 0.875rem; color: #1e40af;">Database not required for local testing</p>
        <a href="index.php?action=home" class="btn btn-primary" style="display: inline-block;">Continue to App (Demo Mode)</a>
      </div>
      <?php endif; ?>
      
      <div class="auth-footer">
        <p>Already have an account? <a href="index.php?action=login">Sign in</a></p>
      </div>
    </div>
    
    <div class="auth-info">
      <div class="info-content">
        <h2>🎉 What You'll Get</h2>
        <div class="feature-list">
          <div class="feature-item">
            <span class="feature-icon">🤖</span>
            <div>
              <strong>AI Recipe Matching</strong>
              <p>Instant recipe suggestions from your pantry</p>
            </div>
          </div>
          <div class="feature-item">
            <span class="feature-icon">📦</span>
            <div>
              <strong>Smart Pantry</strong>
              <p>Track inventory and expiration dates</p>
            </div>
          </div>
          <div class="feature-item">
            <span class="feature-icon">💬</span>
            <div>
              <strong>AI Assistant</strong>
              <p>Get cooking tips and recipe help</p>
            </div>
          </div>
          <div class="feature-item">
            <span class="feature-icon">♻️</span>
            <div>
              <strong>Zero Waste</strong>
              <p>Reduce food waste by 50%</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="assets/js/auth.js"></script>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</body>
</html>


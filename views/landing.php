<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Recipe Creator - Transform your pantry into delicious meals with AI-powered recipe suggestions">
  <meta name="authors" content="Jeremy Ky, Ashley Wu, Shaunak Sinha">
  <title>Recipe Creator - Your Smart Kitchen Companion</title>
  <link rel="stylesheet" href="assets/landing.css">
</head>
<body class="light-mode">
  <!-- Navigation -->
  <nav class="landing-nav">
    <div class="nav-container">
      <div class="nav-logo">
        <span class="logo-icon">🍳</span>
        <span class="logo-text">Recipe Creator</span>
      </div>
      
      <div class="nav-links">
        <a href="#features">Features</a>
        <a href="#how-it-works">How It Works</a>
        <a href="#faq">FAQ</a>
        <a href="#blog">Blog</a>
      </div>
      
      <div class="nav-actions">
        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
          <span class="theme-icon">🌙</span>
        </button>
        <a href="index.php?action=login" class="btn btn-secondary">Sign In</a>
        <a href="index.php?action=signup" class="btn btn-primary">Get Started</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-container">
      <div class="hero-content">
        <h1 class="hero-title">Transform Your Pantry Into <span class="highlight">Delicious Meals</span></h1>
        <p class="hero-subtitle">Never waste food again. Recipe Creator uses AI to match your ingredients with perfect recipes, track your pantry, and guide you through cooking.</p>
        <div class="hero-cta">
          <a href="index.php?action=signup" class="btn btn-primary btn-lg">Start Cooking Free</a>
          <a href="#how-it-works" class="btn btn-secondary btn-lg">See How It Works</a>
        </div>
        <p class="hero-note">✨ No credit card required • Free forever plan available</p>
      </div>
      <div class="hero-image">
        <div class="hero-mockup">
          <div class="mockup-window">
            <div class="mockup-header">
              <span class="mockup-dot"></span>
              <span class="mockup-dot"></span>
              <span class="mockup-dot"></span>
            </div>
            <div class="mockup-content">
              <div class="mockup-card">
                <span class="mockup-icon">🥗</span>
                <div>
                  <strong>5 Recipes Match</strong>
                  <p>Based on your pantry</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="features">
    <div class="container">
      <h2 class="section-title">Everything You Need to Cook Smarter</h2>
      <p class="section-subtitle">Powerful features designed to reduce food waste and inspire your cooking</p>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🤖</div>
          <h3>AI-Powered Matching</h3>
          <p>Our intelligent algorithm matches your pantry ingredients with thousands of recipes, finding perfect dishes you can make right now.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📦</div>
          <h3>Smart Pantry Tracking</h3>
          <p>Keep track of what you have, get notifications when items are running low, and never overbuy at the grocery store again.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">👨‍🍳</div>
          <h3>Step-by-Step Cooking</h3>
          <p>Interactive cooking mode guides you through each step with timers, progress tracking, and helpful tips along the way.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">💬</div>
          <h3>AI Chat Assistant</h3>
          <p>Ask questions, get cooking tips, and receive personalized recipe recommendations from our intelligent chat assistant.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">📱</div>
          <h3>Import from Anywhere</h3>
          <p>Save recipes from your favorite websites with one click, or add your own family recipes to build your personal collection.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">♻️</div>
          <h3>Zero Waste Mission</h3>
          <p>Reduce food waste by using what you already have. Our system prioritizes recipes that maximize your existing ingredients.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section id="how-it-works" class="how-it-works">
    <div class="container">
      <h2 class="section-title">How Recipe Creator Works</h2>
      <p class="section-subtitle">From pantry to plate in three simple steps</p>
      
      <div class="steps">
        <div class="step">
          <div class="step-number">1</div>
          <div class="step-content">
            <h3>Add Your Ingredients</h3>
            <p>Quickly input what's in your pantry, fridge, and freezer. Our smart system recognizes thousands of ingredients and keeps your inventory organized.</p>
          </div>
          <div class="step-visual">📝</div>
        </div>
        
        <div class="step">
          <div class="step-number">2</div>
          <div class="step-content">
            <h3>Get Matched Recipes</h3>
            <p>Our AI instantly analyzes your ingredients and shows you recipes you can make right now, sorted by what you have on hand.</p>
          </div>
          <div class="step-visual">🎯</div>
        </div>
        
        <div class="step">
          <div class="step-number">3</div>
          <div class="step-content">
            <h3>Start Cooking</h3>
            <p>Follow along with interactive cooking mode. Check off steps, set timers, and enjoy your delicious meal made from what you already have.</p>
          </div>
          <div class="step-visual">👨‍🍳</div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section id="faq" class="faq">
    <div class="container">
      <h2 class="section-title">Frequently Asked Questions</h2>
      <p class="section-subtitle">Everything you need to know about Recipe Creator</p>
      
      <div class="faq-grid">
        <div class="faq-item">
          <h3>How does Recipe Creator work?</h3>
          <p>Recipe Creator connects your pantry inventory with our extensive recipe database. When you add ingredients to your pantry, our AI-powered matching system instantly finds recipes you can make. The more you add, the more personalized your recommendations become. You can also chat with our AI assistant for cooking tips and recipe suggestions.</p>
        </div>
        
        <div class="faq-item">
          <h3>Is the AI trustworthy and accurate?</h3>
          <p>Yes! Our AI is built on proven recipe databases and cooking science. All recipe matches are verified for ingredient compatibility and cooking feasibility. The AI assistant provides suggestions based on established culinary principles. However, we always recommend using your best judgment and adjusting recipes to your taste preferences.</p>
        </div>
        
        <div class="faq-item">
          <h3>What membership plans do you offer?</h3>
          <p>We offer a generous free plan that includes pantry tracking, recipe matching, and basic features. Our Premium plan ($9.99/month) adds unlimited AI chat, recipe imports from any website, advanced filtering, and priority support. Enterprise plans are available for food businesses and culinary schools.</p>
        </div>
        
        <div class="faq-item">
          <h3>How is Recipe Creator useful?</h3>
          <p>Recipe Creator helps you reduce food waste, save money on groceries, and discover new recipes using ingredients you already have. It takes the guesswork out of meal planning by showing you exactly what you can make right now. Users report saving 20-30% on grocery bills and reducing food waste by 50%.</p>
        </div>
        
        <div class="faq-item">
          <h3>Can I use Recipe Creator on mobile?</h3>
          <p>Yes! Recipe Creator is fully responsive and works perfectly on phones, tablets, and desktops. Access your pantry and recipes from anywhere. We also have native mobile apps coming soon for iOS and Android.</p>
        </div>
        
        <div class="faq-item">
          <h3>How do you protect my data?</h3>
          <p>We take privacy seriously. Your pantry data and recipes are encrypted and stored securely. We never sell your data to third parties. You can export or delete your data at any time. We're GDPR compliant and use industry-standard security practices.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Section -->
  <section id="blog" class="blog">
    <div class="container">
      <h2 class="section-title">From Our Kitchen</h2>
      <p class="section-subtitle">Tips, tricks, and stories to help you cook smarter</p>
      
      <div class="blog-grid">
        <article class="blog-card">
          <div class="blog-image">
            <div class="blog-image-placeholder">🥗</div>
          </div>
          <div class="blog-content">
            <span class="blog-tag">Getting Started</span>
            <h3>10 Tips to Maximize Your Pantry Efficiency</h3>
            <p>Ever opened your pantry only to discover expired items hiding in the back? You're not alone. A well-organized pantry isn't just aesthetically pleasing—it's a money-saver and time-saver. Start by categorizing items by type (grains, canned goods, spices) and using clear containers so you can see what you have at a glance. Place newer items behind older ones (first-in, first-out method) and keep a running inventory list on your phone. Consider adding shelf risers to maximize vertical space and lazy Susans for corner cabinets. Label everything with purchase or expiration dates, and do a monthly audit to move soon-to-expire items to the front. With Recipe Creator's smart pantry tracking, you'll get automatic reminders when items are running low, helping you avoid both waste and last-minute grocery runs. Remember: a visible pantry is a usable pantry!</p>
            <a href="#blog-pantry" class="blog-link">Read More →</a>
          </div>
        </article>
        
        <article class="blog-card">
          <div class="blog-image">
            <div class="blog-image-placeholder">🤖</div>
          </div>
          <div class="blog-content">
            <span class="blog-tag">AI Features</span>
            <h3>How to Get Better AI Recipe Recommendations</h3>
            <p>Getting generic recipe suggestions? Let's change that. Our AI learns from your behavior, but you can accelerate the process. Start by rating recipes honestly—the AI uses this feedback to understand your taste preferences. Use specific search terms like "quick weeknight dinner" or "vegetarian comfort food" instead of just "pasta." Update your pantry regularly so the AI knows what you actually have available. Tag recipes with custom labels like "family favorite" or "date night" to help the system understand context. The chat feature is incredibly powerful—don't just ask "what should I make?" Try: "I have 30 minutes, chicken thighs, and I'm craving something spicy." The more detailed your inputs, the more personalized your results. Also, explore the "surprise me" feature when you're feeling adventurous—it helps the AI discover patterns you might not even know about yourself!</p>
            <a href="#blog-ai" class="blog-link">Read More →</a>
          </div>
        </article>
        
        <article class="blog-card">
          <div class="blog-image">
            <div class="blog-image-placeholder">♻️</div>
          </div>
          <div class="blog-content">
            <span class="blog-tag">Sustainability</span>
            <h3>Reducing Food Waste: A Complete Guide</h3>
            <p>Did you know that the average household throws away $1,500 worth of food each year? Food waste isn't just an environmental issue—it's draining your wallet. The solution starts with mindful shopping: always check your pantry before heading to the store (Recipe Creator makes this easy with our mobile-friendly pantry view). Buy only what you need and plan meals around ingredients you already have. Master the art of "rescue cooking"—that slightly wilted spinach? Perfect for a frittata. Overripe bananas? Banana bread time. Leftover rice? Fried rice tomorrow. Store produce properly: keep herbs in water like flowers, wrap leafy greens in damp towels, and never store tomatoes in the fridge. Freeze what you can't use immediately—bread, stock, even cheese can be frozen. Use Recipe Creator's expiration tracking to get alerted before items go bad, and try our "use it up" feature to find recipes specifically designed to rescue ingredients. Every meal you make from what you have is a small victory for your budget and the planet.</p>
            <a href="#blog-waste" class="blog-link">Read More →</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="container">
      <h2>Ready to Transform Your Kitchen?</h2>
      <p>Join thousands of home cooks who are saving money, reducing waste, and cooking delicious meals.</p>
      <a href="index.php?action=signup" class="btn btn-primary btn-lg">Get Started Free</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="landing-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-col">
          <h4>Recipe Creator</h4>
          <p>Your intelligent kitchen companion for reducing food waste and discovering delicious recipes.</p>
          <div class="social-links">
            <a href="https://youtube.com/@recipecreator" target="_blank" aria-label="YouTube">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
            </a>
            <a href="https://instagram.com/recipecreator" target="_blank" aria-label="Instagram">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a href="https://twitter.com/recipecreator" target="_blank" aria-label="Twitter">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a href="https://facebook.com/recipecreator" target="_blank" aria-label="Facebook">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
          </div>
        </div>
        
        <div class="footer-col">
          <h4>Product</h4>
          <ul>
            <li><a href="#features">Features</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="index.php?action=signup">Pricing</a></li>
            <li><a href="#faq">FAQ</a></li>
          </ul>
        </div>
        
        <div class="footer-col">
          <h4>Company</h4>
          <ul>
            <li><a href="#about">About Us</a></li>
            <li><a href="#blog">Blog</a></li>
            <li><a href="#careers">Careers</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>
        
        <div class="footer-col">
          <h4>Legal</h4>
          <ul>
            <li><a href="#privacy">Privacy Policy</a></li>
            <li><a href="#terms">Terms of Service</a></li>
            <li><a href="#cookies">Cookie Policy</a></li>
            <li><a href="#licenses">Licenses</a></li>
          </ul>
        </div>
        
        <div class="footer-col">
          <h4>Support</h4>
          <ul>
            <li><a href="#help">Help Center</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <li><a href="#status">System Status</a></li>
            <li><a href="#feedback">Send Feedback</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2025 Recipe Creator. All rights reserved. Jeremy Ky, Ashley Wu, and Shaunak Sinha.</p>
      </div>
    </div>
  </footer>

  <script src="assets/js/landing.js"></script>
</body>
</html>


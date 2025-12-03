<?php
$page_title = 'Chat Search - Recipe Creator';
$current_page = 'chat';
?>

<div class="section-header">
  <h1>AI Cooking Assistant</h1>
  <p>Get personalized recipe recommendations and cooking tips</p>
</div>

<!-- Chat Container -->
<div class="chat-container">
  <!-- Suggestions -->
  <div class="chat-suggestions" id="chat-suggestions">
    <button type="button" class="chip" data-prompt="What can I make with chicken and rice?">
      What can I make with chicken and rice?
    </button>
    <button type="button" class="chip" data-prompt="Give me a quick 30-minute dinner recipe">
      Quick 30-minute dinner
    </button>
    <button type="button" class="chip" data-prompt="How do I cook perfect pasta?">
      How to cook pasta?
    </button>
    <button type="button" class="chip" data-prompt="Suggest a healthy vegetarian meal">
      Healthy vegetarian meal
    </button>
  </div>

  <!-- Messages Area -->
  <div class="chat-messages" id="chat-messages">
    <!-- Messages will be added here dynamically -->
  </div>

  <!-- Input Area -->
  <div class="chat-input-area">
    <form id="chat-form" method="post">
      <div class="chat-input-wrapper">
        <textarea 
          id="chat-prompt" 
          name="prompt" 
          rows="1"
          placeholder="Ask me anything about cooking..." 
          maxlength="500"
          required></textarea>
        <div class="chat-input-actions">
          <span class="char-counter" id="char-counter">0 / 500</span>
          <button type="submit" class="btn-primary">
            <span>Send</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


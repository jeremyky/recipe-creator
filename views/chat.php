<?php
$page_title = 'Chat Search - Recipe Creator';
$current_page = 'chat';
?>

<h1>Chat Search</h1>
<p class="lead">Ask questions in natural language to find recipes, get cooking tips, or plan meals.</p>

<section aria-labelledby="prompt-heading">
  <h2 class="sr-only" id="prompt-heading">Enter your question</h2>
  <div class="card">
    <form method="get" action="index.php?action=chat">
      <div class="form-row">
        <label for="chat-prompt">What would you like to cook?</label>
        <textarea id="chat-prompt" name="prompt" rows="3" 
                  placeholder="e.g. I have chicken and broccoli, what can I make for dinner?" 
                  required><?= h($_GET['prompt'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn btn--primary">Search</button>
    </form>
  </div>
</section>

<section aria-labelledby="results-heading" id="chat-results" style="display: none;">
  <h2 id="results-heading">Response</h2>
  <div class="card" id="chat-response">
    <div class="loading" style="margin: 0 auto 1rem; display: block;"></div>
    <p style="text-align: center; color: var(--muted);">Thinking...</p>
  </div>
</section>


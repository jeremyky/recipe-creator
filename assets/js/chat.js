/**
 * Chat page dynamic behaviors with modern chat interface
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage chat interface
const ChatInterface = {
    maxLength: 500,
    currentLength: 0,
    
    init: function() {
        this.setupCharacterCounter();
        this.setupSuggestions();
        this.setupChatSubmission();
        this.setupTextareaAutoResize();
    },
    
    // Character counter with DOM manipulation
    setupCharacterCounter: function() {
        const textarea = document.querySelector('#chat-prompt');
        const counter = document.querySelector('#char-counter');
        
        if (!textarea || !counter) return;
        
        // Event listener for input changes
        textarea.addEventListener('input', (event) => {
            this.currentLength = event.target.value.length;
            
            // DOM manipulation - update counter
            counter.textContent = `${this.currentLength} / ${this.maxLength}`;
            
            // Modify style based on character count using arrow function
            const updateCounterStyle = (length) => {
                const percentage = (length / this.maxLength) * 100;
                
                counter.classList.remove('warning', 'danger');
                
                if (percentage >= 90) {
                    counter.classList.add('danger');
                } else if (percentage >= 75) {
                    counter.classList.add('warning');
                }
            };
            
            updateCounterStyle(this.currentLength);
        });
    },
    
    // Setup suggestion chips
    setupSuggestions: function() {
        const suggestions = document.querySelectorAll('.chat-suggestions .chip');
        const textarea = document.querySelector('#chat-prompt');
        
        if (!suggestions.length || !textarea) return;
        
        // Event listeners for suggestion buttons
        suggestions.forEach(chip => {
            chip.addEventListener('click', () => {
                const prompt = chip.getAttribute('data-prompt');
                textarea.value = prompt;
                textarea.dispatchEvent(new Event('input'));
                textarea.focus();
            });
        });
    },
    
    // Setup textarea auto-resize
    setupTextareaAutoResize: function() {
        const textarea = document.querySelector('#chat-prompt');
        if (!textarea) return;
        
        textarea.addEventListener('input', () => {
            // Reset height to auto to calculate correct scrollHeight
            textarea.style.height = 'auto';
            // Set height based on scrollHeight
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        });
    },
    
    // Add message to chat
    addMessage: function(content, type = 'user', isSuccess = false) {
        const messagesContainer = document.getElementById('chat-messages');
        if (!messagesContainer) return;
        
        // Hide suggestions after first message
        const suggestions = document.getElementById('chat-suggestions');
        if (suggestions && messagesContainer.children.length === 0) {
            suggestions.style.display = 'none';
        }
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;
        
        // Add success highlight if specified
        if (isSuccess) {
            messageDiv.classList.add('success-highlight');
            messageDiv.style.background = 'var(--color-success-soft)';
            messageDiv.style.borderLeft = '3px solid var(--color-success)';
            messageDiv.style.padding = '12px';
        }
        
        // Create avatar
        const avatar = document.createElement('div');
        avatar.className = 'chat-message-avatar';
        avatar.textContent = type === 'user' ? 'You' : (isSuccess ? '✅' : 'AI');
        
        // Create content
        const messageContent = document.createElement('div');
        messageContent.className = 'chat-message-content';
        
        if (type === 'loading') {
            messageContent.innerHTML = `
                <div class="chat-loading">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
        } else {
            messageContent.innerHTML = `<p>${content}</p>`;
        }
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(messageContent);
        
        messagesContainer.appendChild(messageDiv);
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        return messageDiv;
    },
    
    // Save recipe from AI response to user's collection
    saveRecipeFromAI: function(buttonElement) {
        // Get the recipe text from the container
        const container = buttonElement.closest('.recipe-extract-container');
        const recipeText = container.querySelector('div[style*="monospace"]').textContent;
        
        // Parse the recipe format
        const recipe = this.parseRecipeFormat(recipeText);
        
        if (!recipe) {
            alert('❌ Could not parse recipe. Please try copying manually.');
            return;
        }
        
        // Disable button and show loading
        buttonElement.disabled = true;
        buttonElement.textContent = '💾 Saving...';
        
        // Send to backend
        console.log('💾 Saving recipe:', recipe);
        
        fetch('api/save_ai_recipe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(recipe)
        })
        .then(response => {
            console.log('📡 Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Server response:', data);
            if (data.success) {
                // Handle local storage mode
                if (data.mode === 'local' && data.recipe) {
                    // Save to localStorage for local testing
                    const localRecipes = JSON.parse(localStorage.getItem('local_recipes') || '[]');
                    localRecipes.push({
                        id: data.recipe_id,
                        ...data.recipe
                    });
                    localStorage.setItem('local_recipes', JSON.stringify(localRecipes));
                    
                    console.log('📦 Recipe saved to localStorage:', data.recipe);
                    console.log('📊 Total local recipes:', localRecipes.length);
                    
                    // Show browser notification if supported
                    if (window.Notification && Notification.permission === 'granted') {
                        new Notification('Recipe Saved!', {
                            body: `"${recipe.title}" has been saved to your local storage`,
                            icon: '🍳'
                        });
                    }
                }
                
                // Update button state
                buttonElement.textContent = '✅ Saved!';
                buttonElement.style.background = 'var(--color-success)';
                
                // Show prominent success message with highlighting
                const successMsg = data.mode === 'local' 
                    ? `<strong>Recipe Saved Successfully!</strong><br><br>"${recipe.title}" has been saved to your browser's local storage for testing.<br><br><a href="index.php?action=recipes" style="color: var(--color-primary); font-weight: 600; text-decoration: underline;">📖 View your recipes →</a>`
                    : `<strong>Recipe Saved Successfully!</strong><br><br>"${recipe.title}" has been saved to your collection!<br><br><a href="index.php?action=recipes" style="color: var(--color-primary); font-weight: 600; text-decoration: underline;">📖 View your recipes →</a>`;
                
                this.addMessage(successMsg, 'assistant', true); // true = success highlight
                
                // Scroll to show the success message
                const messagesContainer = document.getElementById('chat-messages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Re-enable after 3 seconds
                setTimeout(() => {
                    buttonElement.disabled = false;
                    buttonElement.textContent = '💾 Save Recipe to Collection';
                    buttonElement.style.background = '';
                }, 3000);
            } else {
                throw new Error(data.error || 'Failed to save recipe');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            buttonElement.disabled = false;
            buttonElement.textContent = '❌ Save Failed';
            buttonElement.style.background = 'var(--color-danger)';
            alert('Failed to save recipe: ' + error.message);
            
            setTimeout(() => {
                buttonElement.textContent = '💾 Save Recipe to Collection';
                buttonElement.style.background = '';
            }, 3000);
        });
    },
    
    // Parse structured recipe format
    parseRecipeFormat: function(text) {
        try {
            // Extract title
            const titleMatch = text.match(/TITLE:\s*(.+)/);
            if (!titleMatch) return null;
            const title = titleMatch[1].trim();
            
            // Extract cuisine
            const cuisineMatch = text.match(/CUISINE:\s*(.+)/);
            const cuisine = cuisineMatch ? cuisineMatch[1].trim() : 'other';
            
            // Extract ingredients
            const ingredientsSection = text.match(/INGREDIENTS:([\s\S]*?)(?=\n\nSTEPS:|$)/);
            if (!ingredientsSection) return null;
            
            const ingredientLines = ingredientsSection[1]
                .split('\n')
                .map(line => line.trim())
                .filter(line => line.startsWith('-'))
                .map(line => line.substring(1).trim());
            
            if (ingredientLines.length === 0) return null;
            const ingredients = ingredientLines.join('\n');
            
            // Extract steps
            const stepsSection = text.match(/STEPS:([\s\S]*?)(?===== RECIPE END|$)/);
            if (!stepsSection) return null;
            
            const stepLines = stepsSection[1]
                .split('\n')
                .map(line => line.trim())
                .filter(line => /^\d+\./.test(line))
                .map(line => line.replace(/^\d+\.\s*/, ''));
            
            if (stepLines.length === 0) return null;
            const steps = stepLines.join('\n');
            
            return {
                title: title,
                cuisine: cuisine,
                ingredients: ingredients,
                steps: steps
            };
        } catch (error) {
            console.error('Parse error:', error);
            return null;
        }
    },
    
    // Setup AJAX chat submission
    setupChatSubmission: function() {
        const form = document.getElementById('chat-form');
        const textarea = document.querySelector('#chat-prompt');
        
        if (!form || !textarea) return;
        
        // Event listener for form submission
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            
            const prompt = textarea.value.trim();
            if (!prompt || prompt.length < 5) {
                alert('Please enter at least 5 characters.');
                return;
            }
            
            // Add user message
            this.addMessage(prompt, 'user');
            
            // Show loading state
            const loadingMessage = this.addMessage('', 'loading');
            loadingMessage.className = 'chat-message assistant';
            
            // Clear textarea
            textarea.value = '';
            textarea.style.height = 'auto';
            textarea.dispatchEvent(new Event('input'));
            
            // Make AJAX request (authentication handled via session)
            fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin', // Important: send session cookie!
                body: JSON.stringify({ 
                    prompt: prompt
                })
            })
            .then(response => {
                if (response.status === 401) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Unauthorized');
                    });
                }
                if (response.status === 429) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Rate limit exceeded');
                    });
                }
                return response.json();
            })
            .then(data => {
                // Remove loading message
                loadingMessage.remove();
                
                if (data.success) {
                    // Format response with special handling for structured recipes
                    let formattedResponse = data.response;
                    
                    // Check if response contains a structured recipe
                    if (formattedResponse.includes('=== RECIPE START ===')) {
                        // Extract the recipe data for the save button
                        const recipeMatch = formattedResponse.match(/=== RECIPE START ===([\s\S]*?)=== RECIPE END ===/);
                        const recipeData = recipeMatch ? recipeMatch[1] : null;
                        
                        // Wrap recipe in a code block with save button
                        formattedResponse = formattedResponse.replace(
                            /(=== RECIPE START ===[\s\S]*?=== RECIPE END ===)/g,
                            '<div class="recipe-extract-container" style="background: var(--color-bg); border: 2px solid var(--color-primary); border-radius: 8px; padding: 16px; margin: 12px 0;"><div style="font-family: monospace; white-space: pre-wrap; font-size: 13px; margin-bottom: 12px; padding: 12px; background: var(--color-bg-elevated); border-radius: 6px;">$1</div><button onclick="window.ChatInterface.saveRecipeFromAI(this)" class="btn-primary" style="width: 100%;">💾 Save Recipe to Collection</button></div>'
                        );
                    }
                    
                    // Format markdown-like text
                    formattedResponse = formattedResponse
                        .replace(/\n\n/g, '</p><p>')
                        .replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>');
                    
                    // Add AI response
                    this.addMessage(formattedResponse, 'assistant');
                    
                    if (data.source === 'fallback') {
                        const noteDiv = document.createElement('div');
                        noteDiv.className = 'chat-message assistant';
                        noteDiv.innerHTML = `
                            <div class="chat-message-avatar">ℹ️</div>
                            <div class="chat-message-content">
                                <p style="font-size: 12px; font-style: italic; opacity: 0.7;">💳 Note: Limited response (OpenAI API quota exceeded). Add credits to your OpenAI account for full AI functionality.</p>
                            </div>
                        `;
                        document.getElementById('chat-messages').appendChild(noteDiv);
                    }
                } else {
                    this.addMessage('Error: ' + (data.error || 'Failed to get response'), 'assistant');
                }
            })
            .catch(error => {
                // Remove loading message
                loadingMessage.remove();
                
                console.error('Chat error:', error);
                let errorMessage = 'Sorry, there was an error processing your request.';
                
                if (error.message.includes('Unauthorized')) {
                    errorMessage = '🔒 Access denied. Please refresh the page and try again.';
                } else if (error.message.includes('Rate limit') || error.message.includes('too many requests')) {
                    errorMessage = '⏱️ Rate limit exceeded. You can send 5 messages every 2 minutes to prevent abuse. Please wait a moment before trying again.';
                } else if (error.message.includes('quota') || error.message.includes('insufficient')) {
                    errorMessage = '💳 API quota exceeded. The OpenAI API has run out of credits. Please add billing to your OpenAI account.';
                } else {
                    errorMessage = '❌ Sorry, there was an error processing your request. Please try again.';
                }
                
                this.addMessage(errorMessage, 'assistant');
            });
        });
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => ChatInterface.init());
} else {
    ChatInterface.init();
}

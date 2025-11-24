/**
 * Chat page dynamic behaviors with character counter and suggestions
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage chat interface
const ChatInterface = {
    maxLength: 500,
    currentLength: 0,
    suggestions: [
        'What can I make with chicken?',
        'I have tomatoes and pasta',
        'Quick dinner ideas',
        'Vegetarian recipes',
        'Dessert recipes'
    ],
    
    init: function() {
        this.setupCharacterCounter();
        this.setupInputValidation();
        this.setupDynamicSuggestions();
        this.setupFormEnhancements();
        this.setupChatSubmission();
    },
    
    
    // Character counter with DOM manipulation
    setupCharacterCounter: function() {
        const textarea = document.querySelector('#chat-prompt');
        if (!textarea) return;
        
        // Create counter element using DOM manipulation
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.fontSize = '0.875rem';
        counter.style.color = 'var(--muted, #6c757d)';
        counter.style.marginTop = '0.5rem';
        counter.textContent = `0 / ${this.maxLength} characters`;
        
        textarea.parentElement.appendChild(counter);
        
        // Event listener for input changes
        textarea.addEventListener('input', (event) => {
            this.currentLength = event.target.value.length;
            
            // DOM manipulation - update counter
            counter.textContent = `${this.currentLength} / ${this.maxLength} characters`;
            
            // Modify style based on character count using arrow function
            const updateCounterStyle = (length) => {
                const percentage = (length / this.maxLength) * 100;
                
                if (percentage >= 90) {
                    counter.style.color = 'var(--danger, #dc3545)';
                    counter.style.fontWeight = 'bold';
                } else if (percentage >= 75) {
                    counter.style.color = 'var(--warning, #ffc107)';
                    counter.style.fontWeight = 'normal';
                } else {
                    counter.style.color = 'var(--muted, #6c757d)';
                    counter.style.fontWeight = 'normal';
                }
            };
            
            updateCounterStyle(this.currentLength);
        });
        
        // Initial count
        this.currentLength = textarea.value.length;
        counter.textContent = `${this.currentLength} / ${this.maxLength} characters`;
    },
    
    // Client-side input validation
    setupInputValidation: function() {
        const textarea = document.querySelector('#chat-prompt');
        const form = document.querySelector('form');
        
        if (!textarea || !form) return;
        
        // Event listener for form submission
        form.addEventListener('submit', (event) => {
            const value = textarea.value.trim();
            const errors = [];
            
            // Validation logic
            if (!value) {
                errors.push('Please enter a question or search term');
            } else if (value.length < 5) {
                errors.push('Please enter at least 5 characters');
            } else if (value.length > this.maxLength) {
                errors.push(`Message must be ${this.maxLength} characters or less`);
            }
            
            if (errors.length > 0) {
                event.preventDefault();
                this.displayValidationErrors(errors);
            } else {
                this.clearErrors();
            }
        });
        
        // Real-time validation
        textarea.addEventListener('blur', () => {
            const value = textarea.value.trim();
            let error = null;
            
            if (!value) {
                error = 'Please enter a question or search term';
            } else if (value.length < 5) {
                error = 'Please enter at least 5 characters';
            }
            
            this.showFieldError(textarea, error);
        });
    },
    
    // Display validation errors
    displayValidationErrors: function(errors) {
        this.clearErrors();
        
        // Anonymous function to create error element
        const createErrorElement = function(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'client-error';
            errorDiv.textContent = message;
            errorDiv.style.color = 'var(--danger, #dc3545)';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.5rem';
            errorDiv.setAttribute('role', 'alert');
            return errorDiv;
        };
        
        const form = document.querySelector('form');
        if (form) {
            errors.forEach(error => {
                form.appendChild(createErrorElement(error));
            });
        }
    },
    
    // Show field error
    showFieldError: function(field, errorMessage) {
        const existingError = field.parentElement.querySelector('.client-error');
        
        if (errorMessage) {
            field.classList.add('error');
            field.style.borderColor = 'var(--danger, #dc3545)';
            
            if (!existingError) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'client-error';
                errorDiv.textContent = errorMessage;
                errorDiv.style.color = 'var(--danger, #dc3545)';
                errorDiv.style.fontSize = '0.875rem';
                errorDiv.style.marginTop = '0.5rem';
                field.parentElement.appendChild(errorDiv);
            } else {
                existingError.textContent = errorMessage;
            }
        } else {
            field.classList.remove('error');
            field.style.borderColor = '';
            if (existingError) {
                existingError.remove();
            }
        }
    },
    
    // Clear errors
    clearErrors: function() {
        document.querySelectorAll('.client-error').forEach(error => error.remove());
        document.querySelectorAll('.error').forEach(field => {
            field.classList.remove('error');
            field.style.borderColor = '';
        });
    },
    
    // Dynamic suggestions with DOM manipulation
    setupDynamicSuggestions: function() {
        const textarea = document.querySelector('#chat-prompt');
        if (!textarea) return;
        
        // Create suggestions container
        const suggestionsContainer = document.createElement('div');
        suggestionsContainer.className = 'suggestions-container';
        suggestionsContainer.style.marginTop = '1rem';
        suggestionsContainer.style.backgroundColor = 'var(--panel, #1a1f3a)';
        suggestionsContainer.style.border = '1px solid var(--border, #2d3447)';
        suggestionsContainer.style.borderRadius = '8px';
        suggestionsContainer.style.padding = '1rem';
        
        const suggestionsTitle = document.createElement('p');
        suggestionsTitle.textContent = 'Suggestions:';
        suggestionsTitle.style.fontSize = '0.9rem';
        suggestionsTitle.style.color = 'var(--text-secondary, #cbd5e1)';
        suggestionsTitle.style.marginBottom = '0.5rem';
        
        const suggestionsList = document.createElement('div');
        suggestionsList.className = 'suggestions-list';
        suggestionsList.style.display = 'flex';
        suggestionsList.style.flexWrap = 'wrap';
        suggestionsList.style.gap = '0.5rem';
        
        // Create suggestion buttons
        this.suggestions.forEach((suggestion, index) => {
            // Anonymous function to create suggestion button
            (function(suggestionText) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'suggestion-btn';
                button.textContent = suggestionText;
                button.style.padding = '0.5rem 1rem';
                button.style.backgroundColor = 'var(--panel, #1a1f3a)';
                button.style.border = '1px solid var(--border, #2d3447)';
                button.style.borderRadius = '6px';
                button.style.cursor = 'pointer';
                button.style.fontSize = '0.875rem';
                button.style.transition = 'all 0.2s ease';
                button.style.color = 'var(--text, #f8fafc)';
                
                // Event listener for suggestion click
                button.addEventListener('click', () => {
                    textarea.value = suggestionText;
                    textarea.dispatchEvent(new Event('input'));
                    textarea.focus();
                });
                
                // Hover effects with style modification
                button.addEventListener('mouseenter', () => {
                    button.style.backgroundColor = 'var(--brand, #6366f1)';
                    button.style.color = 'white';
                    button.style.borderColor = 'var(--brand, #6366f1)';
                });
                
                button.addEventListener('mouseleave', () => {
                    button.style.backgroundColor = 'var(--panel, #1a1f3a)';
                    button.style.color = 'var(--text, #f8fafc)';
                    button.style.borderColor = 'var(--border, #2d3447)';
                });
                
                suggestionsList.appendChild(button);
            })(suggestion);
        });
        
        suggestionsContainer.appendChild(suggestionsTitle);
        suggestionsContainer.appendChild(suggestionsList);
        
        textarea.parentElement.appendChild(suggestionsContainer);
    },
    
    // Form enhancements
    setupFormEnhancements: function() {
        const form = document.querySelector('form');
        const submitButton = form?.querySelector('button[type="submit"]');
        
        if (!form || !submitButton) return;
        
        // Event listener for form submission
        form.addEventListener('submit', function(event) {
            // Modify button style on submit
            submitButton.disabled = true;
            submitButton.textContent = 'Searching...';
            submitButton.style.opacity = '0.7';
            submitButton.style.cursor = 'not-allowed';
        });
    },
    
    // Setup AJAX chat submission
    setupChatSubmission: function() {
        const form = document.querySelector('form[action*="action=chat"]');
        const textarea = document.querySelector('#chat-prompt');
        const resultsSection = document.getElementById('chat-results');
        const responseDiv = document.getElementById('chat-response');
        
        if (!form || !textarea || !resultsSection || !responseDiv) return;
        
        // Authentication handled server-side when chat page loads
        // No password needed in client code
        
        // Event listener for form submission
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            
            const prompt = textarea.value.trim();
            if (!prompt || prompt.length < 5) {
                return;
            }
            
            // Show loading state
            resultsSection.style.display = 'block';
            responseDiv.innerHTML = `
                <div class="loading" style="margin: 0 auto 1rem; display: block;"></div>
                <p style="text-align: center; color: var(--muted);">Thinking...</p>
            `;
            
            // Scroll to results
            resultsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Make AJAX request (authentication handled via session)
            fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
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
                if (data.success) {
                    // Display response
                    let responseHtml = '<div style="line-height: 1.8;">';
                    
                    // Convert markdown-like formatting and links
                    let formattedResponse = data.response
                        .replace(/\n\n/g, '</p><p>')
                        .replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>');
                    
                    // Convert links
                    formattedResponse = formattedResponse.replace(
                        /<a href='([^']+)'>([^<]+)<\/a>/g,
                        '<a href="$1" style="color: var(--accent); text-decoration: underline;">$2</a>'
                    );
                    
                    responseHtml += '<p>' + formattedResponse + '</p>';
                    responseHtml += '</div>';
                    
                    if (data.source === 'fallback') {
                        responseHtml += '<p style="margin-top: 1rem; font-size: 0.85rem; color: var(--muted); font-style: italic;">Note: Using fallback response. Set OPENAI_API_KEY for full AI functionality.</p>';
                    }
                    
                    responseDiv.innerHTML = responseHtml;
                } else {
                    responseDiv.innerHTML = '<p style="color: var(--danger);">Error: ' + (data.error || 'Failed to get response') + '</p>';
                }
            })
            .catch(error => {
                console.error('Chat error:', error);
                let errorMessage = 'Sorry, there was an error processing your request.';
                
                if (error.message.includes('Unauthorized')) {
                    errorMessage = 'Access denied. Please contact the administrator.';
                } else if (error.message.includes('Rate limit')) {
                    errorMessage = error.message;
                } else {
                    errorMessage = 'Sorry, there was an error processing your request. Please try again.';
                }
                
                responseDiv.innerHTML = '<p style="color: var(--danger);">' + errorMessage + '</p>';
            })
            .finally(() => {
                // Reset button
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Search';
                    submitButton.style.opacity = '';
                    submitButton.style.cursor = '';
                }
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


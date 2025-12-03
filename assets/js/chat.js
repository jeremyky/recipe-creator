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
    addMessage: function(content, type = 'user') {
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
        
        // Create avatar
        const avatar = document.createElement('div');
        avatar.className = 'chat-message-avatar';
        avatar.textContent = type === 'user' ? 'You' : 'AI';
        
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
                    // Format response
                    let formattedResponse = data.response
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
                                <p style="font-size: 12px; font-style: italic; opacity: 0.7;">Note: Using fallback response. Set OPENAI_API_KEY for full AI functionality.</p>
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
                    errorMessage = 'Access denied. Please contact the administrator.';
                } else if (error.message.includes('Rate limit')) {
                    errorMessage = error.message;
                } else {
                    errorMessage = 'Sorry, there was an error processing your request. Please try again.';
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

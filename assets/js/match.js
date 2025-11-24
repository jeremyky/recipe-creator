/**
 * Match page dynamic behaviors with filtering and interactive cards
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage recipe matching
const MatchManager = {
    recipes: [],
    maxMissing: 3,
    
    init: function() {
        this.setupSliderInteraction();
        this.setupRecipeCardInteractions();
        this.setupFilterDisplay();
    },
    
    // Dynamic slider interaction with style modification
    setupSliderInteraction: function() {
        const slider = document.querySelector('#max-missing');
        const output = document.querySelector('#max-missing-value');
        
        if (!slider || !output) return;
        
        // Event listener for slider input
        slider.addEventListener('input', (event) => {
            const value = parseInt(event.target.value);
            this.maxMissing = value;
            
            // DOM manipulation - update output display
            output.textContent = value;
            
            // Modify style based on value using arrow function
            const updateSliderStyle = (val) => {
                const percentage = (val / 5) * 100;
                slider.style.background = `linear-gradient(to right, var(--primary, #0066cc) 0%, var(--primary, #0066cc) ${percentage}%, #ddd ${percentage}%, #ddd 100%)`;
            };
            
            updateSliderStyle(value);
            
            // Highlight matching recipes dynamically
            this.highlightMatchingRecipes(value);
        });
    },
    
    // Highlight recipes based on missing ingredient count
    highlightMatchingRecipes: function(maxMissing) {
        // DOM manipulation - update recipe card styles
        document.querySelectorAll('.card').forEach(card => {
            const badge = card.querySelector('.badge');
            if (badge) {
                const missingText = badge.textContent;
                const missingCount = parseInt(missingText.match(/\d+/)?.[0] || '999');
                
                // Modify style based on match quality
                if (missingCount === 0) {
                    card.style.border = '2px solid var(--success, #28a745)';
                    card.style.backgroundColor = 'rgba(40, 167, 69, 0.05)';
                } else if (missingCount <= maxMissing) {
                    card.style.border = '2px solid var(--warning, #ffc107)';
                    card.style.backgroundColor = 'rgba(255, 193, 7, 0.05)';
                } else {
                    card.style.border = '';
                    card.style.backgroundColor = '';
                }
                
                // Add transition for smooth style changes
                card.style.transition = 'border 0.3s ease, background-color 0.3s ease';
            }
        });
    },
    
    // Interactive recipe card behaviors
    setupRecipeCardInteractions: function() {
        // Event listener for card clicks
        document.querySelectorAll('.card').forEach(card => {
            // Anonymous function for click handler
            (function(cardElement) {
                cardElement.addEventListener('click', function(event) {
                    // Don't trigger if clicking a link or button
                    if (event.target.tagName === 'A' || event.target.tagName === 'BUTTON') {
                        return;
                    }
                    
                    // Toggle expanded state with DOM manipulation
                    const isExpanded = cardElement.classList.contains('expanded');
                    
                    if (isExpanded) {
                        cardElement.classList.remove('expanded');
                        cardElement.style.maxHeight = '';
                    } else {
                        cardElement.classList.add('expanded');
                        cardElement.style.maxHeight = 'none';
                    }
                });
            })(card);
            
            // Hover effects with style modification
            card.addEventListener('mouseenter', (event) => {
                event.currentTarget.style.transform = 'scale(1.02)';
                event.currentTarget.style.transition = 'transform 0.2s ease';
            });
            
            card.addEventListener('mouseleave', (event) => {
                event.currentTarget.style.transform = '';
            });
        });
    },
    
    // Dynamic filter display
    setupFilterDisplay: function() {
        const form = document.querySelector('form[action*="action=match"]');
        if (!form) return;
        
        // Event listener for form submission
        form.addEventListener('submit', (event) => {
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Modify button style on submit
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Finding Matches...';
                submitButton.style.opacity = '0.7';
                submitButton.style.cursor = 'not-allowed';
                
                // Arrow function for reset
                const resetButton = () => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Find Matches';
                    submitButton.style.opacity = '';
                    submitButton.style.cursor = '';
                };
                
                // Reset after form submits (will be handled by page reload)
                setTimeout(resetButton, 2000);
            }
        });
        
        // Show current filter value dynamically
        const slider = document.querySelector('#max-missing');
        if (slider) {
            // Anonymous self-invoked function to initialize display
            (function() {
                const value = parseInt(slider.value);
                const output = document.querySelector('#max-missing-value');
                if (output) {
                    output.textContent = value;
                    output.style.fontWeight = 'bold';
                    output.style.color = 'var(--primary, #0066cc)';
                }
            })();
        }
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => MatchManager.init());
} else {
    MatchManager.init();
}


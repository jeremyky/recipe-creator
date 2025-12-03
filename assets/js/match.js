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
        
        // Store the original step value
        const originalStep = slider.getAttribute('step') || '1';
        
        // Initialize gradient
        const initValue = parseInt(slider.value);
        const initPercentage = (initValue / 5) * 100;
        slider.style.background = `linear-gradient(to right, var(--color-primary) 0%, var(--color-primary) ${initPercentage}%, var(--color-border-subtle) ${initPercentage}%, var(--color-border-subtle) 100%)`;
        
        // Remove step to allow smooth dragging
        let isDragging = false;
        
        // Mouse/touch down - start dragging
        slider.addEventListener('mousedown', () => {
            isDragging = true;
            slider.setAttribute('step', '0.01'); // Allow smooth sliding
        });
        
        slider.addEventListener('touchstart', () => {
            isDragging = true;
            slider.setAttribute('step', '0.01'); // Allow smooth sliding
        });
        
        // Event listener for slider input (smooth dragging)
        slider.addEventListener('input', (event) => {
            const value = parseFloat(event.target.value);
            const displayValue = Math.round(value);
            
            // DOM manipulation - update output display
            output.textContent = displayValue;
            
            // Modify style based on value using arrow function
            const updateSliderStyle = (val) => {
                const percentage = (val / 5) * 100;
                slider.style.background = `linear-gradient(to right, var(--color-primary) 0%, var(--color-primary) ${percentage}%, var(--color-border-subtle) ${percentage}%, var(--color-border-subtle) 100%)`;
            };
            
            updateSliderStyle(value);
            
            // Highlight matching recipes dynamically
            this.highlightMatchingRecipes(displayValue);
        });
        
        // Function to snap to nearest integer
        const snapToInteger = () => {
            if (isDragging) {
                isDragging = false;
                
                const value = parseFloat(slider.value);
                const snappedValue = Math.round(value);
                
                // Snap to nearest integer
                slider.value = snappedValue;
                slider.setAttribute('step', originalStep); // Restore original step
                this.maxMissing = snappedValue;
                
                // Update display
                output.textContent = snappedValue;
                
                // Update style for snapped value
                const percentage = (snappedValue / 5) * 100;
                slider.style.background = `linear-gradient(to right, var(--color-primary) 0%, var(--color-primary) ${percentage}%, var(--color-border-subtle) ${percentage}%, var(--color-border-subtle) 100%)`;
                
                // Final highlight update
                this.highlightMatchingRecipes(snappedValue);
            }
        };
        
        // Mouse/touch up - snap to integer
        slider.addEventListener('mouseup', snapToInteger);
        slider.addEventListener('touchend', snapToInteger);
        
        // Also handle when mouse leaves the slider while dragging
        slider.addEventListener('mouseleave', () => {
            if (isDragging) {
                snapToInteger();
            }
        });
        
        // Handle change event as fallback
        slider.addEventListener('change', snapToInteger);
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


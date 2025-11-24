/**
 * Cook page dynamic behaviors with interactive recipe steps
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage cooking session
const CookingSession = {
    steps: [],
    currentStep: 0,
    progress: 0,
    
    init: function() {
        this.setupChecklistInteractions();
        this.setupProgressTracking();
        this.setupStepAnimations();
    },
    
    // Interactive checklist with DOM manipulation
    setupChecklistInteractions: function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        this.steps = Array.from(checkboxes);
        
        // Event listener for each checkbox
        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', (event) => {
                const isChecked = event.target.checked;
                const label = event.target.nextElementSibling;
                
                // DOM manipulation - update label style
                if (isChecked) {
                    label.style.textDecoration = 'line-through';
                    label.style.color = 'var(--muted, #6c757d)';
                    label.style.opacity = '0.7';
                } else {
                    label.style.textDecoration = '';
                    label.style.color = '';
                    label.style.opacity = '';
                }
                
                // Update progress
                this.updateProgress();
            });
        });
    },
    
    // Progress tracking with visual feedback
    setupProgressTracking: function() {
        // Create progress bar element using DOM manipulation
        const form = document.querySelector('form');
        if (!form) return;
        
        // Anonymous function to create progress bar
        const createProgressBar = function() {
            const progressContainer = document.createElement('div');
            progressContainer.className = 'progress-container';
            progressContainer.style.marginTop = '1rem';
            progressContainer.style.marginBottom = '1rem';
            
            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            progressBar.style.width = '100%';
            progressBar.style.height = '20px';
            progressBar.style.backgroundColor = 'var(--light, #e9ecef)';
            progressBar.style.borderRadius = '10px';
            progressBar.style.overflow = 'hidden';
            
            const progressFill = document.createElement('div');
            progressFill.className = 'progress-fill';
            progressFill.style.width = '0%';
            progressFill.style.height = '100%';
            progressFill.style.backgroundColor = 'var(--success, #28a745)';
            progressFill.style.transition = 'width 0.3s ease';
            progressFill.setAttribute('role', 'progressbar');
            progressFill.setAttribute('aria-valuenow', '0');
            progressFill.setAttribute('aria-valuemin', '0');
            progressFill.setAttribute('aria-valuemax', '100');
            
            progressBar.appendChild(progressFill);
            progressContainer.appendChild(progressBar);
            
            const progressText = document.createElement('p');
            progressText.className = 'progress-text';
            progressText.textContent = 'Progress: 0%';
            progressText.style.marginTop = '0.5rem';
            progressText.style.fontSize = '0.9rem';
            progressText.style.color = 'var(--muted, #6c757d)';
            
            progressContainer.appendChild(progressText);
            return { container: progressContainer, fill: progressFill, text: progressText };
        };
        
        const progressElements = createProgressBar();
        form.insertBefore(progressElements.container, form.querySelector('button[type="submit"]'));
        
        // Store reference for updates
        this.progressElements = progressElements;
        this.updateProgress();
    },
    
    // Update progress calculation
    updateProgress: function() {
        const checkedCount = this.steps.filter(step => step.checked).length;
        const totalSteps = this.steps.length;
        this.progress = totalSteps > 0 ? (checkedCount / totalSteps) * 100 : 0;
        
        // DOM manipulation - update progress bar
        if (this.progressElements) {
            this.progressElements.fill.style.width = `${this.progress}%`;
            this.progressElements.fill.setAttribute('aria-valuenow', Math.round(this.progress));
            this.progressElements.text.textContent = `Progress: ${Math.round(this.progress)}%`;
            
            // Modify style based on progress
            if (this.progress === 100) {
                this.progressElements.fill.style.backgroundColor = 'var(--success, #28a745)';
                this.progressElements.text.style.color = 'var(--success, #28a745)';
                this.progressElements.text.style.fontWeight = 'bold';
            } else if (this.progress >= 50) {
                this.progressElements.fill.style.backgroundColor = 'var(--warning, #ffc107)';
            } else {
                this.progressElements.fill.style.backgroundColor = 'var(--primary, #0066cc)';
            }
        }
    },
    
    // Step animations with style modifications
    setupStepAnimations: function() {
        // Arrow function for step highlighting
        const highlightStep = (stepElement, isActive) => {
            if (isActive) {
                stepElement.style.backgroundColor = 'var(--light, #f8f9fa)';
                stepElement.style.padding = '0.75rem';
                stepElement.style.borderRadius = '6px';
                stepElement.style.borderLeft = '4px solid var(--primary, #0066cc)';
                stepElement.style.transition = 'all 0.3s ease';
            } else {
                stepElement.style.backgroundColor = '';
                stepElement.style.padding = '';
                stepElement.style.borderRadius = '';
                stepElement.style.borderLeft = '';
            }
        };
        
        // Event listeners for focus on checkboxes
        this.steps.forEach((checkbox, index) => {
            checkbox.addEventListener('focus', () => {
                const checkboxItem = checkbox.closest('.checkbox-item');
                if (checkboxItem) {
                    highlightStep(checkboxItem, true);
                }
            });
            
            checkbox.addEventListener('blur', () => {
                const checkboxItem = checkbox.closest('.checkbox-item');
                if (checkboxItem && !checkbox.checked) {
                    highlightStep(checkboxItem, false);
                }
            });
        });
        
        // Form submission handler
        const form = document.querySelector('form');
        if (form) {
            // Anonymous function for form submission
            form.addEventListener('submit', function(event) {
                const checkedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
                const totalCount = document.querySelectorAll('input[type="checkbox"]').length;
                
                if (checkedCount < totalCount) {
                    // Optional: warn if not all steps completed
                    // For now, allow submission
                }
                
                // Modify submit button style
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.textContent = 'Starting...';
                    submitButton.disabled = true;
                    submitButton.style.opacity = '0.7';
                }
            });
        }
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => CookingSession.init());
} else {
    CookingSession.init();
}


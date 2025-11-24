/**
 * Upload page dynamic behaviors with client-side validation
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage form validation state
const UploadFormValidator = {
    errors: {},
    formMode: 'manual',
    
    init: function() {
        this.setupFormValidation();
        this.setupModeSwitching();
        this.setupRealTimeValidation();
    },
    
    // Client-side input validation with error messages
    setupFormValidation: function() {
        const manualForm = document.querySelector('form[action*="mode=manual"]');
        const urlForm = document.querySelector('form[action*="mode=url"]');
        
        if (manualForm) {
            // Event listener for form submission validation
            manualForm.addEventListener('submit', (event) => {
                this.errors = {};
                this.validateManualForm(manualForm);
                
                if (Object.keys(this.errors).length > 0) {
                    event.preventDefault();
                    this.displayErrors();
                }
            });
        }
        
        if (urlForm) {
            // Event listener for URL form validation
            urlForm.addEventListener('submit', (event) => {
                this.errors = {};
                this.validateUrlForm(urlForm);
                
                if (Object.keys(this.errors).length > 0) {
                    event.preventDefault();
                    this.displayErrors();
                }
            });
        }
    },
    
    // Validate manual recipe form
    validateManualForm: function(form) {
        const title = form.querySelector('#recipe-title');
        const ingredients = form.querySelector('#recipe-ingredients');
        const steps = form.querySelector('#recipe-steps');
        
        // Validate title
        if (!title || !title.value.trim()) {
            this.errors.title = 'Recipe title is required';
        } else if (title.value.trim().length < 3) {
            this.errors.title = 'Recipe title must be at least 3 characters';
        }
        
        // Validate ingredients
        if (!ingredients || !ingredients.value.trim()) {
            this.errors.ingredients = 'At least one ingredient is required';
        } else {
            const ingredientLines = ingredients.value.trim().split('\n').filter(line => line.trim());
            if (ingredientLines.length === 0) {
                this.errors.ingredients = 'At least one ingredient is required';
            }
        }
        
        // Validate steps
        if (!steps || !steps.value.trim()) {
            this.errors.steps = 'Recipe steps are required';
        } else if (steps.value.trim().length < 10) {
            this.errors.steps = 'Recipe steps must be at least 10 characters';
        }
    },
    
    // Validate URL form
    validateUrlForm: function(form) {
        const urlInput = form.querySelector('#recipe-url');
        
        if (!urlInput || !urlInput.value.trim()) {
            this.errors.url = 'Recipe URL is required';
        } else {
            // Use anonymous function for URL validation
            const isValidUrl = (function(urlString) {
                try {
                    const url = new URL(urlString);
                    return url.protocol === 'http:' || url.protocol === 'https:';
                } catch (e) {
                    return false;
                }
            })(urlInput.value.trim());
            
            if (!isValidUrl) {
                this.errors.url = 'Please enter a valid URL (must start with http:// or https://)';
            }
        }
    },
    
    // Display validation errors with DOM manipulation
    displayErrors: function() {
        // Remove existing error messages
        const existingErrors = document.querySelectorAll('.client-error');
        existingErrors.forEach(error => error.remove());
        
        // DOM manipulation - create and insert error messages
        Object.keys(this.errors).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                // Add error class to field
                field.classList.add('error');
                field.style.borderColor = 'var(--danger, #dc3545)';
                
                // Create error message element
                const errorDiv = document.createElement('div');
                errorDiv.className = 'client-error';
                errorDiv.textContent = this.errors[fieldName];
                errorDiv.style.color = 'var(--danger, #dc3545)';
                errorDiv.style.fontSize = '0.875rem';
                errorDiv.style.marginTop = '0.25rem';
                errorDiv.setAttribute('role', 'alert');
                
                // Insert error message after the field
                field.parentElement.appendChild(errorDiv);
            }
        });
        
        // Scroll to first error
        const firstError = document.querySelector('.client-error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },
    
    // Real-time validation feedback
    setupRealTimeValidation: function() {
        // Arrow function for real-time validation
        const validateField = (field, validator) => {
            field.addEventListener('blur', () => {
                const error = validator(field);
                const existingError = field.parentElement.querySelector('.client-error');
                
                if (error) {
                    field.classList.add('error');
                    field.style.borderColor = 'var(--danger, #dc3545)';
                    
                    if (!existingError) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'client-error';
                        errorDiv.textContent = error;
                        errorDiv.style.color = 'var(--danger, #dc3545)';
                        errorDiv.style.fontSize = '0.875rem';
                        errorDiv.style.marginTop = '0.25rem';
                        field.parentElement.appendChild(errorDiv);
                    } else {
                        existingError.textContent = error;
                    }
                } else {
                    field.classList.remove('error');
                    field.style.borderColor = '';
                    if (existingError) {
                        existingError.remove();
                    }
                }
            });
        };
        
        const titleField = document.querySelector('#recipe-title');
        if (titleField) {
            validateField(titleField, (field) => {
                if (!field.value.trim()) return 'Recipe title is required';
                if (field.value.trim().length < 3) return 'Title must be at least 3 characters';
                return null;
            });
        }
    },
    
    // Dynamic form mode switching with DOM manipulation
    setupModeSwitching: function() {
        const details = document.querySelectorAll('details');
        details.forEach(detail => {
            detail.addEventListener('toggle', (event) => {
                // Modify style when details open/close
                if (detail.open) {
                    detail.style.backgroundColor = 'var(--light, #f8f9fa)';
                    detail.style.padding = '1rem';
                    detail.style.borderRadius = '8px';
                    detail.style.transition = 'background-color 0.3s ease';
                } else {
                    detail.style.backgroundColor = '';
                    detail.style.padding = '';
                }
            });
        });
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => UploadFormValidator.init());
} else {
    UploadFormValidator.init();
}


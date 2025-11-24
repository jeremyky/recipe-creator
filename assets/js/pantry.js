/**
 * Pantry page dynamic behaviors with validation and item management
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage pantry items
const PantryManager = {
    items: [],
    
    init: function() {
        this.setupFormValidation();
        this.setupItemInteractions();
        this.setupQuantityCalculations();
    },
    
    // Client-side input validation
    setupFormValidation: function() {
        const form = document.querySelector('form[action*="pantry_add"]');
        if (!form) return;
        
        // Event listener for form submission
        form.addEventListener('submit', (event) => {
            const errors = this.validateForm(form);
            
            if (errors.length > 0) {
                event.preventDefault();
                this.displayValidationErrors(errors);
            } else {
                // Clear any existing errors
                this.clearErrors();
            }
        });
        
        // Real-time validation using arrow function
        const validateField = (field, validator) => {
            field.addEventListener('blur', () => {
                const error = validator(field);
                this.showFieldError(field, error);
            });
            
            field.addEventListener('input', () => {
                // Clear error on input
                this.showFieldError(field, null);
            });
        };
        
        const nameField = document.querySelector('#ingredient-name');
        const quantityField = document.querySelector('#ingredient-quantity');
        const unitField = document.querySelector('#ingredient-unit');
        
        if (nameField) {
            validateField(nameField, (field) => {
                if (!field.value.trim()) return 'Ingredient name is required';
                if (field.value.trim().length < 2) return 'Name must be at least 2 characters';
                return null;
            });
        }
        
        if (quantityField) {
            validateField(quantityField, (field) => {
                const value = parseFloat(field.value);
                if (isNaN(value) || value < 0) return 'Quantity must be a positive number';
                return null;
            });
        }
    },
    
    // Validate form data
    validateForm: function(form) {
        const errors = [];
        const name = form.querySelector('#ingredient-name');
        const quantity = form.querySelector('#ingredient-quantity');
        const unit = form.querySelector('#ingredient-unit');
        
        if (!name || !name.value.trim()) {
            errors.push({ field: 'name', message: 'Ingredient name is required' });
        }
        
        if (!quantity || isNaN(parseFloat(quantity.value)) || parseFloat(quantity.value) < 0) {
            errors.push({ field: 'quantity', message: 'Valid quantity is required' });
        }
        
        if (!unit || !unit.value) {
            errors.push({ field: 'unit', message: 'Unit is required' });
        }
        
        return errors;
    },
    
    // Display validation errors with DOM manipulation
    displayValidationErrors: function(errors) {
        this.clearErrors();
        
        // Anonymous function to create error elements
        const createErrorElement = function(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'client-error';
            errorDiv.textContent = message;
            errorDiv.style.color = 'var(--danger, #dc3545)';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.setAttribute('role', 'alert');
            return errorDiv;
        };
        
        errors.forEach(error => {
            const field = document.querySelector(`[name="${error.field}"]`);
            if (field) {
                field.classList.add('error');
                field.style.borderColor = 'var(--danger, #dc3545)';
                field.parentElement.appendChild(createErrorElement(error.message));
            }
        });
    },
    
    // Show/hide field error
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
                errorDiv.style.marginTop = '0.25rem';
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
    
    // Clear all errors
    clearErrors: function() {
        document.querySelectorAll('.client-error').forEach(error => error.remove());
        document.querySelectorAll('.error').forEach(field => {
            field.classList.remove('error');
            field.style.borderColor = '';
        });
    },
    
    // Setup interactive item management
    setupItemInteractions: function() {
        // Event listener for delete buttons with confirmation
        document.querySelectorAll('form[action*="pantry_delete"]').forEach(form => {
            form.addEventListener('submit', (event) => {
                if (!confirm('Are you sure you want to remove this ingredient from your pantry?')) {
                    event.preventDefault();
                }
            });
        });
        
        // DOM manipulation - add hover effects to pantry items
        document.querySelectorAll('.pantry-item, article').forEach(item => {
            item.addEventListener('mouseenter', (event) => {
                event.currentTarget.style.backgroundColor = 'var(--light, #f8f9fa)';
                event.currentTarget.style.transition = 'background-color 0.2s ease';
            });
            
            item.addEventListener('mouseleave', (event) => {
                event.currentTarget.style.backgroundColor = '';
            });
        });
    },
    
    // Dynamic quantity calculations
    setupQuantityCalculations: function() {
        // Arrow function for unit conversion helper
        const convertUnit = (value, fromUnit, toUnit) => {
            // Simple conversion logic (simplified for demo)
            const conversions = {
                'lb': { 'oz': 16, 'g': 453.592 },
                'oz': { 'lb': 0.0625, 'g': 28.3495 },
                'g': { 'oz': 0.035274, 'kg': 0.001 },
                'kg': { 'g': 1000, 'lb': 2.20462 }
            };
            
            if (conversions[fromUnit] && conversions[fromUnit][toUnit]) {
                return value * conversions[fromUnit][toUnit];
            }
            return value;
        };
        
        // Add conversion display on quantity input
        const quantityField = document.querySelector('#ingredient-quantity');
        const unitField = document.querySelector('#ingredient-unit');
        
        if (quantityField && unitField) {
            // Anonymous function for conversion display
            (function() {
                const displayConversion = () => {
                    const value = parseFloat(quantityField.value);
                    const unit = unitField.value;
                    
                    if (!isNaN(value) && value > 0 && unit) {
                        // Could add conversion display here
                        // This is a placeholder for future enhancement
                    }
                };
                
                quantityField.addEventListener('input', displayConversion);
                unitField.addEventListener('change', displayConversion);
            })();
        }
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => PantryManager.init());
} else {
    PantryManager.init();
}


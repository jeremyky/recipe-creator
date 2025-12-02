/**
 * Authentication pages dynamic behaviors
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// Auth form validator object
const AuthValidator = {
    errors: {},
    
    init: function() {
        this.setupLoginValidation();
        this.setupSignupValidation();
        this.setupPasswordStrength();
        this.setupGoogleAuth();
    },
    
    // Setup login form validation
    setupLoginValidation: function() {
        const loginForm = document.querySelector('form[action*="login_submit"]');
        if (!loginForm) return;
        
        // Event listener for form submission
        loginForm.addEventListener('submit', (event) => {
            this.errors = {};
            
            const email = loginForm.querySelector('#email');
            const password = loginForm.querySelector('#password');
            
            // Validate email
            if (!this.validateEmail(email.value)) {
                this.errors.email = 'Please enter a valid email address';
            }
            
            // Validate password
            if (!password.value || password.value.length < 6) {
                this.errors.password = 'Password must be at least 6 characters';
            }
            
            if (Object.keys(this.errors).length > 0) {
                event.preventDefault();
                this.displayErrors(loginForm);
            }
        });
        
        // Real-time validation using arrow function
        const validateField = (field, validator) => {
            field.addEventListener('blur', () => {
                const error = validator(field.value);
                this.showFieldError(field, error);
            });
            
            field.addEventListener('input', () => {
                this.showFieldError(field, null);
            });
        };
        
        const emailField = loginForm.querySelector('#email');
        const passwordField = loginForm.querySelector('#password');
        
        if (emailField) {
            validateField(emailField, (value) => {
                if (!this.validateEmail(value)) return 'Invalid email address';
                return null;
            });
        }
        
        if (passwordField) {
            validateField(passwordField, (value) => {
                if (value.length < 6) return 'Password too short';
                return null;
            });
        }
    },
    
    // Setup signup form validation
    setupSignupValidation: function() {
        const signupForm = document.querySelector('form[action*="signup_submit"]');
        if (!signupForm) return;
        
        // Event listener for form submission
        signupForm.addEventListener('submit', (event) => {
            this.errors = {};
            
            const name = signupForm.querySelector('#name');
            const email = signupForm.querySelector('#email');
            const password = signupForm.querySelector('#password');
            const passwordConfirm = signupForm.querySelector('#password_confirm');
            const terms = signupForm.querySelector('input[name="terms"]');
            
            // Validate name
            if (!name.value || name.value.trim().length < 2) {
                this.errors.name = 'Name must be at least 2 characters';
            }
            
            // Validate email
            if (!this.validateEmail(email.value)) {
                this.errors.email = 'Please enter a valid email address';
            }
            
            // Validate password
            if (!password.value || password.value.length < 8) {
                this.errors.password = 'Password must be at least 8 characters';
            }
            
            // Validate password confirmation
            if (password.value !== passwordConfirm.value) {
                this.errors.password_confirm = 'Passwords do not match';
            }
            
            // Validate terms
            if (!terms.checked) {
                this.errors.terms = 'You must agree to the terms and privacy policy';
            }
            
            if (Object.keys(this.errors).length > 0) {
                event.preventDefault();
                this.displayErrors(signupForm);
            }
        });
        
        // Real-time password confirmation check
        const passwordConfirm = signupForm.querySelector('#password_confirm');
        const password = signupForm.querySelector('#password');
        
        if (passwordConfirm && password) {
            passwordConfirm.addEventListener('input', () => {
                if (passwordConfirm.value && password.value !== passwordConfirm.value) {
                    this.showFieldError(passwordConfirm, 'Passwords do not match');
                } else {
                    this.showFieldError(passwordConfirm, null);
                }
            });
        }
    },
    
    // Email validation using arrow function
    validateEmail: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    },
    
    // Display validation errors with DOM manipulation
    displayErrors: function(form) {
        // Remove existing errors
        form.querySelectorAll('.field-error').forEach(error => error.remove());
        
        // Anonymous function to create error element
        const createError = function(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            errorDiv.style.color = '#ef4444';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            return errorDiv;
        };
        
        // Display each error
        Object.keys(this.errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('error');
                field.parentElement.appendChild(createError(this.errors[fieldName]));
            }
        });
    },
    
    // Show/hide field error
    showFieldError: function(field, errorMessage) {
        const existingError = field.parentElement.querySelector('.field-error');
        
        if (errorMessage) {
            field.classList.add('error');
            
            if (!existingError) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error';
                errorDiv.textContent = errorMessage;
                errorDiv.style.color = '#ef4444';
                errorDiv.style.fontSize = '0.875rem';
                errorDiv.style.marginTop = '0.25rem';
                field.parentElement.appendChild(errorDiv);
            } else {
                existingError.textContent = errorMessage;
            }
        } else {
            field.classList.remove('error');
            if (existingError) {
                existingError.remove();
            }
        }
    },
    
    // Password strength indicator
    setupPasswordStrength: function() {
        const passwordField = document.querySelector('#password');
        const strengthIndicator = document.querySelector('#password-strength');
        
        if (!passwordField || !strengthIndicator) return;
        
        // Event listener for password input
        passwordField.addEventListener('input', (event) => {
            const password = event.target.value;
            const strength = this.calculatePasswordStrength(password);
            
            // DOM manipulation - update strength indicator
            strengthIndicator.className = 'password-strength';
            
            if (password.length === 0) {
                strengthIndicator.className = 'password-strength';
            } else if (strength < 40) {
                strengthIndicator.className = 'password-strength weak';
            } else if (strength < 70) {
                strengthIndicator.className = 'password-strength medium';
            } else {
                strengthIndicator.className = 'password-strength strong';
            }
        });
    },
    
    // Calculate password strength
    calculatePasswordStrength: function(password) {
        let strength = 0;
        
        // Length
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 25;
        
        // Contains lowercase
        if (/[a-z]/.test(password)) strength += 15;
        
        // Contains uppercase
        if (/[A-Z]/.test(password)) strength += 15;
        
        // Contains numbers
        if (/\d/.test(password)) strength += 10;
        
        // Contains special characters
        if (/[^A-Za-z0-9]/.test(password)) strength += 10;
        
        return strength;
    },
    
    // Setup Google OAuth
    setupGoogleAuth: function() {
        const googleSigninBtn = document.querySelector('#google-signin');
        const googleSignupBtn = document.querySelector('#google-signup');
        
        // Event listener for Google sign in
        if (googleSigninBtn) {
            googleSigninBtn.addEventListener('click', () => {
                this.initiateGoogleAuth('signin');
            });
        }
        
        // Event listener for Google sign up
        if (googleSignupBtn) {
            googleSignupBtn.addEventListener('click', () => {
                this.initiateGoogleAuth('signup');
            });
        }
    },
    
    // Initiate Google OAuth flow
    initiateGoogleAuth: function(type) {
        // Placeholder for Google OAuth integration
        // In production, this would use Google's OAuth 2.0 library
        alert(`Google ${type} will be implemented with OAuth 2.0 integration. For now, please use email ${type}.`);
        
        // Example of what would be implemented:
        /*
        google.accounts.oauth2.initTokenClient({
            client_id: 'YOUR_GOOGLE_CLIENT_ID',
            scope: 'email profile',
            callback: (response) => {
                // Handle OAuth response
                this.handleGoogleCallback(response, type);
            }
        }).requestAccessToken();
        */
    },
    
    // Handle Google OAuth callback (placeholder)
    handleGoogleCallback: function(response, type) {
        // This would send the OAuth token to the server
        fetch(`index.php?action=google_${type}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                token: response.access_token
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php?action=home';
            }
        });
    }
};

// Form enhancement object
const FormEnhancer = {
    init: function() {
        this.setupInputAnimations();
        this.setupButtonAnimations();
    },
    
    // Add animations to input fields
    setupInputAnimations: function() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        
        inputs.forEach(input => {
            // Event listener for focus
            input.addEventListener('focus', (event) => {
                const label = event.target.parentElement.querySelector('label');
                if (label) {
                    label.style.color = '#6366f1';
                    label.style.transition = 'color 0.2s ease';
                }
            });
            
            // Event listener for blur
            input.addEventListener('blur', (event) => {
                const label = event.target.parentElement.querySelector('label');
                if (label && !event.target.classList.contains('error')) {
                    label.style.color = '';
                }
            });
        });
    },
    
    // Add ripple effect to buttons
    setupButtonAnimations: function() {
        const buttons = document.querySelectorAll('.btn-primary, .btn-provider');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(event) {
                // Create ripple using anonymous function
                (function createRipple() {
                    const ripple = document.createElement('span');
                    const rect = button.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = event.clientX - rect.left - size / 2;
                    const y = event.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255, 255, 255, 0.5)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s ease-out';
                    ripple.style.pointerEvents = 'none';
                    
                    button.style.position = 'relative';
                    button.style.overflow = 'hidden';
                    button.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                })();
            });
        });
    }
};

// Add CSS animation for ripple
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        AuthValidator.init();
        FormEnhancer.init();
    });
} else {
    AuthValidator.init();
    FormEnhancer.init();
}


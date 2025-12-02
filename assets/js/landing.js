/**
 * Landing page dynamic behaviors
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// Theme management object
const ThemeManager = {
    currentTheme: 'light',
    
    init: function() {
        this.loadSavedTheme();
        this.setupThemeToggle();
        this.setupSmoothScroll();
        this.setupAnimations();
    },
    
    // Load saved theme from localStorage
    loadSavedTheme: function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.currentTheme = savedTheme;
        document.body.className = savedTheme === 'dark' ? 'dark-mode' : 'light-mode';
        this.updateThemeIcon();
    },
    
    // Setup theme toggle button
    setupThemeToggle: function() {
        const toggleButton = document.getElementById('theme-toggle');
        if (!toggleButton) return;
        
        // Event listener for theme toggle
        toggleButton.addEventListener('click', () => {
            this.toggleTheme();
        });
    },
    
    // Toggle between light and dark theme
    toggleTheme: function() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        
        // DOM manipulation - update body class
        document.body.className = this.currentTheme === 'dark' ? 'dark-mode' : 'light-mode';
        
        // Save to localStorage
        localStorage.setItem('theme', this.currentTheme);
        
        // Update icon
        this.updateThemeIcon();
        
        // Add animation effect
        this.animateThemeChange();
    },
    
    // Update theme toggle icon
    updateThemeIcon: function() {
        const themeIcon = document.querySelector('.theme-icon');
        if (themeIcon) {
            themeIcon.textContent = this.currentTheme === 'dark' ? '☀️' : '🌙';
        }
    },
    
    // Animate theme change with DOM manipulation
    animateThemeChange: function() {
        const body = document.body;
        body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        // Create ripple effect
        const ripple = document.createElement('div');
        ripple.style.position = 'fixed';
        ripple.style.top = '20px';
        ripple.style.right = '100px';
        ripple.style.width = '30px';
        ripple.style.height = '30px';
        ripple.style.borderRadius = '50%';
        ripple.style.background = this.currentTheme === 'dark' ? '#6366f1' : '#fbbf24';
        ripple.style.transform = 'scale(0)';
        ripple.style.transition = 'transform 0.6s ease';
        ripple.style.pointerEvents = 'none';
        ripple.style.zIndex = '9999';
        
        document.body.appendChild(ripple);
        
        // Trigger animation using arrow function
        setTimeout(() => {
            ripple.style.transform = 'scale(100)';
            ripple.style.opacity = '0';
        }, 10);
        
        // Remove ripple after animation
        setTimeout(() => {
            ripple.remove();
        }, 600);
    },
    
    // Setup smooth scrolling for anchor links
    setupSmoothScroll: function() {
        // Event listener for all anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(event) {
                const href = this.getAttribute('href');
                
                // Skip if href is just "#"
                if (href === '#') return;
                
                event.preventDefault();
                
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // Smooth scroll with DOM manipulation
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update URL without page jump
                    history.pushState(null, null, href);
                }
            });
        });
    },
    
    // Setup scroll animations
    setupAnimations: function() {
        // Intersection Observer for fade-in animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });
        
        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });
    }
};

// Navigation scroll behavior
const NavigationManager = {
    init: function() {
        this.setupStickyNav();
        this.setupMobileMenu();
    },
    
    // Add shadow to nav on scroll
    setupStickyNav: function() {
        const nav = document.querySelector('.landing-nav');
        if (!nav) return;
        
        // Event listener for scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            } else {
                nav.style.boxShadow = 'none';
            }
        });
    },
    
    // Mobile menu functionality (future enhancement)
    setupMobileMenu: function() {
        // Placeholder for mobile menu toggle
        // This would be implemented when adding a hamburger menu
    }
};

// Feature cards interaction
const FeatureInteraction = {
    init: function() {
        this.setupCardHovers();
    },
    
    // Enhanced hover effects for feature cards
    setupCardHovers: function() {
        const cards = document.querySelectorAll('.feature-card, .faq-item, .blog-card');
        
        cards.forEach(card => {
            // Event listener for mouseenter
            card.addEventListener('mouseenter', (event) => {
                const icon = event.currentTarget.querySelector('.feature-icon, .blog-image-placeholder');
                if (icon) {
                    // Animate icon using arrow function
                    const animateIcon = () => {
                        icon.style.transform = 'scale(1.1) rotate(5deg)';
                        icon.style.transition = 'transform 0.3s ease';
                    };
                    animateIcon();
                }
            });
            
            // Event listener for mouseleave
            card.addEventListener('mouseleave', (event) => {
                const icon = event.currentTarget.querySelector('.feature-icon, .blog-image-placeholder');
                if (icon) {
                    icon.style.transform = '';
                }
            });
        });
    }
};

// CTA button interactions
const CTAManager = {
    init: function() {
        this.setupButtonEffects();
    },
    
    // Add ripple effect to CTA buttons
    setupButtonEffects: function() {
        const buttons = document.querySelectorAll('.btn-primary');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(event) {
                // Create ripple effect using anonymous function
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

// Analytics tracking (placeholder)
const Analytics = {
    init: function() {
        this.trackPageView();
        this.trackCTAClicks();
    },
    
    trackPageView: function() {
        // Placeholder for analytics integration
        console.log('Landing page viewed');
    },
    
    trackCTAClicks: function() {
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', () => {
                console.log('CTA clicked:', button.textContent);
            });
        });
    }
};

// Add CSS animation for ripple effect
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

// Initialize all modules when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        ThemeManager.init();
        NavigationManager.init();
        FeatureInteraction.init();
        CTAManager.init();
        Analytics.init();
    });
} else {
    ThemeManager.init();
    NavigationManager.init();
    FeatureInteraction.init();
    CTAManager.init();
    Analytics.init();
}


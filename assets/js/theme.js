/**
 * Theme toggle functionality for Recipe Creator app
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// Theme Manager object
const AppThemeManager = {
    currentTheme: 'dark',
    
    init: function() {
        this.loadSavedTheme();
        this.setupThemeToggle();
        
        // Enable smooth transitions after initial load
        setTimeout(() => {
            document.body.classList.add('theme-transitions-enabled');
        }, 100);
    },
    
    // Load saved theme from localStorage
    loadSavedTheme: function() {
        const savedTheme = localStorage.getItem('app-theme') || 'dark';
        this.currentTheme = savedTheme;
        this.applyTheme(savedTheme);
    },
    
    // Apply theme to body
    applyTheme: function(theme) {
        const themeClass = theme === 'light' ? 'light-mode' : 'dark-mode';
        document.documentElement.className = themeClass;
        document.body.className = themeClass;
        this.updateThemeIcon();
    },
    
    // Setup theme toggle button
    setupThemeToggle: function() {
        const toggleButton = document.getElementById('app-theme-toggle');
        if (!toggleButton) return;
        
        // Event listener for theme toggle
        toggleButton.addEventListener('click', () => {
            this.toggleTheme();
        });
    },
    
    // Toggle between light and dark theme
    toggleTheme: function() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        
        // Apply theme
        this.applyTheme(this.currentTheme);
        
        // Save to localStorage
        localStorage.setItem('app-theme', this.currentTheme);
        
        // Add animation effect
        this.animateThemeChange();
    },
    
    // Update theme toggle icon
    updateThemeIcon: function() {
        const themeIcon = document.querySelector('#app-theme-toggle .theme-icon');
        if (themeIcon) {
            themeIcon.textContent = this.currentTheme === 'dark' ? '🌙' : '☀️';
        }
    },
    
    // Animate theme change with DOM manipulation
    animateThemeChange: function() {
        // Create subtle ripple effect
        const toggleButton = document.getElementById('app-theme-toggle');
        if (!toggleButton) return;
        
        const rect = toggleButton.getBoundingClientRect();
        const ripple = document.createElement('div');
        ripple.style.position = 'fixed';
        ripple.style.top = rect.top + rect.height / 2 + 'px';
        ripple.style.left = rect.left + rect.width / 2 + 'px';
        ripple.style.width = '20px';
        ripple.style.height = '20px';
        ripple.style.borderRadius = '50%';
        ripple.style.background = this.currentTheme === 'light' ? '#fbbf24' : '#4f46e5';
        ripple.style.transform = 'translate(-50%, -50%) scale(0)';
        ripple.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
        ripple.style.pointerEvents = 'none';
        ripple.style.zIndex = '9999';
        ripple.style.opacity = '0.3';
        
        document.body.appendChild(ripple);
        
        // Trigger animation using arrow function
        setTimeout(() => {
            ripple.style.transform = 'translate(-50%, -50%) scale(100)';
            ripple.style.opacity = '0';
        }, 10);
        
        // Remove ripple after animation
        setTimeout(() => {
            ripple.remove();
        }, 500);
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => AppThemeManager.init());
} else {
    AppThemeManager.init();
}


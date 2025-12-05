/**
 * Mobile Menu Functionality
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

(function() {
    'use strict';
    
    const MobileMenu = {
        init: function() {
            this.toggleButton = document.getElementById('mobile-menu-toggle');
            this.nav = document.getElementById('main-nav');
            this.overlay = null;
            
            if (!this.toggleButton || !this.nav) {
                return; // Not on a page with mobile menu
            }
            
            this.createOverlay();
            this.setupEvents();
        },
        
        createOverlay: function() {
            this.overlay = document.createElement('div');
            this.overlay.className = 'mobile-menu-overlay';
            this.overlay.setAttribute('aria-hidden', 'true');
            document.body.appendChild(this.overlay);
        },
        
        setupEvents: function() {
            // Toggle button click
            this.toggleButton.addEventListener('click', () => {
                this.toggle();
            });
            
            // Overlay click to close
            this.overlay.addEventListener('click', () => {
                this.close();
            });
            
            // Close on navigation link click
            const navLinks = this.nav.querySelectorAll('.mobile-nav-links a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    this.close();
                });
            });
            
            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
        },
        
        toggle: function() {
            if (this.isOpen()) {
                this.close();
            } else {
                this.open();
            }
        },
        
        open: function() {
            this.toggleButton.setAttribute('aria-expanded', 'true');
            this.nav.classList.add('mobile-menu-open');
            this.overlay.classList.add('active');
            this.overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            // Focus trap: focus first link in scrollable container
            const navLinks = this.nav.querySelector('.mobile-nav-links');
            if (navLinks) {
                const firstLink = navLinks.querySelector('a');
                if (firstLink) {
                    firstLink.focus();
                }
            }
        },
        
        close: function() {
            this.toggleButton.setAttribute('aria-expanded', 'false');
            this.nav.classList.remove('mobile-menu-open');
            this.overlay.classList.remove('active');
            this.overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            
            // Return focus to toggle button
            this.toggleButton.focus();
        },
        
        isOpen: function() {
            return this.nav.classList.contains('mobile-menu-open');
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            MobileMenu.init();
        });
    } else {
        MobileMenu.init();
    }
})();


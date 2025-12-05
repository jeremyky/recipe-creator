/**
 * Modern Filter System - Interactive Components
 * Inspired by Airbnb, Notion, Linear
 */

const FilterSystem = {
    activeDropdown: null,
    
    init: function() {
        this.setupStickyBehavior();
        this.setupDropdowns();
        this.setupMoreFilters();
        this.setupKeyboardNavigation();
        this.setupFilterChips();
        this.initializeFromURL();
    },
    
    // Sticky filter bar on scroll
    setupStickyBehavior: function() {
        const filterBar = document.querySelector('.filter-bar-container');
        if (!filterBar) return;
        
        let lastScroll = 0;
        const threshold = 10;
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > threshold) {
                filterBar.classList.add('scrolled');
            } else {
                filterBar.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        });
    },
    
    // Dropdown interactions
    setupDropdowns: function() {
        const pills = document.querySelectorAll('.filter-pill[data-dropdown]');
        const backdrop = this.createBackdrop();
        
        console.log('🔧 Setting up dropdowns, found', pills.length, 'pills');
        
        pills.forEach(pill => {
            const dropdownId = pill.getAttribute('data-dropdown');
            const dropdown = document.getElementById(dropdownId);
            
            if (!dropdown) {
                console.warn('⚠️ Dropdown not found for', dropdownId);
                return;
            }
            
            console.log('✅ Setting up dropdown:', dropdownId);
            
            // Click to toggle
            pill.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('🖱️ Pill clicked:', dropdownId);
                this.toggleDropdown(pill, dropdown, backdrop);
            });
            
            // Keyboard support
            pill.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleDropdown(pill, dropdown, backdrop);
                }
            });
            
            // Dropdown option clicks
            const options = dropdown.querySelectorAll('.filter-dropdown-option');
            console.log('  Options found:', options.length);
            
            options.forEach(option => {
                option.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('🖱️ Option clicked:', option.getAttribute('data-value'));
                    this.selectOption(option, dropdown);
                    this.closeDropdown(pill, dropdown, backdrop);
                });
                
                // Visual feedback for hover
                option.addEventListener('mouseenter', () => {
                    console.log('👆 Hovering option:', option.textContent.trim());
                });
                
                // Keyboard support for options
                option.setAttribute('tabindex', '0');
                option.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        option.click();
                    }
                });
            });
        });
        
        // Click backdrop to close
        backdrop.addEventListener('click', () => {
            console.log('🖱️ Backdrop clicked');
            this.closeAllDropdowns(backdrop);
        });
    },
    
    createBackdrop: function() {
        let backdrop = document.querySelector('.dropdown-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'dropdown-backdrop';
            document.body.appendChild(backdrop);
        }
        return backdrop;
    },
    
    toggleDropdown: function(pill, dropdown, backdrop) {
        const isOpen = dropdown.classList.contains('open');
        console.log('🔄 toggleDropdown - currently open:', isOpen);
        
        if (isOpen) {
            console.log('  Closing dropdown');
            this.closeDropdown(pill, dropdown, backdrop);
        } else {
            console.log('  Opening dropdown');
            this.closeAllDropdowns(backdrop);
            this.openDropdown(pill, dropdown, backdrop);
        }
    },
    
    openDropdown: function(pill, dropdown, backdrop) {
        console.log('📂 openDropdown called');
        pill.classList.add('open');
        dropdown.classList.add('open');
        backdrop.classList.add('active');
        this.activeDropdown = { pill, dropdown };
        
        console.log('  Dropdown classes:', dropdown.className);
        console.log('  Dropdown display:', window.getComputedStyle(dropdown).display);
        console.log('  Dropdown opacity:', window.getComputedStyle(dropdown).opacity);
        console.log('  Dropdown pointer-events:', window.getComputedStyle(dropdown).pointerEvents);
        
        // Position dropdown
        const pillRect = pill.getBoundingClientRect();
        dropdown.style.left = '0';
        
        // Focus first option
        const firstOption = dropdown.querySelector('.filter-dropdown-option');
        if (firstOption) {
            setTimeout(() => firstOption.focus(), 100);
        }
        
        console.log('✅ Dropdown should now be visible');
    },
    
    closeDropdown: function(pill, dropdown, backdrop) {
        console.log('📁 closeDropdown called');
        pill.classList.remove('open');
        dropdown.classList.remove('open');
        backdrop.classList.remove('active');
        this.activeDropdown = null;
    },
    
    closeAllDropdowns: function(backdrop) {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('open'));
        document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.remove('open'));
        backdrop.classList.remove('active');
        this.activeDropdown = null;
    },
    
    selectOption: function(option, dropdown) {
        console.log('📝 selectOption called');
        
        // Remove selected from siblings
        dropdown.querySelectorAll('.filter-dropdown-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Mark as selected
        option.classList.add('selected');
        
        // Update pill text
        const pillId = dropdown.getAttribute('data-for-pill');
        console.log('  Pill ID:', pillId);
        const pill = document.getElementById(pillId);
        
        if (pill) {
            const valueSpan = pill.querySelector('.filter-value') || pill.querySelector('.sort-value');
            const optionText = option.textContent.replace('✓', '').trim();
            console.log('  Option text:', optionText);
            
            if (valueSpan) {
                valueSpan.textContent = optionText;
                console.log('  ✅ Updated pill text');
            }
            
            // Mark pill as active if not default value
            const value = option.getAttribute('data-value');
            console.log('  Option value:', value);
            
            if (value) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
        }
        
        // Update hidden input and submit form
        const inputId = pillId.replace('-pill', '-input');
        console.log('  Looking for input:', inputId);
        const input = document.getElementById(inputId);
        
        if (input) {
            const value = option.getAttribute('data-value');
            input.value = value;
            console.log('  ✅ Set input value to:', value);
            
            // Submit the form
            const form = document.getElementById('filter-form');
            if (form) {
                console.log('  ✅ Submitting form...');
                form.submit();
            } else {
                console.error('  ❌ Form not found!');
            }
        } else {
            console.error('  ❌ Input not found:', inputId);
        }
    },
    
    // More Filters collapsible drawer
    setupMoreFilters: function() {
        const toggle = document.querySelector('.more-filters-toggle');
        const drawer = document.querySelector('.more-filters-drawer');
        
        if (!toggle || !drawer) return;
        
        toggle.addEventListener('click', () => {
            const isOpen = drawer.classList.contains('open');
            
            if (isOpen) {
                drawer.classList.remove('open');
                toggle.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                drawer.classList.add('open');
                toggle.classList.add('open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    },
    
    // Keyboard navigation
    setupKeyboardNavigation: function() {
        document.addEventListener('keydown', (e) => {
            // Escape closes dropdowns
            if (e.key === 'Escape') {
                const backdrop = document.querySelector('.dropdown-backdrop');
                if (backdrop) {
                    this.closeAllDropdowns(backdrop);
                }
            }
            
            // Arrow keys navigate dropdown options
            if (this.activeDropdown && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
                e.preventDefault();
                const { dropdown } = this.activeDropdown;
                const options = Array.from(dropdown.querySelectorAll('.filter-dropdown-option'));
                const currentIndex = options.findIndex(opt => opt === document.activeElement);
                
                let nextIndex;
                if (e.key === 'ArrowDown') {
                    nextIndex = currentIndex < options.length - 1 ? currentIndex + 1 : 0;
                } else {
                    nextIndex = currentIndex > 0 ? currentIndex - 1 : options.length - 1;
                }
                
                options[nextIndex].focus();
            }
        });
    },
    
    // Dynamic filter chips
    setupFilterChips: function() {
        const chipsContainer = document.querySelector('.filter-chips');
        if (!chipsContainer) return;
        
        // Listen for chip remove buttons
        chipsContainer.addEventListener('click', (e) => {
            if (e.target.closest('.filter-chip-remove')) {
                const chip = e.target.closest('.filter-chip');
                const filterType = chip.getAttribute('data-filter-type');
                this.removeFilter(filterType);
            }
        });
    },
    
    removeFilter: function(filterType) {
        const url = new URL(window.location);
        url.searchParams.delete(filterType);
        window.location.href = url.toString();
    },
    
    clearAllFilters: function() {
        const url = new URL(window.location);
        const sort = url.searchParams.get('sort'); // Preserve sort
        url.search = '';
        url.searchParams.set('action', 'recipes');
        if (sort) url.searchParams.set('sort', sort);
        window.location.href = url.toString();
    },
    
    updateURL: function() {
        // Get current filter values
        const params = new URLSearchParams(window.location.search);
        
        // This will be called after dropdown selections
        // The form will handle submission
    },
    
    initializeFromURL: function() {
        // Mark active pills based on URL parameters
        const params = new URLSearchParams(window.location.search);
        
        // Mark cuisine pill as active if cuisine is set
        if (params.get('cuisine')) {
            const cuisinePill = document.getElementById('cuisine-pill');
            if (cuisinePill) cuisinePill.classList.add('active');
        }
        
        // Mark sort pill as active if sort is not default
        if (params.get('sort') && params.get('sort') !== 'date_desc') {
            const sortPill = document.getElementById('sort-pill');
            if (sortPill) sortPill.classList.add('active');
        }
        
        // Mark favorites toggle as active
        if (params.get('favorites_only')) {
            const favToggle = document.querySelector('.favorites-toggle');
            if (favToggle) favToggle.classList.add('active');
        }
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => FilterSystem.init());
} else {
    FilterSystem.init();
}


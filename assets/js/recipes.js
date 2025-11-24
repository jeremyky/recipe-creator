/**
 * Recipes page dynamic behaviors with AJAX and jQuery
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to manage recipe search and filtering
const RecipeSearch = {
    currentPage: 1,
    isLoading: false,
    
    init: function() {
        // Use jQuery for this page (as required - at least one but not all screens)
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is required for recipes page');
            return;
        }
        
        this.setupAjaxSearch();
        this.setupFilterInteractions();
    },
    
    // AJAX query that consumes JSON and updates DOM
    setupAjaxSearch: function() {
        const searchInput = jQuery('#search-recipes');
        const cuisineFilter = jQuery('#cuisine-filter');
        const resultsSection = jQuery('#results-heading').parent();
        
        // Debounce function using arrow function
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };
        
        // Perform AJAX search
        const performSearch = () => {
            if (this.isLoading) return;
            
            const searchTerm = searchInput.val() || '';
            const cuisine = cuisineFilter.val() || '';
            
            // Show loading state
            this.isLoading = true;
            this.showLoadingState(resultsSection);
            
            // AJAX request using jQuery
            jQuery.ajax({
                url: 'api/recipes.php',
                method: 'GET',
                data: {
                    q: searchTerm,
                    cuisine: cuisine,
                    page: this.currentPage
                },
                dataType: 'json',
                success: (response) => {
                    this.isLoading = false;
                    this.updateRecipeDisplay(response, resultsSection);
                },
                error: (xhr, status, error) => {
                    this.isLoading = false;
                    this.showError(resultsSection, 'Failed to load recipes. Please try again.');
                    console.error('AJAX Error:', error);
                }
            });
        };
        
        // Event listeners using jQuery
        searchInput.on('input', debounce(performSearch, 500));
        cuisineFilter.on('change', performSearch);
        
        // Initial load if filters are present
        if (searchInput.val() || cuisineFilter.val()) {
            performSearch();
        }
    },
    
    // Update DOM with recipe data from JSON response
    updateRecipeDisplay: function(response, container) {
        // DOM manipulation - clear existing recipes
        const grid = container.find('.grid');
        if (grid.length === 0) {
            container.append('<div class="grid"></div>');
        }
        const gridElement = container.find('.grid').first();
        gridElement.empty();
        
        if (!response.success || !response.data || response.data.length === 0) {
            gridElement.html('<div class="card"><p>No recipes found. <a href="index.php?action=upload">Upload a recipe</a> to get started!</p></div>');
            return;
        }
        
        // Create recipe cards from JSON data
        response.data.forEach((recipe) => {
            const recipeCard = this.createRecipeCard(recipe);
            gridElement.append(recipeCard);
        });
        
        // Update heading with count
        const heading = container.find('h2').first();
        heading.text(`Recipes (${response.pagination.total} found)`);
    },
    
    // Create recipe card element
    createRecipeCard: function(recipe) {
        // Anonymous function to format date
        const formatDate = function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        };
        
        const imageHtml = recipe.image_url 
            ? `<img src="${recipe.image_url}" alt="${recipe.title}" style="width: 100%; height: 100%; object-fit: cover;">`
            : '[Image]';
        
        return jQuery(`
            <article class="recipe-card">
                <div class="recipe-img" role="img" aria-label="Placeholder for ${recipe.title}">
                    ${imageHtml}
                </div>
                <div class="recipe-content">
                    <h3 class="recipe-title">${recipe.title}</h3>
                    <p class="recipe-meta">
                        Created ${formatDate(recipe.created_at)} • 
                        ${recipe.ingredient_count} ingredients
                    </p>
                </div>
            </article>
        `);
    },
    
    // Show loading state with DOM manipulation
    showLoadingState: function(container) {
        const grid = container.find('.grid');
        grid.html(`
            <div class="card" style="text-align: center; padding: 2rem;">
                <div class="loading" style="margin: 0 auto 1rem;"></div>
                <p>Loading recipes...</p>
            </div>
        `);
    },
    
    // Show error message
    showError: function(container, message) {
        const grid = container.find('.grid');
        grid.html(`<div class="card" style="background: var(--danger); color: white;"><p>${message}</p></div>`);
    },
    
    // Setup filter interactions with style modifications
    setupFilterInteractions: function() {
        // Event listener for filter form - modify style on submit
        jQuery('form[action*="action=recipes"]').on('submit', function(event) {
            const form = jQuery(this);
            const submitButton = form.find('button[type="submit"]');
            
            // Modify button style on submit
            submitButton.prop('disabled', true);
            submitButton.text('Filtering...');
            submitButton.css({
                'opacity': '0.7',
                'cursor': 'not-allowed'
            });
            
            // Reset after a short delay (form will submit)
            setTimeout(() => {
                submitButton.prop('disabled', false);
                submitButton.text('Filter');
                submitButton.css({
                    'opacity': '',
                    'cursor': ''
                });
            }, 1000);
        });
        
        // Add hover effects to recipe cards using jQuery
        jQuery(document).on('mouseenter', '.recipe-card', function() {
            jQuery(this).css({
                'transform': 'translateY(-3px)',
                'transition': 'transform 0.2s ease',
                'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.15)'
            });
        });
        
        jQuery(document).on('mouseleave', '.recipe-card', function() {
            jQuery(this).css({
                'transform': '',
                'box-shadow': ''
            });
        });
    }
};

// Initialize when DOM is ready (using jQuery)
jQuery(document).ready(function() {
    RecipeSearch.init();
});


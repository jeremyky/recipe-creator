/**
 * Home page dynamic behaviors
 * Authors: Jeremy Ky, Ashley Wu, Shaunak Sinha
 * CS 4640 Sprint 4
 */

// JavaScript object to store page state and configuration
const HomePageState = {
    cards: [],
    animationEnabled: true,
    init: function() {
        this.cards = Array.from(document.querySelectorAll('.card'));
        this.setupCardInteractions();
        this.setupDynamicContent();
    },
    
    // Setup interactive card hover effects with style modification
    setupCardInteractions: function() {
        // Use arrow function for event handler
        this.cards.forEach((card, index) => {
            // Event listener for mouseenter - modifies style on event
            card.addEventListener('mouseenter', (event) => {
                const target = event.currentTarget;
                target.style.transform = 'translateY(-5px)';
                target.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
                target.style.boxShadow = '0 8px 16px rgba(0, 0, 0, 0.2)';
            });
            
            // Event listener for mouseleave - modifies style on event
            card.addEventListener('mouseleave', (event) => {
                const target = event.currentTarget;
                target.style.transform = 'translateY(0)';
                target.style.boxShadow = '';
            });
        });
    },
    
    // Dynamic content loading and manipulation
    setupDynamicContent: function() {
        // Anonymous function (self-invoked) to initialize dynamic features
        (function() {
            const heroSection = document.querySelector('section[aria-labelledby="hero-heading"]');
            if (heroSection) {
                // DOM manipulation - add dynamic welcome message based on time
                const hour = new Date().getHours();
                let greeting = 'Welcome';
                
                if (hour < 12) {
                    greeting = 'Good Morning';
                } else if (hour < 18) {
                    greeting = 'Good Afternoon';
                } else {
                    greeting = 'Good Evening';
                }
                
                // Create and append dynamic element
                const greetingElement = document.createElement('p');
                greetingElement.className = 'dynamic-greeting';
                greetingElement.textContent = greeting + '! ';
                greetingElement.style.fontSize = '1.1rem';
                greetingElement.style.color = 'var(--primary, #0066cc)';
                greetingElement.style.marginTop = '0.5rem';
                
                const heading = heroSection.querySelector('h1');
                if (heading) {
                    heading.insertAdjacentElement('afterend', greetingElement);
                }
            }
        })();
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => HomePageState.init());
} else {
    HomePageState.init();
}


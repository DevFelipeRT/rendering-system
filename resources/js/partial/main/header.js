/**
 * Header Component JavaScript - Definitive Solution
 * This script's only job is to add/remove the 'scrolled' class to the main <header> element.
 * All styling is handled by the corresponding CSS file.
 */
class HeaderScrollManager {
    constructor() {
        // The main <header> element is the target for our class.
        this.header = document.querySelector('.header');
        this.isScrolling = false;

        // Ensure the script runs only after the DOM is ready.
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        // If the header doesn't exist, do nothing.
        if (!this.header) {
            console.error('Header element with class ".header" not found.');
            return;
        }

        // Set the initial state on page load.
        this.updateHeaderState();

        // Add the scroll event listener.
        window.addEventListener('scroll', () => {
            // Use requestAnimationFrame for performance.
            if (!this.isScrolling) {
                window.requestAnimationFrame(() => {
                    this.updateHeaderState();
                    this.isScrolling = false;
                });
                this.isScrolling = true;
            }
        }, { passive: true });
    }

    updateHeaderState() {
        const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        const isScrolled = scrollPosition > 10;

        // This is the critical action: toggle the class on the <header> element.
        this.header.classList.toggle('scrolled', isScrolled);
    }
}

// Initialize the component.
new HeaderScrollManager();

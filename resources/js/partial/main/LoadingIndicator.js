/**
 * Advanced Rendering System - Loading Indicator Component
 * Manages a multi-stage loading animation for a sophisticated user experience,
 * using the proven hybrid JS/CSS animation technique.
 */
class LoadingIndicator {
    constructor(options = {}) {
        this.loader = document.getElementById('page-loader');
        this.icon = this.loader ? this.loader.querySelector('.loader-icon') : null;
        this.options = {
            minDisplayTime: 1200, // Min time the loader is visible after the intro
            pauseBetweenCycles: 250, // Interval between icon animation cycles
            ...options
        };

        if (!this.loader || !this.icon) {
            console.warn('LoadingIndicator: Required elements not found.');
            return;
        }

        this.isAnimatingForward = true;
        this.boundHandleTransitionEnd = this.handleTransitionEnd.bind(this);
        this.init();
    }

    init() {
        const pageLoadStartTime = Date.now();
        // This duration is now only used for calculating the minimum display time.
        const introAnimationDuration = 1300; 

        // --- Multi-stage Intro Animation ---
        
        // Stage 1: Fade in the content (icon and text) over the white background.
        setTimeout(() => {
            this.loader.classList.add('content-visible');
        }, 100);

        // Stage 2: After the content is visible, fade out the white background
        // to reveal the glass effect.
        setTimeout(() => {
            this.loader.classList.add('glass-revealed');
        }, 600); // This timing creates the sequence of effects.

        // The icon animation loop is managed by listening for the 'transitionend' event.
        this.icon.addEventListener('transitionend', this.boundHandleTransitionEnd);
        
        // CORRECTED: Trigger the very first icon animation step immediately,
        // so it runs in parallel with the background transitions.
        setTimeout(() => {
            this.runAnimationStep();
        }, 100);

        // When the page is fully loaded, begin the hide process.
        window.addEventListener('load', () => {
            const elapsedTime = Date.now() - pageLoadStartTime;
            const minTotalTime = this.options.minDisplayTime + introAnimationDuration;
            const remainingTime = minTotalTime - elapsedTime;

            if (remainingTime > 0) {
                setTimeout(() => this.hideLoader(), remainingTime);
            } else {
                this.hideLoader();
            }
        });
    }

    handleTransitionEnd() {
        setTimeout(() => {
            this.runAnimationStep();
        }, this.options.pauseBetweenCycles);
    }



    runAnimationStep() {
        if (!this.icon) return;
        const rotation = this.isAnimatingForward ? '360deg' : '0deg';
        const translation = this.isAnimatingForward ? '-10px' : '0px';
        this.icon.style.transform = `translateY(${translation}) rotate(${rotation})`;
        this.isAnimatingForward = !this.isAnimatingForward;
    }

    /**
     * Hides the loader and stops the animation loop gracefully.
     */
    hideLoader() {
        if (!this.loader || !this.icon) return;

        this.icon.removeEventListener('transitionend', this.boundHandleTransitionEnd);
        this.loader.classList.add('is-hiding');
        
        setTimeout(() => {
            if (this.loader) {
                this.loader.remove();
            }
        }, 1000); // Safe delay for all transitions to finish
    }
}

// Initialize the Loading Indicator.
new LoadingIndicator({ minDisplayTime: 550, pauseBetweenCycles: 250 });

/**
 * Header Component JavaScript - Advanced Rendering System
 * Handles header-specific functionality for the Quotation System
 * * Features:
 * - Scroll effect for sticky header
 * - Brand animation effects
 * - Mobile menu enhancements
 * - Performance optimizations
 */

class Header {
    constructor() {
        this.header = null;
        this.navbar = null;
        this.brand = null;
        this.navbarToggler = null;
        this.navbarCollapse = null;
        this.isScrolling = false;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeElements();
            this.attachEventListeners();
        });
    }

    initializeElements() {
        this.header = document.querySelector('.header');
        this.navbar = document.querySelector('.navbar');
        this.brand = document.querySelector('.navbar-brand');
        this.navbarToggler = document.querySelector('.navbar-toggler');
        this.navbarCollapse = document.querySelector('.navbar-collapse');
    }

    attachEventListeners() {
        // Scroll effect for header
        this.initScrollEffect();
        
        // Brand hover animations
        this.initBrandAnimation();
        
        // Mobile menu enhancements
        this.initMobileMenu();
    }

    /**
     * Adds scroll effect to navbar
     * Changes navbar appearance when user scrolls
     */
    initScrollEffect() {
        if (!this.navbar) return;

        const handleScroll = () => {
            if (!this.isScrolling) {
                window.requestAnimationFrame(() => {
                    this.updateHeaderOnScroll();
                    this.isScrolling = false;
                });
                this.isScrolling = true;
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
    }

    updateHeaderOnScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const isScrolled = scrollTop > 10;

        // Add/remove scrolled class for styling
        this.navbar.classList.toggle('scrolled', isScrolled);
        
        // Enhanced shadow effect
        if (this.header) {
            if (isScrolled) {
                this.header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            } else {
                this.header.style.boxShadow = '';
            }
        }
    }

    /**
     * Brand hover animation with icon rotation
     */
    initBrandAnimation() {
        if (!this.brand) return;

        this.brand.addEventListener('mouseenter', () => {
            const icon = this.brand.querySelector('i');
            if (icon) {
                icon.style.transform = 'rotate(360deg)';
                icon.style.transition = 'transform 0.6s ease';
            }
        });

        this.brand.addEventListener('mouseleave', () => {
            const icon = this.brand.querySelector('i');
            if (icon) {
                icon.style.transform = 'rotate(0deg)';
            }
        });
    }

    /**
     * Enhanced mobile menu functionality
     */
    initMobileMenu() {
        if (!this.navbarToggler || !this.navbarCollapse) return;

        // Use Bootstrap's custom events for reliable state tracking.
        // 'show.bs.collapse' fires just before the menu starts opening.
        this.navbarCollapse.addEventListener('show.bs.collapse', () => {
            this.navbarToggler.classList.add('active');
            this.navbarCollapse.style.transition = 'all 0.3s ease';
        });

        // 'hide.bs.collapse' fires just before the menu starts closing.
        this.navbarCollapse.addEventListener('hide.bs.collapse', () => {
            this.navbarToggler.classList.remove('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.header?.contains(e.target) && this.navbarCollapse.classList.contains('show')) {
                if (typeof bootstrap !== 'undefined') {
                    const bsCollapse = new bootstrap.Collapse(this.navbarCollapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                } else {
                    this.navbarToggler.click();
                }
            }
        });

        // Close mobile menu when clicking on nav links
        const navLinks = this.navbarCollapse.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (this.navbarCollapse.classList.contains('show')) {
                    this.navbarToggler.click();
                }
            });
        });
    }

    // Public methods for external control
    hideHeader() {
        if (this.header) {
            this.header.style.transform = 'translateY(-100%)';
            this.header.style.transition = 'transform 0.3s ease';
        }
    }

    showHeader() {
        if (this.header) {
            this.header.style.transform = 'translateY(0)';
        }
    }

    // Utility method for responsive adjustments
    handleResize() {
        const isMobile = window.innerWidth < 992;
        
        if (this.header) {
            this.header.classList.toggle('mobile-header', isMobile);
        }
    }
}

// Initialize Header Component
const headerComponent = new Header();

// Expose for potential external use
window.HeaderComponent = headerComponent;

// Handle window resize
window.addEventListener('resize', () => {
    headerComponent.handleResize();
}, { passive: true });

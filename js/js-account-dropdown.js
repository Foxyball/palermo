/**
 * Palermo Account Dropdown
 * Handles account dropdown functionality for the Palermo restaurant website
 */
class PalermoAccountDropdown {
    constructor() {
        this.accountTrigger = document.querySelector('.js-account-dropdown__trigger');
        this.accountContent = document.querySelector('.js-account-dropdown__content');
        this.accountContainer = document.querySelector('.js-account-dropdown');
        this.isOpen = false;
        
        this.init();
    }

    init() {
        if (!this.accountTrigger || !this.accountContent) {
            return;
        }

        // Show the account dropdown by removing hidden class
        this.accountContainer.classList.remove('hidden');
        
        this.setupStyles();
        this.bindEvents();
    }

    setupStyles() {
        const styles = `
            <style id="js-account-dropdown-styles">
                .js-account-dropdown {
                    position: relative;
                }
                
                .js-account-dropdown__trigger {
                    text-decoration: none !important;
                    padding: 10px;
                    border-radius: 4px;
                    color: inherit;
                    transition: background-color 0.2s ease;
                }
                
                .js-account-dropdown__trigger:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                    color: inherit;
                }
                
                .js-account-dropdown__content {
                    position: absolute;
                    top: 100%;
                    right: 0;
                    min-width: 280px;
                    max-width: 320px;
                    z-index: 999;
                    display: none;
                    background-color: #fff;
                    border: 1px solid rgba(0,0,0,0.15);
                }
                
                .js-account-dropdown__header {
                    background-color: #f8f9fa;
                }
                
                .list-group-item-action:hover {
                    background-color: #f8f9fa;
                }

                /* Dark Theme Styles */
                .dark .js-account-dropdown__content {
                    background-color: #2d2d2d !important;
                    color: #ffffff;
                    border: 1px solid #444;
                }
                
                .dark .js-account-dropdown__header {
                    background-color: #1a1a1a !important;
                    border-bottom-color: #444 !important;
                }
                
                .dark .list-group-item-action {
                    background-color: transparent !important;
                    color: #aaa !important;
                }
                
                .dark .list-group-item-action:hover {
                    background-color: rgba(255, 255, 255, 0.1) !important;
                    color: #fff !important;
                }
                
                .dark .text-muted {
                    color: #aaa !important;
                }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
    }

    bindEvents() {
        this.accountTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });

        document.addEventListener('click', (e) => {
            if (!this.accountContainer.contains(e.target)) {
                this.close();
            }
        });
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.accountContent.style.display = 'block';
        this.isOpen = true;
    }

    close() {
        this.accountContent.style.display = 'none';
        this.isOpen = false;
    }
}

// Initialize account dropdown when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.palermoAccount = new PalermoAccountDropdown();
});

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PalermoAccountDropdown;
}
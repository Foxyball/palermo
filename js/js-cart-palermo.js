class PalermoCartDropdown {
    constructor() {
        this.cartContainer = document.querySelector('.js-cart-dropdown');
        this.cartTrigger = document.querySelector('.js-cart-dropdown__trigger');
        this.cartContent = document.querySelector('.js-cart-dropdown__content');
        this.cartCounter = document.querySelector('.js-cart-dropdown__counter');
        this.removeButtons = document.querySelectorAll('.js-cart-dropdown__remove');
        
        this.isOpen = false;
        this.isDarkTheme = this.detectDarkTheme();
        
        this.init();
    }

    init() {
        if (!this.cartTrigger || !this.cartContent) {
            console.warn('Cart dropdown elements not found');
            return;
        }

        this.setupStyles();
        this.bindEvents();
        this.applyTheme();
    }

    detectDarkTheme() {
        // Check if body or document has dark theme class
        return document.body.classList.contains('dark') || 
               document.documentElement.classList.contains('dark') ||
               document.querySelector('.dark') !== null;
    }

    setupStyles() {
        const styles = `
            <style id="js-cart-dropdown-styles">
                .js-cart-dropdown {
                    position: relative;
                }
                
                .js-cart-dropdown__trigger {
                    text-decoration: none !important;
                    padding: 10px;
                    border-radius: 4px;
                    transition: background-color 0.3s ease;
                    color: inherit;
                }
                
                .js-cart-dropdown__trigger:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                    color: inherit;
                }
                
                .js-cart-dropdown__counter {
                    width: 20px;
                    height: 20px;
                    font-size: 12px;
                    min-width: 20px;
                }
                
                .js-cart-dropdown__content {
                    position: absolute;
                    top: 100%;
                    right: 0;
                    min-width: 300px;
                    max-width: 350px;
                    z-index: 999;
                    display: none;
                    opacity: 0;
                    transform: translateY(-10px);
                    transition: opacity 0.3s ease, transform 0.3s ease;
                }
                
                .js-cart-dropdown__content.show {
                    display: block;
                    opacity: 1;
                    transform: translateY(0);
                }
                
                .js-cart-dropdown__item:last-child {
                    border-bottom: none !important;
                }
                
                .js-cart-dropdown__item-img {
                    width: 50px;
                    height: 50px;
                    object-fit: cover;
                    border-radius: 4px;
                }
                
                .js-cart-dropdown__remove {
                    text-decoration: none;
                    padding: 5px;
                    border-radius: 50%;
                    transition: background-color 0.2s ease;
                }
                
                .js-cart-dropdown__remove:hover {
                    background-color: rgba(220, 53, 69, 0.1);
                }

                /* Dark Theme Styles */
                .dark .js-cart-dropdown__content {
                    background-color: #2d2d2d !important;
                    color: #ffffff;
                    border: 1px solid #444;
                }
                
                .dark .js-cart-dropdown__header {
                    border-bottom-color: #444 !important;
                }
                
                .dark .js-cart-dropdown__footer {
                    background-color: #1a1a1a !important;
                    border-top-color: #444 !important;
                }
                
                .dark .js-cart-dropdown__item {
                    border-bottom-color: #444 !important;
                }
                
                .dark .js-cart-dropdown__item-price {
                    color: #aaa !important;
                }
                
                .dark .js-cart-dropdown__view-btn {
                    background-color: #444 !important;
                    border-color: #444 !important;
                    color: #fff !important;
                }
                
                .dark .js-cart-dropdown__view-btn:hover {
                    background-color: #555 !important;
                    border-color: #555 !important;
                }
            </style>
        `;
        
        const existingStyles = document.getElementById('js-cart-dropdown-styles');
        if (existingStyles) {
            existingStyles.remove();
        }
        
        document.head.insertAdjacentHTML('beforeend', styles);
    }

    bindEvents() {
        // Toggle cart dropdown
        this.cartTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleCart();
        });

        document.addEventListener('click', (e) => {
            if (!this.cartContainer.contains(e.target)) {
                this.closeCart();
            }
        });

        this.removeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.removeItem(e.target.closest('.js-cart-dropdown__item'));
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeCart();
            }
        });

        const observer = new MutationObserver(() => {
            const newTheme = this.detectDarkTheme();
            if (newTheme !== this.isDarkTheme) {
                this.isDarkTheme = newTheme;
                this.applyTheme();
            }
        });

        observer.observe(document.body, { 
            attributes: true, 
            attributeFilter: ['class'] 
        });
    }

    toggleCart() {
        if (this.isOpen) {
            this.closeCart();
        } else {
            this.openCart();
        }
    }

    openCart() {
        this.cartContent.style.display = 'block';
        // Force reflow
        this.cartContent.offsetHeight;
        this.cartContent.classList.add('show');
        this.isOpen = true;
    }

    closeCart() {
        this.cartContent.classList.remove('show');
        setTimeout(() => {
            if (!this.isOpen) {
                this.cartContent.style.display = 'none';
            }
        }, 300);
        this.isOpen = false;
    }

    removeItem(itemElement) {
        if (!itemElement) return;

        // Add remove animation
        itemElement.style.opacity = '0';
        itemElement.style.transform = 'translateX(100px)';
        itemElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

        setTimeout(() => {
            itemElement.remove();
            this.updateCartCounter();
            this.updateCartTotal();
            
            // Close cart if empty
            const remainingItems = document.querySelectorAll('.js-cart-dropdown__item');
            if (remainingItems.length === 0) {
                this.showEmptyCart();
            }
        }, 300);
    }

    updateCartCounter() {
        const itemCount = document.querySelectorAll('.js-cart-dropdown__item').length;
        this.cartCounter.textContent = itemCount;
        
        if (itemCount === 0) {
            this.cartCounter.style.display = 'none';
        }
    }

    updateCartTotal() {
        const items = document.querySelectorAll('.js-cart-dropdown__item');
        let total = 0;
        
        items.forEach(item => {
            const priceText = item.querySelector('.js-cart-dropdown__item-price').textContent;
            const price = parseFloat(priceText.match(/[\d.]+/)[0]);
            total += price;
        });
        
        const totalElement = document.querySelector('.js-cart-dropdown__total .text-danger');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2) + ' лв';
        }
    }

    showEmptyCart() {
        const itemsContainer = document.querySelector('.js-cart-dropdown__items');
        if (itemsContainer) {
            itemsContainer.innerHTML = '<p class="text-center text-muted mb-0">Количката е празна</p>';
        }
        
        const footer = document.querySelector('.js-cart-dropdown__footer');
        if (footer) {
            footer.style.display = 'none';
        }
    }

    applyTheme() {
        if (this.isDarkTheme) {
            document.body.classList.add('dark');
        }
        
        if (this.cartContent) {
            this.cartContent.style.display = this.cartContent.style.display;
        }
    }

    addItem(item) {
        console.log('Adding item to cart:', item);
    }

    getCartItems() {
        return Array.from(document.querySelectorAll('.js-cart-dropdown__item'));
    }

    clearCart() {
        const items = document.querySelectorAll('.js-cart-dropdown__item');
        items.forEach(item => item.remove());
        this.updateCartCounter();
        this.showEmptyCart();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.palermoCart = new PalermoCartDropdown();
});

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PalermoCartDropdown;
}
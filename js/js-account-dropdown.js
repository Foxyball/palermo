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

        this.accountContainer.classList.remove('hidden');
        
        this.bindEvents();
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

document.addEventListener('DOMContentLoaded', () => {
    window.palermoAccount = new PalermoAccountDropdown();
});
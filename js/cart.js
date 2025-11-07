const PalermoCart = (function () {
    'use strict';

    // config
    const config = {
        baseUrl: document.getElementById('js-base-url')?.value || window.location.origin + '/',
        bgnToEurRate: 1.95583,
        endpoints: {
            add: 'include/cart_add.php',
            remove: 'include/cart_remove.php',
            data: 'include/cart_data.php'
        },
        selectors: {
            counter: '.js-cart-dropdown__counter',
            items: '.js-cart-dropdown__items',
            total: '.js-cart-dropdown__total span:last-child',
            dropdown: '.js-cart-dropdown',
            trigger: '.js-cart-dropdown__trigger',
            remove: '.js-cart-remove'
        }
    };

    // Initialize cart
    function init() {
        loadCartData();
        bindEvents();
    }

    // Bind event handlers
    function bindEvents() {
        // Toggle cart dropdown
        $(document).on('click', config.selectors.trigger, function (e) {
            e.preventDefault();
            $(config.selectors.dropdown).toggleClass('active');
            $('.js-account-dropdown').removeClass('active');
        });

        // Close cart when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest(config.selectors.dropdown).length) {
                $(config.selectors.dropdown).removeClass('active');
            }
        });

        // Remove item from cart
        $(document).on('click', config.selectors.remove, function (e) {
            e.preventDefault();
            const cartKey = $(this).data('cart-key');
            removeItem(cartKey);
        });
    }

    // Load cart data from server
    function loadCartData() {
        $.ajax({
            url: config.baseUrl + config.endpoints.data,
            method: 'GET',
            dataType: 'json'
        })
            .done(function (response) {
                if (response.success) {
                    updateDisplay(response);
                }
            })
            .fail(function () {
                console.error('Failed to load cart data');
            });
    }

    // Add item to cart
    function addItem(formData) {
        return $.ajax({
            url: config.baseUrl + config.endpoints.add,
            method: 'POST',
            data: formData,
            dataType: 'json'
        });
    }

    // Remove item from cart
    function removeItem(cartKey) {
        $.ajax({
            url: config.baseUrl + config.endpoints.remove,
            method: 'POST',
            data: { cart_key: cartKey },
            dataType: 'json'
        })
            .done(function (response) {
                if (response.success) {
                    showAlert('Item removed from cart', 'success');
                    updateDisplay(response);
                } else {
                    showAlert(response.message || 'Failed to remove item', 'danger');
                }
            })
            .fail(function () {
                showAlert('An error occurred', 'danger');
            });
    }

    // Update cart display
    function updateDisplay(data) {
        updateCounter(data.cart_count);
        updateItems(data.items);
        updateTotal(data.cart_total);
    }

    // Update cart counter
    function updateCounter(count) {
        const $counter = $(config.selectors.counter);
        if (count > 0) {
            $counter.text(count).css('display', 'inline-flex');
        } else {
            $counter.text('0').css('display', 'none');
        }
    }

    // Update cart items list
    function updateItems(items) {
        const $items = $(config.selectors.items);

        if (!items || items.length === 0) {
            $items.html('<p class="text-muted text-center py-4">Your cart is empty</p>');
            return;
        }

        let html = '';
        items.forEach(function (item) {
            html += buildItemHtml(item);
        });
        $items.html(html);
    }

    // Build HTML for cart item
    function buildItemHtml(item) {
        const imageSrc = item.image ? config.baseUrl + item.image : config.baseUrl + 'images/svg/burger-house.svg';
        const itemUrl = config.baseUrl + 'art/' + item.slug;

        let addonsHtml = '';
        if (item.addons && item.addons.length > 0) {
            addonsHtml = '<div class="text-muted small">';
            item.addons.forEach(function (addon) {
                addonsHtml += '<span class="me-2">+ ' + addon.name + '</span>';
            });
            addonsHtml += '</div>';
        }

        return `
            <div class="js-cart-dropdown__item d-flex align-items-center pb-3 mb-3 border-bottom">
                <div class="js-cart-dropdown__item-image me-3">
                    <a href="${itemUrl}">
                        <img src="${imageSrc}" alt="${item.name}" class="js-cart-dropdown__item-img">
                    </a>
                </div>
                <div class="js-cart-dropdown__item-desc flex-grow-1">
                    <div class="js-cart-dropdown__item-title fw-bold">
                        <a href="${itemUrl}" class="text-white text-decoration-none">${item.name}</a>
                    </div>
                    ${addonsHtml}
                    <div class="js-cart-dropdown__item-price text-muted">
                        ${item.quantity}x ${formatPrice(item.item_price)}
                    </div>
                </div>
                <div class="js-cart-dropdown__item-actions">
                    <a href="#" class="js-cart-remove text-danger" data-cart-key="${item.key}" title="Remove">X</a>
                </div>
            </div>
        `;
    }

    // Update cart total
    function updateTotal(total) {
        $(config.selectors.total).html(formatPrice(total));
    }

    // Format price with BGN and EUR
    function formatPrice(price) {
        const bgnPrice = parseFloat(price).toFixed(2);
        const eurPrice = (price / config.bgnToEurRate).toFixed(2);
        return bgnPrice + ' лв / ' + eurPrice + ' €';
    }

    function showAlert(message, type) {
        $('.bootstrap-alert-container .alert').remove();

        if (!$('.bootstrap-alert-container').length) {
            $('body').append('<div class="bootstrap-alert-container"></div>');
        }

        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $('.bootstrap-alert-container').append(alertHtml);

        setTimeout(function () {
            $('.bootstrap-alert-container .alert').fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }

    return {
        init: init,
        add: addItem,
        remove: removeItem,
        refresh: loadCartData,
        showAlert: showAlert,
        formatPrice: formatPrice
    };
})();

$(document).ready(function () {
    PalermoCart.init();
});

window.PalermoCart = PalermoCart;

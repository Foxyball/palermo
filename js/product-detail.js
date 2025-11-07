$(document).ready(function () {
    // Check if we're on the product detail page
    if (!$('.js-add-to-cart-form').length) {
        return;
    }

    const basePrice = parseFloat($('.js-base-price').val());
    const bgnToEurRate = parseFloat($('.js-bgn-to-eur-rate').val());
    const baseUrl = $('.js-base-url').val() || '/';

    /**
     * Calculate and update the total price based on quantity and selected addons
     */
    function updateTotalPrice() {
        const quantity = parseInt($('.js-quantity').val()) || 1;
        let totalPrice = basePrice * quantity;

        // Add selected addons
        $('.addon-checkbox:checked').each(function () {
            const addonPrice = parseFloat($(this).data('price')) || 0;
            totalPrice += addonPrice * quantity;
        });

        // Convert to EUR
        const priceEur = (totalPrice / bgnToEurRate).toFixed(2);
        const priceBgn = totalPrice.toFixed(2);

        // Add animation effect
        const $priceElement = $('.js-total-price');
        $priceElement.addClass('updating');

        setTimeout(function () {
            $priceElement.text(priceBgn + ' лв / ' + priceEur + ' €');
            $priceElement.removeClass('updating');
        }, 150);
    }

    /**
     * Quantity decrease button handler
     */
    $('.js-qty-decrease').on('click', function () {
        const $qty = $('.js-quantity');
        const currentVal = parseInt($qty.val()) || 1;
        if (currentVal > 1) {
            $qty.val(currentVal - 1);
            updateTotalPrice();
        }
    });

    /**
     * Quantity increase button handler
     */
    $('.js-qty-increase').on('click', function () {
        const $qty = $('.js-quantity');
        const currentVal = parseInt($qty.val()) || 1;
        const max = parseInt($qty.attr('max')) || 99;
        if (currentVal < max) {
            $qty.val(currentVal + 1);
            updateTotalPrice();
        }
    });

    /**
     * Quantity input validation
     */
    $('.js-quantity').on('input', function () {
        let val = parseInt($(this).val()) || 1;
        const min = parseInt($(this).attr('min')) || 1;
        const max = parseInt($(this).attr('max')) || 99;

        if (val < min) val = min;
        if (val > max) val = max;

        $(this).val(val);
        updateTotalPrice();
    });

    /**
     * Update price when addons change
     */
    $('.addon-checkbox').on('change', function () {
        updateTotalPrice();
    });

    /**
     * Add to cart form submission
     */
    $('.js-add-to-cart-form').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const $btn = $('.js-add-to-cart-btn');
        const originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="icon-line-loader icon-spin"></i> Adding...');

        PalermoCart.add(formData)
            .done(function (response) {
                if (response.success) {
                    PalermoCart.showAlert(response.message || 'Product added to cart', 'success');
                    PalermoCart.refresh();
                } else {
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = baseUrl + response.redirect;
                        }, 1500);
                    } else {
                        PalermoCart.showAlert(response.message || 'Failed to add product', 'danger');
                    }
                }
            })
            .fail(function () {
                PalermoCart.showAlert('An error occurred. Please try again.', 'danger');
            })
            .always(function () {
                $btn.prop('disabled', false).html(originalText);
            });
    });
});

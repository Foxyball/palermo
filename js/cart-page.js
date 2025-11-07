$(document).ready(function () {
    const baseUrl = window.location.origin + '/';
    const bgnToEurRate = 1.95583;

    /**
     * Update quantity - decrease
     */
    $(document).on('click', '.qty-decrease', function () {
        const cartKey = $(this).data('cart-key');
        const $input = $(`.quantity-input[data-cart-key="${cartKey}"]`);
        const currentQty = parseInt($input.val()) || 1;

        if (currentQty > 1) {
            updateQuantity(cartKey, currentQty - 1);
        }
    });

    /**
     * Update quantity - increase
     */
    $(document).on('click', '.qty-increase', function () {
        const cartKey = $(this).data('cart-key');
        const $input = $(`.quantity-input[data-cart-key="${cartKey}"]`);
        const currentQty = parseInt($input.val()) || 1;

        if (currentQty < 99) {
            updateQuantity(cartKey, currentQty + 1);
        }
    });

    /**
     * Remove item from cart
     */
    $(document).on('click', '.remove-item', function () {
        const cartKey = $(this).data('cart-key');
        const $cartItem = $(`.cart-item[data-cart-key="${cartKey}"]`);

        $cartItem.addClass('updating');

        $.ajax({
            url: baseUrl + 'include/cart_remove.php',
            method: 'POST',
            data: { cart_key: cartKey },
            dataType: 'json'
        })
        .done(function (response) {
            if (response.success) {
                // Fade out and remove the item
                $cartItem.fadeOut(300, function () {
                    $(this).remove();
                    
                    // Update cart summary
                    updateCartSummary(response);

                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        location.reload(); // Reload to show empty cart message
                    }

                    // Update mini cart
                    if (window.PalermoCart) {
                        window.PalermoCart.refresh();
                    }
                });

            } else {
                $cartItem.removeClass('updating');
                showAlert(response.message || 'Failed to remove item', 'danger');
            }
        })
        .fail(function () {
            $cartItem.removeClass('updating');
            showAlert('An error occurred. Please try again.', 'danger');
        });
    });

    /**
     * Update item quantity
     */
    function updateQuantity(cartKey, newQuantity) {
        const $cartItem = $(`.cart-item[data-cart-key="${cartKey}"]`);
        const $input = $(`.quantity-input[data-cart-key="${cartKey}"]`);
        const $itemTotal = $cartItem.find('.item-total-price');

        $cartItem.addClass('updating');
        $itemTotal.addClass('updating');

        $.ajax({
            url: baseUrl + 'include/cart_update.php',
            method: 'POST',
            data: {
                cart_key: cartKey,
                quantity: newQuantity
            },
            dataType: 'json'
        })
        .done(function (response) {
            if (response.success) {
                // Update quantity input
                $input.val(newQuantity);

                // Update item total
                const itemData = response.items.find(item => item.key === cartKey);
                if (itemData) {
                    const itemTotal = itemData.item_total;
                    const itemTotalEur = itemTotal / bgnToEurRate;
                    
                    $itemTotal.html(
                        itemTotal.toFixed(2) + ' лв<br>' +
                        '<small class="text-muted">' + itemTotalEur.toFixed(2) + ' €</small>'
                    );
                }

                // Update cart summary
                updateCartSummary(response);

                // Update mini cart
                if (window.PalermoCart) {
                    window.PalermoCart.refresh();
                }

                $cartItem.removeClass('updating');
                $itemTotal.removeClass('updating');
            } else {
                $cartItem.removeClass('updating');
                $itemTotal.removeClass('updating');
                showAlert(response.message || 'Failed to update quantity', 'danger');
            }
        })
        .fail(function () {
            $cartItem.removeClass('updating');
            $itemTotal.removeClass('updating');
            showAlert('An error occurred. Please try again.', 'danger');
        });
    }

    /**
     * Update cart summary (totals)
     */
    function updateCartSummary(data) {
        const total = data.cart_total || 0;
        const count = data.cart_count || 0;
        const totalEur = total / bgnToEurRate;

        $('#cart-count').text(count);
        $('#cart-subtotal-bgn').text(total.toFixed(2) + ' лв');
        $('#cart-subtotal-eur').text(totalEur.toFixed(2) + ' €');
        $('#cart-total-bgn').text(total.toFixed(2) + ' лв');
        $('#cart-total-eur').text(totalEur.toFixed(2) + ' €');
    }

    /**
     * Show alert message
     */
    function showAlert(message, type) {
        // Remove existing alerts
        $('.bootstrap-alert-container .alert').remove();

        // Create container if doesn't exist
        if (!$('.bootstrap-alert-container').length) {
            $('body').append('<div class="bootstrap-alert-container"></div>');
        }

        // Create alert
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $('.bootstrap-alert-container').append(alertHtml);

        // Auto-hide after 3 seconds
        setTimeout(function () {
            $('.bootstrap-alert-container .alert').fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }
});

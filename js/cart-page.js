$(document).ready(function () {
    const baseUrl = document.getElementById('js-base-url')?.value || window.location.origin + '/';
    const bgnToEurRate = 1.95583;


    $(document).on('click', '.qty-decrease', function () {
        const cartKey = $(this).data('cart-key');
        const $input = $(`.quantity-input[data-cart-key="${cartKey}"]`);
        const currentQty = parseInt($input.val()) || 1;

        if (currentQty > 1) {
            updateQuantity(cartKey, currentQty - 1);
        }
    });


    $(document).on('click', '.qty-increase', function () {
        const cartKey = $(this).data('cart-key');
        const $input = $(`.quantity-input[data-cart-key="${cartKey}"]`);
        const currentQty = parseInt($input.val()) || 1;

        if (currentQty < 99) {
            updateQuantity(cartKey, currentQty + 1);
        }
    });


    $(document).on('click', '.remove-item', function () {
        const cartKey = $(this).data('cart-key');
        const $cartItem = $(`.cart-item[data-cart-key="${cartKey}"]`);

        $.ajax({
            url: baseUrl + 'include/cart_remove.php',
            method: 'POST',
            data: { cart_key: cartKey },
            dataType: 'json'
        })
        .done(function (response) {
            if (response.success) {
                $cartItem.fadeOut(300, function () {
                    $(this).remove();
                    
                    updateCartSummary(response);

                    if ($('.cart-item').length === 0) {
                        location.reload();
                    }

                    if (window.PalermoCart) {
                        window.PalermoCart.refresh();
                    }
                });

            } else {
                showAlert(response.message || 'Failed to remove item', 'danger');
            }
        })
        .fail(function () {
            showAlert('An error occurred. Please try again.', 'danger');
        });
    });


    function updateQuantity(cartKey, newQuantity) {
        const $cartItem = $(`.cart-item[data-cart-key="${cartKey}"]`);
        const $input = $(`.quantity-input[data-cart-key="${cartKey}"]`);
        const $itemTotal = $cartItem.find('.item-total-price');

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
             
                $input.val(newQuantity);

                const itemData = response.items.find(item => item.key === cartKey);
                if (itemData) {
                    const itemTotal = itemData.item_total;
                    const itemTotalEur = itemTotal / bgnToEurRate;
                    
                    $itemTotal.html(
                        itemTotal.toFixed(2) + ' лв<br>' +
                        '<small class="text-muted">' + itemTotalEur.toFixed(2) + ' €</small>'
                    );
                }

                updateCartSummary(response);

                if (window.PalermoCart) {
                    window.PalermoCart.refresh();
                }

            } else {
                showAlert(response.message || 'Failed to update quantity', 'danger');
            }
        })
        .fail(function () {
            showAlert('An error occurred. Please try again.', 'danger');
        });
    }


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
});

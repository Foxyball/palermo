$(document).ready(function () {
    /**
     * Checkout form submission
     */
    $('#checkout-form').on('submit', function (e) {
        e.preventDefault();

        // Validate required fields
        const address = $('#order_address').val().trim();
        
        if (!address) {
            showAlert('Please enter your delivery address', 'danger');
            $('#order_address').focus();
            return;
        }

        if (address.length < 10) {
            showAlert('Please enter a complete delivery address', 'danger');
            $('#order_address').focus();
            return;
        }

        // Disable submit button and show loading state
        const $btn = $('#place-order-btn');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).addClass('loading').html('Processing...');

        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json'
        })
        .done(function (response) {
            if (response.success) {
                showAlert('Order placed successfully! Redirecting...', 'success');
                
                // Redirect to order success page
                setTimeout(function () {
                    window.location.href = response.redirect || 'order-success?order_id=' + response.order_id;
                }, 1500);
            } else {
                $btn.prop('disabled', false).removeClass('loading').html(originalHtml);
                showAlert(response.message || 'Failed to place order. Please try again.', 'danger');
            }
        })
        .fail(function (xhr, status, error) {
            $btn.prop('disabled', false).removeClass('loading').html(originalHtml);
            showAlert('An error occurred. Please try again.', 'danger');
            console.error('Order submission error:', error);
        });
    });

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

        // Auto-hide after 5 seconds
        setTimeout(function () {
            $('.bootstrap-alert-container .alert').fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    $('textarea').on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

$(function () {
    // Status update handler
    $(document).on('click', '.js-order-status-update-btn', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const orderId = $btn.data('order-id');
        const currentStatusId = $btn.data('current-status-id');

        $('#order_id').val(orderId);
        $('#new_status').val(currentStatusId);
        $('#statusUpdateModal').modal('show');
    });

    // Confirm status update
    $('#confirmStatusUpdate').on('click', function () {
        const $btn = $(this);
        const orderId = $('#order_id').val();
        const newStatusId = $('#new_status').val();

        if (!orderId || !newStatusId) {
            toastr.error('Please select a status');
            return;
        }

        $btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: './include/ajax_order_update_status.php',
            method: 'POST',
            data: {
                id: orderId,
                status_id: newStatusId
            },
            dataType: 'json'
        })
            .done(function (resp) {
                if (resp && resp.success) {
                    toastr.success('Order status updated successfully');
                    $('#statusUpdateModal').modal('hide');
                    // Reload the page to show updated status
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(resp && resp.message ? resp.message : 'Update failed');
                }
            })
            .fail(function (xhr) {
                let msg = 'Server error';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            })
            .always(function () {
                $btn.prop('disabled', false).text('Update Status');
            });
    });

    // SweetAlert2 delete handler
    $(document).on('click', '.js-order-delete-btn', async function (e) {
        e.preventDefault();
        const $btn = $(this);
        const orderId = $btn.data('order-id');
        const customerName = $btn.data('order-customer');

        const confirmed = await Swal.fire({
            title: 'Delete Order?',
            html: `<p class="mb-1">You are about to delete order #${orderId} for <strong>${$('<div>').text(customerName).html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            reverseButtons: true,
            focusCancel: true,
        }).then(r => r.isConfirmed);

        if (!confirmed) return;

        $btn.prop('disabled', true).addClass('opacity-50');

        $.ajax({
            url: './include/ajax_order_delete.php',
            method: 'POST',
            data: {
                id: orderId,
            },
            dataType: 'json'
        })
            .done(function (resp) {
                if (resp && resp.success) {
                    toastr.success('Order deleted');
                    // Remove row from table
                    const $row = $btn.closest('tr');
                    $row.fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    toastr.error(resp && resp.message ? resp.message : 'Delete failed');
                }
            })
            .fail(function (xhr) {
                let msg = 'Server error';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            })
            .always(function () {
                $btn.prop('disabled', false).removeClass('opacity-50');
            });
    });

}); // end of document ready
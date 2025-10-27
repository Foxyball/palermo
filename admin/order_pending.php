<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
require_once(__DIR__ . '/../include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

$pendingStatusStmt = $pdo->prepare('SELECT id FROM order_statuses WHERE LOWER(name) = "pending" LIMIT 1');
$pendingStatusStmt->execute();
$pendingStatus = $pendingStatusStmt->fetch(PDO::FETCH_ASSOC);

if (!$pendingStatus) {
    $_SESSION['error'] = 'Pending status not found in order_statuses table';
    header('Location: order_list');
    exit;
}

$pendingStatusId = $pendingStatus['id'];

$whereSql = ' WHERE o.status_id = :pending_status_id';
$params = [':pending_status_id' => $pendingStatusId];

if ($search !== '') {
    $whereSql .= ' AND (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
    $params[':keyword'] = '%' . $search . '%';
    $params[':id'] = $search;
}

$countSql = 'SELECT COUNT(*) FROM orders o LEFT JOIN users u ON o.user_id = u.id' . $whereSql;
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$totalPendingOrdersCount = $stmtCount->fetchColumn();

$paginator = new Paginator($totalPendingOrdersCount, $page, $perPage);

$dataSql = 'SELECT 
            o.id, 
            o.user_id,
            o.amount,
            o.status_id,
            o.created_at,
            CONCAT(u.first_name, " ", u.last_name) AS customer_name,
            u.email AS customer_email,
            os.name AS status_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_statuses os ON o.status_id = os.id '
    . $whereSql . ' ORDER BY o.created_at DESC LIMIT :lim OFFSET :off';

$stmt = $pdo->prepare($dataSql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':lim', $paginator->limit(), PDO::PARAM_INT);
$stmt->bindValue(':off', $paginator->offset(), PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusesStmt = $pdo->prepare('SELECT id, name FROM order_statuses WHERE active = "1" ORDER BY id ASC');
$statusesStmt->execute();
$availableStatuses = $statusesStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php
headerContainer();
?>

<title>Pending Orders | <?php echo SITE_TITLE; ?></title>

</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php navbarContainer(); ?>

        <?php sidebarContainer(); ?>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Pending Orders</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="order_list">All Orders</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Pending Orders</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">


                            <div class="card">
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <h3 class="card-title mb-0">Pending Orders</h3>
                                        <form class="d-flex" method="GET" action="order_pending" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control"
                                                        placeholder="Search order ID, customer name, email" value="<?php echo htmlspecialchars($search); ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="order_pending"
                                                        title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="card-tools ms-md-auto">
                                        <div class="btn-group" role="group">
                                            <a href="order_export.php?status=pending<?php echo $search !== '' ? '&q=' . urlencode($search) : ''; ?>" 
                                               class="btn btn-success btn-sm" 
                                               title="Export Pending Orders to Excel">
                                                <i class="bi bi-file-earmark-excel"></i> Export
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Customer</th>
                                                    <th>Amount</th>
                                                    <th>Order Status</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($orders)) { ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="bi bi-clipboard-x-fill fs-1"></i>
                                                                <p class="mt-2">No pending orders found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php foreach ($orders as $order) { ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong>#<?php echo $order['id']; ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div>
                                                                    <?php if (!empty($order['customer_name']) && trim($order['customer_name']) !== '') { ?>
                                                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                                                    <?php } else { ?>
                                                                        <span class="text-muted">Guest Customer</span>
                                                                    <?php } ?>
                                                                    <?php if (!empty($order['customer_email'])) { ?>
                                                                        <br><small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div>
                                                                    <span class="fw-bold text-success"><?php echo formatOrderPrice($order['amount']); ?></span>
                                                                    <br><small class="text-muted"><?php echo formatOrderPrice($order['amount'], 'EUR'); ?></small>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span class="badge bg-warning text-dark">
                                                                    <?php echo htmlspecialchars($order['status_name']); ?>
                                                                </span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <small class="text-muted">
                                                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                                                    <br>
                                                                    <?php echo date('g:i A', strtotime($order['created_at'])); ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <a href="order_show?id=<?php echo $order['id']; ?>"
                                                                        class="btn btn-sm btn-outline-info" title="View Details">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-success js-order-status-update-btn"
                                                                        data-order-id="<?php echo $order['id']; ?>"
                                                                        data-current-status-id="<?php echo $order['status_id']; ?>"
                                                                        title="Update Status">
                                                                        <i class="bi bi-arrow-repeat"></i>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger js-order-delete-btn"
                                                                        data-order-id="<?php echo $order['id']; ?>"
                                                                        data-order-customer="<?php echo htmlspecialchars($order['customer_name'] ?: 'Guest Customer'); ?>"
                                                                        title="Delete Order">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if (!empty($orders)) { ?>
                                    <div class="card-footer">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">
                                                    <?php
                                                    $start = $paginator->offset() + 1;
                                                    $end = $paginator->offset() + count($orders);
                                                    ?>
                                                    <?php if ($totalPendingOrdersCount > 0) { ?>
                                                        Showing <?php echo $start; ?>–<?php echo $end; ?> of <?php echo $totalPendingOrdersCount; ?>
                                                    <?php } else { ?>
                                                        No results
                                                    <?php } ?>
                                                </small>
                                                <?php if ($search !== '') { ?>
                                                    <small class="text-muted">Filtered by "<?php echo htmlspecialchars($search); ?>"</small>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-4 text-md-center">
                                                <nav aria-label="Pagination">
                                                    <ul class="pagination pagination-sm mb-0 justify-content-center">
                                                        <li class="page-item <?php echo !$paginator->hasPrev() ? 'disabled' : ''; ?>">
                                                            <a class="page-link"
                                                                href="<?php echo buildPageUrl(max(1, $paginator->currentPage - 1), 'order_pending'); ?>"
                                                                tabindex="-1">&laquo;</a>
                                                        </li>
                                                        <?php foreach ($paginator->pages() as $pg) { ?>
                                                            <li class="page-item <?php echo ($pg === $paginator->currentPage) ? 'active' : ''; ?>">
                                                                <a class="page-link"
                                                                    href="<?php echo buildPageUrl($pg, 'order_pending'); ?>"><?php echo $pg; ?></a>
                                                            </li>
                                                        <?php } ?>
                                                        <li class="page-item <?php echo !$paginator->hasNext() ? 'disabled' : ''; ?>">
                                                            <a class="page-link"
                                                                href="<?php echo buildPageUrl(min($paginator->totalPages, $paginator->currentPage + 1), 'order_pending'); ?>">&raquo;</a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                            <div class="col-md-4 text-md-end">
                                                <small class="text-muted d-block">Last
                                                    updated: <?php echo date('M j, Y g:i A'); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusUpdateModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="statusUpdateForm">
                        <input type="hidden" id="order_id" name="order_id">
                        <div class="mb-3">
                            <label for="new_status" class="form-label">New Status</label>
                            <select class="form-select" id="new_status" name="new_status" required>
                                <option value="">-- Select Status --</option>
                                <?php foreach ($availableStatuses as $status) { ?>
                                    <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Update Status</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
        }
    </style>

    <!-- AJAX Status Update -->
    <script>
        $(function() {
            // Status update handler
            $(document).on('click', '.js-order-status-update-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const orderId = $btn.data('order-id');
                const currentStatusId = $btn.data('current-status-id');

                $('#order_id').val(orderId);
                $('#new_status').val(currentStatusId);
                $('#statusUpdateModal').modal('show');
            });

            // Confirm status update
            $('#confirmStatusUpdate').on('click', function() {
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
                    .done(function(resp) {
                        if (resp && resp.success) {
                            toastr.success('Order status updated successfully');
                            $('#statusUpdateModal').modal('hide');
                            // Reload the page to show updated status
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(resp && resp.message ? resp.message : 'Update failed');
                        }
                    })
                    .fail(function(xhr) {
                        let msg = 'Server error';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text('Update Status');
                    });
            });

            // SweetAlert2 delete handler
            $(document).on('click', '.js-order-delete-btn', async function(e) {
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
                    .done(function(resp) {
                        if (resp && resp.success) {
                            toastr.success('Order deleted');
                            // Remove row from table
                            const $row = $btn.closest('tr');
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            toastr.error(resp && resp.message ? resp.message : 'Delete failed');
                        }
                    })
                    .fail(function(xhr) {
                        let msg = 'Server error';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).removeClass('opacity-50');
                    });
            });
        });
    </script>
</body>

</html>
<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
require_once(__DIR__ . '/../repositories/admin/OrderStatusRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;


$orderStatusRepository = new OrderStatusRepository($pdo);
$totalStatusesCount = $orderStatusRepository->countAll($search);

$paginator = new Paginator($totalStatusesCount, $page, $perPage);
$orderStatuses = $orderStatusRepository->findAll($search, $paginator->limit(), $paginator->offset());

?>

<?php
headerContainer();
?>

<title>Order Statuses | <?php echo SITE_TITLE; ?></title>

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
                            <h3 class="mb-0">Order Status Management</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Order Status List</li>
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
                                        <h3 class="card-title mb-0">All Order Statuses</h3>
                                        <form class="d-flex" method="GET" action="order_status_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control" placeholder="Search name, id" value="<?php echo $search; ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="order_status_list" title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="card-tools ms-md-auto">
                                        <a href="order_status_add" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus"></i> Add New Status
                                        </a>
                                    </div>

                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Status Name</th>
                                                    <th>Active</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($orderStatuses)) { ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="bi bi-list-ul fs-1"></i>
                                                                <p class="mt-2">No order statuses found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php foreach ($orderStatuses as $status) { ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong>#<?php echo $status['id']; ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo htmlspecialchars($status['name']); ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input js-order-status-toggle" 
                                                                           type="checkbox" 
                                                                           data-status-id="<?php echo $status['id']; ?>" 
                                                                           <?php echo $status['active'] == '1' ? 'checked' : ''; ?>>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <small class="text-muted">
                                                                    <?php echo date('M j, Y', strtotime($status['created_at'])); ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <a href="order_status_edit?id=<?php echo $status['id']; ?>" 
                                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger js-order-status-delete-btn"
                                                                            data-status-id="<?php echo $status['id']; ?>"
                                                                            data-status-name="<?php echo htmlspecialchars($status['name']); ?>">
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
                                <?php if (!empty($orderStatuses)) { ?>
                                    <?php renderPagination($paginator, $totalStatusesCount, count($orderStatuses), 'order_status_list', $search); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <!-- AJAX Change Status -->
    <script>
        $(function () {
            $('.js-order-status-toggle:not(:disabled)').on('change', function () {
                const $cb = $(this);
                const statusId = $cb.data('status-id');
                const originalChecked = !$cb.prop('checked');
                $cb.prop('disabled', true);

                $.ajax({
                    url: './include/ajax_order_toggle_status.php',
                    method: 'POST',
                    data: {
                        id: statusId,
                    },
                    dataType: 'json'
                })
                    .done(function (resp) {
                        if (!resp || resp.success !== true) {
                            $cb.prop('checked', originalChecked);
                            toastr.error(resp && resp.message ? resp.message : 'Failed to update status');
                        } else {
                            toastr.success('Status updated');
                        }
                    })
                    .fail(function (xhr) {
                        $cb.prop('checked', originalChecked);
                        let msg = 'Network / server error';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    })
                    .always(function () {
                        $cb.prop('disabled', false);
                    });
            });

            // SweetAlert2 delete handler
            $(document).on('click', '.js-order-status-delete-btn', async function (e) {
                e.preventDefault();
                const $btn = $(this);
                const statusId = $btn.data('status-id');
                const statusName = $btn.data('status-name');

                const confirmed = await Swal.fire({
                    title: 'Delete Order Status?',
                    html: `<p class="mb-1">You are about to delete <strong>${$('<div>').text(statusName).html()}</strong>.</p><small class="text-danger">This action cannot be undone.</small>`,
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
                    url: './include/ajax_order_status_delete.php',
                    method: 'POST',
                    data: {
                        id: statusId,
                    },
                    dataType: 'json'
                })
                    .done(function (resp) {
                        if (resp && resp.success) {
                            toastr.success('Order status deleted');
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
        });
    </script>
</body>

</html>

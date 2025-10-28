<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
require_once(__DIR__ . '/../include/functions.php');
require_once(__DIR__ . '/../repositories/admin/OrderRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$orderRepo = new OrderRepository($pdo);

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

$totalOrdersCount = $orderRepo->countAll($search);
$paginator = new Paginator($totalOrdersCount, $page, $perPage);
$orders = $orderRepo->findAll($search, $paginator->limit(), $paginator->offset());

$availableStatuses = $orderRepo->getActiveStatuses();
?>

<?php
headerContainer();
?>

<title>Orders | <?php echo SITE_TITLE; ?></title>

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
                            <h3 class="mb-0">Orders Management</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Orders List</li>
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
                                        <h3 class="card-title mb-0">All Orders</h3>
                                        <form class="d-flex" method="GET" action="order_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control"
                                                        placeholder="Search order ID, customer name, email" value="<?php echo htmlspecialchars($search); ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="order_list"
                                                        title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>


                                    <div class="card-tools ms-md-auto">
                                        <div class="btn-group" role="group">
                                            <a href="order_export_generate.php<?php echo $search !== '' ? '?q=' . urlencode($search) : ''; ?>"
                                                class="btn btn-success btn-sm"
                                                title="Export to Excel">
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
                                                                <p class="mt-2">No orders found</p>
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
                                                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                                                    <br><small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div>
                                                                    <span class="fw-bold text-success"><?php echo formatOrderPrice($order['amount'], true); ?></span>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <?php echo getStatusBadge($order['status_name']); ?>
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
                                                                        data-order-customer="<?php echo htmlspecialchars($order['customer_name']); ?>"
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
                                    <?php renderPagination($paginator, $totalOrdersCount, count($orders), 'order_list', $search); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <?php include(__DIR__ . '/partials/order_status_modal.php'); ?>

    <script src="js/palermoAdminCrud.js"></script>
</body>

</html>
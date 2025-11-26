<?php
session_start();
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/repositories/frontend/UserOrderRepository.php');
include(__DIR__ . '/include/html_functions.php');

require_user_login();

$userId = $_SESSION['user_id'];
$userOrderRepo = new UserOrderRepository($pdo);

$orders = $userOrderRepo->getUserOrders($userId);

$pageTitle = 'My Orders - ' . SITE_TITLE;
?>

<?php headerContainer(); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/orders.css">
</head>

<body class="stretched">

    <div class="body-overlay"></div>

    <div id="side-panel" class="dark" style="background: #101010 url('<?php echo BASE_URL; ?>images/icon-bg-white.png') repeat center center;"></div>

    <div id="wrapper" class="clearfix">
        <?php navbarContainer(); ?>

        <!-- MAIN CONTENT -->
        <section id="content" class="dark-color">
            <div class="content-wrap">

                <div class="section dark m-0" style="padding: 80px 0; background: #1a1a1a;">
                    <div class="container">

                        <!-- Page Header -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h1 class="page-title mb-2">My Orders</h1>
                                <p class="text-muted">View your order history and track your orders</p>
                            </div>
                        </div>

                        <?php if (empty($orders)): ?>
                            <!-- No Orders -->
                            <div class="row justify-content-center">
                                <div class="col-lg-6">
                                    <div class="empty-orders-message text-center">
                                        <h3 class="mb-3">No orders yet</h3>
                                        <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping!</p>
                                        <a href="<?php echo BASE_URL; ?>product-cat/all" class="btn btn-danger btn-lg">
                                            Browse Menu
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Orders List -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="orders-container">
                                        <?php foreach ($orders as $order): ?>
                                            <div class="order-card">
                                                <div class="order-header">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6 mb-3 mb-md-0">
                                                            <h5 class="order-id mb-2">
                                                                Order #<?php echo $order['id']; ?>
                                                            </h5>
                                                            <small class="text-muted">
                                                                <?php echo date('F j, Y - g:i A', strtotime($order['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <div class="col-md-6 text-md-end">
                                                            <div class="mb-2">
                                                                <span class="order-status <?php echo getStatusClass($order['status_name']); ?>">
                                                                    <?php echo $order['status_name']; ?>
                                                                </span>
                                                            </div>
                                                            <div class="order-total">
                                                                <strong><?php echo formatOrderPrice($order['amount']); ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="order-footer">
                                                    <a href="<?php echo BASE_URL; ?>order-detail?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-light">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

</body>

</html>

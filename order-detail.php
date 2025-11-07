<?php
session_start();
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/repositories/frontend/UserOrderRepository.php');
include(__DIR__ . '/include/html_functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: ' . BASE_URL . 'orders');
    exit;
}

$userOrderRepo = new UserOrderRepository($pdo);
$order = $userOrderRepo->getUserOrderDetails($orderId, $userId);

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: ' . BASE_URL . 'orders');
    exit;
}

$items = $order['items'] ?? [];

$pageTitle = 'Order #' . $order['id'] . ' - ' . SITE_TITLE;
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
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h1 class="page-title mb-2">Order #<?php echo $order['id']; ?></h1>
                                        <p class="text-muted mb-0">
                                            <i class="icon-line-calendar me-1"></i>
                                            <?php echo date('F j, Y - g:i A', strtotime($order['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <a href="<?php echo BASE_URL; ?>orders" class="btn btn-outline-light">
                                            Back to Orders
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Order Info -->
                            <div class="col-lg-8 mb-4">
                                <!-- Order Status & Total -->
                                <div class="order-card mb-4">
                                    <div class="order-header">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <h6 class="mb-2 text-muted">Status</h6>
                                                <span class="order-status <?php echo getStatusClass($order['status_name']); ?>">
                                                    <?php echo htmlspecialchars($order['status_name']); ?>
                                                </span>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <h6 class="mb-2 text-muted">Total Amount</h6>
                                                <div class="order-total text-success">
                                                    <strong><?php echo formatOrderPrice($order['amount']); ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Address -->
                                <?php if ($order['order_address']): ?>
                                    <div class="order-card mb-4">
                                        <div class="order-header">
                                            <h5 class="mb-0">
                                                <i class="icon-line-map-marker me-2"></i>Delivery Address
                                            </h5>
                                        </div>
                                        <div class="order-body">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['order_address'])); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Order Notes -->
                                <?php if ($order['message']): ?>
                                    <div class="order-card mb-4">
                                        <div class="order-header">
                                            <h5 class="mb-0">
                                                <i class="icon-line-note me-2"></i>Order Notes
                                            </h5>
                                        </div>
                                        <div class="order-body">
                                            <p class="mb-0 text-muted"><?php echo nl2br(htmlspecialchars($order['message'])); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Order Items -->
                            <div class="col-lg-4">
                                <div class="order-card">
                                    <div class="order-header">
                                        <h5 class="mb-0">
                                            <i class="icon-line-basket me-2"></i>Order Items
                                        </h5>
                                    </div>
                                    <div class="order-body">
                                        <?php if (empty($items)): ?>
                                            <p class="text-muted text-center mb-0">No items found</p>
                                        <?php else: ?>
                                            <div class="order-items-list">
                                                <?php 
                                                $grandTotal = 0;
                                                foreach ($items as $item): 
                                                    $addonTotal = 0;
                                                    foreach ($item['addons'] as $addon) {
                                                        $addonTotal += (float)$addon['addon_price'];
                                                    }
                                                    $effectiveUnitPrice = $item['unit_price'] + $addonTotal;
                                                    $lineTotal = $effectiveUnitPrice * $item['qty'];
                                                    $grandTotal += $lineTotal;
                                                ?>
                                                    <div class="order-item-detail mb-3 pb-3 border-bottom">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name'] ?: 'Unknown Product'); ?></h6>
                                                                <small class="text-muted">
                                                                    <?php echo $item['qty']; ?> × <?php echo formatOrderPrice($effectiveUnitPrice, false); ?>
                                                                </small>
                                                            </div>
                                                            <div class="text-end">
                                                                <strong><?php echo formatOrderPrice($lineTotal, false); ?></strong>
                                                            </div>
                                                        </div>
                                                        <?php if (!empty($item['addons'])): ?>
                                                            <div class="item-addons">
                                                                <?php foreach ($item['addons'] as $addon): ?>
                                                                    <small class="d-block text-muted">
                                                                        + <?php echo htmlspecialchars($addon['addon_name']); ?>
                                                                        (<?php echo formatOrderPrice($addon['addon_price'], false); ?>)
                                                                    </small>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="order-total-section pt-3 border-top">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Total:</strong>
                                                    <strong class="text-success"><?php echo formatOrderPrice($grandTotal); ?></strong>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

</body>

</html>

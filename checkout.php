<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/Cart.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

require_user_login();

$cart = new Cart($pdo);
$cartData = $cart->getData();
$items = $cartData['items'];
$cartTotal = $cartData['cart_total'];
$cartCount = $cartData['cart_count'];

if (empty($items)) {
    header('Location: ' . BASE_URL . 'cart');
    exit;
}

$userId = $_SESSION['user_id'];
$userFirstName = $_SESSION['user_first_name'] ?? '';
$userLastName = $_SESSION['user_last_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$phone = $_SESSION['user_phone'] ?? '';

$pageTitle = 'Checkout - ' . SITE_TITLE;
?>

<?php headerContainer(); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/checkout.css">
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
                            <div class="col-12 text-center">
                                <h1 class="page-title mb-2">Checkout</h1>
                                <p class="text-muted">Complete your order</p>
                            </div>
                        </div>

                        <form id="checkout-form" method="POST" action="<?php echo BASE_URL; ?>include/process_order.php">
                            <div class="row">

                                <div class="col-lg-8 mb-4">
                                    <div class="checkout-card">

                                        <div class="checkout-section mb-4">
                                            <h4 class="section-title mb-3">
                                                <i class="icon-line-user me-2"></i>Customer Information
                                            </h4>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="first_name"
                                                        name="first_name"
                                                        value="<?php echo htmlspecialchars($userFirstName); ?>"
                                                        readonly>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="last_name"
                                                        name="last_name"
                                                        value="<?php echo htmlspecialchars($userLastName); ?>"
                                                        readonly>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email"
                                                        class="form-control"
                                                        id="email"
                                                        name="email"
                                                        value="<?php echo htmlspecialchars($userEmail); ?>"
                                                        readonly>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                    <input type="tel"
                                                        class="form-control"
                                                        id="order_phone"
                                                        name="order_phone"
                                                        value="<?php echo htmlspecialchars($phone); ?>"
                                                        placeholder="Enter your phone number"
                                                        required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="checkout-section mb-4">
                                            <h4 class="section-title mb-3">
                                                <i class="icon-line-map-marker me-2"></i>Delivery Address
                                            </h4>
                                            <div class="mb-3">
                                                <label for="order_address" class="form-label">Address <span class="text-danger">*</span></label>
                                                <textarea class="form-control"
                                                    id="order_address"
                                                    name="order_address"
                                                    rows="3"
                                                    placeholder="Enter your full delivery address"
                                                    required></textarea>
                                                <small class="text-muted">Please provide your complete address including street, building number, city, and postal code</small>
                                            </div>
                                        </div>

                                        <div class="checkout-section">
                                            <h4 class="section-title mb-3">
                                                <i class="icon-line-note me-2"></i>Order Notes
                                            </h4>
                                            <div class="mb-3">
                                                <label for="message" class="form-label">Additional Notes (Optional)</label>
                                                <textarea class="form-control"
                                                    id="message"
                                                    name="message"
                                                    rows="3"
                                                    placeholder="Special instructions for your order (e.g., delivery time, allergies, etc.)"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="order-summary">
                                        <h4 class="summary-title mb-4">Order Summary</h4>

                                        <div class="order-items mb-4">
                                            <?php foreach ($items as $item) { ?>
                                                <div class="order-item mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <div class="order-item-image me-3">
                                                            <?php if (!empty($item['image'])) { ?>
                                                                <img src="<?php echo BASE_URL . $item['image']; ?>"
                                                                    alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                    class="img-fluid rounded">
                                                            <?php } else { ?>
                                                                <img src="<?php echo BASE_URL; ?>images/svg/burger-house.svg"
                                                                    alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                    class="img-fluid rounded placeholder-image">
                                                            <?php } ?>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="order-item-name mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>

                                                            <?php if (!empty($item['addons'])) { ?>
                                                                <div class="order-item-addons mb-1">
                                                                    <?php foreach ($item['addons'] as $addon) { ?>
                                                                        <small class="text-muted d-block">+ <?php echo htmlspecialchars($addon['name']); ?></small>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>

                                                            <small class="text-muted">
                                                                Qty: <?php echo $item['quantity']; ?> × <?php echo number_format($item['item_price'], 2); ?> лв
                                                            </small>
                                                        </div>
                                                        <div class="order-item-total">
                                                            <strong><?php echo number_format($item['item_total'], 2); ?> лв</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="order-total mb-4 pt-3 border-top">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Subtotal</span>
                                                <span><?php echo number_format($cartTotal, 2); ?> лв</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted"></span>
                                                <span class="text-muted"><?php echo number_format($cartTotal / BGN_TO_EUR_RATE, 2); ?> €</span>
                                            </div>
                                            <div class="d-flex justify-content-between pt-3 border-top">
                                                <strong style="font-size: 1.2rem;">Total</strong>
                                                <strong class="text-danger" style="font-size: 1.5rem;">
                                                    <?php echo number_format($cartTotal, 2); ?> лв
                                                </strong>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    <?php echo number_format($cartTotal / BGN_TO_EUR_RATE, 2); ?> €
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-danger btn-lg" id="place-order-btn">
                                                Place Order
                                            </button>
                                            <a href="<?php echo BASE_URL; ?>cart" class="btn btn-outline-light">
                                                Back to Cart
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

    <script src="<?php echo BASE_URL; ?>js/checkout.js"></script>

</body>

</html>
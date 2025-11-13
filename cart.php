<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/Cart.php');
include(__DIR__ . '/include/html_functions.php');

require_user_login();

$cart = new Cart($pdo);
$cartData = $cart->getData();
$items = $cartData['items'];
$cartTotal = $cartData['cart_total'];
$cartCount = $cartData['cart_count'];

$pageTitle = 'Shopping Cart - ' . SITE_TITLE;
?>

<?php headerContainer(); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/cart.css">
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
                                <h1 class="page-title mb-2">Shopping Cart</h1>
                                <p class="text-muted">Review your items before checkout</p>
                            </div>
                        </div>

                        <?php if (empty($items)): ?>
                            <!-- Empty Cart -->
                            <div class="row justify-content-center">
                                <div class="col-lg-6">
                                    <div class="empty-cart-message text-center">
                                        <h3 class="mb-3">Your cart is empty</h3>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>

                            <!-- Cart Items -->
                            <div class="row">
                                <div class="col-lg-8 mb-4">
                                    <div class="cart-items-container">
                                        <?php foreach ($items as $item): ?>
                                            <div class="cart-item" data-cart-key="<?php echo htmlspecialchars($item['key']); ?>">
                                                <div class="row align-items-center">

                                                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                                                        <div class="cart-item-image">
                                                            <?php if (!empty($item['image'])): ?>
                                                                <img src="<?php echo BASE_URL . $item['image']; ?>"
                                                                    alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                    class="img-fluid rounded">
                                                            <?php else: ?>
                                                                <img src="<?php echo BASE_URL; ?>images/svg/burger-house.svg"
                                                                    alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                    class="img-fluid rounded placeholder-image">
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                                                        <h5 class="cart-item-name mb-2">
                                                            <a href="<?php echo BASE_URL . 'art/' . $item['slug']; ?>"
                                                                class="text-white text-decoration-none">
                                                                <?php echo htmlspecialchars($item['name']); ?>
                                                            </a>
                                                        </h5>

                                                        <!-- Product Price -->
                                                        <div class="text-muted small mb-2">
                                                            Price: <?php echo number_format($item['price'], 2); ?> лв / <?php echo number_format($item['price'] / BGN_TO_EUR_RATE, 2); ?> €
                                                        </div>

                                                        <!-- Addons -->
                                                        <?php if (!empty($item['addons'])): ?>
                                                            <div class="cart-item-addons">
                                                                <small class="text-muted">Extras:</small>
                                                                <?php foreach ($item['addons'] as $addon): ?>
                                                                    <span class="badge bg-secondary">
                                                                        + <?php echo htmlspecialchars($addon['name']); ?>
                                                                        (<?php echo number_format($addon['price'], 2); ?> лв / <?php echo number_format($addon['price'] / BGN_TO_EUR_RATE, 2); ?> €)
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                                                        <div class="quantity-controls d-flex align-items-center justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-light qty-decrease"
                                                                data-cart-key="<?php echo htmlspecialchars($item['key']); ?>">
                                                                -
                                                            </button>
                                                            <input type="number"
                                                                class="form-control quantity-input mx-2 text-center"
                                                                value="<?php echo $item['quantity']; ?>"
                                                                min="1"
                                                                max="99"
                                                                data-cart-key="<?php echo htmlspecialchars($item['key']); ?>"
                                                                readonly>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-light qty-increase"
                                                                data-cart-key="<?php echo htmlspecialchars($item['key']); ?>">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 col-6 text-md-end">
                                                        <div class="cart-item-total mb-2">
                                                            <strong class="item-total-price">
                                                                <?php
                                                                $itemTotal = $item['item_price'] * $item['quantity'];
                                                                echo number_format($itemTotal, 2);
                                                                ?> лв
                                                            </strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?php
                                                                $itemTotalEur = $itemTotal / BGN_TO_EUR_RATE;
                                                                echo number_format($itemTotalEur, 2);
                                                                ?> €
                                                            </small>
                                                        </div>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger remove-item"
                                                            data-cart-key="<?php echo htmlspecialchars($item['key']); ?>">
                                                            <i class="icon-line-trash me-1"></i>Remove
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Cart Summary -->
                                <div class="col-lg-4">
                                    <div class="cart-summary">
                                        <h4 class="summary-title mb-4">Order Summary</h4>

                                        <div class="summary-details mb-4">
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="text-muted">Items</span>
                                                <span id="cart-count"><?php echo $cartCount; ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="text-muted">Subtotal</span>
                                                <span id="cart-subtotal-bgn"><?php echo number_format($cartTotal, 2); ?> лв</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                                <span class="text-muted"></span>
                                                <span class="text-muted" id="cart-subtotal-eur">
                                                    <?php echo number_format($cartTotal / BGN_TO_EUR_RATE, 2); ?> €
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <strong>Total</strong>
                                                <strong id="cart-total-bgn" class="text-danger" style="font-size: 1.5rem;">
                                                    <?php echo number_format($cartTotal, 2); ?> лв
                                                </strong>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted" id="cart-total-eur">
                                                    <?php echo number_format($cartTotal / BGN_TO_EUR_RATE, 2); ?> €
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <a href="<?php echo BASE_URL; ?>checkout" class="btn btn-danger btn-lg">
                                                Proceed to Checkout
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>product-cat/all" class="btn btn-outline-light">
                                                Continue Shopping
                                            </a>
                                        </div>
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

    <script src="js/cart-page.js"></script>

</body>

</html>
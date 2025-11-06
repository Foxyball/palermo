<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/repositories/frontend/ProductRepository.php');
include(__DIR__ . '/include/html_functions.php');

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . BASE_URL . '404');
    exit;
}

$productRepository = new ProductRepository($pdo);
$product = $productRepository->getBySlug($slug);

if (!$product) {
    header('Location: ' . BASE_URL . '404');
    exit;
}

$addons = $productRepository->getProductAddons($product['id']);

$pageTitle = htmlspecialchars($product['name']) . ' - ' . SITE_TITLE;
?>

<?php headerContainer(); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/product-detail.css">
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

                        <!-- Product Detail -->
                        <div class="row justify-content-center">
                            <div class="col-lg-10">

                                <div class="product-detail-card">
                                    <div class="row">

                                        <!-- Product Image -->
                                        <div class="col-md-5 mb-4 mb-md-0">
                                            <div class="product-image-container">
                                                <?php if (!empty($product['image'])) { ?>
                                                    <img src="<?php echo BASE_URL . $product['image']; ?>"
                                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                        class="img-fluid rounded product-image">
                                                <?php } else { ?>
                                                    <img src="<?php echo BASE_URL; ?>images/svg/burger-house.svg"
                                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                        class="img-fluid rounded product-image placeholder-image">
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="col-md-7">
                                            <div class="product-info">

                                                <!-- Category Badge -->
                                                <div class="mb-3">
                                                    <span class="badge bg-danger"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                                </div>

                                                <!-- Product Title -->
                                                <h1 class="product-title text-white mb-3">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </h1>

                                                <!-- Short Description -->
                                                <?php if (!empty($product['short_description'])) { ?>
                                                    <p class="product-short-description text-white-50 mb-4">
                                                        <?php echo htmlspecialchars($product['short_description']); ?>
                                                    </p>
                                                <?php } ?>

                                                <!-- Price -->
                                                <div class="product-price mb-4">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="price-value text-danger fw-bold fs-4">
                                                            <?php echo displayPrice($product['price']); ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <form id="add-to-cart-form" class="add-to-cart-form js-add-to-cart-form">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" class="js-base-price" value="<?php echo $product['price']; ?>">
                                                    <input type="hidden" class="js-bgn-to-eur-rate" value="<?php echo BGN_TO_EUR_RATE; ?>">
                                                    <input type="hidden" class="js-base-url" value="<?php echo BASE_URL; ?>">

                                                    <!-- Addons -->
                                                    <?php if (!empty($addons)) { ?>
                                                        <div class="product-addons mb-4">
                                                            <h5 class="text-white mb-3">Available Add-ons</h5>
                                                            <div class="addons-list">
                                                                <?php foreach ($addons as $addon) { ?>
                                                                    <div class="form-check addon-item">
                                                                        <input class="form-check-input addon-checkbox"
                                                                            type="checkbox"
                                                                            name="addons[]"
                                                                            value="<?php echo $addon['id']; ?>"
                                                                            id="addon_<?php echo $addon['id']; ?>"
                                                                            data-price="<?php echo $addon['price']; ?>">
                                                                        <label class="form-check-label text-white" for="addon_<?php echo $addon['id']; ?>">
                                                                            <?php echo htmlspecialchars($addon['name']); ?>
                                                                            <span class="text-danger">
                                                                                (+<?php echo displayPrice($addon['price']); ?>)
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <!-- Quantity -->
                                                    <div class="product-quantity mb-4">
                                                        <label for="quantity" class="form-label text-white">Quantity:</label>
                                                        <div class="quantity-control">
                                                            <button type="button" class="btn btn-outline-light btn-sm qty-btn js-qty-decrease">
                                                                -
                                                            </button>
                                                            <input type="number"
                                                                class="form-control form-control-sm text-center js-quantity"
                                                                name="quantity"
                                                                value="1"
                                                                min="1"
                                                                max="99">
                                                            <button type="button" class="btn btn-outline-light btn-sm qty-btn js-qty-increase">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Total Price Display -->
                                                    <div class="total-price-display mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="total-label text-white fw-semibold">Total:</span>
                                                            <span class="total-value text-white fw-bold fs-4 js-total-price">
                                                                <?php echo displayPrice($product['price']); ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Add to Cart Button -->
                                                    <div class="product-actions">
                                                        <button type="submit" class="btn btn-danger btn-lg w-100 add-to-cart-btn js-add-to-cart-btn">
                                                            Add to Cart
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>

                                    </div>

                                    <!-- Long Description -->
                                    <?php if (!empty($product['long_description'])) { ?>
                                        <div class="row mt-5">
                                            <div class="col-12">
                                                <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
                                                <h3 class="text-white mb-3">Description</h3>
                                                <div class="product-description text-white-50">
                                                    <?php echo $product['long_description']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>

    </div>

    <script src="js/product-detail.js"></script>

</body>

</html>
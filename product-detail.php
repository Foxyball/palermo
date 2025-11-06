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
                                                            <i class="icon-shopping-cart"></i>
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

    <script>
        $(document).ready(function() {
            const basePrice = parseFloat($('.js-base-price').val());
            const bgnToEurRate = parseFloat($('.js-bgn-to-eur-rate').val());

            // Function to calculate and update total price
            function updateTotalPrice() {
                const quantity = parseInt($('.js-quantity').val()) || 1;
                let totalPrice = basePrice * quantity;

                // Add selected addons
                $('.addon-checkbox:checked').each(function() {
                    const addonPrice = parseFloat($(this).data('price')) || 0;
                    totalPrice += addonPrice * quantity;
                });

                // Convert to EUR
                const priceEur = (totalPrice / bgnToEurRate).toFixed(2);
                const priceBgn = totalPrice.toFixed(2);

                // Add animation effect
                const $priceElement = $('.js-total-price');
                $priceElement.addClass('updating');
                
                setTimeout(function() {
                    $priceElement.text(priceBgn + ' лв / ' + priceEur + ' €');
                    $priceElement.removeClass('updating');
                }, 150);
            }

            // Quantity controls
            $('.js-qty-decrease').on('click', function() {
                const $qty = $('.js-quantity');
                const currentVal = parseInt($qty.val()) || 1;
                if (currentVal > 1) {
                    $qty.val(currentVal - 1);
                    updateTotalPrice();
                }
            });

            $('.js-qty-increase').on('click', function() {
                const $qty = $('.js-quantity');
                const currentVal = parseInt($qty.val()) || 1;
                const max = parseInt($qty.attr('max')) || 99;
                if (currentVal < max) {
                    $qty.val(currentVal + 1);
                    updateTotalPrice();
                }
            });

            // Validate quantity input
            $('.js-quantity').on('input', function() {
                let val = parseInt($(this).val()) || 1;
                const min = parseInt($(this).attr('min')) || 1;
                const max = parseInt($(this).attr('max')) || 99;
                
                if (val < min) val = min;
                if (val > max) val = max;
                
                $(this).val(val);
                updateTotalPrice();
            });

            // Update price when addons change
            $('.addon-checkbox').on('change', function() {
                updateTotalPrice();
            });

            // Add to cart form submission
            $('.js-add-to-cart-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                const $btn = $('.js-add-to-cart-btn');
                
                $btn.prop('disabled', true).html('<i class="icon-line-loader icon-spin"></i> Adding...');

                $.ajax({
                    url: '<?php echo BASE_URL; ?>include/cart_add.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json'
                })
                .done(function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Product added to cart!');
                        // Update cart count if you have one
                        if (response.cart_count) {
                            $('.cart-count').text(response.cart_count);
                        }
                    } else {
                        toastr.error(response.message || 'Failed to add product to cart');
                    }
                })
                .fail(function() {
                    toastr.error('An error occurred. Please try again.');
                })
                .always(function() {
                    $btn.prop('disabled', false).html('<i class="icon-shopping-cart"></i> Add to Cart');
                });
            });
        });
    </script>

</body>
</html>

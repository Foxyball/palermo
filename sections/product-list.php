<?php
$productsPerPage = 12;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalProducts = $productRepository->countByCategorySlug($categorySlug);

$paginator = new Paginator($totalProducts, $currentPage, $productsPerPage);
$products = $productRepository->getByCategorySlug($categorySlug, $paginator->limit(), $paginator->offset());
?>

<!-- Page Header -->
<div class="row mb-5">
    <div class="col-12 text-center">
        <div class="d-flex align-items-center justify-content-center dotted-bg mb-4">
            <h1 class="font-border display-4 ls1 fw-bold mb-0"><?php echo htmlspecialchars($currentCategory['name']); ?></h1>
        </div>
        <p class="lead text-white-50">Explore our delicious selection of <?php echo htmlspecialchars(strtolower($currentCategory['name'])); ?></p>
    </div>
</div>

<!-- Product List -->
<?php if (!empty($products)): ?>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 dark">
            <?php foreach ($products as $product): ?>
                <a href="<?php echo BASE_URL; ?>art/<?php echo $product['slug']; ?>" class="text-decoration-none">
                    <div class="price-menu-warp img-hover-block" data-img="<?php echo !empty($product['image']) ? BASE_URL . $product['image'] : BASE_URL . 'images/svg/items.svg'; ?>">
                        <div class="price-header">
                            <div class="price-name color"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="price-dots">
                                <span class="separator-dots"></span>
                            </div>
                            <div class="price-price"><?php echo displayPrice((float)$product['price']); ?></div>
                        </div>
                        <?php if (!empty($product['short_description'])): ?>
                            <p class="price-desc"><?php echo htmlspecialchars($product['short_description']); ?></p>
                        <?php endif; ?>
                        <div class="mt-2">
                            <span class="btn btn-outline-danger btn-sm">View Details</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($paginator->totalPages > 1): ?>
        <?php renderPagination($paginator, 'cat/' . $categorySlug); ?>
    <?php endif; ?>

<?php else: ?>
    <div class="row">
        <div class="col-12 text-center">
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i>
                No products available in this category at the moment. Check back soon!
            </div>
        </div>
    </div>
<?php endif; ?>

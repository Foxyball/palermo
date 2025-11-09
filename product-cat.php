<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/repositories/frontend/ProductRepository.php');
require_once(__DIR__ . '/repositories/frontend/CategoryRepository.php');
require_once(__DIR__ . '/admin/include/Paginator.php');
include(__DIR__ . '/include/html_functions.php');
require_once(__DIR__ . '/include/functions.php');

$categorySlug = $_GET['slug'] ?? '';

if (empty($categorySlug)) {
    header('Location: ' . BASE_URL);
    exit;
}

$categoryRepository = new CategoryRepository($pdo);
$categories = $categoryRepository->getActive();
$currentCategory = null;

foreach ($categories as $cat) {
    if ($cat['slug'] === $categorySlug) {
        $currentCategory = $cat;
        break;
    }
}

if (!$currentCategory) {
    header('Location: ' . BASE_URL . '404');
    exit;
}

$productRepository = new ProductRepository($pdo);
$pageTitle = htmlspecialchars($currentCategory['name']) . ' - ' . SITE_TITLE;

// Prepare SEO data
$seoData = [
    'title' => $pageTitle,
    'description' => 'Browse our ' . htmlspecialchars($currentCategory['name']) . ' menu at Palermo - Authentic Italian Pizza & Grill',
    'image' => !empty($currentCategory['image']) ? $currentCategory['image'] : 'images/palermo_logo.png',
    'url' => BASE_URL . 'cat/' . $categorySlug
];
?>

<?php headerContainer($seoData); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/product-cat.css">
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
                        <?php include(__DIR__ . '/sections/product-list.php'); ?>
                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

</body>

</html>

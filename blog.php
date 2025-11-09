<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/repositories/frontend/BlogRepository.php');
require_once(__DIR__ . '/admin/include/Paginator.php');
include(__DIR__ . '/include/html_functions.php');

$blogRepository = new BlogRepository($pdo);
$pageTitle = 'Blog - ' . SITE_TITLE;

// Prepare SEO data
$seoData = [
    'title' => $pageTitle,
    'description' => 'Read the latest news and articles from Palermo - Authentic Italian Pizza & Grill Restaurant',
    'url' => BASE_URL . 'blog'
];
?>

<?php headerContainer($seoData); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/latest-blog.css">
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
                        <?php include(__DIR__ . '/sections/blog-list.php'); ?>
                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

</body>

</html>

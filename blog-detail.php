<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/repositories/frontend/BlogRepository.php');
include(__DIR__ . '/include/html_functions.php');

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . BASE_URL . 'blog');
    exit;
}

$blogRepository = new BlogRepository($pdo);
$blog = $blogRepository->getBySlug($slug);

if (!$blog) {
    header('Location: ' . BASE_URL . '404');
    exit;
}

$galleryImages = [];
if (!empty($blog['gallery_id'])) {
    $galleryImages = $blogRepository->getGalleryImages($blog['gallery_id']);
}

$pageTitle = htmlspecialchars($blog['title']) . ' - ' . SITE_TITLE;
?>

<?php headerContainer(); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/blog-detail.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/fancybox.css">
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

                        <!-- Blog Content -->
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <article class="blog-post">

                                    <div class="blog-meta mb-3">
                                        <?php if (!empty($blog['category_name'])) { ?>
                                            <span class="badge bg-danger me-2"><?php echo htmlspecialchars($blog['category_name']); ?></span>
                                        <?php } ?>
                                        <span class="text-muted">
                                            <i class="bi bi-calendar3"></i>
                                            <?php echo date('F j, Y', strtotime($blog['created_at'])); ?>
                                        </span>
                                    </div>

                                    <h1 class="blog-title text-white mb-4">
                                        <?php echo htmlspecialchars($blog['title']); ?>
                                    </h1>

                                    <?php if (!empty($blog['image'])) { ?>
                                        <div class="blog-image mb-4">
                                            <img src="<?php echo BASE_URL . $blog['image']; ?>"
                                                alt="<?php echo $blog['title']; ?>"
                                                class="img-fluid rounded">
                                        </div>
                                    <?php } ?>

                                    <div class="blog-content text-white-50">
                                        <?php echo $blog['description']; ?>
                                    </div>

                                    <?php if (!empty($galleryImages)) { ?>
                                        <div class="blog-gallery mt-5">
                                            <div class="row g-3">
                                                <?php foreach ($galleryImages as $imagePath) { ?>
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="<?php echo BASE_URL . $imagePath; ?>"
                                                            data-fancybox="gallery"
                                                            data-caption="Gallery image">
                                                            <div class="gallery-item">
                                                                <img src="<?php echo BASE_URL . $imagePath; ?>"
                                                                    alt="Gallery image"
                                                                    class="img-fluid rounded">
                                                            </div>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </article>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

    <script src="<?php echo BASE_URL; ?>js/fancybox.umd.js"></script>
    <script>
        Fancybox.bind('[data-fancybox="gallery"]', {
            Thumbs: {
                type: "classic",
            },
            Toolbar: {
                display: {
                    left: [],
                    middle: [],
                    right: ["close"],
                },
            },
        });
    </script>

</body>

</html>
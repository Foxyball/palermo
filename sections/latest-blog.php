<?php
// Fetch latest blog posts
$blogRepository = new BlogRepository($pdo);
$latestBlogs = $blogRepository->getLatest(3);
?>

<div id="blog" class="section dark m-0">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <div class="d-flex align-items-center justify-content-center dotted-bg mb-4">
                    <h1 class="font-border display-4 ls1 fw-bold mb-0">Latest News</h1>
                </div>
                <p class="lead text-white-50">Discover the latest news, recipes, and stories from our kitchen</p>
            </div>
        </div>

        <?php if (!empty($latestBlogs)) { ?>
            <div class="row">
                <?php foreach ($latestBlogs as $blog) { ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="<?php echo BASE_URL; ?>bart/<?php echo $blog['slug']; ?>" class="text-decoration-none">
                            <div class="card bg-dark border-secondary h-100 blog-card-clickable">
                                <?php if (!empty($blog['image'])) { ?>
                                    <img src="<?php echo BASE_URL . $blog['image']; ?>"
                                        class="card-img-top"
                                        alt="<?php echo htmlspecialchars($blog['title']); ?>">
                                <?php } else { ?>
                                    <div class="card-img-top placeholder bg-secondary">
                                        <i class="bi bi-newspaper"></i>
                                    </div>
                                <?php } ?>

                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <?php if (!empty($blog['category_name'])): ?>
                                            <span class="badge bg-danger mb-2"><?php echo htmlspecialchars($blog['category_name']); ?></span>
                                        <?php endif; ?>
                                        <small class="text-muted d-block">
                                            <?php echo date('F j, Y', strtotime($blog['created_at'])); ?>
                                        </small>
                                    </div>

                                    <h5 class="card-title text-white">
                                        <?php echo htmlspecialchars($blog['title']); ?>
                                    </h5>

                                    <p class="card-text text-white-50 flex-grow-1">
                                        <?php
                                        $description = strip_tags($blog['description']);
                                        echo htmlspecialchars(mb_substr($description, 0, 120)) . (mb_strlen($description) > 120 ? '...' : '');
                                        ?>
                                    </p>

                                    <span class="btn btn-outline-light btn-sm mt-auto">
                                        Read More
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>

            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="<?php echo BASE_URL; ?>blog" class="btn btn-danger btn-lg">View All Articles</a>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-white-50">No blog posts available at the moment. Check back soon!</p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
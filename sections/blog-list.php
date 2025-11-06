<?php
$search = $_GET['search'] ?? '';
$blogsPerPage = 9;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalBlogs = $blogRepository->countAll($search);

$paginator = new Paginator($totalBlogs, $currentPage, $blogsPerPage);

$blogs = $blogRepository->getAll($search, $paginator->limit(), $paginator->offset());
?>

<div class="row mb-5">
    <div class="col-12 text-center">
        <div class="d-flex align-items-center justify-content-center dotted-bg mb-4">
            <h1 class="font-border display-4 ls1 fw-bold mb-0">Our Blog</h1>
        </div>
        <p class="lead text-white-50">Discover the latest news, recipes, and stories from our kitchen</p>

        <!-- Search Form -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-6 col-lg-5">
                <form method="GET" action="<?php echo BASE_URL; ?>blog" class="position-relative">
                    <div class="input-group input-group-lg">
                        <input type="text"
                            name="search"
                            class="form-control bg-dark text-white border-secondary"
                            placeholder="Search articles..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            aria-label="Search blog posts">
                        <button class="btn btn-danger" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($blogs)) { ?>
    <div class="row">
        <?php foreach ($blogs as $blog) { ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo BASE_URL; ?>bart/<?php echo $blog['slug']; ?>" class="text-decoration-none">
                    <div class="card bg-dark border-secondary h-100 blog-card-clickable">
                        <?php if (!empty($blog['image'])) {  ?>
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
                                <?php if (!empty($blog['category_name'])) { ?>
                                    <span class="badge bg-danger mb-2"><?php echo htmlspecialchars($blog['category_name']); ?></span>
                                <?php } ?>
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

    <?php if (!empty($blogs)) { ?>
        <?php renderPagination($paginator, 'blog', 2, $search); ?>
    <?php } ?>

<?php } else { ?>
    <div class="row">
        <div class="col-12 text-center">

            No blog posts available

        </div>
    </div>
    </div>
<?php } ?>
<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/include/Paginator.php');
require_once(__DIR__ . '/../repositories/admin/BlogRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$blogRepo = new BlogRepository($pdo);

$search = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;
$perPage = 10;

$totalBlogsCount = $blogRepo->countAll($search);

$paginator = new Paginator($totalBlogsCount, $page, $perPage);

$blogs = $blogRepo->findAll($search, $paginator->limit(), $paginator->offset());

?>

<?php
headerContainer();
?>

<title>Blog | <?php echo SITE_TITLE; ?></title>

</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php navbarContainer(); ?>

        <?php sidebarContainer(); ?>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Admin Management</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Blog List</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <h3 class="card-title mb-0">All Blog</h3>
                                        <form class="d-flex" method="GET" action="blog_list" role="search">
                                            <div class="input-group input-group-sm">
                                                <label>
                                                    <input type="text" name="q" class="form-control" placeholder="Search title, id" value="<?php echo $search; ?>" />
                                                </label>
                                                <button class="btn btn-outline-secondary" type="submit" title="Search">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <?php if ($search !== '') { ?>
                                                    <a class="btn btn-outline-danger" href="blog_list" title="Clear search">&times;</a>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="card-tools ms-md-auto">
                                        <a href="blog_add" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus"></i> Add New Post
                                        </a>
                                    </div>

                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Image</th>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th>Author</th>
                                                    <th>Gallery</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($blogs)) { ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="bi bi-person-x-fill fs-1"></i>
                                                                <p class="mt-2">No posts found</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <?php foreach ($blogs as $blog) { ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <strong>#<?php echo $blog['id']; ?></strong>
                                                            </td>
                                                            <td class="align-middle">
                                                                <img src="<?php echo '<?php echo BASE_URL; ?>' . htmlspecialchars($blog['image']); ?>" alt="img" style="max-width:100px; max-height:80px; object-fit:cover;" />
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo $blog['title']; ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo $blog['category_name'] ?? 'N/A'; ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo $blog['admin_name'] ?? 'N/A'; ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <?php echo $blog['gallery_title'] ?? 'N/A'; ?>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input js-blog-status-toggle" type="checkbox" data-blog-id="<?php echo $blog['id']; ?>" <?php echo $blog['status'] == '1' ? 'checked' : ''; ?>>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <small class="text-muted">
                                                                    <?php echo date('M j, Y', strtotime($blog['created_at'])); ?>
                                                                </small>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <a href="blog_edit?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger js-blog-delete-btn"
                                                                        data-blog-id="<?php echo $blog['id']; ?>"
                                                                        data-blog-name="<?php echo ($blog['title']); ?>">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if (!empty($blogs)) { ?>
                                    <?php renderPagination($paginator, $totalBlogsCount, count($blogs), 'blog_list', $search); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

    </div>

    <script src="js/palermoAdminCrud.js"></script>
</body>

</html>
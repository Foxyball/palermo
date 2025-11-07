<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/BlogCategoryRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$errors = [];
$blogCategoryRepo = new BlogCategoryRepository($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Blog category name is required';
    }

    if (empty($errors)) {
        if ($blogCategoryRepo->nameExists($name)) {
            $errors[] = 'Blog category name already exists.';
        }
    }

    if (empty($errors)) {
        try {
            $blogCategoryRepo->create($name, '1');
            $_SESSION['success'] = 'Blog category created successfully';
            header('Location: blog_category_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error creating the blog category. Please try again.';
        }
    }
}

headerContainer();
?>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php navbarContainer(); ?>
        <?php sidebarContainer(); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Add New Blog Category</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="blog_category_list">Blog Category List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Blog Category</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">Create Blog Category</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($errors)) { ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $err) { ?>
                                                    <li><?php echo htmlspecialchars($err); ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                    <form method="post" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Blog Category Name *</label>
                                            <input type="text" name="name" class="form-control"
                                                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required
                                                placeholder="e.g. News">
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="blog_category_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Create Blog Category</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php footerContainer(); ?>
    </div>
</body>

</html>
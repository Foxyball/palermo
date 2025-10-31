<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/CategoryRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($categoryId <= 0) {
    $_SESSION['error'] = 'Invalid category selected.';
    header('Location: category_list');
    exit;
}

$categoryRepository = new CategoryRepository($pdo);
$categoryToEdit = $categoryRepository->findById($categoryId);

if (!$categoryToEdit) {
    $_SESSION['error'] = 'Category not found.';
    header('Location: category_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = generateSlug($name);

    // Validation
    if ($name === '') {
        $errors[] = 'Category name is required';
    }

    if (empty($errors)) {
        if ($categoryRepository->nameExists($name, $categoryId)) {
            $errors[] = 'Category name already exists. Please choose a different name.';
        }
    }

    if (empty($errors)) {
        try {
            $categoryRepository->update($categoryId, $name, $slug);
            $_SESSION['success'] = 'Category updated successfully';
            header('Location: category_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating the category. Please try again.';
        }
    }
} else {
    $name = $categoryToEdit['name'];
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Edit Category</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="category_list">Category List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Update Category</h3>
                                    <span class="badge text-bg-<?php echo ($categoryToEdit['active'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($categoryToEdit['active'] == '1') ? 'Active' : 'Inactive'; ?>
                                    </span>
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
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Category Name *</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required placeholder="e.g. Main Dishes">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Slug</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($categoryToEdit['slug']); ?>" readonly disabled>
                                            <small class="text-muted">URL-friendly</small>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="category_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update Category</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    Created: <?php echo date('M j, Y g:i A', strtotime($categoryToEdit['created_at'])); ?>
                                    | Last Updated: <?php echo date('M j, Y g:i A', strtotime($categoryToEdit['updated_at'])); ?>
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
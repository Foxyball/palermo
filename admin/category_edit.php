<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$categoryId = $_GET['id'] ?? 0;
if ($categoryId <= 0) {
    $_SESSION['error'] = 'Invalid category selected.';
    header('Location: category_list');
    exit;
}

$stmt = $pdo->prepare('SELECT id, name, slug, active, created_at, updated_at FROM categories WHERE id = ? LIMIT 1');
$stmt->execute([$categoryId]);
$category_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category_to_edit) {
    $_SESSION['error'] = 'Category not found.';
    header('Location: category_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Category name is required';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ? AND id != ? LIMIT 1');
        $stmt->execute([$name, $categoryId]);
        if ($stmt->fetch()) {
            $errors[] = 'Category name already exists. Please choose a different name.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('UPDATE categories SET name = ?, updated_at = NOW() WHERE id = ? LIMIT 1');
            $stmt->execute([$name, $categoryId]);
            $_SESSION['success'] = 'Category updated successfully';
            header('Location: category_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating the category. Please try again.';
        }
    }
} else {
    $name = $category_to_edit['name'];
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
                                    <span class="badge text-bg-<?php echo ($category_to_edit['active'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($category_to_edit['active'] == '1') ? 'Active' : 'Inactive'; ?>
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
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($category_to_edit['slug']); ?>" readonly disabled>
                                            <small class="text-muted">URL-friendly</small>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="category_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update Category</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    Created: <?php echo date('M j, Y g:i A', strtotime($category_to_edit['created_at'])); ?>
                                    | Last Updated: <?php echo date('M j, Y g:i A', strtotime($category_to_edit['updated_at'])); ?>
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
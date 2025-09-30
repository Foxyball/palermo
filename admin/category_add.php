<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    $slug = generateSlug($name);

    // Validation
    if ($name === '') {
        $errors[] = 'Category name is required';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ? LIMIT 1');
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $errors[] = 'Category name already exists.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug, active, created_at, updated_at) VALUES (?, ?, "1", NOW(), NOW())');
            $stmt->execute([$name, $slug]);
            $_SESSION['success'] = 'Category created successfully';
            header('Location: category_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error creating the category. Please try again.';
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Add New Category</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="category_list">Category List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Category</li>
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
                                    <h3 class="card-title">Create Category</h3>
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
                                            <label class="form-label">Category Name *</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required placeholder="e.g. Main Dishes">
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="category_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Create Category</button>
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
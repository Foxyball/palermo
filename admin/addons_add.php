<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/AddonRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$errors = [];

$addonRepo = new AddonRepository($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Addons name is required';
    }
    if ($price === '' || !is_numeric($price) || $price < 0) {
        $errors[] = 'Valid price is required';
    }

    if (empty($errors)) {

        if ($addonRepo->nameExists($name)) {
            $errors[] = 'Addons name already exists.';
        }
    }

    if (empty($errors)) {
        try {
            $addonRepo->create($name, (float)$price, '1');
            $_SESSION['success'] = 'Addons created successfully';
            header('Location: addons_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error creating the addons. Please try again.';
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Add New Addons</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="addons_list">Addons List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Addons</li>
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
                                    <h3 class="card-title">Create Addons</h3>
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
                                            <label class="form-label">Addons Name *</label>
                                            <input type="text" name="name" class="form-control"
                                                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required
                                                placeholder="e.g. Extra Cheese">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Price *</label>
                                            <input type="number" name="price" class="form-control" min="0" step="0.01"
                                                value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required
                                                placeholder="e.g. 2.50">
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="addons_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Create Addons</button>
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
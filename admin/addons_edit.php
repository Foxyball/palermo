<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/AddonRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$addonId = $_GET['id'] ?? 0;
if ($addonId <= 0) {
    $_SESSION['error'] = 'Invalid addon selected.';
    header('Location: addons_list');
    exit;
}

$addonRepo = new AddonRepository($pdo);
$addonToEdit = $addonRepo->findById((int)$addonId);

if (!$addonToEdit) {
    $_SESSION['error'] = 'Addon not found.';
    header('Location: addons_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Addon name is required';
    }
    if ($price === '' || !is_numeric($price) || $price < 0) {
        $errors[] = 'Valid price is required';
    }

    if (empty($errors)) {
        if ($addonRepo->nameExists($name, (int)$addonId)) {
            $errors[] = 'Addon name already exists. Please choose a different name.';
        }
    }

    if (empty($errors)) {
        try {
            $updated = $addonRepo->update((int)$addonId, $name, (float)$price);
            if ($updated) {
                $_SESSION['success'] = 'Addon updated successfully';
                header('Location: addons_list');
                exit;
            } else {
                $errors[] = 'Failed to update addon. Please try again.';
            }
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating the addon. Please try again.';
        }
    }
} else {
    $name = $addonToEdit['name'];
    $price = $addonToEdit['price'];
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Edit Addon</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="addons_list">Addons List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Addon</li>
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
                                    <h3 class="card-title mb-0">Update Addon</h3>
                                    <span class="badge text-bg-<?php echo ($addonToEdit['status'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($addonToEdit['status'] == '1') ? 'Active' : 'Inactive'; ?>
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
                                            <label class="form-label">Addon Name *</label>
                                            <input type="text" name="name" class="form-control"
                                                value="<?php echo htmlspecialchars($name); ?>" required
                                                placeholder="e.g. Extra Cheese">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Price *</label>
                                            <input type="number" name="price" class="form-control" min="0" step="0.01"
                                                value="<?php echo htmlspecialchars($price); ?>" required
                                                placeholder="e.g. 2.50">
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="addons_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update Addon</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    Created: <?php echo date('M j, Y g:i A', strtotime($addonToEdit['created_at'])); ?>
                                    | Last
                                    Updated: <?php echo date('M j, Y g:i A', strtotime($addonToEdit['updated_at'])); ?>
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
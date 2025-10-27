<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/OrderStatusRepository.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$statusId = $_GET['id'] ?? 0;
if ($statusId <= 0) {
    $_SESSION['error'] = 'Invalid order status selected.';
    header('Location: order_status_list');
    exit;
}

$orderStatusRepository = new OrderStatusRepository($pdo);
$statusToEdit = $orderStatusRepository->findById($statusId);

if (!$statusToEdit) {
    $_SESSION['error'] = 'Order status not found.';
    header('Location: order_status_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Order status name is required';
    }

    if (empty($errors)) {
        // Check if name already exists for a different status
        $stmt = $pdo->prepare('SELECT id FROM order_statuses WHERE name = ? AND id != ? LIMIT 1');
        $stmt->execute([$name, $statusId]);
        if ($stmt->fetch()) {
            $errors[] = 'Order status name already exists. Please choose a different name.';
        }
    }

    if (empty($errors)) {
        try {
            $orderStatusRepository->update($statusId, $name, $statusToEdit['active']);
            $_SESSION['success'] = 'Order status updated successfully';
            header('Location: order_status_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating the order status. Please try again.';
        }
    }
} else {
    $name = $statusToEdit['name'];
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
                        <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Edit Order Status</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                            <li class="breadcrumb-item"><a href="order_status_list">Order Status List</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Order Status</li>
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
                                <h3 class="card-title mb-0">Update Order Status</h3>
                                <span class="badge text-bg-<?php echo ($statusToEdit['active'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($statusToEdit['active'] == '1') ? 'Active' : 'Inactive'; ?>
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
                                        <label class="form-label">Order Status Name *</label>
                                        <input type="text" name="name" class="form-control"
                                               value="<?php echo htmlspecialchars($name); ?>" required
                                               placeholder="e.g. Ready for Pickup">
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <a href="order_status_list" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Order Status</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-muted small">
                                Created: <?php echo date('M j, Y g:i A', strtotime($statusToEdit['created_at'])); ?>
                                | Last
                                Updated: <?php echo date('M j, Y g:i A', strtotime($statusToEdit['updated_at'])); ?>
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

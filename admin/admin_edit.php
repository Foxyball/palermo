<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$current_admin = getCurrentAdmin($pdo);
$current_admin_id = $current_admin['admin_id'] ?? null;
$is_current_super_admin = isCurrentSuperAdmin($current_admin);

$admin_id = $_GET['id'] ?? 0;
if ($admin_id <= 0) {
    $_SESSION['error'] = 'Invalid administrator selected.';
    header('Location: admin_list');
    exit;
}

$stmt = $pdo->prepare('SELECT admin_id, admin_name, admin_email, active, is_super_admin, created_at, updated_at FROM admins WHERE admin_id = ? LIMIT 1');
$stmt->execute([$admin_id]);
$admin_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin_to_edit) {
    $_SESSION['error'] = 'Administrator not found.';
    header('Location: admin_list');
    exit;
}

if (!$is_current_super_admin && $admin_id !== $current_admin_id) {
    $_SESSION['error'] = 'You do not have permission to edit this administrator.';
    header('Location: admin_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['admin_name'] ?? '');
    $email = trim($_POST['admin_email'] ?? '');
    $password = $_POST['admin_password'] ?? '';
    $is_super_admin = isset($_POST['is_super_admin']) ? 1 : 0;

    // Validation
    if ($name === '') {
        $errors[] = 'Name is required';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    if ($email !== $admin_to_edit['admin_email']) {
        $stmt = $pdo->prepare('SELECT admin_id FROM admins WHERE admin_email = ? AND admin_id != ? LIMIT 1');
        $stmt->execute([$email, $admin_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already in use by another administrator';
        }
    }

    $password_hash = null;
    if ($password !== '') {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } else {
            $password_hash = md5($password);
        }
    }


    if (empty($errors)) {
        $fields = ['admin_name = ?', 'admin_email = ?'];
        $params = [$name, $email];
        if ($password_hash !== null) {
            $fields[] = 'admin_password = ?';
            $params[] = $password_hash;
        }
        if ($is_current_super_admin) {
            $fields[] = 'is_super_admin = ?';
            $params[] = $is_super_admin;
        }
        $fields[] = 'updated_at = NOW()';
        $params[] = $admin_id;

        $sql = 'UPDATE admins SET ' . implode(', ', $fields) . ' WHERE admin_id = ? LIMIT 1';
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['success'] = 'Administrator updated successfully';
            header('Location: admin_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an unexpected error. Please try again.';
        }
    }
} else {
    $name = $admin_to_edit['admin_name'];
    $email = $admin_to_edit['admin_email'];
    $is_super_admin = $admin_to_edit['is_super_admin'] ?? 0;
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
                            <h3 class="mb-0">Edit Administrator <?php echo ($admin_to_edit['admin_name']); ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="admin_list">Admin List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Admin</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Update Administrator</h3>
                                    <span class="badge text-bg-<?php echo ($admin_to_edit['active'] == '1') ? 'success' : 'secondary'; ?>">
                                        <?php echo ($admin_to_edit['active'] == '1') ? 'Active' : 'Inactive'; ?>
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
                                            <label class="form-label">Name</label>
                                            <input type="text" name="admin_name" class="form-control" value="<?php echo $name; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="admin_email" class="form-control" value="<?php echo $email; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password <small class="text-muted">(leave blank to keep unchanged)</small></label>
                                            <input type="password" name="admin_password" class="form-control" autocomplete="new-password">
                                        </div>
                                        <?php if ($is_current_super_admin) { ?>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" <?php echo !empty($is_super_admin) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_super_admin">Super Admin</label>
                                            </div>
                                        <?php } ?>
                                        <div class="d-flex justify-content-between">
                                            <a href="admin_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-muted small">
                                    Created: <?php echo date('M j, Y g:i A', strtotime($admin_to_edit['created_at'])); ?>
                                    | Last Updated: <?php echo date('M j, Y g:i A', strtotime($admin_to_edit['updated_at'])); ?>
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
<?php

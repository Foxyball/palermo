<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$currentAdmin = getCurrentAdmin($pdo);
if (!isCurrentSuperAdmin($currentAdmin)) {
    $_SESSION['error'] = 'You do not have permission to access the Add Admin page.';
    header('Location: admin_list');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['admin_name'] ?? null);
    $email = trim($_POST['admin_email'] ?? null);
    $password = $_POST['admin_password'] ?? null;
    $isSuperAdmin = isset($_POST['is_super_admin']) ? 1 : 0;

    if ($name === '') {
        $errors[] = 'Name is required';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT admin_id FROM admins WHERE admin_email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already in use';
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare('INSERT INTO admins (admin_name, admin_email, admin_password, active, is_super_admin, created_at, updated_at) VALUES (?, ?, ?, "1", ?, NOW(), NOW())');
            $stmt->execute([$name, $email, $passwordHash, $isSuperAdmin]);
            $_SESSION['success'] = 'Administrator created successfully';
            header('Location: admin_list');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error creating the admin. Please try again.';
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> |Add New Admin</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="admin_list">Admin List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Admin</li>
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
                                <div class="card-header">
                                    <h3 class="card-title">Create Administrator</h3>
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
                                            <label class="form-label">Name *</label>
                                            <input type="text" name="admin_name" class="form-control"
                                                   value="<?php echo htmlspecialchars($_POST['admin_name'] ?? ''); ?>"
                                                   required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email *</label>
                                            <input type="email" name="admin_email" class="form-control"
                                                   value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>"
                                                   required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password *</label>
                                            <input type="password" name="admin_password" class="form-control" required>
                                            <small class="text-muted">Min 6 characters</small>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_super_admin"
                                                   name="is_super_admin" <?php echo isset($_POST['is_super_admin']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_super_admin">Super Admin</label>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="admin_list" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Create Admin</button>
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
<?php

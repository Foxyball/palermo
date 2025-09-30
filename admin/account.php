<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$current_admin = getCurrentAdmin($pdo);
if (!$current_admin) {
    $_SESSION['error'] = 'Unable to load account information.';
    header('Location: index');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['admin_name'] ?? '');
    $email = trim($_POST['admin_email'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = 'Name is required';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    if (empty($errors) && $email !== $current_admin['admin_email']) {
        $stmt = $pdo->prepare('SELECT admin_id FROM admins WHERE admin_email = ? AND admin_id != ? LIMIT 1');
        $stmt->execute([$email, $current_admin['admin_id']]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already in use by another admin';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('UPDATE admins SET admin_name = ?, admin_email = ?, updated_at = NOW() WHERE admin_id = ? LIMIT 1');
            $stmt->execute([$name, $email, $current_admin['admin_id']]);

            $_SESSION['success'] = 'Profile updated successfully';
            header('Location: account');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating your profile. Please try again.';
        }
    }
} else {
    $name = $current_admin['admin_name'];
    $email = $current_admin['admin_email'];
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Edit Profile</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
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
                                    <h3 class="card-title">Edit Profile</h3>
                                    <p class="card-text mb-0 text-muted">Update your account information</p>
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

                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="user-avatar me-3" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;background:#f8f9fa;color:#003b79;border-radius:50%;">
                                                <?php echo strtoupper($current_admin['admin_name'][0]); ?>
                                            </div>
                                            <div>
                                                <h5 class="mb-0"><?php echo htmlspecialchars($current_admin['admin_name']); ?></h5>
                                                <small class="text-muted"><?php echo htmlspecialchars($current_admin['admin_email']); ?></small>
                                                <?php if (isCurrentSuperAdmin($current_admin)) { ?>
                                                    <span class="badge text-bg-primary ms-2">Super Admin</span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Name *</label>
                                            <input type="text" name="admin_name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Address *</label>
                                            <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="index" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-lg"></i> Update Profile
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer">
                                    <div class="row text-center">
                                        <div class="col">
                                            <small class="text-muted">
                                                <i class="bi bi-shield-check"></i>
                                                Your information is encrypted and secure
                                            </small>
                                        </div>
                                        <div class="col">
                                            <a href="reset_password" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-key"></i> Change Password
                                            </a>
                                        </div>
                                    </div>
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
<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../repositories/admin/AdminRepository.php');
require_once(__DIR__ . '/../include/smtp_class.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$adminRepo = new AdminRepository($pdo);
$currentAdmin = getCurrentAdmin($pdo);

if (!$currentAdmin) {
    $_SESSION['error'] = 'Unable to load account information.';
    header('Location: index');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if ($currentPassword === '') {
        $errors[] = 'Current password is required';
    }
    if ($newPassword === '') {
        $errors[] = 'New password is required';
    } elseif (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters';
    }
    if ($confirmPassword === '') {
        $errors[] = 'Password confirmation is required';
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirmation do not match';
    }

    // Verify the current password
    $adminPassword = null;
    if (empty($errors)) {
        $adminPassword = $adminRepo->getPasswordHash($currentAdmin['admin_id']);

        if (!$adminPassword || md5($currentPassword) !== $adminPassword) {
            $errors[] = 'Current password is incorrect';
        }
    }

    // Check if new password is same as current
    if (empty($errors)) {
        if (md5($newPassword) === $adminPassword) {
            $errors[] = 'New password must be different from current password';
        }
    }

    // Update password
    if (empty($errors)) {
        $newPasswordHash = md5($newPassword);
        try {
            $updated = $adminRepo->updatePassword($currentAdmin['admin_id'], $newPasswordHash);

            if (!$updated) {
                $errors[] = 'Failed to update password. Please try again.';
            } else {
                // Send email notification about password change
                $emailSubject = 'Password Changed - ' . SITE_TITLE;
                $emailBody = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background: #f8f9fa; }
                    .footer { padding: 10px; text-align: center; color: #666; font-size: 12px; }
                    .alert { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 15px 0; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>" . SITE_TITLE . " - Security Alert</h2>
                    </div>
                    <div class='content'>
                        <h3>Password Changed Successfully</h3>
                        <p>Hello " . htmlspecialchars($currentAdmin['admin_name']) . ",</p>
                        <p>Your admin account password has been successfully changed.</p>
                        
                        <div class='alert'>
                            <strong>Account Details:</strong><br>
                            Name: " . htmlspecialchars($currentAdmin['admin_name']) . "<br>
                            Email: " . htmlspecialchars($currentAdmin['admin_email']) . "<br>
                            Admin ID: #" . htmlspecialchars($currentAdmin['admin_id']) . "<br>
                            Date: " . date('M j, Y g:i A') . "<br>
                            IP Address: " . htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "
                        </div>
                        
                        <p>If you did not make this change, please contact the system administrator immediately.</p>
                        <p>For security reasons, please ensure you:</p>
                        <ul>
                            <li>Keep your new password secure and confidential</li>
                            <li>Do not share your login credentials with anyone</li>
                            <li>Log out when you're finished using the admin panel</li>
                        </ul>
                    </div>
                    <div class='footer'>
                        <p>This is an automated security notification from " . SITE_TITLE . "</p>
                        <p>Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>";

                try {
                    $emailSent = sendEmail(
                        $currentAdmin['admin_email'],
                        $currentAdmin['admin_name'],
                        $emailSubject,
                        $emailBody
                    );
                } catch (Exception $e) {
                    $emailSent = false;
                }

                $_SESSION['success'] = 'Password updated successfully.';

                header('Location: reset_password');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = 'There was an error updating your password. Please try again.';
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
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?> | Change Password</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="account">My Account</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Change Password</li>
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
                                    <h3 class="card-title">Change Password</h3>
                                    <p class="card-text mb-0 text-muted">Update your account password for security</p>
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
                                                <?php echo strtoupper($currentAdmin['admin_name'][0]); ?>
                                            </div>
                                            <div>
                                                <h5 class="mb-0"><?php echo htmlspecialchars($currentAdmin['admin_name']); ?></h5>
                                                <small class="text-muted"><?php echo htmlspecialchars($currentAdmin['admin_email']); ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Current Password *</label>
                                            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password *</label>
                                            <input type="password" name="new_password" class="form-control" required autocomplete="new-password">
                                            <small class="text-muted">Minimum 6 characters</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password *</label>
                                            <input type="password" name="confirm_password" class="form-control" required autocomplete="new-password">
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="account" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-key"></i> Update Password
                                            </button>
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
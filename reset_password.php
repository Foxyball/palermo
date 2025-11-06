<?php
require_once(__DIR__ . '/include/connect.php');
include(__DIR__ . '/include/html_functions.php');

$errors = [];
$successMessage = '';
$token = $_GET['token'] ?? '';
$validToken = false;
$userEmail = '';

// Validate token
if ($token) {
    $stmt = $pdo->prepare('
        SELECT email 
        FROM password_reset_tokens 
        WHERE token = ? 
        AND expires_at > NOW() 
        AND used_at IS NULL 
        LIMIT 1
    ');
    $stmt->execute([$token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tokenData) {
        $validToken = true;
        $userEmail = $tokenData['email'];
    } else {
        $errors[] = 'This password reset link is invalid or has expired. Please request a new one.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($password === '') {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }
    
    if ($confirmPassword === '') {
        $errors[] = 'Password confirmation is required';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Update user password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?');
            $stmt->execute([$passwordHash, $userEmail]);
            
            // Mark token as used
            $stmt = $pdo->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE token = ?');
            $stmt->execute([$token]);
            
            $pdo->commit();
            $successMessage = 'Your password has been successfully updated! You can now log in with your new password.';
            
        } catch (PDOException $e) {
            $pdo->rollback();
            $errors[] = 'Database error occurred. Please try again later.';
        }
    }
}

headerContainer();
?>
<title>Reset Password | <?php echo SITE_TITLE; ?></title>
<link rel="stylesheet" href="css/auth.css">

</head>

<body class="stretched">
    <div class="body-overlay"></div>
    <div id="wrapper" class="clearfix">
        
        <?php navbarContainer(); ?>

        <section id="content">
            <div class="content-wrap">
                <div class="container">
                    <div class="page-section">
                        <div class="auth-card">
                            <div class="auth-header">
                                <h2><i class="fas fa-lock"></i> Reset Password</h2>
                                <p style="margin: 0; opacity: 0.9;">
                                    <?php if ($validToken) { ?>
                                        Set your new password for <?php echo htmlspecialchars($userEmail); ?>
                                    <?php } else { ?>
                                        Invalid or expired reset link
                                    <?php } ?>
                                </p>
                            </div>
                            
                            <div class="auth-body">
                                <?php if (!empty($errors)) { ?>
                                    <div class="alert alert-danger">
                                        <strong><i class="fas fa-exclamation-triangle"></i> Error:</strong>
                                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                            <?php foreach ($errors as $err) { ?>
                                                <li><?php echo htmlspecialchars($err); ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                                
                                <?php if ($successMessage) { ?>
                                    <div class="alert alert-success">
                                        <strong><i class="fas fa-check-circle"></i> Success!</strong><br>
                                        <?php echo htmlspecialchars($successMessage); ?>
                                        <div class="mt-3">
                                            <a href="index.php" class="btn btn-outline">
                                                <i class="fas fa-sign-in-alt"></i> Go to Login
                                            </a>
                                        </div>
                                    </div>
                                <?php } elseif ($validToken) { ?>
                                    <form method="post" novalidate>
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-key"></i> New Password
                                            </label>
                                            <input 
                                                type="password" 
                                                name="password" 
                                                class="form-control" 
                                                placeholder="Enter your new password"
                                                required
                                                autocomplete="new-password"
                                                id="password"
                                            >
                                            <small class="text-muted" id="strength-text">Minimum 6 characters</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-key"></i> Confirm New Password
                                            </label>
                                            <input 
                                                type="password" 
                                                name="confirm_password" 
                                                class="form-control" 
                                                placeholder="Confirm your new password"
                                                required
                                                autocomplete="new-password"
                                                id="confirm_password"
                                            >
                                            <small class="text-muted" id="match-text"></small>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary" id="submit-btn">
                                            <i class="fas fa-save"></i> Update Password
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <div class="text-center">
                                        <p style="color: #6c757d; margin-bottom: 20px;">
                                            The password reset link you used is either invalid or has expired.
                                        </p>
                                        <a href="forgot_password.php" class="btn btn-outline">
                                            <i class="fas fa-redo"></i> Request New Reset Link
                                        </a>
                                    </div>
                                <?php } ?>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <a href="index.php" class="back-link">
                                            <i class="fas fa-arrow-left"></i> Back to Home
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Security Tips -->
                        <?php if ($validToken && !$successMessage) { ?>
                        <div style="max-width: 500px; margin: 40px auto 0; text-align: center;">
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <h5 style="color: #333; margin-bottom: 15px;">
                                    <i class="fas fa-shield-alt"></i> Password Security Tips
                                </h5>
                                <ul style="color: #6c757d; font-size: 14px; text-align: left; list-style: none; padding: 0;">
                                    <li style="margin-bottom: 8px;"><i class="fas fa-check text-success"></i> Use at least 8 characters</li>
                                    <li style="margin-bottom: 8px;"><i class="fas fa-check text-success"></i> Include uppercase and lowercase letters</li>
                                    <li style="margin-bottom: 8px;"><i class="fas fa-check text-success"></i> Add numbers and special characters</li>
                                    <li style="margin-bottom: 8px;"><i class="fas fa-check text-success"></i> Avoid common words or personal info</li>
                                </ul>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

    <!-- JavaScripts -->
    <script src="js/jquery.js"></script>
    <script src="js/plugins.min.js"></script>
    <script src="js/functions.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const matchText = document.getElementById('match-text');
            const submitBtn = document.getElementById('submit-btn');
            
            function checkPasswordMatch() {
                if (!confirmInput || !matchText || !passwordInput) return;
                
                const password = passwordInput.value;
                const confirm = confirmInput.value;
                
                if (confirm === '') {
                    matchText.textContent = '';
                    matchText.style.color = '';
                } else if (password === confirm) {
                    matchText.textContent = '✓ Passwords match';
                    matchText.style.color = '#28a745';
                } else {
                    matchText.textContent = '✗ Passwords do not match';
                    matchText.style.color = '#dc3545';
                }
            }
        });
    </script>
</body>
</html>
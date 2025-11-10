<?php
require_once(__DIR__ . '/include/connect.php');
include(__DIR__ . '/include/html_functions.php');

$errors = [];
$successMessage = '';
$token = $_GET['token'] ?? '';
$validToken = false;
$userEmail = '';

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
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?');
            $stmt->execute([$passwordHash, $userEmail]);
            
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
                                            <a href="<?php echo BASE_URL; ?>login" class="btn btn-outline">
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
                                        <a href="forgot_password" class="btn btn-outline">
                                            <i class="fas fa-redo"></i> Request New Reset Link
                                        </a>
                                    </div>
                                <?php } ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php footerContainer(); ?>
    </div>

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
                    matchText.textContent = 'Passwords match';
                    matchText.style.color = '#28a745';
                } else {
                    matchText.textContent = 'Passwords do not match';
                    matchText.style.color = '#dc3545';
                }
            }
        });
    </script>
</body>
</html>
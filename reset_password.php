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

<style>
.page-section {
    padding: 80px 0;
    min-height: 70vh;
}

.auth-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    max-width: 500px;
    margin: 0 auto;
}

.auth-header {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 30px;
    text-align: center;
}

.auth-body {
    padding: 40px;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
    display: block;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.btn {
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.btn-primary {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    width: 100%;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.btn-outline {
    background: transparent;
    color: #dc3545;
    border: 2px solid #dc3545;
}

.btn-outline:hover {
    background: #dc3545;
    color: white;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: #6c757d;
}

.mt-3 {
    margin-top: 1rem;
}

.back-link {
    color: #dc3545;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

.password-strength {
    height: 5px;
    background: #e9ecef;
    border-radius: 3px;
    margin-top: 5px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-weak { background: #dc3545; width: 25%; }
.strength-fair { background: #ffc107; width: 50%; }
.strength-good { background: #28a745; width: 75%; }
.strength-strong { background: #28a745; width: 100%; }
</style>

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
                                            <div class="password-strength">
                                                <div class="password-strength-bar" id="strength-bar"></div>
                                            </div>
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
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            const matchText = document.getElementById('match-text');
            const submitBtn = document.getElementById('submit-btn');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const strength = getPasswordStrength(password);
                    updatePasswordStrength(strength, password.length);
                    checkPasswordMatch();
                });
            }
            
            if (confirmInput) {
                confirmInput.addEventListener('input', checkPasswordMatch);
            }
            
            function getPasswordStrength(password) {
                let score = 0;
                if (password.length >= 6) score++;
                if (password.length >= 8) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;
                return score;
            }
            
            function updatePasswordStrength(strength, length) {
                if (!strengthBar || !strengthText) return;
                
                const classes = ['strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
                const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                
                strengthBar.className = 'password-strength-bar';
                
                if (length === 0) {
                    strengthText.textContent = 'Minimum 6 characters';
                    strengthText.style.color = '#6c757d';
                } else if (length < 6) {
                    strengthBar.classList.add('strength-weak');
                    strengthText.textContent = 'Too short';
                    strengthText.style.color = '#dc3545';
                } else if (strength > 0 && strength <= 4) {
                    strengthBar.classList.add(classes[Math.min(strength - 1, 3)]);
                    strengthText.textContent = 'Password strength: ' + texts[Math.min(strength - 1, 4)];
                    strengthText.style.color = strength >= 3 ? '#28a745' : (strength >= 2 ? '#ffc107' : '#dc3545');
                }
            }
            
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
<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/smtp_class.php');
include(__DIR__ . '/include/html_functions.php');

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($errors)) {
        // Check if email exists in users table
        $stmt = $pdo->prepare('SELECT id, first_name, last_name FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $errors[] = 'No account found with that email address';
        } else {
            try {
                // Generate secure token
                $token = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Clean up old tokens for this email
                $stmt = $pdo->prepare('DELETE FROM password_reset_tokens WHERE email = ? OR expires_at < NOW()');
                $stmt->execute([$email]);
                
                // Insert new token
                $stmt = $pdo->prepare('INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)');
                $stmt->execute([$email, $token, $expiresAt]);
                
                // Send email
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/palermo/reset_password.php?token=" . $token;
                $userName = trim($user['first_name'] . ' ' . $user['last_name']);
                
                $emailSubject = 'Password Reset - ' . SITE_TITLE;
                $emailBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                        .content { padding: 30px; background: #f8f9fa; border-radius: 0 0 8px 8px; }
                        .button { 
                            display: inline-block; 
                            padding: 12px 24px; 
                            background: #dc3545; 
                            color: white; 
                            text-decoration: none; 
                            border-radius: 5px; 
                            margin: 20px 0;
                            font-weight: bold;
                        }
                        .footer { 
                            padding: 20px; 
                            text-align: center; 
                            color: #666; 
                            font-size: 12px; 
                            border-top: 1px solid #ddd; 
                            margin-top: 20px;
                        }
                        .alert { 
                            background: #fff3cd; 
                            border: 1px solid #ffeaa7; 
                            padding: 15px; 
                            margin: 15px 0; 
                            border-radius: 4px; 
                            border-left: 4px solid #ffc107;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>" . SITE_TITLE . "</h2>
                            <h3>Password Reset Request</h3>
                        </div>
                        <div class='content'>
                            <h3>Hello " . htmlspecialchars($userName) . ",</h3>
                            <p>We received a request to reset your password. If you made this request, click the button below to set a new password:</p>
                            
                            <div style='text-align: center;'>
                                <a href='" . $resetLink . "' class='button'>Reset My Password</a>
                            </div>
                            
                            <div class='alert'>
                                <strong>Security Information:</strong><br>
                                • This link will expire in 1 hour<br>
                                • If you didn't request this, you can safely ignore this email<br>
                                • Your current password remains unchanged until you create a new one
                            </div>
                            
                            <p>If the button doesn't work, copy and paste this link into your browser:</p>
                            <p style='word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 4px; font-family: monospace;'>" . $resetLink . "</p>
                            
                            <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
                        </div>
                        <div class='footer'>
                            <p>This is an automated message from " . SITE_TITLE . "</p>
                            <p>Please do not reply to this email.</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                $emailSent = sendEmail(
                    $email,
                    $userName,
                    $emailSubject,
                    $emailBody
                );
                
                if ($emailSent) {
                    $successMessage = 'Password reset instructions have been sent to your email address.';
                    $_POST = []; // Clear form
                } else {
                    $errors[] = 'Failed to send email. Please try again later.';
                }
                
            } catch (PDOException $e) {
                $errors[] = 'Database error occurred. Please try again later.';
            }
        }
    }
}

headerContainer();
?>
<title>Forgot Password | <?php echo SITE_TITLE; ?></title>

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
                                <h2><i class="fas fa-key"></i> Forgot Password</h2>
                                <p style="margin: 0; opacity: 0.9;">Enter your email to receive reset instructions</p>
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
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Check your email inbox and spam folder for the reset link.
                                            </small>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <?php if (!$successMessage) { ?>
                                    <form method="post" novalidate>
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-envelope"></i> Email Address
                                            </label>
                                            <input 
                                                type="email" 
                                                name="email" 
                                                class="form-control" 
                                                placeholder="Enter your email address"
                                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                                required
                                                autocomplete="email"
                                            >
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send Reset Instructions
                                        </button>
                                    </form>
                                <?php } ?>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Remember your password? 
                                        <a href="index.php" class="back-link">Back to Login</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Help Section -->
                        <div style="max-width: 500px; margin: 40px auto 0; text-align: center;">
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <h5 style="color: #333; margin-bottom: 15px;">
                                    <i class="fas fa-question-circle"></i> Need Help?
                                </h5>
                                <p style="color: #6c757d; margin: 0; font-size: 14px;">
                                    If you're having trouble receiving the reset email, please check your spam folder 
                                    or contact support for assistance.
                                </p>
                            </div>
                        </div>
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
</body>
</html>
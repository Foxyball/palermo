<?php

require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/html_functions.php');

if (isset($_SESSION['user_logged_in'])) {
    header('location: index');
    exit;
}

if (!isset($_SESSION['user_logged_in']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $hashedToken = hash('sha256', $token);
    
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, remember_expires FROM users WHERE remember_token = ? AND active = '1' LIMIT 1");
        $stmt->execute([$hashedToken]);
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user['remember_expires'] && strtotime($user['remember_expires']) > time()) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_last_name'] = $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_logged_in'] = true;
                
                session_regenerate_id(true);
                
                header('location: index');
                exit;
            } else {
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
    } catch (PDOException $e) {
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, password, active FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user['active'] == '0') {
                $_SESSION['error'] = 'Your account is inactive. Please contact support.';
                header('location: login');
                exit;
            }

            if (password_verify($password, $user['password'])) {
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $rehashStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? LIMIT 1");
                    $rehashStmt->execute([$newHash, $user['id']]);
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_last_name'] = $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_logged_in'] = true;

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    setcookie('remember_token', $token, $expiresAt, '/', '', false, true);
                    
                    $hashedToken = hash('sha256', $token);
                    $updateStmt = $pdo->prepare("UPDATE users SET remember_token = ?, remember_expires = FROM_UNIXTIME(?) WHERE id = ?");
                    $updateStmt->execute([$hashedToken, $expiresAt, $user['id']]);
                }

                session_regenerate_id(true);

                header('location: index');
                exit;
            } else {
                $_SESSION['error'] = 'Wrong email or password.';
                header('location: login');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Wrong email or password.';
            header('location: login');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Something went wrong. Please try again.';
        header('location: login');
        exit;
    }
}

$pageTitle = 'Login';

headerContainer();
?>

<title><?php echo $pageTitle . ' | ' . SITE_TITLE; ?></title>

</head>

<body class="stretched">

    <div id="wrapper" class="clearfix">

        <?php navbarContainer(); ?>

        <section id="page-title" class="page-title-parallax page-title-dark include-header" 
                 style="background-image: url('images/others/section-1.jpg'); padding: 120px 0;" 
                 data-bottom-top="background-position:0px 300px;" 
                 data-top-bottom="background-position:0px -300px;">
            <div class="container clearfix">
                <h1><?php echo $pageTitle; ?></h1>
                <span>Sign in to your account</span>
            </div>
        </section>

        <section id="content">
            <div class="content-wrap">
                <div class="container clearfix">
                    
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['error']);
                                    unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($_SESSION['success']);
                                    unset($_SESSION['success']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <div class="card">
                                <div class="card-body p-5">
                                    <h3 class="mb-4 text-center">Welcome Back</h3>
                                    
                                    <form action="" method="post">
                                        <div class="mb-4">
                                            <label for="email" class="form-label fw-bold">Email Address</label>
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="Enter your email" required />
                                        </div>

                                        <div class="mb-4">
                                            <label for="password" class="form-label fw-bold">Password</label>
                                            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" required />
                                        </div>

                                        <div class="mb-4 d-flex justify-content-between align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" />
                                                <label class="form-check-label" for="remember">
                                                    Remember me
                                                </label>
                                            </div>
                                            <a href="forgot_password" class="text-decoration-none">Forgot password?</a>
                                        </div>

                                        <div class="d-grid mb-4">
                                            <button type="submit" name="login" class="btn btn-primary btn-lg">
                                                Sign In
                                            </button>
                                        </div>

                                        <hr>

                                        <div class="text-center">
                                            <p class="mb-0">Don't have an account? <a href="register" class="fw-bold">Sign Up</a></p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <?php footerContainer(); ?>

    </div>


</body>

</html>

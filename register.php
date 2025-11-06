<?php

require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/html_functions.php');

if (isset($_SESSION['user_logged_in'])) {
    header('location: index');
    exit;
}

if (isset($_POST['register'])) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    $errors = [];

    if (empty($firstName)) {
        $errors[] = 'First name is required.';
    }

    if (empty($lastName)) {
        $errors[] = 'Last name is required.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $checkStmt->execute([$email]);

            if ($checkStmt->rowCount() > 0) {
                $errors[] = 'Email already exists.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, phone, active, created_at) VALUES (?, ?, ?, ?, ?, '1', NOW())");
                $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone]);

                $_SESSION['success'] = 'Registration successful! Please login.';
                header('location: login');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

$pageTitle = 'Register';

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
                <span>Create your account</span>
            </div>
        </section>

        <section id="content">
            <div class="content-wrap">
                <div class="container clearfix">
                    
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['error'];
                                    unset($_SESSION['error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <div class="card">
                                <div class="card-body p-5">
                                    <h3 class="mb-4 text-center">Create Account</h3>
                                    
                                    <form action="" method="post">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label for="first_name" class="form-label fw-bold">First Name</label>
                                                <input type="text" name="first_name" id="first_name" class="form-control form-control-lg" 
                                                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" 
                                                       placeholder="Enter first name" required />
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <label for="last_name" class="form-label fw-bold">Last Name</label>
                                                <input type="text" name="last_name" id="last_name" class="form-control form-control-lg" 
                                                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" 
                                                       placeholder="Enter last name" required />
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="email" class="form-label fw-bold">Email Address</label>
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" 
                                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                                   placeholder="Enter your email" required />
                                        </div>

                                        <div class="mb-4">
                                            <label for="phone" class="form-label fw-bold">Phone Number <span class="text-muted">(Optional)</span></label>
                                            <input type="tel" name="phone" id="phone" class="form-control form-control-lg" 
                                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                                                   placeholder="Enter your phone number" />
                                        </div>

                                        <div class="mb-4">
                                            <label for="password" class="form-label fw-bold">Password</label>
                                            <input type="password" name="password" id="password" class="form-control form-control-lg" 
                                                   placeholder="Create a password" required />
                                            <small class="text-muted">Minimum 6 characters</small>
                                        </div>

                                        <div class="mb-4">
                                            <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-lg" 
                                                   placeholder="Confirm your password" required />
                                        </div>

                                        <div class="mb-4 form-check">
                                            <input class="form-check-input" type="checkbox" id="terms" required />
                                            <label class="form-check-label" for="terms">
                                                I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a>
                                            </label>
                                        </div>

                                        <div class="d-grid mb-4">
                                            <button type="submit" name="register" class="btn btn-primary btn-lg">
                                                Create Account
                                            </button>
                                        </div>

                                        <hr>

                                        <div class="text-center">
                                            <p class="mb-0">Already have an account? <a href="login" class="fw-bold">Sign In</a></p>
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

    <div id="gotoTop" class="fas fa-arrow-up"></div>

    <script src="js/jquery.js"></script>
    <script src="js/plugins.min.js"></script>
    <script src="js/hover-animate.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/cart.js"></script>

</body>

</html>

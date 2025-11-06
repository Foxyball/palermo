<?php
session_start();
require_once(__DIR__ . '/include/connect.php');
include(__DIR__ . '/include/html_functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$pageTitle = 'Thank You - ' . SITE_TITLE;
?>

<?php headerContainer(); ?>

<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/auth.css">
</head>

<body class="stretched">

    <div class="body-overlay"></div>

    <div id="side-panel" class="dark" style="background: #101010 url('<?php echo BASE_URL; ?>images/icon-bg-white.png') repeat center center;"></div>

    <div id="wrapper" class="clearfix">
        <?php navbarContainer(); ?>

        <!-- MAIN CONTENT -->
        <section id="content" class="dark-color">
            <div class="content-wrap">

                <div class="page-section">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-6 col-md-8">

                                <div class="auth-card text-center">

                                    <div class="auth-header mb-4">
                                        <h2 class="mb-3">Thank You!</h2>
                                        <p style="margin: 0; font-size: 1.1rem;">Your order has been placed successfully.</p>
                                    </div>

                                    <div class="auth-body">
                                        <p class="text-muted mb-4">
                                            We've received your order and will begin processing it shortly.
                                            You will receive a confirmation email soon.
                                        </p>

                                        <div class="d-grid gap-2">
                                            <a href="<?php echo BASE_URL; ?>account" class="btn btn-primary btn-lg">
                                                <i class="icon-line-user me-2"></i>View My Orders
                                            </a>
                                        </div>
                                    </div>
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
<?php

require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/include/html_functions.php');

if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header('location: login');
    exit;
}

$pageTitle = 'My Account';

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
                <span>Welcome back, <?php echo htmlspecialchars($_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']); ?>!</span>
            </div>
        </section>

        <section id="content">
            <div class="content-wrap">
                <div class="container clearfix">
                    
                    <div class="row">
                        <div class="col-lg-3 mb-5 mb-lg-0">
                            <div class="list-group">
                                <a href="account" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-user me-2"></i>My Profile
                                </a>
                                <a href="orders" class="list-group-item list-group-item-action">
                                    <i class="fas fa-shopping-bag me-2"></i>My Orders
                                </a>
                                <a href="logout" class="list-group-item list-group-item-action text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="mb-0">Account Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>First Name:</strong>
                                            <p><?php echo htmlspecialchars($_SESSION['user_first_name']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Last Name:</strong>
                                            <p><?php echo htmlspecialchars($_SESSION['user_last_name']); ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <strong>Email:</strong>
                                            <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This is a placeholder page. Account management features coming soon!
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

    <div id="gotoTop" class="fas fa-arrow-up"></div>

    <script src="js/jquery.js"></script>
    <script src="js/plugins.min.js"></script>
    <script src="js/hover-animate.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/cart.js"></script>

</body>

</html>

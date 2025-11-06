<?php

function headerContainer(): void
{

?>


    <!DOCTYPE html>
    <html dir="ltr" lang="en-US">

    <head>

        <base href="<?php echo BASE_URL; ?>">

        <meta http-equiv="content-type" content="text/html; charset=utf-8" />

        <link href="https://fonts.googleapis.com/css?family=Dosis:400,500,600,700|Open+Sans:400,600,700|Dancing+Script&display=swap" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="./css/bootstrap.css" type="text/css" />
        <link rel="stylesheet" href="./css/style.css" type="text/css" />
        <link rel="stylesheet" href="./css/dark.css" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./css/font-icons.css" type="text/css" />
        <link rel="stylesheet" href="./css/animate.css" type="text/css" />
        <link rel="stylesheet" href="./css/magnific-popup.css" type="text/css" />
        <link rel="icon" href="favicon.ico" type="image/x-icon" />

        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="stylesheet" href="css/burger.css" type="text/css" />
        <link rel="stylesheet" href="css/fonts.css" type="text/css" />

        <script src="js/js-cart-palermo.js"></script>
        <script src="js/js-account-dropdown.js"></script>



    <?php }


function footerContainer(): void
{

    ?>



        <footer id="footer" class="dark noborder pt-3" style="background-image: url('images/others/section-5.jpg');" data-top-bottom="background-position:100% -300px" data-bottom-top="background-position: 100% 300px">
            <div class="container">

                <div class="footer-widgets-wrap">

                    <div class="row col-mb-50">
                        <div class="col-lg-8">


                            <div class="row col-mb-30">
                                <div class="col-6 col-lg-4">
                                    <div class="widget">

                                        <h4 class="fw-bold">VISIT US</h4>

                                        <ul class="list-unstyled ms-0">
                                            <li class="mb-2"><a class="text-white-50" href="tel:+359789888871">+359 78 98 88 71</a></li>
                                            <li class="mb-2"><a class="text-white-50" href="tel:+359885835171">+359 885 83 51 71</a></li>
                                            <li class="mb-2"><a class="text-white-50" href="mailto:office@pizzaroyalkn.eu">office@pizzaroyalkn.eu</a></li>
                                            <li class="mb-2"><a class="text-white-50" href="https://facebook.com/royal.kn" target="_blank">facebook.com/royal.kn</a></li>
                                        </ul>
                                        2 Vlasina Street<br>
                                        Kyustendil, Bulgaria

                                    </div>
                                </div>
                                <div class="col-6 col-lg-4">
                                    <div class="widget">

                                        <h4 class="fw-bold">Useful Links</h4>

                                        <ul class="list-unstyled ms-0">
                                            <li class="mb-2"><a class="text-white-50" href="index.php">Home</a></li>
                                            <li class="mb-2"><a class="text-white-50" href="#">Menu</a></li>
                                            <li class="mb-2"><a class="text-white-50" href="contacts.php">Contact</a></li>
                                            <li class="mb-2"><a class="text-white-50" href="#">Help</a></li>
                                        </ul>

                                    </div>
                                </div>

                            </div>

                        </div>


                    </div>

                </div><!-- .footer-widgets-wrap end -->
            </div>

            <!-- Copyrights
			============================================= -->
            <div id="copyrights">
                <div class="container">

                    <div class="row col-mb-30">
                        <div class="col-md-6 text-center text-md-start">
                            &copy; Pizza Royal, Kyustendil. All rights reserved.<br>
                            <div class="copyright-links"><a href="https://balikgstudio.eu" target="_blank">Maintained by BalikG Studio.</a></div>
                        </div>

                    </div>

                </div>
            </div><!-- #copyrights end -->
        </footer><!-- #footer end -->

        </div><!-- #wrapper end -->



        <div id="gotoTop" class="fas fa-arrow-up"></div>

        <script src="js/jquery.js"></script>
        <script src="js/plugins.min.js"></script>
        <script src="js/hover-animate.js"></script>

        <script src="js/functions.js"></script>
        <script src="js/cart.js"></script>

        <style>
            .bootstrap-alert-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                width: 100%;
            }
            
            .bootstrap-alert-container .alert {
                margin-bottom: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                animation: slideInRight 0.3s ease-out;
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @media (max-width: 576px) {
                .bootstrap-alert-container {
                    left: 10px;
                    right: 10px;
                    max-width: none;
                }
            }
        </style>

    <?php }


function navbarContainer(): void
{
    global $pdo;
    require_once(__DIR__ . '/../repositories/frontend/CategoryRepository.php');
    $categoryRepository = new CategoryRepository($pdo);
    $categories = $categoryRepository->getActive();
    ?>

        <header id="header" class="transparent-header dark" data-sticky-class="dark-color" data-sticky-shrink-offset="0">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row">

                        <div id="logo">
                            <a href="<?php echo BASE_URL; ?>index" class="standard-logo" data-dark-logo="images/royal_logo.png" data-sticky-logo="images/royal_logo.png"><img src="images/royal_logo.png" alt="Royal Logo"></a>
                            <a href="<?php echo BASE_URL; ?>index" class="retina-logo" data-dark-logo="images/royal_logo.png" data-sticky-logo="images/royal_logo.png"><img src="images/royal_logo.png" alt="Royal Logo"></a>
                        </div>


                        <div id="primary-menu-trigger">
                            <svg class="svg-trigger" viewBox="0 0 100 100">
                                <path d="m 30,33 h 40 c 3.722839,0 7.5,3.126468 7.5,8.578427 0,5.451959 -2.727029,8.421573 -7.5,8.421573 h -20"></path>
                                <path d="m 30,50 h 40"></path>
                                <path d="m 70,67 h -40 c 0,0 -7.5,-0.802118 -7.5,-8.365747 0,-7.563629 7.5,-8.634253 7.5,-8.634253 h 20"></path>
                            </svg>
                        </div>


                        <nav class="primary-menu">

                            <ul class="one-page-menu menu-container" data-easing="easeInOutExpo" data-speed="1250" data-offset="60">
                                <li class="menu-item"><a class="menu-link" href="<?php echo BASE_URL; ?>">
                                        <div>Home</div>
                                    </a></li>
                                <li class="menu-item"><a class="menu-link" href="#">
                                        <div>Menu</div>
                                    </a>
                                    <ul class="sub-menu-container rounded-bottom">
                                        <?php if (!empty($categories)) { ?>
                                            <?php foreach ($categories as $category): ?>
                                                <li class="menu-item">
                                                    <a class="menu-link" href="<?php echo BASE_URL . 'cat/' . htmlspecialchars($category['slug']); ?>">
                                                        <div><?php echo htmlspecialchars($category['name']); ?></div>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php } else { ?>
                                            <li class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <div>No categories available</div>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <li class="menu-item"><a class="menu-link" href="<?php echo BASE_URL; ?>blog">
                                        <div>Blog</div>
                                    </a></li>
                                <li class="menu-item"><a class="menu-link" href="<?php echo BASE_URL; ?>contacts">
                                        <div>Contacts</div>
                                    </a></li>
                            </ul>

                        </nav>

                        <!-- Header Actions (Account & Cart) -->
                        <div class="d-flex align-items-center ms-auto">
                            <!-- Account Dropdown -->
                            <div class="hidden js-account-dropdown d-flex align-items-center me-2">
                                <a href="#" class="js-account-dropdown__trigger d-flex align-items-center text-white">
                                    <i class="fas fa-user me-1"></i>
                                    <span class="d-none d-md-inline me-1">
                                        <?php echo isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] 
                                            ? htmlspecialchars($_SESSION['user_first_name']) 
                                            : 'Account'; ?>
                                    </span>
                                    <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                </a>
                                <div class="js-account-dropdown__content shadow-lg rounded">
                                    <div class="js-account-dropdown__header p-3 border-bottom">
                                        <h5 class="mb-0">
                                            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                                                Welcome, <?php echo htmlspecialchars($_SESSION['user_first_name']); ?>!
                                            <?php else: ?>
                                                My Account
                                            <?php endif; ?>
                                        </h5>
                                        <small class="text-muted">
                                            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                                                <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                                            <?php else: ?>
                                                Sign in to your account
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="js-account-dropdown__items p-3">
                                        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                                            <!-- Logged In Menu -->
                                            <div class="list-group list-group-flush">
                                                <a href="<?php echo BASE_URL; ?>account" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                                    <i class="fas fa-user me-2"></i>My Profile
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>orders" class="list-group-item list-group-item-action border-0 px-0 py-2">
                                                    <i class="fas fa-shopping-bag me-2"></i>My Orders
                                                </a>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-grid">
                                                <a href="<?php echo BASE_URL; ?>logout?logout" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <!-- Not Logged In Menu -->
                                            <div class="d-grid gap-2 mb-3">
                                                <a href="<?php echo BASE_URL; ?>login" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>register" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-user-plus me-2"></i>Register
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Shopping Cart -->
                            <div class="hidden js-cart-dropdown d-flex align-items-center">
                                <a href="#" class="js-cart-dropdown__trigger d-flex align-items-center text-white">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    <span class="js-cart-dropdown__counter bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center">0</span>
                                </a>
                                <div class="js-cart-dropdown__content shadow-lg rounded">
                                    <div class="js-cart-dropdown__header p-3 border-bottom">
                                        <h4 class="mb-0">Shopping Cart</h4>
                                    </div>
                                    <div class="js-cart-dropdown__items p-3">
                                        <div class="js-cart-dropdown__item d-flex align-items-center pb-3 border-bottom">
                                            <div class="js-cart-dropdown__item-image me-3">
                                                <img src="" alt="" class="js-cart-dropdown__item-img">
                                            </div>
                                            <div class="js-cart-dropdown__item-desc flex-grow-1">
                                                <div class="js-cart-dropdown__item-title fw-bold"></div>
                                                <div class="js-cart-dropdown__item-price text-muted"></div>
                                            </div>
                                            <div class="js-cart-dropdown__item-actions">
                                                <a href="#" class="js-cart-dropdown__remove text-danger"><i class="fas fa-times"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="js-cart-dropdown__footer p-3 border-top">
                                        <div class="js-cart-dropdown__total d-flex justify-content-between mb-3">
                                            <span class="fw-bold">Total:</span>
                                            <span class="fw-bold text-danger"></span>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo BASE_URL; ?>cart" class="btn btn-sm js-cart-dropdown__view-btn">View Cart</a>
                                            <a href="<?php echo BASE_URL; ?>checkout" class="btn btn-danger btn-sm js-cart-dropdown__order-btn">Checkout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Header Actions -->

                        </div>
                    </div>
                </div>
                <div class="header-wrap-clone"></div>
        </header>

    <?php }


function sliderContainer(): void
{

    ?>


        <section id="slider" class="slider-element min-vh-100 page-section slide-img include-header" data-animate="img-to-right" style="background: url('images/hero_bg.png') center center no-repeat; background-size: cover;">
            <div class="slider-inner">

                <div class="vertical-middle">
                    <div class="container dark">
                        <div class="row">
                            <div class="col-lg-6 col-md-8 dotted-bg parallax" data-start="top: 0px; opacity: 1" data-400="top: 50px; opacity: 0.3">
                                <div class="emphasis-title" data-animate="fadeInUp">
                                    <div class="before-heading font-secondary color">Pizza & Grill</div>
                                    <h1 class="font-border"> Palermo!</h1>
                                </div>
                                <p class="lead text-white-50" data-animate="fadeInUp" data-delay="100">We're an Italian restaurant, located in the heart of the city, serving authentic Italian cuisine.</p>
                                <a data-scrollto="#delivery" data-easing="easeInOutExpo" data-speed="1250" class="button button-large button-rounded px-4 button-border button-light button-white fw-semibold" data-animate="fadeInUp" data-delay="200">Delivery</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    <?php }


function renderPagination(Paginator $paginator, string $baseUrl, int $range = 2, string $search = ''): void
{
    if ($paginator->totalPages <= 1) {
        return;
    }
    
    $startPage = max(1, $paginator->currentPage - $range);
    $endPage = min($paginator->totalPages, $paginator->currentPage + $range);
    
    $searchQuery = $search !== '' ? '&search=' . $search : '';
    ?>
    
    <div class="row mt-5">
        <div class="col-12">
            <nav aria-label="Pagination">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item <?php echo !$paginator->hasPrev() ? 'disabled' : ''; ?>">
                        <a class="page-link"
                           href="<?php echo BASE_URL . $baseUrl; ?>?page=<?php echo max(1, $paginator->currentPage - 1) . $searchQuery; ?>"
                           aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <?php
                    // First page
                    if ($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo BASE_URL . $baseUrl; ?>?page=1<?php echo $searchQuery; ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Page numbers -->
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?php echo ($i === $paginator->currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo BASE_URL . $baseUrl; ?>?page=<?php echo $i . $searchQuery; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Last page -->
                    <?php if ($endPage < $paginator->totalPages): ?>
                        <?php if ($endPage < $paginator->totalPages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo BASE_URL . $baseUrl; ?>?page=<?php echo $paginator->totalPages . $searchQuery; ?>">
                                <?php echo $paginator->totalPages; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <li class="page-item <?php echo !$paginator->hasNext() ? 'disabled' : ''; ?>">
                        <a class="page-link"
                           href="<?php echo BASE_URL . $baseUrl; ?>?page=<?php echo min($paginator->totalPages, $paginator->currentPage + 1) . $searchQuery; ?>"
                           aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    
    <?php
}

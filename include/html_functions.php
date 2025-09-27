<?php

function headerContainer(): void
{

?>


    <!DOCTYPE html>
    <html dir="ltr" lang="en-US">

    <head>

        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="author" content="SemiColonWeb" />

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



    <?php }


function footerContainer(): void
{

    ?>

        <!-- Go To Top
	============================================= -->
        <div id="gotoTop" class="fas fa-arrow-up"></div>

        <!-- JavaScripts
	============================================= -->
        <script src="js/jquery.js"></script>
        <script src="js/plugins.min.js"></script>
        <script src="js/hover-animate.js"></script>

        <!-- Footer Scripts
	============================================= -->
        <script src="js/functions.js"></script>

    <?php }


function navbarContainer(): void
{

    ?>

        <!-- Header
		============================================= -->
        <header id="header" class="transparent-header dark" data-sticky-class="dark-color" data-sticky-shrink-offset="0">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row">

                        <!-- Logo
						============================================= -->
                        <div id="logo">
                            <a href="index.php" class="standard-logo" data-dark-logo="images/royal_logo.png" data-sticky-logo="images/royal_logo.png"><img src="images/royal_logo.png" alt="Royal Logo"></a>
                            <a href="index.php" class="retina-logo" data-dark-logo="images/royal_logo.png" data-sticky-logo="images/royal_logo.png"><img src="images/royal_logo.png" alt="Royal Logo"></a>
                        </div><!-- #logo end -->


                        <div id="primary-menu-trigger">
                            <svg class="svg-trigger" viewBox="0 0 100 100">
                                <path d="m 30,33 h 40 c 3.722839,0 7.5,3.126468 7.5,8.578427 0,5.451959 -2.727029,8.421573 -7.5,8.421573 h -20"></path>
                                <path d="m 30,50 h 40"></path>
                                <path d="m 70,67 h -40 c 0,0 -7.5,-0.802118 -7.5,-8.365747 0,-7.563629 7.5,-8.634253 7.5,-8.634253 h 20"></path>
                            </svg>
                        </div>

                        <!-- Primary Navigation
						============================================= -->
                        <nav class="primary-menu">

                            <ul class="one-page-menu menu-container" data-easing="easeInOutExpo" data-speed="1250" data-offset="60">
                                <li class="menu-item"><a class="menu-link" href="https://dev.balikgstudio.eu/pizzaroyalkn/">
                                        <div>Начало</div>
                                    </a></li>
                                <li class="menu-item"><a class="menu-link" href="#">
                                        <div>Меню</div>
                                    </a>
                                    <ul class="sub-menu-container rounded-bottom">
                                        <li class="menu-item"><a class="menu-link" href="salati">
                                                <div>Салати</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="supi">
                                                <div>Супи</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="predqstiq">
                                                <div>Топли предястия</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="pileshko">
                                                <div>Ястия с пилешко</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="svinsko">
                                                <div>Ястия със свинско</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="riba">
                                                <div>Риба</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="zapekanki">
                                                <div>Запеканки</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="spageti">
                                                <div>Спагети</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="pici">
                                                <div>Пици и бургери</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="hlebcheta">
                                                <div>Хлебчета</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="sosove">
                                                <div>Сосове</div>
                                            </a></li>
                                        <li class="menu-item"><a class="menu-link" href="deserti">
                                                <div>Десерти</div>
                                            </a></li>
                                    </ul>
                                </li>
                                <li class="menu-item"><a class="menu-link" href="#">
                                        <div>Контакти</div>
                                    </a></li>
                            </ul>

                        </nav><!-- #primary-menu end -->

                        <!-- Cart
                        ============================================= -->
                        <div class="js-cart-dropdown d-flex align-items-center ms-auto">
                            <a href="#" class="js-cart-dropdown__trigger d-flex align-items-center text-white">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span class="js-cart-dropdown__counter bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center">1</span>
                            </a>
                            <div class="js-cart-dropdown__content shadow-lg rounded">
                                <div class="js-cart-dropdown__header p-3 border-bottom">
                                    <h4 class="mb-0">Shopping Cart</h4>
                                </div>
                                <div class="js-cart-dropdown__items p-3">
                                    <!-- Dummy cart item -->
                                    <div class="js-cart-dropdown__item d-flex align-items-center pb-3 border-bottom">
                                        <div class="js-cart-dropdown__item-image me-3">
                                            <img src="images/menu-items/пилешка_супа.jpg" alt="Пилешка супа" class="js-cart-dropdown__item-img">
                                        </div>
                                        <div class="js-cart-dropdown__item-desc flex-grow-1">
                                            <div class="js-cart-dropdown__item-title fw-bold">Пилешка супа</div>
                                            <div class="js-cart-dropdown__item-price text-muted">1x 4.50 лв</div>
                                        </div>
                                        <div class="js-cart-dropdown__item-actions">
                                            <a href="#" class="js-cart-dropdown__remove text-danger"><i class="fas fa-times"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="js-cart-dropdown__footer p-3 border-top">
                                    <div class="js-cart-dropdown__total d-flex justify-content-between mb-3">
                                        <span class="fw-bold">Total:</span>
                                        <span class="fw-bold text-danger">4.50 лв</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-sm js-cart-dropdown__view-btn">View Cart</a>
                                        <a href="#" class="btn btn-danger btn-sm js-cart-dropdown__order-btn">Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="header-wrap-clone"></div>
        </header><!-- #header end -->

    <?php }


function sliderContainer(): void
{

    ?>


        <!-- Slider
		============================================= -->
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

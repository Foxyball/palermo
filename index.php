<?php
require_once(__DIR__ . '/include/connect.php');
require_once(__DIR__ . '/repositories/frontend/BlogRepository.php');
include(__DIR__ . '/include/html_functions.php');

$seoData = [
    'title' => SITE_TITLE . ' - Authentic Italian Pizza & Grill Restaurant',
    'description' => 'Welcome to Palermo - Your destination for authentic Italian pizza and grill cuisine. Order online or visit us for the best Italian food experience.',
    'url' => BASE_URL
];
?>

<?php headerContainer($seoData); ?>

<title><?php echo SITE_TITLE; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/latest-blog.css">
</head>

<body class="stretched">

	<div class="body-overlay"></div>

	<div id="side-panel" class="dark" style="background: #101010 url('images/icon-bg-white.png') repeat center center;"></div>


	<div id="wrapper" class="clearfix">
		<div class="col-12 col-md-auto">

			<div class="top-links text-center">
				<ul class="top-links-container">
					<li class="top-links-item"><a href="#"><i class="fas fa-truck"></i>Order now from 9:00 AM to 10:00 PM</a></li>
					<li class="top-links-item"><a href="#"><i class="fas fa-phone"></i> 088 480 0809 or 078 98 88 71</a></li>
					<li class="top-links-item"><a href="#"><i class="fas fa-envelope"></i>support@palermo.com</a></li>
				</ul>
			</div>

		</div>

		<?php navbarContainer(); ?>

		<?php sliderContainer(); ?>


		<!-- MAIN CONTENT -->

		<section id="content" class="dark-color">
			<div class="content-wrap p-0">

				<div id="story" class="page-section">


					<div class="section m-0 p-0">
					</div>

					<div class="section dark-color m-0 p-0">
						<div class="container dark">
							<div class="clear"></div>
						</div>
					</div>
				</div>

				<div class="clear"></div>



			</div>

			<div class="section dark my-0" style="padding: 60px 0 120px; background: linear-gradient(to bottom, #101010, transparent), url('images/icon-bg-white.png') repeat center center;">
				<div class="container mx-auto" style="max-width: 1000px">
					<div class="row align-items-center">
						<div class="col-md-6 text-center text-md-start">
							<h2 class="display-4 fw-bold text-uppercase ls1" data-animate="fadeInUp">За доставка!</h2>
							<h4 class="font-secondary text-white-50" data-animate="fadeInUp" data-delay="100"> БЕЗПЛАТНА ДОСТАВКА при поръчка на стойност над 10.00 лв. в рамките на гр. Кюстендил!</h4>
						</div>
						<div class="col-md-6">
							<div class="slide-img" data-animate="img-to-left">
								<img src="images/svg/delivery.svg" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php include(__DIR__ . '/sections/latest-blog.php'); ?>

			<div id="delivery" class="section page-section dark m-0 pb-0 pb-md-5 slide-img" data-animate="img-to-left" style="background: #101010 url('images/icon-bg-white.png') repeat center center;">
				<div class="container pt-3 pb-4">
					<div class="row">
						<div class="col-sm-5" style="line-height: 1.7; z-index: 1">
							<address class="d-block mb-5">
								<div class="font-secondary h5 mb-2 color">Address:</div>
								<span class="h6 text-white ls1 fw-normal font-primary">
									Dobrich, st. Kaliakra 55<br>
									Кюстендил, България<br>
								</span>
							</address>
							<div class="font-secondary h5 mb-2 color">Call for Delivery:</div>
							<p class="d-block h6 text-white ls1 fw-normal font-primary mb-5">+359 78 98 88 71</p>
							<p class="d-block h6 text-white ls1 fw-normal font-primary mb-5">+359 885 83 51 71</p>

							<div class="font-secondary h5 mb-2 color">Email:</div>
							<a href="mailto:support@palermo.com" class="d-block h6 text-white ls1 fw-normal font-primary mb-5">support@palermo.com</a>

							<div class="font-secondary h5 mb-2 color">Working Hours:</div>
							<div class="h6 text-white ls1 fw-normal font-primary">Every day from <b>10:00 AM</b> to <b>12:00 AM</b><br>
								Delivery is available every day from <b>11:00 AM</b> to <b>11:00 PM</b></div>
						</div>
						<span class="text-uppercase text-white ls1 fw-normal font-primary"><b>FREE DELIVERY</b> for orders over <b>25.00 lv.</b></span>
					</div>
				</div>
			</div>

	</div>

	</div>

	</section>


	<?php footerContainer(); ?>

</body>

</html>

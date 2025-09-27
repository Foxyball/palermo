<?php
require_once(__DIR__ . '/include/connect.php');
include(__DIR__ . '/include/html_functions.php');
?>

<?php headerContainer(); ?>

<title>Palermo</title>
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

				<div id="todays-menu" class="section dark m-0" style="padding: 80px 0; background: #101010 url('images/icon-bg-white.png') repeat center center;">
					<div class="container">
						<div class="row">
							<div class="col-md-5 dark order-2 order-md-1">
								<div class="bottommargin">
									<div class="d-flex align-items-center dotted-bg">
										<h1 class="font-border display-4 ls1 fw-bold mb-0 ms-3">Today's Menu</h1>
									</div>
								</div>

								<div class="clear"></div>

								<div class="price-menu-warp img-hover-block" data-img="images/menu-items/пилешка_супа.jpg">
									<div class="price-header">
										<div class="price-name color">Chicken Soup</div>
										<div class="price-dots">
											<span class="separator-dots"></span>
										</div>
										<div class="price-price">300g - 4.50 lv</div>
									</div>
									<p class="price-desc">Traditional chicken soup with homemade noodles and fresh vegetables.</p>
								</div>

								<div class="price-menu-warp img-hover-block" data-img="images/menu-items/Пилешки-филенца-с-корнфлейкс.jpg">
									<div class="price-header">
										<div class="price-name color">Cornflake Chicken Fillets</div>
										<div class="price-dots">
											<span class="separator-dots"></span>
										</div>
										<div class="price-price">250g - 12.90 lv</div>
									</div>
									<p class="price-desc">Crispy chicken fillets breaded in cornflakes, served with fries.</p>
								</div>

								<div class="price-menu-warp img-hover-block" data-img="images/menu-items/burgers/double.jpg">
									<div class="price-header">
										<div class="price-name color">Double Burger</div>
										<div class="price-dots">
											<span class="separator-dots"></span>
										</div>
										<div class="price-price">400g - 15.90 lv</div>
									</div>
									<p class="price-desc">Juicy double burger with beef, cheese, lettuce, and special sauce.</p>
								</div>

							</div>
							<div class="col-md-7 order-1 order-md-2">
								<div class="slide-img" data-animate="img-to-right">
									<img src="images/svg/items.svg" alt="Today's Menu" style="max-width: 100%; height: auto;">
								</div>
							</div>
						</div>
					</div>
				</div>


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

			<div id="blog" class="section dark m-0" style="padding: 80px 0; background: #1a1a1a;">
				<div class="container">
					<div class="row mb-5">
						<div class="col-12 text-center">
							<div class="d-flex align-items-center justify-content-center dotted-bg mb-4">
								<h1 class="font-border display-4 ls1 fw-bold mb-0">Latest News</h1>
							</div>
							<p class="lead text-white-50">Discover the latest news, recipes, and stories from our kitchen</p>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="card bg-dark border-secondary h-100" style="transition: transform 0.3s ease; border: 1px solid #444;">
								<img src="images/menu-items/пилешка_супа.jpg" class="card-img-top" alt="Traditional Recipes" style="height: 200px; object-fit: cover;">
								<div class="card-body d-flex flex-column">
									<div class="mb-2">
										<span class="badge bg-danger mb-2">Recipes</span>
										<small class="text-muted d-block">September 27, 2025</small>
									</div>
									<h5 class="card-title text-white">Secrets of Traditional Chicken Soup</h5>
									<p class="card-text text-white-50 flex-grow-1">We reveal the secrets of our most popular soup - how to prepare the perfect chicken soup with homemade noodles and fresh vegetables.</p>
									<a href="#" class="btn btn-outline-light btn-sm mt-auto">Read More</a>
								</div>
							</div>
						</div>

						<div class="col-lg-4 col-md-6 mb-4">
							<div class="card bg-dark border-secondary h-100" style="transition: transform 0.3s ease; border: 1px solid #444;">
								<img src="images/menu-items/burgers/double.jpg" class="card-img-top" alt="New Menu Items" style="height: 200px; object-fit: cover;">
								<div class="card-body d-flex flex-column">
									<div class="mb-2">
										<span class="badge bg-success mb-2">News</span>
										<small class="text-muted d-block">September 25, 2025</small>
									</div>
									<h5 class="card-title text-white">New Burgers on the Menu</h5>
									<p class="card-text text-white-50 flex-grow-1">Introducing our new creations - a double burger with a unique combination of flavors. Try it today with free delivery.</p>
									<a href="#" class="btn btn-outline-light btn-sm mt-auto">Read More</a>
								</div>
							</div>
						</div>

						<div class="col-lg-4 col-md-6 mb-4">
							<div class="card bg-dark border-secondary h-100" style="transition: transform 0.3s ease; border: 1px solid #444;">
								<img src="images/svg/delivery.svg" class="card-img-top bg-secondary p-4" alt="Delivery Service" style="height: 200px; object-fit: contain;">
								<div class="card-body d-flex flex-column">
									<div class="mb-2">
										<span class="badge bg-info mb-2">Services</span>
										<small class="text-muted d-block">September 20, 2025</small>
									</div>
									<h5 class="card-title text-white">Improved Delivery in Kyustendil</h5>
									<p class="card-text text-white-50 flex-grow-1">We have expanded our delivery area and reduced delivery times. Free delivery for orders over 10 lv within the city.</p>
									<a href="#" class="btn btn-outline-light btn-sm mt-auto">Read More</a>
								</div>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-12 text-center">
							<a href="#" class="btn btn-danger btn-lg">View All Articles</a>
						</div>
					</div>
				</div>
			</div>

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
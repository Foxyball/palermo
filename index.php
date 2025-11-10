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

		<?php include(__DIR__ . '/sections/top-bar.php'); ?>

		<?php navbarContainer(); ?>

		<?php sliderContainer(); ?>


		<!-- MAIN CONTENT -->

		<section id="content" class="dark-color">

			<?php include(__DIR__ . '/sections/featured-categories.php'); ?>

			<?php include(__DIR__ . '/sections/latest-blog.php'); ?>

			<?php include(__DIR__ . '/sections/delivery-section.php'); ?>

	</div>

	</div>

	</section>


	<?php footerContainer(); ?>

</body>

</html>
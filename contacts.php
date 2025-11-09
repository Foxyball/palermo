<?php
require_once(__DIR__ . '/include/connect.php');
include(__DIR__ . '/include/html_functions.php');

function verifyRecaptcha(string $response, string $secret, ?string $remoteIp = null): bool
{
    if ($response === '') {
        return false;
    }

    $endpoint = 'https://www.google.com/recaptcha/api/siteverify';
    $payload = http_build_query([
        'secret'   => $secret,
        'response' => $response,
        'remoteip' => $remoteIp,
    ]);

    $verifyResponse = null;

    if (function_exists('curl_version')) {
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $verifyResponse = curl_exec($ch);
        curl_close($ch);
    } else {
        $verifyResponse = file_get_contents($endpoint . '?' . $payload);
    }

    if ($verifyResponse === false || $verifyResponse === null) {
        return false;
    }

    $data = json_decode($verifyResponse, true);

    return !empty($data['success']);
}

if (isset($_POST['submit_contact'])) {
    $name    = htmlspecialchars(trim($_POST['contact_name'])) ?? '';
    $email   = trim($_POST['contact_email']) ?? '';
    $phone   = htmlspecialchars(trim($_POST['contact_phone'])) ?? '';
    $subject = htmlspecialchars(trim($_POST['contact_subject'])) ?? '';
    $message = htmlspecialchars(trim($_POST['contact_message'])) ?? '';

    $recaptchaResponse = trim($_POST['g-recaptcha-response']) ?? '';
    $recaptchaSecret   = GOOGLE_RECAPTCHA_SECRET_KEY;
    $remoteIp          = $_SERVER['REMOTE_ADDR'] ?? null;

    if (!verifyRecaptcha($recaptchaResponse, $recaptchaSecret, $remoteIp)) {
        $_SESSION['error'] = 'reCAPTCHA verification failed. Please try again.';
        header('Location: contacts.php');
        exit();
    }

    if ($name === '' || $email === '' || $message === '') {
        $_SESSION['error'] = 'Please fill in all required fields.';
        header('Location: contacts.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please provide a valid email address.';
        header('Location: contacts.php');
        exit();
    }

    $_SESSION['success'] = 'Your message has been sent successfully!';
    header('Location: contacts.php');
    exit();
}

// Prepare SEO data
$seoData = [
    'title' => SITE_TITLE . ' | Contacts',
    'description' => 'Get in touch with Palermo - Authentic Italian Pizza & Grill. Visit us or contact us for reservations and inquiries.',
    'url' => BASE_URL . 'contacts'
];
?>

<?php headerContainer($seoData); ?>

<!-- GOOGLE RECAPTCHA  -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<title><?php echo SITE_TITLE; ?> | Contacts</title>
</head>

<body class="stretched">

    <div class="body-overlay"></div>

    <div id="side-panel" class="dark" style="background: #101010 url('images/icon-bg-white.png') repeat center center;">

    </div>

    <!-- Document Wrapper
    ============================================= -->
    <div id="wrapper" class="clearfix">
        <div class="col-12 col-md-auto">

            <!-- Top Links
            ============================================= -->
            <div class="top-links text-center">
                <ul class="top-links-container">
                    <li class="top-links-item"><a href="#"><i class="fas fa-truck"></i>Order now from 11:00 AM to 11:00 PM</a></li>
                    <li class="top-links-item"><a href="#"><i class="fas fa-phone"></i> 0885 83 51 71 or 078 98 88 71</a></li>
                    <li class="top-links-item"><a href="#"><i class="fas fa-envelope"></i>office@palermo.eu</a></li>
                </ul>
            </div><!-- .top-links end -->

        </div>

        <?php navbarContainer(); ?>

        <!-- Page Title
        ============================================= -->
        <section id="page-title" class="page-title-parallax page-title-dark dark" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/others/section-2.jpg') center center no-repeat; background-size: cover; padding: 120px 0;">
            <div class="container clearfix">
                <h1>Contact Us</h1>
                <span>We'd love to hear from you</span>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                </ol>
            </div>
        </section><!-- #page-title end -->



        <!-- MAIN CONTENT -->

        <section id="content" class="dark-color">
            <div class="content-wrap" style="padding: 80px 0;">

                <div class="container">
                    <div class="row gx-5">

                        <!-- Contact Form -->
                        <div class="col-lg-8 mb-5">
                            <div class="card bg-dark border-secondary">
                                <div class="card-header bg-danger text-white">
                                    <h3 class="mb-0"><i class="fas fa-envelope me-2"></i>Send us a message</h3>
                                </div>
                                <div class="card-body p-4">

                                    <?php if (isset($_SESSION['success'])) { ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php
                                            echo $_SESSION['success'];
                                            unset($_SESSION['success']);
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>

                                    <?php } else if (isset($_SESSION['error'])) { ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php
                                            echo $_SESSION['error'];
                                            unset($_SESSION['error']);
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php } ?>

                                    <form id="contact-form" name="contact-form" action="" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="contact-name" class="form-label text-white">Name *</label>
                                                <input type="text" class="form-control bg-dark text-white border-secondary" id="contact-name" name="contact_name" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="contact-email" class="form-label text-white">Email *</label>
                                                <input type="email" class="form-control bg-dark text-white border-secondary" id="contact-email" name="contact_email" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="contact-phone" class="form-label text-white">Phone</label>
                                                <input type="tel" class="form-control bg-dark text-white border-secondary" id="contact-phone" name="contact_phone">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="contact-subject" class="form-label text-white">Subject</label>
                                                <select class="form-select bg-dark text-white border-secondary" id="contact-subject" name="contact_subject">
                                                    <option value="">Select a subject...</option>
                                                    <option value="order">Order</option>
                                                    <option value="complaint">Complaint</option>
                                                    <option value="suggestion">Suggestion</option>
                                                    <option value="general">General Question</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact-message" class="form-label text-white">Message *</label>
                                            <textarea class="form-control bg-dark text-white border-secondary" id="contact-message" name="contact_message" rows="6" required placeholder="Write your message here..."></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_RECAPTCHA_SITE_KEY; ?>"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="contact-privacy" name="contact_privacy" required>
                                                <label class="form-check-label text-white-50" for="contact-privacy">
                                                    I agree to the processing of my personal data *
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-danger btn-lg" name="submit_contact">
                                                <i class="fas fa-paper-plane me-2"></i>Send Message
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-lg-4">
                            <div class="card bg-dark border-secondary mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contact Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h5 class="text-danger"><i class="fas fa-map-marker-alt me-2"></i>Address</h5>
                                        <p class="text-white-50 mb-0">2 Vlasina Street<br><?php echo SITE_TITLE; ?>, Bulgaria</p>
                                    </div>
                                    <div class="mb-4">
                                        <h5 class="text-danger"><i class="fas fa-phone me-2"></i>Phone Numbers</h5>
                                        <p class="text-white-50 mb-1">
                                            <a href="tel:+359889888871" class="text-white-50 text-decoration-none">+359 78 98 88 71</a>
                                        </p>
                                        <p class="text-white-50 mb-0">
                                            <a href="tel:+359885835171" class="text-white-50 text-decoration-none">+359 885 83 51 71</a>
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <h5 class="text-danger"><i class="fas fa-envelope me-2"></i>Email</h5>
                                        <p class="text-white-50 mb-0">
                                            <a href="mailto:office@palermo.eu" class="text-white-50 text-decoration-none">office@palermo.eu</a>
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <h5 class="text-danger"><i class="fas fa-clock me-2"></i>Working Hours</h5>
                                        <p class="text-white-50 mb-1">Every Day: <strong>10:00 AM - 12:00 AM</strong></p>
                                        <p class="text-white-50 mb-0">Delivery: <strong>11:00 AM - 11:00 PM</strong></p>
                                    </div>
                                    <div>
                                        <h5 class="text-danger"><i class="fas fa-truck me-2"></i>Delivery</h5>
                                        <p class="text-white-50 mb-0">
                                            <strong class="text-success">FREE DELIVERY</strong><br>
                                            for orders over <strong>10.00 BGN</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </section><!-- #content end -->

        <?php footerContainer(); ?>

    </div><!-- #wrapper end -->

</body>

</html>
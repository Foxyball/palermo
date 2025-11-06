<?php
http_response_code(404);
require_once(__DIR__ . '/include/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - <?php echo SITE_TITLE; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/404.css">
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-title">Page Not Found</div>
        <div class="error-message">
            Sorry, the page you are looking for doesn't exist or has been moved.
        </div>
        
        <?php if (isset($_SERVER['REQUEST_URI'])): ?>
            <div class="requested-url">
                <strong>Requested URL:</strong> <?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>
            </div>
        <?php endif; ?>
        
        <div class="error-message">
            You can try:
            <ul>
                <li>Checking the URL for typos</li>
                <li>Going back to the previous page</li>
                <li>Visiting our homepage</li>
            </ul>
        </div>
        
        <a href="<?php echo BASE_URL; ?>" class="home-link">← Back to Home</a>
    </div>
    
</body>
</html>
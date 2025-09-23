<?php
// Set the HTTP response code to 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Error 404</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            padding: 50px;
            margin: 0;
        }
        .error-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .error-message {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #666;
        }
        .home-link {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .home-link:hover {
            background-color: #2980b9;
        }
        .requested-url {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #e74c3c;
            margin: 20px 0;
            font-family: monospace;
            text-align: left;
        }
    </style>
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
            <ul style="text-align: left; display: inline-block;">
                <li>Checking the URL for typos</li>
                <li>Going back to the previous page</li>
                <li>Visiting our homepage</li>
            </ul>
        </div>
        
        <a href="/palermo" class="home-link">← Back to Home</a>
    </div>
    
    <script>
        // Optional: Log the 404 error for debugging
        console.log('404 Error - Page not found:', window.location.href);
    </script>
</body>
</html>
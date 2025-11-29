<?php

class EmailTemplateGenerator
{
    private string $siteTitle;
    private string $baseUrl;

    public function __construct()
    {
        $this->siteTitle = SITE_TITLE;
        $this->baseUrl = "http://" . $_SERVER['HTTP_HOST'] . BASE_URL;
    }


    public function generateWelcomeEmail(string $email, string $resetLink): string
    {
        $title = "Welcome to {$this->siteTitle}";
        $heading = "Welcome aboard!";
        $content = $this->getWelcomeContent($email, $resetLink);

        return $this->buildEmailTemplate($title, $heading, $content, $email);
    }

    public function generateTestAccountEmail(string $email, string $password): string
    {
        $title = "Welcome to {$this->siteTitle} - Test Account";
        $heading = "Your Test Account is Ready!";
        $content = $this->getTestAccountContent($email, $password);

        return $this->buildEmailTemplate($title, $heading, $content, $email, true);
    }

    public function generateOrderConfirmationEmail(string $email, int $orderId, float $totalAmount, array $items, string $orderAddress, string $orderPhone, ?string $message = null): string
    {
        $title = "Order Confirmation - {$this->siteTitle}";
        $heading = "Thank You for Your Order!";
        $content = $this->getOrderConfirmationContent($orderId, $totalAmount, $items, $orderAddress, $orderPhone, $message);

        return $this->buildEmailTemplate($title, $heading, $content, $email);
    }

    private function buildEmailTemplate(string $title, string $heading, string $content, string $email, bool $isTestAccount = false): string
    {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title}</title>
            {$this->getEmailStyles()}
        </head>
        <body>
            <div class='email-container'>
                {$this->getEmailHeader($isTestAccount)}
                <div class='content'>
                    <h2>{$heading}</h2>
                    {$content}
                </div>
                {$this->getEmailFooter($email)}
            </div>
        </body>
        </html>";
    }


    private function getEmailStyles(): string
    {
        $cssUrl = $this->baseUrl . "css/email-template.css";
        return "<link rel='stylesheet' href='{$cssUrl}'>";
    }


    private function getEmailHeader(bool $isTestAccount = false): string
    {
        $testBadge = $isTestAccount ? "<div class='test-badge'>TEST ACCOUNT</div>" : "";

        return "<div class='header'>
            <h1>{$this->siteTitle}</h1>
            {$testBadge}
        </div>";
    }


    private function getEmailFooter(string $email): string
    {
        $currentYear = date('Y');

        return "<div class='footer'>
            <p><strong>{$this->siteTitle}</strong></p>
            <p>This email was sent to {$email}</p>
            <div class='social-links'>
                <a href='{$this->baseUrl}'>Visit Website</a> |
                <a href='{$this->baseUrl}contacts'>Contact Us</a>
            </div>
            <p style='margin-top: 15px; font-size: 12px; color: #999999;'>
                © {$currentYear} {$this->siteTitle}. All rights reserved.
            </p>
        </div>";
    }


    private function getWelcomeContent(string $email, string $resetLink): string
    {
        return "
        <p>Hello and welcome to {$this->siteTitle}! We're excited to have you join our community.</p>
        
        <p>Your account has been successfully created with the email address: <strong>{$email}</strong></p>
        
        <div class='info-box'>
            <h3>Next Step: Set Your Password</h3>
            <p>To complete your account setup and ensure security, please click the button below to create your password. This link will expire in 24 hours for your security.</p>
        </div>
        
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$resetLink}' class='cta-button'>Set My Password</a>
        </div>
        
        <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
        <p style='word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 14px;'>{$resetLink}</p>
        
        <p>If you didn't expect this email or have any questions, please contact our support team.</p>
        
        <p>Thank you for choosing {$this->siteTitle}!</p>";
    }


    private function getTestAccountContent(string $email, string $password): string
    {
        $loginUrl = $this->baseUrl . "admin/login.php";

        return "
        <p>Hello! Your test account for {$this->siteTitle} has been successfully created and is ready to use.</p>
        
        <div class='credentials-box'>
            <h3>🔑 Your Login Credentials</h3>
            <div class='credential-item'>
                <span class='credential-label'>Email Address:</span>
                <div class='credential-value'>{$email}</div>
            </div>
            <div class='credential-item'>
                <span class='credential-label'>Temporary Password:</span>
                <div class='credential-value'>{$password}</div>
            </div>
        </div>
        
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$loginUrl}' class='cta-button'>Login to Your Account</a>
        </div>
        
        <div class='warning-box'>
            <h3>⚠️ Security Reminder</h3>
            <p><strong>This is a test account with a default password.</strong> For security reasons, please change your password after your first login. You can do this from your account settings.</p>
        </div>
        
        <p>If the login button above doesn't work, you can copy and paste this link into your browser:</p>
        <p style='word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 14px;'>{$loginUrl}</p>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        
        <p>Welcome to {$this->siteTitle}!</p>";
    }


    private function getOrderConfirmationContent(int $orderId, float $totalAmount, array $items, string $orderAddress, string $orderPhone, ?string $message = null): string
    {
        $orderDetailsUrl = $this->baseUrl . "order-detail?id={$orderId}";
        $bgnToEurRate = BGN_TO_EUR_RATE;
        
        // Build items list
        $itemsHtml = "";
        foreach ($items as $item) {
            $itemPriceBgn = number_format($item['price'], 2);
            $itemPriceEur = number_format($item['price'] / $bgnToEurRate, 2);
            $itemTotalBgn = number_format($item['item_price'] * $item['quantity'], 2);
            $itemTotalEur = number_format($item['item_price'] * $item['quantity'] / $bgnToEurRate, 2);
            $qty = $item['quantity'];
            $productName = htmlspecialchars($item['name'] ?? 'Product');
            
            $itemsHtml .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #eeeeee;'>{$productName}</td>
                <td style='padding: 12px; border-bottom: 1px solid #eeeeee; text-align: center;'>{$qty}</td>
                <td style='padding: 12px; border-bottom: 1px solid #eeeeee; text-align: right;'>{$itemPriceBgn} BGN / {$itemPriceEur} €</td>
                <td style='padding: 12px; border-bottom: 1px solid #eeeeee; text-align: right; font-weight: 600;'>{$itemTotalBgn} BGN / {$itemTotalEur} €</td>
            </tr>";
            
            // Add addons if any
            if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addon) {
                    $addonPriceBgn = number_format($addon['price'], 2);
                    $addonPriceEur = number_format($addon['price'] / $bgnToEurRate, 2);
                    $addonName = htmlspecialchars($addon['name'] ?? 'Add-on');
                    $itemsHtml .= "
                    <tr>
                        <td style='padding: 8px 12px 8px 30px; border-bottom: 1px solid #f8f9fa; color: #666; font-size: 14px;'>+ {$addonName}</td>
                        <td style='padding: 8px 12px; border-bottom: 1px solid #f8f9fa;'></td>
                        <td style='padding: 8px 12px; border-bottom: 1px solid #f8f9fa; text-align: right; color: #666; font-size: 14px;'>{$addonPriceBgn} BGN / {$addonPriceEur} €</td>
                        <td style='padding: 8px 12px; border-bottom: 1px solid #f8f9fa;'></td>
                    </tr>";
                }
            }
        }
        
        $totalBgn = number_format($totalAmount, 2);
        $totalEur = number_format($totalAmount / $bgnToEurRate, 2);
        $messageHtml = $message ? "
        <div class='info-box'>
            <h3>Order Notes</h3>
            <p style='font-style: italic;'>{$message}</p>
        </div>" : "";

        return "
        <p>Your order has been successfully placed! We're preparing your delicious meal and will deliver it to you soon.</p>
        
        <div class='info-box'>
            <h3>Order Details</h3>
            <p><strong>Order Number:</strong> #{$orderId}</p>
            <p><strong>Delivery Address:</strong><br>{$orderAddress}</p>
            <p><strong>Contact Phone:</strong> {$orderPhone}</p>
        </div>
        
        {$messageHtml}
        
        <div style='margin: 30px 0;'>
            <h3 style='color: #9c0000; margin-bottom: 15px;'>Order Summary</h3>
            <table style='width: 100%; border-collapse: collapse; background-color: #ffffff; border: 1px solid #eeeeee;'>
                <thead>
                    <tr style='background-color: #f8f9fa;'>
                        <th style='padding: 12px; text-align: left; border-bottom: 2px solid #9c0000;'>Item</th>
                        <th style='padding: 12px; text-align: center; border-bottom: 2px solid #9c0000;'>Qty</th>
                        <th style='padding: 12px; text-align: right; border-bottom: 2px solid #9c0000;'>Price</th>
                        <th style='padding: 12px; text-align: right; border-bottom: 2px solid #9c0000;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
                <tfoot>
                    <tr style='background-color: #f8f9fa;'>
                        <td colspan='3' style='padding: 15px; text-align: right; font-weight: 700; font-size: 18px; border-top: 2px solid #9c0000;'>Total:</td>
                        <td style='padding: 15px; text-align: right; font-weight: 700; font-size: 18px; color: #9c0000; border-top: 2px solid #9c0000;'>{$totalBgn} BGN / {$totalEur} €</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$orderDetailsUrl}' class='cta-button'>View Order Details</a>
        </div>
        
        <p>You can track your order status anytime by logging into your account.</p>
        
        <p>If you have any questions about your order, please don't hesitate to contact us.</p>
        
        <p>Thank you for choosing {$this->siteTitle}!</p>";
    }
}


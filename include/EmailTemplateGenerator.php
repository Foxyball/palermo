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
        return "<style>
            body { 
                margin: 0; 
                padding: 0; 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f5f5f5;
                line-height: 1.6;
            }
            .email-container { 
                max-width: 600px; 
                margin: 0 auto; 
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .header { 
                background-color: #9c0000; 
                color: #ffffff; 
                padding: 30px 20px;
                text-align: center;
            }
            .header h1 { 
                margin: 0; 
                font-size: 28px;
                font-weight: 700;
                letter-spacing: 1px;
            }
            .test-badge {
                background-color: #ff6b35;
                color: #ffffff;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                margin-top: 10px;
                display: inline-block;
                letter-spacing: 1px;
            }
            .content { 
                padding: 40px 30px;
                color: #444444;
            }
            .content h2 {
                color: #9c0000;
                margin: 0 0 20px 0;
                font-size: 24px;
                font-weight: 600;
            }
            .content p {
                margin: 0 0 20px 0;
                font-size: 16px;
                color: #555555;
            }
            .cta-button { 
                display: inline-block;
                background-color: #9c0000;
                color: #ffffff !important;
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: 600;
                font-size: 16px;
                margin: 20px 0;
                transition: background-color 0.3s ease;
            }
            .cta-button:hover {
                background-color: #7a0000;
            }
            .info-box {
                background-color: #f8f9fa;
                border-left: 4px solid #9c0000;
                padding: 20px;
                margin: 25px 0;
                border-radius: 0 5px 5px 0;
            }
            .info-box h3 {
                color: #9c0000;
                margin: 0 0 10px 0;
                font-size: 18px;
                font-weight: 600;
            }
            .credentials-box {
                background-color: #f8f9fa;
                border: 2px solid #9c0000;
                padding: 25px;
                margin: 25px 0;
                border-radius: 8px;
                text-align: center;
            }
            .credentials-box h3 {
                color: #9c0000;
                margin: 0 0 15px 0;
                font-size: 18px;
                font-weight: 600;
            }
            .credential-item {
                background-color: #ffffff;
                padding: 12px;
                margin: 10px 0;
                border-radius: 5px;
                border-left: 4px solid #9c0000;
                text-align: left;
            }
            .credential-label {
                font-weight: 600;
                color: #9c0000;
                display: block;
                font-size: 14px;
                margin-bottom: 5px;
            }
            .credential-value {
                font-family: 'Courier New', monospace;
                background-color: #f8f9fa;
                padding: 8px;
                border-radius: 3px;
                font-size: 16px;
                word-break: break-all;
                color: #333;
            }
            .warning-box {
                background-color: #fff3cd;
                border-left: 4px solid #ff6b35;
                padding: 20px;
                margin: 25px 0;
                border-radius: 0 5px 5px 0;
            }
            .warning-box h3 {
                color: #ff6b35;
                margin: 0 0 10px 0;
                font-size: 16px;
                font-weight: 600;
            }
            .warning-box p {
                margin: 0;
                font-size: 14px;
                color: #856404;
            }
            .footer {
                background-color: #f8f9fa;
                padding: 25px 30px;
                text-align: center;
                border-top: 1px solid #eeeeee;
                color: #666666;
                font-size: 14px;
            }
            .footer a {
                color: #9c0000;
                text-decoration: none;
            }
            .footer a:hover {
                text-decoration: underline;
            }
            .social-links {
                margin: 15px 0;
            }
            .social-links a {
                display: inline-block;
                margin: 0 10px;
                color: #9c0000;
                text-decoration: none;
            }
            @media only screen and (max-width: 600px) {
                .email-container {
                    width: 100% !important;
                }
                .content {
                    padding: 20px 15px !important;
                }
                .header {
                    padding: 20px 15px !important;
                }
                .header h1 {
                    font-size: 24px !important;
                }
                .cta-button {
                    display: block !important;
                    text-align: center !important;
                    margin: 20px 0 !important;
                }
                .credentials-box {
                    padding: 15px !important;
                }
            }
        </style>";
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
}

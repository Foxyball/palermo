<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function sendEmail(
    string $to,
    string $toName,
    string $subject,
    string $body,
    ?string $altBody = null,
    array $cc = [],
    array $bcc = [],
    array $attachments = []
): bool {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '2e8c8b7fb68bbe';
        $mail->Password   = '3790b2afcc9d97';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Additional settings for development
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom('palermo@no-reply.com', 'Palermo');
        $mail->addAddress($to, $toName);

        // CC and BCC
        foreach ($cc as $ccAddress) {
            $mail->addCC($ccAddress);
        }
        foreach ($bcc as $bccAddress) {
            $mail->addBCC($bccAddress);
        }

        // Attachments
        foreach ($attachments as $file => $name) {
            $mail->addAttachment($file, $name);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?? strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

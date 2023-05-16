<?php

namespace Application\Lib\AuthTools;

require 'mailer/src/Exception.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthTools
{
    public static function generate_activation_code(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function send_activation_email(string $email, string $activation_code): bool
    {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host = getenv('SMTP_HOST');
            $mail->Port = getenv('SMTP_PORT');
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Username = getenv('SENDER_EMAIL_ADDRESS');
            $mail->Password = getenv('SENDER_EMAIL_PASSWORD');

            // create the activation link
            $appUrl = getenv("APP_URL");
            $activation_link = $appUrl . "/index.php?action=activate&email=$email&activation_code=$activation_code";

            // Set email content and details
            $mail->setFrom(getenv('SENDER_EMAIL_ADDRESS'), 'Sender Name');
            $mail->addAddress($email);
            $mail->Subject = 'Camagru — Please activate your account';
            $mail->Body = <<<MESSAGE
                Hi,
                Please click the following link to activate your account:
                $activation_link
                MESSAGE;

            // Send the email
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new \Exception('Failed to send email: ' . $mail->ErrorInfo);
        }
    }
}

<?php

namespace Application\Lib\MailingTools;

require 'mailer/src/Exception.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';
require_once 'config/setup.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailingTools
{
    public static function generate_activation_code(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function generate_password_token(): string
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
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Username = SENDER_EMAIL_ADDRESS;
            $mail->Password = SENDER_EMAIL_PASSWORD;

            // create the activation link
            $appUrl = APP_URL;
            $activation_link = $appUrl . "/index.php?action=activate&email=$email&activation_code=$activation_code";

            // Set email content and details
            $mail->setFrom(SENDER_EMAIL_ADDRESS, 'Camagru');
            $mail->addAddress($email);
            $mail->Subject = 'Camagru >> Please activate your account';
            $mail->Body = <<<MESSAGE
                Hi,

                Please click on the following link to activate your account:
                
                $activation_link
                MESSAGE;

            // Send the email
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new \Exception('Failed to send email: ' . $mail->ErrorInfo);
        }
    }

    public static function send_forgotten_password_email(string $email, string $token): bool
    {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Username = SENDER_EMAIL_ADDRESS;
            $mail->Password = SENDER_EMAIL_PASSWORD;

            // create the activation link
            $appUrl = APP_URL;
            $password_link = $appUrl . "/index.php?action=new_password&email=$email&token=$token";

            // Set email content and details
            $mail->setFrom(SENDER_EMAIL_ADDRESS, 'Camagru');
            $mail->addAddress($email);
            $mail->Subject = 'Camagru >> Reset your password';
            $mail->Body = <<<MESSAGE
                Hi,

                Please click on the following link to reset your password:

                $password_link
                MESSAGE;

            // Send the email
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new \Exception('Failed to send email: ' . $mail->ErrorInfo);
        }
    }

    public static function notify_post_author(string $email, string $username, string $post_id): bool
    {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Username = SENDER_EMAIL_ADDRESS;
            $mail->Password = SENDER_EMAIL_PASSWORD;

            // create the activation link
            $appUrl = APP_URL;
            $post_link = $appUrl . "/index.php?action=post&id=$post_id";

            // Set email content and details
            $mail->setFrom(SENDER_EMAIL_ADDRESS, 'Camagru');
            $mail->addAddress($email);
            $mail->Subject = 'Camagru >> New comment on your post';
            $mail->Body = <<<MESSAGE
                Hi $username,

                Someone commented on your post. Click on the following link to see it:

                $post_link
                MESSAGE;

            // Send the email
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new \Exception('Failed to send email: ' . $mail->ErrorInfo);
        }   
    }
}

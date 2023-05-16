<?php

require 'mailer/src/Exception.php';
require 'mailer/src/PHPMailer.php';
require 'mailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Configure SMTP settings
    $mail->isSMTP();
    $mail->Host = getenv('SMTP_HOST'); // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = getenv('SENDER_EMAIL_ADDRESS'); // Replace with your SMTP username
    $mail->Password = getenv('SENDER_EMAIL_PASSWORD'); // Replace with your SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587; // Replace with your SMTP port number

    // Set email content and details
    $mail->setFrom(getenv('SENDER_EMAIL_ADDRESS'), 'Sender Name');
    $mail->addAddress('magritt98@yahoo.com', 'Recipient Name');
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email from PHPMailer';

    // Send the email
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo 'Failed to send email: ' . $mail->ErrorInfo;
}

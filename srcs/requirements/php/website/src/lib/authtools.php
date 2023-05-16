<?php

namespace Application\Lib\AuthTools;

class AuthTools
{
    public static function generate_activation_code(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function send_activation_email(string $email, string $activation_code): bool
    {
        $appUrl = getenv("APP_URL");
        $senderEmailAddress = getenv("SENDER_EMAIL_ADDRESS");
        $senderEmailPassword = getenv("SENDER_EMAIL_PASSWORD");
        $smtpHost = getenv("SMTP_HOST");
        $smtpPort = getenv("SMTP_PORT");

        // create the activation link
        $activation_link = $appUrl . "/index.php?action=activate&email=$email&activation_code=$activation_code";

        // set email subject & body
        $subject = 'Please activate your account';
        $message = <<<MESSAGE
            Hi,
            Please click the following link to activate your account:
            $activation_link
            MESSAGE;
        // email header
        $header = "From:" . $senderEmailAddress;

        // set SMTP server details
        $smtp = array(
            'host' => $smtpHost,
            'port' => $smtpPort,
            'username' => $senderEmailAddress,
            'password' => $senderEmailPassword,
        );

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // ini_set('SMTP', $smtp['host']);
        // ini_set('smtp_port', $smtp['port']);
        // ini_set('sendmail_from', $senderEmailAddress);
        // ini_set('sendmail_path', "/usr/sbin/sendmail -t -i -f $senderEmailAddress");

        // set SMTP credentials
        // ini_set('smtp_username', $smtp['username']);
        // ini_set('smtp_password', $smtp['password']);

        // send the email
        return mail($email, $subject, nl2br($message), $header);
    }
}

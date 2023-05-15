<?php

namespace Application\Lib\Auth;

class Auth
{
    public static function generate_activation_code(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function send_activation_email(string $email, string $activation_code): void
    {
        $appUrl = getenv("APP_URL");
        $senderEmailAddress = getenv("SENDER_EMAIL_ADDRESS");

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

        // send the email
        mail($email, $subject, nl2br($message), $header);
    }
}

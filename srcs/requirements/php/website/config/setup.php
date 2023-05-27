<?php
define('SMTP_PORT', intval(getenv('SMTP_PORT')));
define('SMTP_HOST', getenv('SMTP_HOST'));
define('SENDER_EMAIL_ADDRESS', getenv('SENDER_EMAIL_ADDRESS'));
define('SENDER_EMAIL_PASSWORD', getenv('SENDER_EMAIL_PASSWORD'));

$inputString = getenv('SESSION_MANAGER');
$regex = '/e(\d{1,2})r(\d{1,2})p(\d{1,2})/i';

if (preg_match($regex, $inputString, $matches)) {
    $extractedString = $matches[0];
    define('APP_URL', 'http://' . $extractedString . ':8000');
} else {
    define('APP_URL', getenv('APP_URL'));
}
?>
<?php
define('SMTP_PORT', intval(getenv('SMTP_PORT')));
define('SMTP_HOST', getenv('SMTP_HOST'));
define('SENDER_EMAIL_ADDRESS', getenv('SENDER_EMAIL_ADDRESS'));
define('SENDER_EMAIL_PASSWORD', getenv('SENDER_EMAIL_PASSWORD'));
define('APP_URL', getenv('APP_URL'));
?>
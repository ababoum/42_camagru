<?php
//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = getenv('MYSQL_HOST');
// Database use name
$user = getenv('MYSQL_USER');
//database user password
$pass = getenv('MYSQL_PASSWORD');
// Database name
$dbname = getenv('MYSQL_DATABASE');

// check the MySQL connection status
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    echo ("Connection failed: " . $conn);
}

?>
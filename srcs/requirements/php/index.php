<?php
//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = getenv('MYSQL_HOST');

// Database use name
$user = getenv('MYSQL_USER');

//database user password
$pass = getenv('MYSQL_PASSWORD');

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to MySQL server successfully!";
}
?>
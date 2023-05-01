<?php
session_start();
include "db_conn.php";
if (
    isset($_POST['uname']) && isset($_POST['password']) &&
    isset($_POST['email']) && isset($_POST['re_password'])
) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $uname = validate($_POST['uname']);
    $email = validate($_POST['email']);
    $pass = validate($_POST['password']);
    $re_pass = validate($_POST['re_password']);
    if (empty($uname)) {
        header("Location: signup_form.php?error=Username is required");
        exit();
    } else if (empty($email)) {
        header("Location: signup_form.php?error=Email is required");
        exit();
    } else if (empty($pass)) {
        header("Location: signup_form.php?error=Password is required");
        exit();
    } else if (empty($re_pass)) {
        header("Location: signup_form.php?error=Password confirmation is required");
        exit();
    } else {
        // Check if the passwords match
        if ($pass !== $re_pass) {
            header("Location: signup_form.php?error=The passwords do not match");
            exit();
        }
        // Check the password's complexity
        $uppercase = preg_match('@[A-Z]@', $pass);
        $lowercase = preg_match('@[a-z]@', $pass);
        $number    = preg_match('@[0-9]@', $pass);
        $specialChars = preg_match('@[^\w]@', $pass);
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($pass) < 8) {
            header("Location: signup_form.php?error=Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.");
            exit();
        }
        // Hash the password with a generic salt
        $pass = md5($pass . "random");
        // Check the email's format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: signup_form.php?error=Invalid email format");
            exit();
        }
        // Check if the username is already taken
        $sql = "SELECT * FROM users WHERE username='$uname'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) === 1) {
            header("Location: signup_form.php?error=Username already taken");
            exit();
        }
        // Check if the email is already taken
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) === 1) {
            header("Location: signup_form.php?error=Email already taken");
            exit();
        }
        // Insert the new user into the database
        $sql = "INSERT INTO users(username, email, password) VALUES('$uname', '$email', '$pass')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header("Location: index.php");
            exit();
        } else {
            header("Location: signup_form.php?error=Unknown error occurred. Please try again");
            exit();
        }
    }
} else {
    header("Location: signup_form.php");
    exit();
}
?>
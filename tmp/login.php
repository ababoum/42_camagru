<?php
session_start();
include "db_conn.php";
if (isset($_POST['uname']) && isset($_POST['password'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);
    if (empty($uname)) {
        header("Location: login_form.php?error=Username is required");
        exit();
    } else if (empty($pass)) {
        header("Location: login_form.php?error=Password is required");
        exit();
    } else {
        $pass = md5($pass . "random");
        $sql = "SELECT * FROM users WHERE username='$uname' AND password='$pass'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['username'] === $uname && $row['password'] === $pass) {
                // Successful login
                header("Location index.php");
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['id'] = $row['id'];
                exit();
            } else {
                header("Location: login_form.php?error=Incorrect username or password");
                exit();
            }
        } else {
            header("Location: login_form.php?error=Incorrect username or password");
            exit();
        }
    }
} else {
    header("Location: login_form.php");
    exit();
}
?>
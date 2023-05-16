<?php

require_once('src/controllers/comment/add.php');
require_once('src/controllers/homepage.php');
require_once('src/controllers/post.php');
require_once('src/controllers/login.php');
require_once('src/controllers/signup.php');
require_once('src/controllers/profile.php');
require_once('src/controllers/auth.php');

use Application\Controllers\Comment\Add\AddComment;
use Application\Controllers\Homepage\Homepage;
use Application\Controllers\Post\Post;
use Application\Controllers\Login\Login;
use Application\Controllers\Signup\Signup;
use Application\Controllers\Profile\Profile;
use Application\Controllers\Auth\Auth;

session_start();
try {
    // Routes availables for non logged in users
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // AN ACTION IS REQUESTED
        if (isset($_GET['action']) && $_GET['action'] !== '') {
            // Received login form data
            if ($_GET['action'] === 'login' && isset($_POST['username']) && isset($_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                (new Login())->logIn($username, $password);
            }
            // Request the login form
            else if ($_GET['action'] === 'login') {
                (new Login())->execute();
            }
            // Signup process
            elseif ($_GET['action'] === 'signup') {
                // Receive signup form data
                if (
                    isset($_POST['username']) && isset($_POST['password'])
                    && isset($_POST['re_password']) && isset($_POST['email'])
                ) {
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $re_password = $_POST['re_password'];
                    $email = $_POST['email'];
                    (new Signup())->signUp($username, $password, $re_password, $email);
                }
                // Empty signup form
                else {
                    (new Signup())->execute();
                }
            } else if ($_GET['action'] === 'reset_password') {
                // Received reset password form data
                if (isset($_POST['email'])) {
                    $email = $_POST['email'];
                    (new Login())->send_password_link($email);
                }
                // Request the empty email form
                else {
                    (new Login())->reset_password_email_form();
                }
            }
            // New password within reset
            else if ($_GET['action'] === 'new_password') {
                // Received new password form data
                if (isset($_POST['password']) && isset($_POST['re_password'])) {
                    $password = $_POST['password'];
                    $re_password = $_POST['re_password'];
                    $email = $_SESSION['email'];
                    $token = $_SESSION['token'];
                    (new Login())->update_password($token, $email, $password, $re_password);
                }
                // Received reset password token (after opening the link in the email)
                else if (isset($_GET['token']) && isset($_GET['email'])) {
                    $token = $_GET['token'];
                    $email = $_GET['email'];
                    (new Login())->reset_password($token, $email);
                }
                // Empty form for new password 
                else if (isset($_SESSION['email']) && isset($_SESSION['token'])) {
                    $email = $_SESSION['email'];
                    $token = $_SESSION['token'];
                    (new Login())->new_password_form($token, $email);
                }
            } else {
                throw new Exception("The page requested doesn't exist, or you need to be <b>logged in</b> to access it.");
            }
        } else {
            // NO ACTION IS REQUESTED
            (new Login())->execute();
        }
    }
    // Routes availables for logged in users
    else {
        // AN ACTION IS REQUESTED
        if (isset($_GET['action']) && $_GET['action'] !== '') {
            // POST RELATED ROUTES
            if ($_GET['action'] === 'post') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $id = $_GET['id'];

                    (new Post())->execute($id);
                } else {
                    throw new Exception('No image id sent');
                }
            }
            // USER RELATED ROUTES
            else if ($_GET['action'] === 'update_user') {
                // Received update form data
                $username = $_POST['username'] ?? "";
                $email = $_POST['email'] ?? "";
                $password = $_POST['password'] ?? "";
                $re_password = $_POST['re_password'] ?? "";
                $id = $_SESSION['id'] ?? "";
                (new Profile())->update_user($id, $username, $email, $password, $re_password);
            }
            // COMMENT RELATED ROUTES
            elseif ($_GET['action'] === 'addComment') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $id = $_GET['id'];

                    (new AddComment())->execute($id, $_POST);
                } else {
                    throw new Exception('No image id sent');
                }
            }
            // LOGOUT RELATED ROUTES
            else if ($_GET['action'] === 'logout') {
                session_destroy();
                header('Location: index.php');
            }
            // PROFILE RELATED ROUTES
            else if ($_GET['action'] === 'profile') {
                (new Profile())->execute();
            }
            // ACTIVATION RELATED ROUTES
            else if ($_GET['action'] === 'activate') {
                if (isset($_GET['email']) && isset($_GET['activation_code'])) {
                    $email = $_GET['email'];
                    $activation_code = $_GET['activation_code'];
                    (new Auth())->activate_user($email, $activation_code);
                } else {
                    throw new Exception('No email or activation code sent.');
                }
            } else if ($_GET['action'] === 'resend_activation') {
                if (isset($_SESSION['id'])) {
                    $user_id = $_SESSION['id'];
                    (new Auth())->resend_activation($user_id);
                } else {
                    throw new Exception("Current user cannot be identified.");
                }
            }
            // UNDEFINED ROUTE
            else {
                throw new Exception("The page requested doesn't exist.");
            }
        }
        // NO ACTION IS REQUESTED
        else {
            (new Homepage())->execute();
        }
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();

    require('templates/error.php');
}

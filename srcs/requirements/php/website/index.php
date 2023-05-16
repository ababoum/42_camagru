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
        if (isset($_GET['action']) && $_GET['action'] !== '') {
            // AN ACTION IS REQUESTED
            if ($_GET['action'] === 'login') {
                // Received login form data
                $username = $_POST['username'] ?? "";
                $password = $_POST['password'] ?? "";
                (new Login())->logIn($username, $password);
            } elseif ($_GET['action'] === 'signup') {
                if (
                    isset($_POST['username']) && isset($_POST['password'])
                    && isset($_POST['re_password']) && isset($_POST['email'])
                ) {
                    // Received signup form data
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $re_password = $_POST['re_password'];
                    $email = $_POST['email'];
                    (new Signup())->signUp($username, $password, $re_password, $email);
                } else {
                    (new Signup())->execute();
                }
            } else if ($_GET['action'] === 'reset_password') {
                if (isset($_POST['email'])) {
                    // Received reset password form data
                    $email = $_POST['email'];
                    (new Login())->sendPasswordLink($email);
                } else {
                    (new Login())->resetPassword();
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
                if (isset($_GET['email']) && isset($_GET['code'])) {
                    $email = $_GET['email'];
                    $code = $_GET['code'];
                    (new Auth())->activate_user($email, $code);
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

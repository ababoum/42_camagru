<?php

require_once('src/controllers/homepage.php');
require_once('src/controllers/gallery.php');
require_once('src/controllers/post.php');
require_once('src/controllers/comment.php');
require_once('src/controllers/webcam.php');
require_once('src/controllers/login.php');
require_once('src/controllers/signup.php');
require_once('src/controllers/profile.php');
require_once('src/controllers/auth.php');

use Application\Controllers\Homepage\Homepage;
use Application\Controllers\Gallery\Gallery;
use Application\Controllers\Post\Post;
use Application\Controllers\Comment\Comment;
use Application\Controllers\Webcam\Webcam;
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
            // GALLERY RELATED ROUTES
            if ($_GET['action'] === 'gallery') {
                if (isset($_GET['page']) && $_GET['page'] > 0) {
                    $page = $_GET['page'];
                    (new Gallery())->execute_page($page, '');
                } else if (isset($_GET['page']) && $_GET['page'] <= 0) {
                    throw new Exception('Page number must be greater than 0');
                } else {
                    (new Gallery())->execute_page(1, ''); // page 1 by default
                }
            }
            // POST RELATED ROUTES
            else if ($_GET['action'] === 'post') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $id = $_GET['id'];
                    (new Post())->show($id, '');
                } else {
                    throw new Exception('No image id sent');
                }
            }
            // Received login form data
            else if (
                $_GET['action'] === 'login'
                && isset($_POST['username'])
                && isset($_POST['password'])
            ) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                (new Login())->log_in($username, $password);
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
                    (new Login())->new_password_form();
                }
            } else {
                throw new Exception("The page requested doesn't exist, or you need to be <b>logged in</b> to access it.");
            }
        } else {
            // NO ACTION IS REQUESTED
            (new Homepage())->execute();
        }
    }
    // Routes availables for logged in users
    else {
        // AN ACTION IS REQUESTED
        if (isset($_GET['action']) && $_GET['action'] !== '') {
            // GALLERY RELATED ROUTES
            if ($_GET['action'] === 'gallery') {
                if (isset($_GET['page']) && $_GET['page'] > 0) {
                    $page = $_GET['page'];
                    (new Gallery())->execute_page($page, $_SESSION['id']);
                } else if (isset($_GET['page']) && $_GET['page'] <= 0) {
                    throw new Exception('Page number must be greater than 0');
                } else {
                    (new Gallery())->execute_page(1, $_SESSION['id']); // page 1 by default
                }
            }
            // POST RELATED ROUTES
            else if ($_GET['action'] === 'post') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $id = $_GET['id'];
                    (new Post())->show($id, $_SESSION['id']);
                } else {
                    throw new Exception('No image id sent');
                }
            } else if ($_GET['action'] === 'delete_post') { // active user only
                if (!isset($_SESSION['active']) || !$_SESSION['active']) {
                    throw new Exception("You need to be an active user to access this page.");
                }
                if (isset($_GET['id'])) {
                    $page = $_GET['page'] ?? 1;
                    if (!isset($_GET['source'])) {
                        $source = 'gallery';
                    } else if ($_GET['source'] === 'cam') {
                        $source = 'cam';
                    } else if ($_GET['source'] === 'gallery') {
                        $source = 'gallery';
                    }
                    $post_id = $_GET['id'];
                    (new Post())->delete_post($post_id, $source, $page);
                }
            } else if ($_GET['action'] === 'unlike_post') {
                if (isset($_GET['id'])) {
                    $post_id = $_GET['id'];
                    $current_page = $_GET['page'] ?? 1;
                    $current_page = (int) $current_page;
                    if ($current_page <= 0) {
                        $current_page = 1;
                    }
                    (new Post())->unlike_post($post_id, $_SESSION['id'], $current_page);
                } else {
                    throw new Exception('No image id sent');
                }
            } else if ($_GET['action'] === 'like_post') {
                if (isset($_GET['id'])) {
                    $post_id = $_GET['id'];
                    $current_page = $_GET['page'] ?? 1;
                    $current_page = (int) $current_page;
                    if ($current_page <= 0) {
                        $current_page = 1;
                    }
                    (new Post())->like_post($post_id, $_SESSION['id'], $current_page);
                } else {
                    throw new Exception('No image id sent');
                }
            }
            // COMMENT RELATED ROUTES
            elseif ($_GET['action'] === 'add_comment') {
                if (
                    isset($_GET['post_id']) && $_GET['post_id'] > 0
                    && isset($_POST['comment']) && !empty($_POST['comment'])
                ) {
                    $post_id = $_GET['post_id'];
                    $user_id = $_SESSION['id'];
                    $comment = $_POST['comment'];
                    (new Comment())->add_comment($post_id, $user_id, $comment);
                } else {
                    throw new Exception('Invalid comment creation request.');
                }
            } else if ($_GET['action'] === 'delete_comment') {
                if (isset($_GET['id'])) {
                    $comment_id = $_GET['id'];
                    $post_id = $_GET['post_id'] ?? 0;
                    $user_id = $_SESSION['id'];
                    (new Comment())->delete_comment($comment_id, $post_id, $user_id);
                } else {
                    throw new Exception('No comment id sent');
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
            } else if ($_GET['action'] === 'update_email_notifications') {
                if (isset($_GET['value'])) {
                    $value = $_GET['value'];
                    $user_id = $_SESSION['id'];
                    (new Profile())->update_email_notifications($user_id, $value);
                } else {
                    throw new Exception('No value sent');
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
            // WEBCAM RELATED ROUTES (active users only)
            else if ($_GET['action'] === 'webcam') {
                if (!isset($_SESSION['active']) || !$_SESSION['active']) {
                    throw new Exception("You need to be an active user to access this page.");
                }
                (new Webcam())->execute();
            } else if ($_GET['action'] === 'save_shot') {
                if (!isset($_SESSION['active']) || !$_SESSION['active']) {
                    throw new Exception("You need to be an active user to access this page.");
                }
                if (isset($_POST['webcamImage']) && isset($_POST['selectedSticker'])) {
                    $img = str_replace('data:image/png;base64,', '', $_POST['webcamImage']);
                    $filter = $_POST['selectedSticker'];
                    (new Webcam())->save_shot($img, $filter, $_SESSION['id']);
                } else {
                    throw new Exception("No image or filter sent.");
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
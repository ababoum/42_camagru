<?php

require_once('src/controllers/comment/add.php');
require_once('src/controllers/homepage.php');
require_once('src/controllers/post.php');
require_once('src/controllers/login.php');
require_once('src/controllers/signup.php');
require_once('src/controllers/profile.php');

use Application\Controllers\Comment\Add\AddComment;
use Application\Controllers\Homepage\Homepage;
use Application\Controllers\Post\Post;
use Application\Controllers\Login\Login;
use Application\Controllers\Signup\Signup;
use Application\Controllers\Profile\Profile;

session_start();
try {
    // Routes availables for non logged in users
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        if (isset($_GET['action']) && $_GET['action'] !== '') {
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
            } 
            
            else {
                throw new Exception("The page requested doesn't exist, or you need to be <b>logged in</b> to access it.");
            }
        } else {
            (new Login())->execute();
        }
    }
    // Routes availables for logged in users
    else {
        if (isset($_GET['action']) && $_GET['action'] !== '') {
            if ($_GET['action'] === 'post') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $identifier = $_GET['id'];

                    (new Post())->execute($identifier);
                } else {
                    throw new Exception('No image identifier sent');
                }
            } else if ($_GET['action'] === 'update_user') {
                // Received update form data
                $username = $_POST['username'] ?? "";
                $email = $_POST['email'] ?? "";
                $password = $_POST['password'] ?? "";
                $re_password = $_POST['re_password'] ?? "";
                $id = $_SESSION['identifier'] ?? "";
                (new Profile())->updateUser($id, $username, $email, $password, $re_password);
            } elseif ($_GET['action'] === 'addComment') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $identifier = $_GET['id'];

                    (new AddComment())->execute($identifier, $_POST);
                } else {
                    throw new Exception('No image identifier sent');
                }
            } else if ($_GET['action'] === 'logout') {
                session_destroy();
                header('Location: index.php');
            } else if ($_GET['action'] === 'profile') {
                (new Profile())->execute();
            }            
            else {
                throw new Exception("The page requested doesn't exist.");
            }
        } else {
            (new Homepage())->execute();
        }
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();

    require('templates/error.php');
}
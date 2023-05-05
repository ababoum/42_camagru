<?php

require_once('src/controllers/comment/add.php');
require_once('src/controllers/homepage.php');
require_once('src/controllers/post.php');
require_once('src/controllers/login.php');
require_once('src/controllers/signup.php');

use Application\Controllers\Comment\Add\AddComment;
use Application\Controllers\Homepage\Homepage;
use Application\Controllers\Post\Post;
use Application\Controllers\Login\Login;
use Application\Controllers\Signup\Signup;

session_start();
try {
    // Routes availables for non logged in users
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        if (isset($_GET['action']) && $_GET['action'] !== '') {
            if ($_GET['action'] === 'login') {
                // Received login form data
                $username = $_POST['username'];
                $password = $_POST['password'];
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
            } else {
                throw new Exception("The page requested doesn't exist.");
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
            } elseif ($_GET['action'] === 'addComment') {
                if (isset($_GET['id']) && $_GET['id'] > 0) {
                    $identifier = $_GET['id'];

                    (new AddComment())->execute($identifier, $_POST);
                } else {
                    throw new Exception('No image identifier sent');
                }
            } else {
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
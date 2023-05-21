<?php

namespace Application\Controllers\Signup;

require_once('src/lib/database.php');
require_once('src/model/user.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\User\UserRepository;

class Signup
{
    public function execute()
    {
        require('templates/signup.php');
    }

    public function signUp(string $username, string $password, string $re_password, string $email)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();
        $user = $userRepository->create_user($username, $password, $re_password, $email);

        if ($user === false) {
            throw new \Exception('Impossible to create a new user!');
        }
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user->username;
        $_SESSION['id'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['active'] = $user->active;
        $_SESSION['accept_notifications'] = $user->accept_notifications;

        $_SESSION['info'] = 'Your account has been created successfully! You are now logged in.
        <b>You still need to verify your email address. Please check your inbox.</b>';

        header('Location: index.php');
    }
}

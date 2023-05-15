<?php

namespace Application\Controllers\Login;

require_once('src/lib/database.php');
require_once('src/model/user.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\User\UserRepository;

class Login
{
    public function execute()
    {
        require('templates/login.php');
    }

    public function resetPassword()
    {
        require('templates/reset_password.php');
    }

    public function logIn(string $username, string $password)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();
        $user = $userRepository->log_user($username, $password);

        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user->username;
        $_SESSION['identifier'] = $user->identifier;
        $_SESSION['email'] = $user->email;
        $_SESSION['active'] = $user->active;

        header('Location: index.php');
    }

    public function sendPasswordLink(string $email)
    {
        // TODO: Send email with link to reset password

        $_SESSION['info'] = 'A link to reset your password has been sent to your email address.';
        header('Location: index.php');
    }
}
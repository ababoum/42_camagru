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

    public function logIn(string $username, string $password)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();
        $user = $userRepository->logUser($username, $password);

        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user->username;
        $_SESSION['identifier'] = $user->identifier;
        $_SESSION['email'] = $user->email;

        header('Location: index.php');
    }
}
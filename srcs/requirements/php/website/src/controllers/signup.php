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
        $user = $userRepository->createUser($username, $password, $re_password, $email);

        if ($user === false) {
            throw new \Exception('Impossible to create a new user!');
        }
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user->username;
        $_SESSION['identifier'] = $user->identifier;
        $_SESSION['email'] = $user->email;

        header('Location: index.php');
    }
}
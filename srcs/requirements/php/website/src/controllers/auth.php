<?php

namespace Application\Controllers\Auth;

require_once('src/lib/database.php');
require_once('src/model/user.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\User\User;
use Application\Model\User\UserRepository;

class Auth
{
    public function activate_user(string $email, string $activation_code)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user_id = $userRepository->find_unverified_user($email, $activation_code);

        if ($user_id) {
            $userRepository->activate_user($user_id);
            $_SESSION['info'] = 'Your account has been activated.';
        }

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            $_SESSION['active'] = true;
            header('Location: index.php?action=profile');
        } else {
            header('Location: index.php');
        }
    }
}
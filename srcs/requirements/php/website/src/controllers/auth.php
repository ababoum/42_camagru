<?php

namespace Application\Controllers\Auth;

require_once('src/lib/database.php');
require_once('src/model/user.php');
require_once('src/lib/authtools.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Lib\AuthTools\AuthTools;
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

    public function resend_activation(string $user_id)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user = $userRepository->get_user_by_id($user_id);

        if (!$user) {
            throw new \Exception("User cannot be identified.");
        }

        // Generate an activation code
        $activation_code = AuthTools::generate_activation_code();
        $hashed_activation_code = password_hash($activation_code, PASSWORD_DEFAULT);
        $activation_expiration = date('Y-m-d H:i:s', strtotime('+1 day'));

        // Update the user record
        $statement = $userRepository->connection->getConnection()->prepare(
            'UPDATE users
            SET activation_code = ?, activation_expiration = ?
            WHERE id = ?'
        );

        if (!$statement->execute([$hashed_activation_code, $activation_expiration, $user_id])) {
            throw new \Exception("Something went wrong. Try again later.");
        }

        if (!AuthTools::send_activation_email($user->email, $activation_code)) {
            $_SESSION['error'] = 'Something went wrong. Try again later.';
        } else {
            $_SESSION['info'] = 'Activation email sent.';
        }

        header('Location: index.php?action=profile');
    }
}

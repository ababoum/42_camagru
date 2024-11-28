<?php

namespace Application\Controllers\Profile;

require_once('src/lib/database.php');
require_once('src/model/user.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\User\User;
use Application\Model\User\UserRepository;

class Profile
{
    public DatabaseConnection $connection;

    public function execute()
    {
        $user = new User();
        $user->id = $_SESSION['id'];
        $user->username = $_SESSION['username'];
        $user->email = $_SESSION['email'];
        $user->active = $_SESSION['active'];
        $user->accept_notifications = $_SESSION['accept_notifications'];

        require('templates/profile.php');
    }

    public function update_username(string $id, string $username)
    {
        if (empty($username)) {
            throw new \Exception('Username field cannot be empty.');
        }
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user = $userRepository->get_user_by_id($id);

        if (!empty($username) && $username !== $user->username) {
            $user->username = $userRepository->update_username($id, $username);
        }

        $_SESSION['username'] = $user->username;
        $_SESSION['info'] = 'Username updated successfully.';
        header('Location: index.php?action=profile');
    }

    public function update_email(string $id, string $email)
    {
        if (empty($email)) {
            throw new \Exception('Email field cannot be empty.');
        }

        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user = $userRepository->get_user_by_id($id);

        if ($email !== $user->email) {
            $user->email = $userRepository->update_email($id, $email);
        }

        $_SESSION['email'] = $user->email;
        $_SESSION['info'] = 'Email updated successfully.';
        header('Location: index.php?action=profile');
    }


    public function update_password(string $id, string $password, string $re_password)
    {
        if (empty($password) || empty($re_password)) {
            throw new \Exception('Password fields cannot be empty.');
        }
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user = $userRepository->get_user_by_id($id);

        if (!empty($password) && !empty($re_password)) {
            if ($password !== $re_password) {
                throw new \Exception('Passwords don\'t match.');
            }
            $userRepository->update_password($id, $password);
        }

        $_SESSION['info'] = 'Password updated successfully.';
        header('Location: index.php?action=profile');
    }

    public function update_email_notifications(string $user_id, int $accept_notifications)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        if ($accept_notifications !== 1 && $accept_notifications !== 0) {
            throw new \Exception('Invalid value for email notifications.');
        }

        $bool = false;
        if ($accept_notifications === 1)
        {
            $bool = true;
        }
        $userRepository->update_email_notifications($user_id, $bool);

        $_SESSION['accept_notifications'] = $accept_notifications;

        header('Location: index.php?action=profile');
        
    }
}
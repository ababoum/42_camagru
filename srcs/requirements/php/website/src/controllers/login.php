<?php

namespace Application\Controllers\Login;

require_once('src/lib/database.php');
require_once('src/model/user.php');

use Application\Lib\MailingTools\MailingTools;
use Application\Lib\Database\DatabaseConnection;
use Application\Model\User\UserRepository;

class Login
{
    public DatabaseConnection $connection;

    public function execute()
    {
        require('templates/login.php');
    }

    public function reset_password_email_form()
    {
        require('templates/reset_password.php');
    }

    public function new_password_form()
    {
        require('templates/new_password.php');
    }

    public function update_password(string $token, string $email, string $password, string $re_password)
    {
        // Check if the token is valid
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $statement = $userRepository->connection->getConnection()->prepare(
            'SELECT * FROM password_resets WHERE email = :email AND expiration > NOW()'
        );
        $statement->execute([
            ':email' => $email
        ]);

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result || !password_verify($token, $result['token'])) {
            $_SESSION['error'] = 'Invalid token or token expired.';
            header('Location: index.php?action=reset_password');
            exit();
        }

        // Check if the passwords match
        if ($password !== $re_password) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: index.php?action=new_password&token=' . $token . '&email=' . $email);
            exit();
        }

        // Update the password
        $user = $userRepository->get_user_by_email($email);
        try {
            $userRepository->update_password($user->id, $password);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=new_password&token=' . $token . '&email=' . $email);
            exit();
        }

        // Delete the token from the database
        $statement = $userRepository->connection->getConnection()->prepare(
            'DELETE FROM password_resets WHERE email = :email'
        );
        $statement->execute([
            ':email' => $email
        ]);

        // Redirect to the login page
        $_SESSION['success'] = 'Your password has been reset successfully.';
        header('Location: index.php?action=login');
        exit();
    }

    public function reset_password(string $token, string $email)
    {
        // Check if the token is valid
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $statement = $userRepository->connection->getConnection()->prepare(
            'SELECT * FROM password_resets
            WHERE email = :email AND expiration > NOW()'
        );
        $statement->execute([
            ':email' => $email
        ]);

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result || !password_verify($token, $result['token'])) {
            $_SESSION['error'] = 'Invalid token or token expired.';
            header('Location: index.php?action=reset_password');
            exit();
        }

        // Redirect to the reset password form
        $_SESSION['email'] = $email;
        $_SESSION['token'] = $token;

        header('Location: index.php?action=new_password');
        exit();
    }

    public function log_in(string $username, string $password)
    {
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        try {
            $user = $userRepository->log_user($username, $password);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=login');
            exit();
        }

        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user->username;
        $_SESSION['id'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['active'] = $user->active;
        $_SESSION['accept_notifications'] = $user->accept_notifications;

        header('Location: index.php');
    }

    public function send_password_link(string $email)
    {
        // Check if the email exists in the database
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user = $userRepository->get_user_by_email($email);

        if (!$user) {
            $_SESSION['error'] = 'No account found with the email address: <b>'. $email . '</b>';
            header('Location: index.php?action=reset_password');
            exit();
        }

        $token = MailingTools::generate_password_token();
        $hashed_token = password_hash($token, PASSWORD_DEFAULT);

        // Delete any existing tokens for this email/user
        $statement = $userRepository->connection->getConnection()->prepare(
            'DELETE FROM password_resets
            WHERE email = :email'
        );
        $statement->execute([
            ':email' => $email
        ]);

        // Add the token to the database
        $statement = $userRepository->connection->getConnection()->prepare(
            'INSERT INTO password_resets (email, token, expiration)
            VALUES (:email, :token, NOW() + INTERVAL \'5 minutes\')'
        );
        $statement->bindValue(':email', $email);
        $statement->bindValue(':token', $hashed_token);
        $statement->execute();

        if ($statement->rowCount() === 0) {
            throw new \Exception('Something went wrong. Please try again.');
        }

        // Send the email
        MailingTools::send_forgotten_password_email($email, $token);

        $_SESSION['info'] = 'A link to reset your password has been sent to your email address.';
        header('Location: index.php');
    }
}
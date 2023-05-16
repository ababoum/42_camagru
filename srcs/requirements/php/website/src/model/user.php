<?php

namespace Application\Model\User;

require_once('src/lib/database.php');
require_once('src/lib/authtools.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Lib\AuthTools\AuthTools;

class User
{
    public string $username;
    public string $id;
    public string $email;
    public bool $active;
}

class UserRepository
{
    public DatabaseConnection $connection;

    public function validate(string $data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function log_user(string $username, string $password): User
    {
        // Validate username
        $username = $this->validate($username);
        if (empty($username)) {
            throw new \Exception('Username is required.');
        }

        // Validate password
        $password = $this->validate($password);
        if (empty($password)) {
            throw new \Exception('Password is required.');
        }

        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, email, password, active FROM users WHERE username = ?"
        );
        $statement->execute([$username]);
        $row = $statement->fetch();

        if ($row === false) {
            throw new \Exception('Username doesn\'t exist.');
        }

        // Check if password is correct
        if (!password_verify($password, $row['password'])) {
            throw new \Exception('Incorrect password.');
        }

        $user = new User();
        $user->id = $row['id'];
        $user->username = $row['username'];
        $user->email = $row['email'];
        $user->active = $row['active'];

        return $user;
    }

    public function create_user(string $username, string $password, string $re_password, string $email): User
    {
        // Check if a field is empty
        if (empty($username) || empty($password) || empty($re_password) || empty($email)) {
            throw new \Exception('All sign up form fields are required.');
        }

        // Check if username is valid
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new \Exception('Username can only contain letters, numbers and underscores.');
        }

        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email is not valid.');
        }

        // Check if passwords match
        if ($password !== $re_password) {
            throw new \Exception('Passwords do not match.');
        }

        // Check the password complexity
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            throw new \Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
        }

        // Check if username already exists
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id FROM users WHERE username = ?"
        );
        $statement->execute([$username]);
        $row = $statement->fetch();
        if ($row !== false) {
            throw new \Exception('Username already exists.');
        }

        // Check if email already exists
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $statement->execute([$email]);
        $row = $statement->fetch();
        if ($row !== false) {
            throw new \Exception('Email already exists.');
        }

        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Generate an activation code
        $activation_code = AuthTools::generate_activation_code();
        $hashed_activation_code = password_hash($activation_code, PASSWORD_DEFAULT);
        $activation_expiration = date('Y-m-d H:i:s', strtotime('+1 day'));


        // Insert the user into the database
        $statement = $this->connection->getConnection()->prepare(
            "INSERT INTO users (username, password, email, activation_code, activation_expiration)
            VALUES (?, ?, ?, ?, ?)"
        );
        $statement->execute([$username, $password, $email, $hashed_activation_code, $activation_expiration]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Something went wrong. Please try again.');
        }

        // Send an activation email
        AuthTools::send_activation_email($email, $activation_code);

        // Prepare a user instance to log the user in
        $user = new User();
        $user->id = $this->connection->getConnection()->lastInsertId();
        $user->username = $username;
        $user->email = $email;
        $user->active = false;

        return $user;
    }

    public function get_user_by_id(string $id): User
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT username, email, active FROM users WHERE id = ?"
        );
        $statement->execute([$id]);
        $row = $statement->fetch();

        if ($row === false) {
            throw new \Exception('User doesn\'t exist.');
        }

        $user = new User();
        $user->id = $id;
        $user->username = $row['username'];
        $user->email = $row['email'];
        $user->active = $row['active'];

        return $user;
    }

    public function get_user_by_email(string $email): User
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, email, active FROM users WHERE email = ?"
        );
        $statement->execute([$email]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        $user = new User();
        $user->id = $row['id'];
        $user->username = $row['username'];
        $user->email = $email;
        $user->active = $row['active'];

        return $user;
    }

    public function find_unverified_user(string $email, string $activation_code)
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT id, activation_code, activation_expiration < now() as expired
            FROM users
            WHERE active = 0 AND email = ?'
        );
        $statement->execute([$email]);
        $row = $statement->fetch();

        if ($row === false) {
            throw new \Exception('Email doesn\'t exist, or user is already verified.');
        }

        if ($row['expired'] === 1) {
            throw new \Exception('Activation code has expired.');
        }

        if (!password_verify($activation_code, $row['activation_code'])) {
            throw new \Exception('Incorrect activation code.');
        }

        return $row['id'];
    }

    public function activate_user(int $user_id): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'UPDATE users
            SET active = 1,
                activated_at = CURRENT_TIMESTAMP
            WHERE id = ?'
        );
        return $statement->execute([$user_id]);
    }

    public function update_username(string $id, string $new_username): string
    {
        // Validate username
        $new_username = $this->validate($new_username);
        if (empty($new_username)) {
            throw new \Exception('Username is required.');
        }

        // Check if username is valid
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
            throw new \Exception('Username can only contain letters, numbers and underscores.');
        }

        // Check if username already exists
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id FROM users WHERE username = ?"
        );
        $statement->execute([$new_username]);
        $row = $statement->fetch();
        if ($row !== false) {
            throw new \Exception('Username already exists.');
        }

        // Update the username
        $statement = $this->connection->getConnection()->prepare(
            "UPDATE users SET username = ? WHERE id = ?"
        );
        $statement->execute([$new_username, $id]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Something went wrong. Please try again.');
        }

        return $new_username;
    }

    public function updateEmail(string $id, string $new_email): string
    {
        // Validate email
        $new_email = $this->validate($new_email);
        if (empty($new_email)) {
            throw new \Exception('Email is required.');
        }

        // Check if email is valid
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email is not valid.');
        }

        // Check if email already exists
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $statement->execute([$new_email]);
        $row = $statement->fetch();
        if ($row !== false) {
            throw new \Exception('Email already exists.');
        }

        // Update the email
        $statement = $this->connection->getConnection()->prepare(
            "UPDATE users SET email = ? WHERE id = ?"
        );
        $statement->execute([$new_email, $id]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Something went wrong. Please try again.');
        }

        return $new_email;
    }

    public function updatePassword(string $id, string $new_password)
    {
        // Validate password
        if (empty($new_password)) {
            throw new \Exception('Password is required.');
        }

        // Check the password complexity
        $uppercase = preg_match('@[A-Z]@', $new_password);
        $lowercase = preg_match('@[a-z]@', $new_password);
        $number = preg_match('@[0-9]@', $new_password);
        $specialChars = preg_match('@[^\w]@', $new_password);
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($new_password) < 8) {
            throw new \Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
        }

        // Hash the password
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password
        $statement = $this->connection->getConnection()->prepare(
            "UPDATE users SET password = ? WHERE id = ?"
        );
        $statement->execute([$new_password, $id]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Something went wrong. Please try again.');
        }
    }
}

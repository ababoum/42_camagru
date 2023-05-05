<?php

namespace Application\Model\User;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class User
{
    public string $username;
    public string $identifier;
    public string $email;
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

    public function logUser(string $username, string $password): User
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
            "SELECT id, username, email, password FROM users WHERE username = ?"
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
        $user->identifier = $row['id'];
        $user->username = $row['username'];
        $user->email = $row['email'];

        return $user;
    }

    public function createUser(string $username, string $password, string $re_password, string $email): User
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

        // Insert the user into the database
        $statement = $this->connection->getConnection()->prepare(
            "INSERT INTO users (username, password, email) VALUES (?, ?, ?)"
        );
        $statement->execute([$username, $password, $email]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Something went wrong. Please try again.');
        }

        // Log the user in
        $user = new User();
        $user->identifier = $this->connection->getConnection()->lastInsertId();
        $user->username = $username;
        $user->email = $email;

        return $user;
    }
}
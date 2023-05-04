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

    public function createUser(string $username, string $password, string $email)
    {
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

        // TBC
    }
}
<?php

namespace Application\Model\User;

require_once('src/lib/database.php');
require_once('src/lib/mailingtools.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Lib\MailingTools\MailingTools;

class User {
    public string $username;
    public string $id;
    public string $email;
    public bool $active;
    public bool $accept_notifications;
}

class UserRepository {
    private DatabaseConnection $connection;
    private const PASSWORD_MIN_LENGTH = 8;
    private const USERNAME_MAX_LENGTH = 50;
    private const EMAIL_MAX_LENGTH = 255;

    public function __construct(DatabaseConnection $connection) {
        $this->connection = $connection;
    }

    private function validate(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public function log_user(string $username, string $password): User {
        $username = $this->validate($username);
        $password = $this->validate($password);

        if (empty($username) || empty($password)) {
            throw new \Exception('Username and password are required.');
        }

        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, email, password, active, accept_notifications FROM users WHERE username = ?"
        );
        $statement->execute([$username]);
        $row = $statement->fetch();

        if ($row === false || !password_verify($password, $row['password'])) {
            throw new \Exception('Invalid credentials.');
        }

        if (!$row['active']) {
            throw new \Exception('Please verify your email address.');
        }

        return $this->createUserFromRow($row);
    }

    public function create_user(string $username, string $password, string $re_password, string $email): User {
        $username = $this->validate($username);
        $email = $this->validate($email);

        if (empty($username) || empty($password) || empty($re_password) || empty($email)) {
            throw new \Exception('All sign up form fields are required.');
        }

        if (strlen($username) > self::USERNAME_MAX_LENGTH) {
            throw new \Exception('Username is too long.');
        }

        if (strlen($email) > self::EMAIL_MAX_LENGTH) {
            throw new \Exception('Email is too long.');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new \Exception('Username can only contain letters, numbers and underscores.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email is not valid.');
        }

        if ($password !== $re_password) {
            throw new \Exception('Passwords do not match.');
        }

        $this->validatePasswordStrength($password);

        if ($this->userExists('username', $username) || $this->userExists('email', $email)) {
            throw new \Exception('Username or email already exists.');
        }

        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $activationCode = MailingTools::generate_activation_code();
        $hashedActivationCode = password_hash($activationCode, PASSWORD_ARGON2ID);

        $statement = $this->connection->getConnection()->prepare(
            "INSERT INTO users (username, password, email, activation_code) VALUES ($1, $2, $3, $4) RETURNING id"
        );
        $statement->execute([$username, $hashedPassword, $email, $hashedActivationCode]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Failed to create user. Please try again.');
        }

        MailingTools::send_activation_email($email, $activationCode);

        $user = new User();
        $user->id = $this->connection->getConnection()->lastInsertId();
        $user->username = $username;
        $user->email = $email;
        $user->active = false;
        $user->accept_notifications = true;

        return $user;
    }

    public function get_user_by_id(string $id): User {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT username, email, active, accept_notifications FROM users WHERE id = $1"
        );
        $statement->execute([$id]);
        $row = $statement->fetch();

        if ($row === false) {
            throw new \Exception('User not found.');
        }

        return $this->createUserFromRow($row, $id);
    }

    public function get_user_by_email(string $email): ?User {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, username, email, active, accept_notifications FROM users WHERE email = $1"
        );
        $statement->execute([$email]);
        $row = $statement->fetch();

        return $row ? $this->createUserFromRow($row) : null;
    }

    public function find_unverified_user(string $email, string $activationCode): int {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT id, activation_code FROM users WHERE active = FALSE AND email = ?'
        );
        $statement->execute([$email]);
        $row = $statement->fetch();

        if ($row === false || !password_verify($activationCode, $row['activation_code'])) {
            throw new \Exception('Invalid activation attempt.');
        }

        return $row['id'];
    }

    public function activate_user(int $userId): bool {
        $statement = $this->connection->getConnection()->prepare(
            'UPDATE users SET active = TRUE, activated_at = CURRENT_TIMESTAMP WHERE id = ?'
        );
        return $statement->execute([$userId]);
    }

    public function update_username(string $id, string $newUsername): string {
        $newUsername = $this->validate($newUsername);

        if (empty($newUsername) || strlen($newUsername) > self::USERNAME_MAX_LENGTH) {
            throw new \Exception('Invalid username.');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
            throw new \Exception('Username can only contain letters, numbers and underscores.');
        }

        if ($this->userExists('username', $newUsername)) {
            throw new \Exception('Username already exists.');
        }

        $this->updateUserField($id, 'username', $newUsername);
        return $newUsername;
    }

    public function updateEmail(string $id, string $newEmail): string {
        $newEmail = $this->validate($newEmail);

        if (empty($newEmail) || strlen($newEmail) > self::EMAIL_MAX_LENGTH || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email.');
        }

        if ($this->userExists('email', $newEmail)) {
            throw new \Exception('Email already exists.');
        }

        $this->updateUserField($id, 'email', $newEmail);
        return $newEmail;
    }

    public function updatePassword(string $id, string $newPassword): void {
        if (empty($newPassword)) {
            throw new \Exception('Password is required.');
        }

        $this->validatePasswordStrength($newPassword);

        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        $this->updateUserField($id, 'password', $hashedPassword);
    }

    public function update_email_notifications(string $userId, bool $acceptNotifications): bool {
        $this->updateUserField($userId, 'accept_notifications', $acceptNotifications);
        return $acceptNotifications;
    }

    private function validatePasswordStrength(string $password): void {
        if (
            strlen($password) < self::PASSWORD_MIN_LENGTH ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^A-Za-z0-9]/', $password)
        ) {
            throw new \Exception('Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters long and include uppercase, lowercase, number, and special character.');
        }
    }

    private function userExists(string $field, string $value): bool {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id FROM users WHERE $field = ?"
        );
        $statement->execute([$value]);
        return $statement->fetch() !== false;
    }

    private function updateUserField(string $id, string $field, $value): void {
        $statement = $this->connection->getConnection()->prepare(
            "UPDATE users SET $field = $1 WHERE id = $2"
        );
        $statement->execute([$value, $id]);

        if ($statement->rowCount() === 0) {
            throw new \Exception('Failed to update user. Please try again.');
        }
    }

    private function createUserFromRow(array $row, ?string $id = null): User {
        $user = new User();
        $user->id = $id ?? $row['id'];
        $user->username = $row['username'];
        $user->email = $row['email'];
        $user->active = (bool)$row['active'];
        $user->accept_notifications = (bool)$row['accept_notifications'];
        return $user;
    }
}

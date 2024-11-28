<?php

namespace Application\Lib\Database;

require_once('config/database.php');

class DatabaseConnection
{
    public ?\PDO $database = null;

    public function getConnection(): \PDO
    {
        if ($this->database === null) {
            $dbhost = DBHOST;
            $dbname = DBNAME;
            $dbuser = DBUSER;
            $dbpass = DBPASS;
            try {
                $this->database = new \PDO(
                    "pgsql:host=$dbhost;dbname=$dbname",
                    $dbuser,
                    $dbpass,
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );
            } catch (\PDOException $e) {
                // Handle connection error
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        return $this->database;
    }

    public function testConnection(): bool
    {
        try {
            $statement = $this->getConnection()->query("SELECT 1");
            return $statement !== false;
        } catch (\PDOException $e) {
            throw new \Exception('Database connection test failed: ' . $e->getMessage());
        }
    }
}

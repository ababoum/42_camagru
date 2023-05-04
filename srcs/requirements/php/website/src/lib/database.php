<?php

namespace Application\Lib\Database;

class DatabaseConnection
{
    public ?\PDO $database = null;

    public function getConnection(): \PDO
    {
        if ($this->database === null) {
            $dbhost = getenv('MYSQL_HOST');
            $dbname = getenv('MYSQL_DATABASE');
            $dbuser = getenv('MYSQL_USER');
            $dbpass = getenv('MYSQL_PASSWORD');
            $this->database = new \PDO(
                "mysql:host=$dbhost;dbname=$dbname;charset=utf8",
                $dbuser,
                $dbpass
            );
        }

        return $this->database;
    }
}
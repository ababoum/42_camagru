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
            $this->database = new \PDO(
                "mysql:host=$dbhost;dbname=$dbname;charset=utf8",
                $dbuser,
                $dbpass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        }

        return $this->database;
    }
}
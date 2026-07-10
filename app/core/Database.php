<?php

require_once __DIR__ . '/../config/App.php';

class Database
{
    private $connection;

    public function connect()
    {
        if ($this->connection === null) {

            try {

                $this->connection = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS
                );

                $this->connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                $this->connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $e) {

                die("Database Error: " . $e->getMessage());

            }

        }

        return $this->connection;
    }
}
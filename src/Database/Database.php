<?php

namespace Nightfury\TaskManagementSystem\Database;

use PDO;
use PDOException;

final class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host = 'localhost';
        $db_name = 'tasks_management';
        $username = 'root';
        $password = 'password';

        try {
            $this->connection = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            exit();
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->connection;
    }
}

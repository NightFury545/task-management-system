<?php

namespace Nightfury\TaskManagementSystem\Models;

use Nightfury\TaskManagementSystem\Database\Database;
use PDO;

class User
{
    private static PDO $db;
    private static string $table = 'users';

    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private string $password
    ) {
        if (!isset(self::$db)) {
            self::getDb();
        }
    }

    public static function all(): array
    {
        $stmt = self::getDb()->query("SELECT * FROM " . self::$table);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return self::rowMap($row);
        }, $rows);
    }


    public static function findById(int $id): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM " . self::$table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? self::rowMap($data) : null;
    }

    public static function findByUsername(string $username): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM " . self::$table . " WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? self::rowMap($data) : null;
    }

    public function save(): ?self
    {
        if ($this->id) {
            $stmt = self::getDb()->prepare(
                "UPDATE " . self::$table . " SET username = :username, email = :email, password = :password WHERE id = :id"
            );
            $success = $stmt->execute([
                'id' => $this->id,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password
            ]);
        } else {
            $stmt = self::getDb()->prepare(
                "INSERT INTO " . self::$table . " (username, email, password) VALUES (:username, :email, :password)"
            );
            $success = $stmt->execute([
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password
            ]);

            if ($success) {
                $this->id = (int)self::getDb()->lastInsertId();
            }
        }

        return $success ? $this : null;
    }

    public static function delete(int $id): bool
    {
        $stmt = self::getDb()->prepare("DELETE FROM " . self::$table . " WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    private static function rowMap(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            username: $data['username'],
            email: $data['email'],
            password: $data['password']
        );
    }

    private static function getDb(): PDO
    {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
}

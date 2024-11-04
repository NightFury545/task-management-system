<?php

namespace Nightfury\TaskManagementSystem\Models;

use DateMalformedStringException;
use DateTime;
use Nightfury\TaskManagementSystem\Database\Database;
use Nightfury\TaskManagementSystem\Models\Enums\Status;
use PDO;

class Task
{
    private static PDO $db;
    private static string $table = 'tasks';

    public function __construct(
        private int $id,
        private string $title,
        private string $description,
        private Status $status,
        private User $user,
        private DateTime $createdAt,
        private DateTime $updatedAt
    ) {
        if (!isset(self::$db)) {
            self::getDb();
        }
    }

    public static function all(): array
    {
        $stmt = self::getDb()->query("SELECT * FROM " . self::$table);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => self::rowMap($row), $rows);
    }

    public static function findById(int $id): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM " . self::$table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? self::rowMap($data) : null;
    }

    public static function findByTitle(string $title): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM " . self::$table . " WHERE title = :title");
        $stmt->execute(['title' => $title]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? self::rowMap($data) : null;
    }

    public static function findAllByUserId(int $userId): array
    {
        $stmt = self::getDb()->prepare("SELECT * FROM " . self::$table . " WHERE user_id = :userId");
        $stmt->execute(['userId' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => self::rowMap($row), $rows);
    }

    public function save(): ?self
    {
        if ($this->id) {
            $stmt = self::getDb()->prepare("UPDATE " . self::$table . " SET title = :title, description = :description, status = :status, user_id = :user_id, updatedAt = :updatedAt WHERE id = :id");
            $success = $stmt->execute([
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status->value,
                'user_id' => $this->user->getId(),
                'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s')
            ]);
        } else {
            $stmt = self::getDb()->prepare("INSERT INTO " . self::$table . " (title, description, status, user_id, createdAt, updatedAt) VALUES (:title, :description, :status, :user_id, :createdAt, :updatedAt)");
            $success = $stmt->execute([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status->value,
                'user_id' => $this->user->getId(),
                'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
                'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s')
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    private static function rowMap(array $data): self
    {
        $createdAt = self::parseDate($data['createdAt'] ?? null);
        $updatedAt = self::parseDate($data['updatedAt'] ?? null);

        return new self(
            id: (int)$data['id'],
            title: $data['title'],
            description: $data['description'],
            status: Status::from($data['status']),
            user: User::findById((int)$data['user_id']),
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    private static function parseDate(?string $dateString): DateTime
    {
        try {
            return new DateTime($dateString);
        } catch (DateMalformedStringException $e) {
            error_log("Invalid date format: " . $e->getMessage());
            return new DateTime();
        }
    }

    private static function getDb(): PDO
    {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
}

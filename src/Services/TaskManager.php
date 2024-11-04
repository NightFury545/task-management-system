<?php

namespace Nightfury\TaskManagementSystem\Services;

use DateTime;
use Nightfury\TaskManagementSystem\Models\Enums\Status;
use Nightfury\TaskManagementSystem\Models\Task;
use Nightfury\TaskManagementSystem\Models\User;

class TaskManager
{
    public function addTask(string $title, string $description, Status $status, int $userId): Task
    {
        $user = User::findById($userId);

        return (new Task(
            id: 0,
            title: $title,
            description: $description,
            status: $status,
            user: $user,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        ))->save();
    }

    public function updateTask(Task $task, string $title, string $description, Status $status): Task
    {
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStatus($status);
        $task->setUpdatedAt(new DateTime());

        return $task->save();
    }

    public function deleteTask(int $id): bool
    {
        return Task::delete($id);
    }

    public function getTasksByUserId(int $userId): array
    {
        return Task::findAllByUserId($userId);
    }

    public function getTaskById(int $id): ?Task
    {
        return Task::findById($id);
    }

    public function getTaskByTitle(string $title): ?Task
    {
        return Task::findByTitle($title);
    }

    public function isTaskOwnedByUser(int $taskId, int $userId): bool
    {
        $task = $this->getTaskById($taskId);

        return $task !== null && $task->getUser()->getId() === $userId;
    }

}

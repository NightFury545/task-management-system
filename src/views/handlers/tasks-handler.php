<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

use Nightfury\TaskManagementSystem\Models\Enums\Status;
use Nightfury\TaskManagementSystem\Services\TaskManager;

$taskManager = new TaskManager();
$userId = $_SESSION['user_id'];

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title) && !empty($description)) {
        $status = Status::IN_PROGRESS;
        $task = $taskManager->addTask($title, $description, $status, $userId);

        header("Location: /tasks");
        exit();
    } else {
        echo "Будь ласка, заповніть всі поля.";
    }
} elseif ($requestMethod === 'PUT') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    $taskId = $inputData['task_id'] ?? null;
    $title = trim($inputData['title'] ?? '');
    $description = trim($inputData['description'] ?? '');
    $status = Status::from($inputData['status']) ?? Status::IN_PROGRESS;

    if ($taskId && !empty($title) && !empty($description)) {
        if ($taskManager->isTaskOwnedByUser($taskId, $userId)) {
            $task = $taskManager->getTaskById($taskId);
            $taskManager->updateTask($task, $title, $description, $status);
            echo json_encode(['status' => 'success', 'redirect' => '/tasks']);
            exit();
        } else {
            echo "Ви не маєте прав для редагування цієї задачі.";
        }
    } else {
        echo "Будь ласка, заповніть всі поля для оновлення.";
    }
} elseif ($requestMethod === 'DELETE') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    $taskId = $inputData['task_id'] ?? null;

    if ($taskId) {
        if ($taskManager->isTaskOwnedByUser($taskId, $userId)) {
            $taskManager->deleteTask($taskId);
            echo json_encode(['status' => 'success', 'redirect' => '/tasks']);
            exit();
        } else {
            echo "Ви не маєте прав для видалення цієї задачі.";
        }
    } else {
        echo "Задача для видалення не вказана.";
    }
} else {
    echo "Непідтримуваний метод запиту.";
}

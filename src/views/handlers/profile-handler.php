<?php
echo password_hash('123123123123', PASSWORD_DEFAULT);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

use Nightfury\TaskManagementSystem\Models\User;

$userId = $_SESSION['user_id'];
$user = User::findById($userId);

if (!$user) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Користувача не знайдено.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    $username = $inputData['username'];
    $email = $inputData['email'];
    $password = $inputData['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
    }

    $user->setUsername($username);
    $user->setEmail($email);

    if ($user->save()) {
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Не вдалося оновити профіль.']);
    }
    exit();
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Метод не дозволено.']);


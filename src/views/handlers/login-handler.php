<?php

use Nightfury\TaskManagementSystem\Services\UserService;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $userService = new UserService();

    $user = $userService->login($username, $password);

    if ($user === null) {
        echo "Помилка входу: неправильне ім'я користувача або пароль.";
    } else {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();

        header("Location: /");
        exit();
    }
}

<?php

use Nightfury\TaskManagementSystem\Services\UserService;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $userService = new UserService();

    $user = $userService->createUser($username, $email, $password);

    if ($user === null) {
        echo "Помилка реєстрації: користувач з таким ім'ям або електронною поштою вже існує.";
    } else {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();

        header("Location: /");
        exit();
    }
}

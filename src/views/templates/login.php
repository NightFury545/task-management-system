<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: /");
    exit();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="/static/login-signup-style.css">
</head>
<body>
<div class="container">
    <h2>Вхід</h2>
    <form action="/login" method="post">
        <label for="username">Ім'я користувача:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Увійти</button>
    </form>
</div>
</body>
</html>

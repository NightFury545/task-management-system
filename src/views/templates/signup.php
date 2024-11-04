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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="/static/login-signup-style.css">
</head>
<body>
<div class="container">
    <h2>Реєстрація</h2>
    <form action="/signup" method="post">
        <label for="username">Ім'я користувача:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Зареєструватися</button>
    </form>
</div>
</body>
</html>

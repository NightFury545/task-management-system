<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

use Nightfury\TaskManagementSystem\Models\User;

$userId = $_SESSION['user_id'];
$user = User::findById($userId);

if (!$user) {
    header("Location: /login");
    exit();
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Профіль користувача</title>
    <link rel="stylesheet" href="/static/profile-style.css">
</head>
<body>

<div class="profile-container">
    <h2>Профіль користувача: <?php echo htmlspecialchars($user->getUsername()); ?></h2>

    <form id="profile-form">
        <input type="hidden" id="user-id" value="<?php echo $user->getId(); ?>">

        <label for="username">Ім'я користувача:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user->getUsername()); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password">

        <button type="submit">Зберегти зміни</button>
    </form>
</div>

<script>
    document.getElementById('profile-form').onsubmit = function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let jsonData = {};

        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch('/profile', {
            method: 'PUT',
            body: JSON.stringify(jsonData),
            headers: { 'Content-Type': 'application/json' }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Зміни успішно збережено!");
                    window.location.reload();
                } else {
                    alert("Помилка: " + data.message);
                }
            })
            .catch(error => console.error('Fetch error:', error));
    };
</script>

</body>
</html>


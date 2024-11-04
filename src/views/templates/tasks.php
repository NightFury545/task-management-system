<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

use Nightfury\TaskManagementSystem\Models\User;
use Nightfury\TaskManagementSystem\Services\TaskManager;

$taskManager = new TaskManager();

$userId = $_SESSION['user_id'];
$user = User::findById($userId);

$tasks = $taskManager->getTasksByUserId($userId);

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Задачі</title>
    <link rel="stylesheet" href="/static/tasks-style.css">
</head>
<body>

<div class="tasks-container">
    <h2>Список задач для користувача: <?php
        echo htmlspecialchars($user->getUsername()); ?></h2>

    <?php
    if (empty($tasks)): ?>
        <p>Немає задач для відображення.</p>
    <?php
    else: ?>
        <ul class="tasks-list">
            <?php
            foreach ($tasks as $task): ?>
                <li class="task-item">
                    <div class="task-details">
                        <h3><?php
                            echo htmlspecialchars($task->getTitle()); ?></h3>
                        <p><?php
                            echo htmlspecialchars($task->getDescription()); ?></p>
                        <p class="task-status">Статус: <?php
                            echo htmlspecialchars($task->getStatus()->value); ?></p>
                        <div class="task-dates">
                            <p><small>Створено: <?php
                                    echo $task->getCreatedAt()->format('Y-m-d H:i'); ?></small></p>
                            <p><small>Оновлено: <?php
                                    echo $task->getUpdatedAt()->format('Y-m-d H:i'); ?></small></p>
                        </div>
                    </div>
                    <div class="task-actions" data-task-id="<?= $task->getId(); ?>">
                        <button class="edit-task-action">Редагувати</button>
                        <button class="delete-task-action">Видалити</button>
                    </div>
                </li>
            <?php
            endforeach; ?>
        </ul>
    <?php
    endif; ?>

    <button id="add-task-btn">Додати задачу</button>

    <div id="add-task-form" style="display:none;">
        <h3>Створити нову задачу</h3>
        <form method="POST" action="/tasks">
            <label for="title">Назва:</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Опис:</label>
            <textarea name="description" id="description" required></textarea>

            <button type="submit">Створити задачу</button>
        </form>
    </div>
</div>

<div id="edit-task-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3>Редагувати задачу</h3>
        <form id="edit-task-form">
            <input type="hidden" id="edit-task-id" name="task_id">
            <label for="edit-title">Назва:</label>
            <input type="text" id="edit-title" name="title" required>
            <label for="edit-description">Опис:</label>
            <textarea id="edit-description" name="description" required></textarea>
            <label for="edit-status">Статус:</label>
            <select id="edit-status" name="status">
                <option value="В процесі">В процесі</option>
                <option value="Виконано">Виконано</option>
                <option value="Не виконано">Не виконано</option>
            </select>
            <button type="submit">Зберегти</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-task-btn').onclick = function () {
        let form = document.getElementById('add-task-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    };

    document.querySelectorAll('.edit-task-action').forEach(function (button) {
        button.onclick = function () {
            let taskItem = this.closest('.task-item');
            let taskId = taskItem.querySelector('.task-actions').dataset.taskId;
            let title = taskItem.querySelector('h3').innerText;
            let description = taskItem.querySelector('p').innerText;

            let status = taskItem.querySelector('.task-status').innerText.split(' ')[1].trim();

            document.getElementById('edit-task-id').value = taskId;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-status').value = status;

            document.getElementById('edit-task-modal').style.display = 'block';
        };
    });

    document.querySelector('.close-btn').onclick = function () {
        document.getElementById('edit-task-modal').style.display = 'none';
    };

    window.onclick = function (event) {
        let modal = document.getElementById('edit-task-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    document.getElementById('edit-task-form').onsubmit = function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        let jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch('/tasks', {
            method: 'PUT',
            body: JSON.stringify(jsonData),
            headers: {'Content-Type': 'application/json'}
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    console.error('Error updating task:', data.message);
                }
            })
            .catch(error => console.error('Fetch error:', error));
    };

    document.querySelectorAll('.delete-task-action').forEach(function (button) {
        button.onclick = function () {
            let taskItem = this.closest('.task-item');
            let taskId = taskItem.querySelector('.task-actions').dataset.taskId;

            if (confirm("Ви впевнені, що хочете видалити цю задачу?")) {
                fetch('/tasks', {
                    method: 'DELETE',
                    body: JSON.stringify({task_id: taskId}),
                    headers: {'Content-Type': 'application/json'}
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect;
                        } else {
                            console.error('Error deleting task:', data.message);
                        }
                    })
                    .catch(error => console.error('Fetch error:', error));
            }
        };
    });
</script>

</body>
</html>

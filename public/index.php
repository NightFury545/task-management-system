<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nightfury\TaskManagementSystem\Routing\Router;

function view($view): void
{
    include __DIR__ . "/../src/views/templates/{$view}.php";
}

function handler($handler): void
{
    include __DIR__ . "/../src/views/handlers/{$handler}.php";
}

$router = new Router();

$router->addRoute('GET', '/', function () {
    view("home");
});

$router->addRoute('GET', '/signup', function () {
    view("signup");
});

$router->addRoute('GET', '/login', function () {
    view("login");
});

$router->addRoute('GET', '/tasks', function () {
    view("tasks");
});

$router->addRoute('GET', '/profile', function () {
    view("profile");
});

$router->addRoute('POST', '/signup', function () {
    handler("signup-handler");
});

$router->addRoute('POST', '/login', function () {
    handler("login-handler");
});

$router->addRoute('GET', '/logout', function () {
    handler("logout-handler");
});

$router->addRoute('POST', '/tasks', function () {
    handler("tasks-handler");
});

$router->addRoute('PUT', '/tasks', function () {
    handler("tasks-handler");
});

$router->addRoute('DELETE', '/tasks', function () {
    handler("tasks-handler");
});

$router->addRoute('PUT', '/profile', function () {
    handler("profile-handler");
});

$router->dispatch();
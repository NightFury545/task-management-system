<?php

namespace Nightfury\TaskManagementSystem\Routing;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $requestedPath = $_SERVER['REQUEST_URI'];
        $requestedMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestedMethod && $route['path'] === $requestedPath) {
                call_user_func($route['handler']);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}

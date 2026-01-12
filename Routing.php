<?php

class Router
{
    private static array $routes = [
        'GET' => [],
        'POST' => []
    ];

    public static function get(string $path, string $action): void
    {
        self::$routes['GET'][$path] = $action;
    }

    public static function post(string $path, string $action): void
    {
        self::$routes['POST'][$path] = $action;
    }

    public static function run(string $path): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method][$path])) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        [$controllerName, $methodName] = explode('@', self::$routes[$method][$path]);

        $controllerPath = __DIR__ . "/Controllers/$controllerName.php";

        if (!file_exists($controllerPath)) {
            http_response_code(500);
            echo "Controller not found";
            return;
        }

        require_once $controllerPath;

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Method not found";
            return;
        }

        $controller->$methodName();
    }
}

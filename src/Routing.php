<?php

#require_once 'src/controllers/DefaultController.php';

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
        $method = $_SERVER['REQUEST_METHOD'];        # 'GET' lub 'POST'

        if (!isset(self::$routes[$method][$path])) { # brak trasy
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        [$controllerName, $methodName] = explode('@', self::$routes[$method][$path]);


        // Załaduj plik kontrolera
        $controllerFile = __DIR__ . "/controllers/$controllerName.php";
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Controller not found";
            return;
        }
        require_once $controllerFile;

        // Załaduj i wywołaj metodę kontrolera
        $controller = new $controllerName();
        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Method not found";
            return;
        }
        $controller->$methodName();
    }
}

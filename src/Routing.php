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

        // Spróbuj dokładne dopasowanie
        if (isset(self::$routes[$method][$path])) {
            self::executeRoute(self::$routes[$method][$path]);
            return;
        }

        // Spróbuj dopasować parametryczne trasy
        foreach (self::$routes[$method] as $routePath => $action) {
            if (self::matchRoute($routePath, $path, $params)) {
                self::executeRoute($action, $params);
                return;
            }
        }

        // Brak trasy
        http_response_code(404);
        echo '404 Not Found';
    }

    private static function matchRoute(string $routePath, string $path, &$params = []): bool
    {
        $routeParts = explode('/', $routePath);
        $pathParts = explode('/', $path);

        if (count($routeParts) !== count($pathParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            $routePart = $routeParts[$i];
            $pathPart = $pathParts[$i];

            // Parametr dynamiczny (np. :id)
            if (strpos($routePart, ':') === 0) {
                $paramName = substr($routePart, 1);
                if ($paramName === 'id' && !is_numeric($pathPart)) {
                    return false;
                }
                $params[$paramName] = $pathPart;
            } else if ($routePart !== $pathPart) {
                return false;
            }
        }

        return true;
    }

    private static function executeRoute(string $action, array $params = []): void
    {
        [$controllerName, $methodName] = explode('@', $action);

        // Załaduj plik kontrolera (obsługa podkatalogi)
        $controllerFile = __DIR__ . "/controllers/$controllerName.php";
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Controller not found: $controllerFile";
            return;
        }
        require_once $controllerFile;

        // Wyciągnij nazwę klasy (ostatnia część po /)
        $className = basename($controllerName);
        
        // Załaduj i wywołaj metodę kontrolera
        $controller = new $className();
        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Method not found";
            return;
        }

        // Wywołaj metodę z parametrami
        if (!empty($params)) {
            call_user_func_array([$controller, $methodName], array_values($params));
        } else {
            $controller->$methodName();
        }
    }
}

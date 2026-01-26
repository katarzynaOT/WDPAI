<?php

class AppController
{
    protected function render(string $view, array $params = []): void
    {
        extract($params);

        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../views/layout.php';

        if (!file_exists($viewPath)) {
            die("View $view not found");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }

    protected function redirect(string $path): void
    {
        header("Location: /$path");
        exit;
    }

    protected function isLogged(): bool
    {
        return isset($_SESSION['user']);
    }

    protected function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

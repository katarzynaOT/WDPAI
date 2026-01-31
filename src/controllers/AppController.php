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
        exit();
    }

    protected function isLogged(): bool
    {
        if (session_status() === PHP_SESSION_NONE) 
            return false;
    
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin(): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            exit();
        }
    }

    protected function getUser(): ?array
    {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role'],
                'first_name' => $_SESSION['user_first_name'],
                'last_name' => $_SESSION['user_last_name']
            ];
        }
        return null;
    }
}

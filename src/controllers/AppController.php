<?php

class AppController{

    protected function renderHtml(string $viewName): void
    {
        $htmlPath = __DIR__ . '/../../public/views/' . $viewName . '.html';

        if (file_exists($htmlPath)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($htmlPath);
            exit();
        }
    
        // Fallback do PHP view (jeśli będzie)
        $phpPath = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($phpPath)) {
            $layoutPath = __DIR__ . '/../views/layout.php';
            ob_start();
            require $phpPath;
            $content = ob_get_clean();
            require $layoutPath;
            exit();
        }
    
        http_response_code(404);
        echo "View $viewName not found at: " . htmlspecialchars($htmlPath);
        exit();
    }

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

    protected function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            http_response_code(405); // Method Not Allowed
            $this->render('errors/405');
            //$this->redirect('');
            exit();
        }
    }

    protected function requireGet(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') 
        {
            http_response_code(405); // Method Not Allowed
            $this->render('errors/405');
            exit();
        }
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

    protected function getSanitizedPostData(): array
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $data[$key] = array_map('trim', $value);
                $data[$key] = array_map('htmlspecialchars', $data[$key]);
            } else {
                $data[$key] = trim($value);
                $data[$key] = htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8');
            }
        }
        return $data;
    }
}

<?php

require_once __DIR__ . '/../services/LoginService.php';
require_once __DIR__ . '/../services/RegistrationService.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AppController.php';

class AuthorizationController extends AppController {
    private LoginService $loginService;
    private RegistrationService $registrationService;

    public function __construct()
    {
        $this->loginService = new LoginService(new UserRepository());
        $this->registrationService = new RegistrationService();
    }

    public function showRegister(): void 
    {
        $this->renderHtml('register');
    }

    public function showLogin(): void 
    {
        $this->renderHtml('login');
    }

    public function register(): void
    {
        $this->requirePost();
        $formData = $this->getSanitizedPostData();
        try {
            $role = $formData['role'] ?? null;
            if ($role === 'student') {
                $user = $this->registrationService->registerStudent($formData);
            } else if ($role === 'tutor') {
                $user = $this->registrationService->registerTutor($formData);
            } else {
                throw new Exception('Nie wybrano roli');
            }
            session_regenerate_id(true);
            $this->setUserSession($user);
            $this->redirectToDashboard($user->role);
        } catch (Exception $e) {
            $this->renderHtml('register', [
                'error' => $e->getMessage(),
                'formData' => $formData
            ]);
        }
    }
    

    public function registerStudent(): void
    {
        $this->requirePost();
        
        // Pobierz i wyczyść dane
        $formData = $this->getSanitizedPostData(); 
        try {
            // Serwis rejestracji studenta
            $user = $this->registrationService->registerStudent($formData); 

            // Auto-login po rejestracji
            session_regenerate_id(true);
            $this->setUserSession($user);

            // Przekieruj do dashboardu
            $this->redirectToDashboard($user->role);
        } catch (Exception $e) { 
            // Zachowaj wpisane dane w formularzu
            $this->renderHtml('register', [
                'error' => $e->getMessage(),
                'formData' => $formData
            ]);
        }
    }

    public function registerTutor() : void
    {
        $this->requirePost();
        $formData = $this->getSanitizedPostData();
        
        try {
            $user = $this->registrationService->registerTutor($formData);
            
            session_regenerate_id(true);
            $this->setUserSession($user);
            
            $this->redirectToDashboard($user->role);
        } catch (Exception $e) {
            $this->renderHtml('register', [
                'error' => $e->getMessage(),
                'formData' => $formData
            ]);
        }
    }

    public function login(): void 
    {
        $this->requirePost();

        if (empty($_POST['email']) || empty($_POST['password'])) 
        {
            $this->renderHtml('login', ['error' => 'All fields are required (email & password)']);
            return;
        }

        try {
            $user = $this->loginService->login(
                $_POST['email'], 
                $_POST['password']
            );

            // Debug logowania
            error_log("User logged in: " . $user->email . " Role: " . $user->role);

            // REGENERACJA ID SESJI PRZED USTAWIENIEM DANYCH
            session_regenerate_id(true);  // Usun starą sesję

            // Ustaw sesję
            $this->setUserSession($user);

            // Debug sesji
            error_log("Session after login: " . print_r($_SESSION, true));

            // Logowanie sukcesu (dla admina)
            error_log("User {$user->id} logged in successfully from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

            $this->redirectToDashboard($user->role);

        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->renderHtml('login', ['error' => $error]);
        }
    }

    public function logout(): void 
    {
        // Log wylogowania
        if (isset($_SESSION['user_id'])) {
            error_log("User {$_SESSION['user_id']} logged out");
        }

        $this->destroySession(); //session_destroy(); 
        $this->redirect('login');
    }


    // Metody pomocnicze
    private function setUserSession(User $user): void
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        session_regenerate_id(true); // Zmiana ID sesji przy logowaniu

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_first_name'] = $user->firstName;
        $_SESSION['user_last_name'] = $user->lastName;
        $_SESSION['login_time'] = time();
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
    }

    private function destroySession(): void
    {
        // Wyczyść wszystkie zmienne sesji
        $_SESSION = [];
        
        // Usuń cookie sesji
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Zniszcz sesję
        session_destroy();
        
        // Rozpocznij nową, czystą sesję
        session_start();
        session_regenerate_id(true);
    }

    private function redirectToDashboard(string $role): void
    {
        switch ($role) {
            case 'student':
                $this->redirect('student/dashboard');
                break;
            case 'tutor':
                $this->redirect('tutor/dashboard');
                break;
            default:
                $this->redirect(''); // fallback
        }
    }
}

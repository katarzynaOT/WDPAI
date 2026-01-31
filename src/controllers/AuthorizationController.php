<?php

require_once __DIR__ . '/../services/LoginService.php';
require_once __DIR__ . '/../services/RegistrationService.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AppController.php';

class AuthorizationController extends AppController 
{
    private LoginService $loginService;
    private RegistrationService $registrationService;
    
    public function __construct()
    {
        //parent::__construct();

        // Same serwisy, bez REPO (bo serwisy tworza wlasne)
        $this->loginService = new LoginService(new UserRepository());
        $this->registrationService = new RegistrationService();
    }

    public function showRegister(): void 
    {
        $this->render('auth/register');
    }

    public function showStudentRegister(): void 
    {
        $this->render('auth/register-student');
    }

    public function showTutorRegister(): void 
    {
        $this->render('auth/register-tutor');
    }

    public function showLogin(): void 
    {
        $this->render('auth/login');
    }

    public function registerStudent(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            $this->redirect('register/student'); //TODO: czy student potrzebne?
            return;
        }
        
        // Pobierz i wyczyść dane
        $formData = $this->getSanitizedPostData(); // TODO: czy potrzebne?

        try {
            // Serwis rejestracji studenta
            $user = $this->registrationService->registerStudent($formData); 

            // Auto-login po rejestracji
            session_regenerate_id(true);
            $this->setUserSession($user);

            // Przekieruj do dashboardu
            $this->redirectToDashboard($user->role);
        } catch (Exception $e) { // TODO: lepszy Exception?
            // Zachowaj wpisane dane w formularzu
            $this->render('auth/register-student', [
                'error' => $e->getMessage(),
                'formData' => $formData
            ]);
        }
    }

    public function registerTutor() : void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
            {
            $this->redirect('register/tutor');
            return;
        }
        
        $formData = $this->getSanitizedPostData();
        
        try {
            $user = $this->registrationService->registerTutor($formData);
            
            session_regenerate_id(true);
            $this->setUserSession($user);
            
            // Przekieruj do dashboardu
            $this->redirectToDashboard($user->role);
        } catch (Exception $e) {
            $this->render('auth/register-tutor', [
                'error' => $e->getMessage(),
                'formData' => $formData
            ]);
        }
    }

    public function login(): void 
    {
        // Tylko POST (na GET same widoki)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            //http_response_code(405); // Method Not Allowed - tylko POST dla /login
            $this->redirect('login');
            return;
        }

        // Sprawdź czy dane POST istnieją/są przesłane
        if (empty($_POST['email']) || empty($_POST['password'])) 
        {
            $this->render('auth/login', ['error' => 'All fields are required (email & password)']);
            return;
        }

        try {
            $user = $this->loginService->login(
                $_POST['email'], //TODO: trim?
                $_POST['password']
            );

            // Debug logowania
            error_log("User logged in: " . $user->email . " Role: " . $user->role);

            // REGENERACJA ID SESJI PRZED USTAWIENIEM DANYCH
            session_regenerate_id(true);  // Usuwa starą sesję

            // Ustaw sesję
            $this->setUserSession($user);

            // Debug sesji
            error_log("Session after login: " . print_r($_SESSION, true));

            // Logowanie sukcesu (dla admina)
            error_log("User {$user->id} logged in successfully from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

            $this->redirectToDashboard($user->role);

        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->render('auth/login', ['error' => $error]);

            //TODO: sprawdzic czy ponizej ok?
            /*
            // Generic error message dla bezpieczeństwa
            $error = 'Nieprawidłowy email lub hasło';
            
            // Logowanie błędu (szczegóły tylko w logach)
            error_log("Login failed for email: $email - " . $e->getMessage());
            
            //$this->render('auth/login', [
                'error' => $error,
                'preservedEmail' => htmlspecialchars($email) // Zachowaj email w formularzu
            ]);*/
        }
    }

    public function logout(): void 
    {
        // Log wylogowania
        if (isset($_SESSION['user_id'])) {
            error_log("User {$_SESSION['user_id']} logged out");
        }

        $this->destroySession(); //session_destroy(); 
        $this->redirect('');
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
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            default:
                $this->redirect('dashboard'); // fallback
        }
    }

    // TODO: potrzebne?
    private function getSanitizedPostData(): array
    {
        $data = [];
        
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                // Dla checkboxów (np. subjects[])
                $data[$key] = array_map('trim', $value);
                $data[$key] = array_map('htmlspecialchars', $data[$key]);
            } else {
                $data[$key] = trim($value);
                $data[$key] = htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8');
            }
        }
        
        return $data;
    }

    // TODO: CSRF w formularzu
}

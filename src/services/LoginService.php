<?php

require_once __DIR__ . '/../repositories/UserRepository.php';

class LoginService
{
    private UserRepository $userRepository;

    public function __construct() 
    {
        $this->userRepository = new UserRepository();
    }
    
    public function login(string $email, string $password): User
    {
        // Walidacja podstawowa
        if (empty($email) || empty($password)) 
        {
            throw new Exception('Email and password are required');
        }

        // Walidacja formatu email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            throw new Exception('Invalid email format');
        }
        
        // Pobierz usera z repo
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            // Bez informacji czy user istnieje (security)
            throw new Exception('Invalid credentials');
        }
        
        // Weryfikuj hasło
        if (!password_verify($password, $user->passwordHash)) {
            throw new Exception('Invalid credentials');
        }
        
        // Aktualizuj czas ostatniego logowania
        $this->userRepository->updateLastLogin($user->id);
        
        return $user;
    }
    
    public function logout(int $userId): void
    {
        // Możesz logować wylogowanie
        //error_log("User $userId logged out");
    }
}
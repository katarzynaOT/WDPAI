<?php

require_once __DIR__ . '/../repositories/UserRepository.php';

class AuthService {

    private UserRepository $userRepository;

    public function __construct() 
    {
        $this->userRepository = $userRepository;
    }

    public function register(string $email, string $password, string $firstName): void 
    {
        // 1. Walidacja podstawowa
        if (empty($email) || empty($password) || empty($firstName)) {
            throw new Exception('All fields are required');
        }
        
        // 2. Walidacja formatu email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // 3. Walidacja długości hasła (opcjonalnie)
        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }

        // Sprawdź czy użytkownik już istnieje
        if ($this->userRepository->findByEmail($email)) {
            throw new Exception('Email is already registered');
        }

        // Hashowanie hasła
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Utwórz nowego użytkownika
        $user = new User(null, $email, $passwordHash, $firstName);

        // Zapisz użytkownika w bazie danych
        $this->userRepository->save($user);
    }

    public function login(string $email, string $password): User 
    {
        // 1. Walidacja podstawowa
        if (empty($email) || empty($password)) {
            throw new Exception('Email and password are required');
        }
        
        // 2. Walidacja formatu email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // 3. Walidacja długości (opcjonalnie)
        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }

        // POBIEZR UZYTKOWNIKA
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user->passwordHash)) { // Sprawdzenie hasła
            throw new Exception('Invalid credentials');
        }

        return $user;
    }
}

<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/StudentRepository.php';
require_once __DIR__ . '/../repositories/TutorRepository.php';
require_once __DIR__ . '/../repositories/SubjectRepository.php';

class RegistrationService
{
    private UserRepository $userRepository;
    private StudentRepository $studentRepository;
    private TutorRepository $tutorRepository;
    private SubjectRepository $subjectRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->studentRepository = new StudentRepository();
        $this->tutorRepository = new TutorRepository();
        $this->subjectRepository = new SubjectRepository();
    }
    
    public function registerStudent(array $data): User
    {
        // 1. Walidacja
        $this->validateRegistrationData($data);
        
        // 2. Sprawdź czy email wolny
        if ($this->userRepository->findByEmail($data['email'])) 
        {
            throw new Exception('Email already registered');
        }
        
        // 3. TRANSACTION - nie w serviise TODO
        $db = Database::getConnection();
        $db->beginTransaction();
        
        try {
            // 4. Stwórz Usera
            $userId = $this->userRepository->create([
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'role' => 'student'
            ]);
            
            // 5. Stwórz Student
            $this->studentRepository->create([
                'user_id' => $userId,
                'level' => $data['level'],
                'learning_goals' => $data['learning_goals'] ?? null
            ]);
            
            $db->commit();
            
            // 6. Zwróć Usera
            return $this->userRepository->findById($userId);
            
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }
    
    public function registerTutor(array $data): User
    {
        // 1. Walidacja
        $this->validateRegistrationData($data);
        
        // 2. Sprawdź czy email wolny
        if ($this->userRepository->findByEmail($data['email'])) 
        {
            throw new Exception('Email already registered');
        }
        
        // 3. TRANSACTION
        $db = Database::getConnection();
        $db->beginTransaction();
        
        try {
            // 4. Stwórz Usera
            $userId = $this->userRepository->create([
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'role' => 'tutor'
            ]);
            
            // 5. Stwórz Tutor
            $this->tutorRepository->create([
                'user_id' => $userId,
                'experience_years' => $data['experience_years'],
                'bio' => $data['bio'] ?? null
            ]);
            
            $db->commit();
            
            // 6. Zwróć Usera
            return $this->userRepository->findById($userId);
            
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }
    
    // Wspólna walidacja dla studenta i tutora
    private function validateRegistrationData(array $data): void
    {
        // Walidacja email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Nieprawidłowy format email');
        }
        
        // Walidacja hasła
        if (strlen($data['password']) < 8) {
            throw new Exception('Hasło musi mieć co najmniej 8 znaków');
        }
        
        if (!preg_match('/[A-Z]/', $data['password'])) {
            throw new Exception('Hasło musi zawierać przynajmniej jedną wielką literę');
        }
        
        if (!preg_match('/[0-9]/', $data['password'])) {
            throw new Exception('Hasło musi zawierać przynajmniej jedną cyfrę');
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            throw new Exception('Hasła nie są identyczne');
        }
        
        // Walidacja imienia i nazwiska
        if (empty($data['first_name']) || empty($data['last_name'])) {
            throw new Exception('Imię i nazwisko są wymagane');
        }
        
        if (strlen($data['first_name']) < 2 || strlen($data['last_name']) < 2) {
            throw new Exception('Imię i nazwisko muszą mieć co najmniej 2 znaki');
        }
    }

    public function getSubjectsForRegistration(): array
    {
        return $this->subjectRepository->findAll();
    }
}
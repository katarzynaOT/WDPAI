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
        // Walidacja
        $this->validateRegistrationData($data);
        
        // Czy wolny email
        if ($this->userRepository->findByEmail($data['email'])) 
        {
            throw new Exception('Email already registered');
        }
        
        // TRANSAKCJA - nie w serviise TODO
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
            
            // Student
            $this->studentRepository->create([
                'user_id' => $userId,
                'level' => isset($data['student_level']) ? $data['student_level'] : null,
                'learning_goals' => $data['learning_goals'] ?? null
            ]);
            
            $db->commit();
            
            return $this->userRepository->findById($userId);
            
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }
    
    public function registerTutor(array $data): User
    {
        // Walidacja
        $this->validateRegistrationData($data);
        
        // Czy wolny email
        if ($this->userRepository->findByEmail($data['email'])) 
        {
            throw new Exception('Email already registered');
        }
        
        // TRANSAKCJA 
        $db = Database::getConnection();
        $db->beginTransaction();
        
        try {
            $userId = $this->userRepository->create([
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'role' => 'tutor',
                'hourly_rate' => isset($data['hourly_rate']) ? $data['hourly_rate'] : null
            ]);
            
            $this->tutorRepository->create([
                'user_id' => $userId,
                'hourly_rate' => isset($data['hourly_rate']) ? $data['hourly_rate'] : null,
                'experience_years' => isset($data['experience_years']) ? $data['experience_years'] : null,
                'bio' => $data['bio'] ?? null
            ]);
            
            $db->commit();
            
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
        
        if (!isset($data['confirm_password'])) {
            throw new Exception('Potwierdzenie hasła jest wymagane');
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
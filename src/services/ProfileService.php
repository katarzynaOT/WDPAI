<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/StudentRepository.php';
require_once __DIR__ . '/../repositories/TutorRepository.php';

class ProfileService
{
    private UserRepository $userRepository;
    private StudentRepository $studentProfileRepository;
    private TutorRepository $tutorProfileRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->studentProfileRepository = new StudentRepository();
        $this->tutorProfileRepository = new TutorRepository();
    }

    public function getBasicData(int $userId): User
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        return $user;
    }

    public function updateBasicData(int $userId, array $data): User
    {
        // Walidacja
        $this->validateBasicData($data, $userId);
        
        // Aktualizacja
        $this->userRepository->update($userId, [
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'phone' => trim($data['phone'] ?? ''),
            'email' => trim($data['email'])
        ]);
        
        return $this->userRepository->findById($userId);
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): void
    {
        $user = $this->userRepository->findById($userId);
        
        if (!password_verify($currentPassword, $user->passwordHash)) {
            throw new Exception('Current password is incorrect');
        }
        
        if (strlen($newPassword) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }
        
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userRepository->changePassword($userId, $newHash);
    }

    public function getStudentProfile(int $userId): array
    {
        $profile = $this->studentProfileRepository->findByUserId($userId);
        
        if (!$profile) {
            // Stwórz domyślny profil jeśli nie istnieje
            $profileId = $this->studentProfileRepository->create([
                'user_id' => $userId,
                'level' => 'liceum',
                'learning_goals' => ''
            ]);
            $profile = $this->studentProfileRepository->findByUserId($userId);
        }
        
        return $profile;
    }
    
    public function updateStudentProfile(int $userId, array $data): void
    {
        $profile = $this->studentProfileRepository->findByUserId($userId);
        
        if (!$profile) {
            throw new Exception('Student profile does not exist');
        }
        
        $this->validateStudentProfileData($data);
        
        $this->studentProfileRepository->update($profile['id'], [
            'level' => $data['level'],
            'learning_goals' => trim($data['learning_goals'] ?? '')
        ]);
    }

    public function getTutorProfile(int $userId): array
    {
        $profile = $this->tutorProfileRepository->findByUserId($userId);
        
        if (!$profile) {
            throw new Exception('Tutor profile does not exist');
        }
        
        return $profile;
    }

    public function updateTutorProfile(int $userId, array $data): void
    {
        $profile = $this->tutorProfileRepository->findByUserId($userId);
        
        if (!$profile) {
            throw new Exception('Tutor profile does not exist');
        }
        
        $this->validateTutorProfileData($data);
        
        // Aktualizuj dane profilu
        $this->tutorProfileRepository->update($profile['id'], [
            'bio' => trim($data['bio']),
            'education' => trim($data['education'] ?? ''),
            'experience_years' => (int)($data['experience_years'] ?? 0),
            'description' => trim($data['description'] ?? '')
        ]);
        
        // Aktualizuj przedmioty jeśli przesłano
        if (isset($data['subjects']) && is_array($data['subjects'])) {
            $this->updateTutorSubjects($profile['id'], $data['subjects']);
        }
    }
    
    private function updateTutorSubjects(int $tutorProfileId, array $subjectIds): void
    {
        // Najpierw usuń wszystkie
        $currentSubjects = $this->tutorProfileRepository->getSubjects($tutorProfileId);
        foreach ($currentSubjects as $subject) {
            $subjectId = $subject['subject_id'] ?? ($subject['id'] ?? null);
            if ($subjectId !== null) {
                $this->tutorProfileRepository->removeSubject($tutorProfileId, $subjectId);
            }
        }
        
        // Dodaj nowe
        foreach ($subjectIds as $subjectId) {
            $this->tutorProfileRepository->addSubject(
                $tutorProfileId,
                (int)$subjectId,
                'intermediate' // Można dodać poziom jako parametr
            );
        }
    }
    
    private function validateBasicData(array $data, int $currentUserId): void
    {
        if (empty($data['first_name']) || empty($data['last_name'])) {
            throw new Exception('Imię i nazwisko są wymagane');
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Not a valid email address');
        }
        
        // Sprawdz czy email nie jest już uzywany przez innego użytkownika, ale nie podawaj informacji, ze email uzywany
        $existingUser = $this->userRepository->findByEmail($data['email']);
        if ($existingUser && $existingUser->id != $currentUserId) {
            throw new Exception('Not valid email address');
        }
    }
    
    private function validateStudentProfileData(array $data): void
    {
        if (empty($data['level'])) {
            throw new Exception('Education level is required');
        }
        
        $allowedLevels = ['podstawówka', 'liceum', 'technikum', 'studia', 'inny'];
        if (!in_array($data['level'], $allowedLevels)) {
            throw new Exception('Invalid education level');
        }
    }
    
    private function validateTutorProfileData(array $data): void
    {
        if (empty(trim($data['bio']))) {
            throw new Exception('Bio is required');
        }
        
        if (strlen(trim($data['bio'])) < 50) {
            throw new Exception('Bio must be at least 50 characters long');
        }
        
        if (empty($data['subjects']) || !is_array($data['subjects'])) {
            throw new Exception('Select at least one subject');
        }
    }
}
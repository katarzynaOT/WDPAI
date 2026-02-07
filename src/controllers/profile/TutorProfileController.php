<?php

require_once __DIR__ . '/ProfileController.php';
require_once __DIR__ . '/../../repositories/SubjectRepository.php';
require_once __DIR__ . '/../../services/SubjectService.php'; 

class TutorProfileController extends ProfileController 
{
    private SubjectService $subjectService;

    public function __construct()
    {
        parent::__construct();
        $this->subjectService = new SubjectService();
    }

    public function showHtml(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            $this->redirect('login');
            return;
        }
        $this->renderHtml('tutor/profile');
    }

    public function showData(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        $user = $this->profileService->getBasicData($userId);
        $profile = $this->profileService->getTutorProfile($userId);
        $data = [
            'name' => ($user->firstName ?? '') . ' ' . ($user->lastName ?? ''),
            'email' => $user->email ?? '',
            'phone' => $user->phone ?? '',
            'subjects' => $profile['subjects'] ?? '-',
            'description' => $profile['description'] ?? '-',
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function edit(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            $this->redirect('login');
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $profile = $this->profileService->getTutorProfile($userId);
            $subjects = $this->subjectService->getAllSubjects(); 
            
            $this->renderHtml('tutor/edit-profile', [
                'profile' => $profile,
                'subjects' => $subjects,
                'page' => 'profile',
                'subpage' => 'tutor-profile'
            ]);
            
        } catch (Exception $e) {
            $this->renderHtml('tutor/edit-profile', [
                'error' => $e->getMessage(),
                'page' => 'profile',
                'subpage' => 'tutor-profile'
            ]);
        }
    }

    public function update(): void
    {
        $this->requirePost();

        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            $this->redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $this->profileService->updateTutorProfile($userId, $_POST);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function dashboard(): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? null;

        if ($userRole === 'tutor') {
            $this->renderHtml('tutor/dashboard');
        } else {
            $this->redirect('');
        }
    }
}
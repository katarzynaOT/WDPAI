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
            
            $this->render('tutor/edit-profile', [
                'profile' => $profile,
                'subjects' => $subjects,
                'page' => 'profile',
                'subpage' => 'tutor-profile'
            ]);
            
        } catch (Exception $e) {
            $this->render('tutor/edit-profile', [
                'error' => $e->getMessage(),
                'page' => 'profile',
                'subpage' => 'tutor-profile'
            ]);
        }
    }

    public function update(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            $this->redirect('login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('tutor/profile');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $this->profileService->updateTutorProfile($userId, $_POST);
            
            $_SESSION['flash_success'] = 'Profil został zaktualizowany';
            $this->redirect('tutor/profile');
            
        } catch (Exception $e) {
            $profile = $this->profileService->getTutorProfile($userId);
            $subjects = $this->subjectService->getAllSubjects();
            
            $this->render('tutor/edit-profile', [
                'profile' => $profile,
                'subjects' => $subjects,
                'error' => $e->getMessage(),
                'page' => 'profile',
                'subpage' => 'tutor-profile'
            ]);
        }
    }
}
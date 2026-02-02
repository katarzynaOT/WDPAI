<?php

require_once __DIR__ . '/ProfileController.php';

class StudentProfileController extends ProfileController
{
    public function edit(): void
    {
        // AUTORYZATION: Zalogowany jako student
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') 
        {
            $this->redirect('login');
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $profile = $this->profileService->getStudentProfile($userId); // ✅ Serwis z rodzica
            
            $this->render('student/edit-profile', [
                'profile' => $profile,
                'page' => 'profile',
                'subpage' => 'student-profile'
            ]);
            
        } catch (Exception $e) {
            $this->render('student/edit-profile', [
                'error' => $e->getMessage(),
                'page' => 'profile',
                'subpage' => 'student-profile'
            ]);
        }
    }

    public function update(): void
    {
        $this->requirePost();

        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') {
            $this->redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $this->profileService->updateStudentProfile($userId, $_POST); 
            
            $_SESSION['flash_success'] = 'Profil studenta został zaktualizowany';
            $this->redirect('student/profile');
            
        } catch (Exception $e) {
            $profile = $this->profileService->getStudentProfile($userId);
            
            $this->render('student/edit-profile', [
                'profile' => $profile,
                'error' => $e->getMessage(),
                'page' => 'profile',
                'subpage' => 'student-profile'
            ]);
        }
    }
}
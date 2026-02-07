<?php

require_once __DIR__ . '/ProfileController.php';

class StudentProfileController extends ProfileController 
{
    public function showHtml(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') {
            $this->redirect('login');
            return;
        }
        $this->renderHtml('student/profile');
    }

    // Endpoint do AJAX
    public function showData(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        $user = $this->profileService->getBasicData($userId);
        $profile = $this->profileService->getStudentProfile($userId);
        $data = [
            'name' => ($user->firstName ?? '') . ' ' . ($user->lastName ?? ''),
            'email' => $user->email ?? '',
            'phone' => $user->phone ?? '',
            'class' => $profile['level'] ?? '',
            'learning_goals' => $profile['learning_goals'] ?? '',
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    public function edit(): void
    {
        // AUTORYZACJA
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') 
        {
            $this->redirect('login');
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $profile = $this->profileService->getStudentProfile($userId); 
            
            $this->renderHtml('student/edit-profile', [
                'profile' => $profile,
                'page' => 'profile',
                'subpage' => 'student-profile'
            ]);
            
        } catch (Exception $e) {
            $this->renderHtml('student/edit-profile', [
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
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $userId = $_SESSION['user_id'];
        try {
            $this->profileService->updateStudentProfile($userId, $_POST);
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

        if ($userRole === 'student') {
            $this->renderHtml('student/dashboard');
        } else {
            $this->redirect('');
        }
    }

}

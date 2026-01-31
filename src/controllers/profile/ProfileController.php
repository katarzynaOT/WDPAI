<?php
// src/controllers/ProfileController.php

require_once __DIR__ . '/../AppController.php';
require_once __DIR__ . '/../../services/ProfileService.php';

abstract class ProfileController extends AppController
{
    protected ProfileService $profileService; 
    
    public function __construct()
    {
        //parent::__construct();
        $this->profileService = new ProfileService(); 
    }


    public function editBasic(): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $user = $this->profileService->getBasicData($userId); 
            
            $this->render('profile/edit-basic', [
                'user' => $user,
                'page' => 'profile'
            ]);
            
        } catch (Exception $e) {
            $this->render('profile/edit-basic', [
                'error' => $e->getMessage(),
                'page' => 'profile'
            ]);
        }
    }
    
    public function updateBasic(): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile/basic');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $user = $this->profileService->updateBasicData($userId, $_POST); // ✅ Serwis
            
            // Aktualizuj sesję
            $_SESSION['user_first_name'] = $user->firstName;
            $_SESSION['user_last_name'] = $user->lastName;
            
            $_SESSION['flash_success'] = 'Dane zostały zaktualizowane';
            $this->redirect('profile/basic');
            
        } catch (Exception $e) {
            $user = $this->profileService->getBasicData($userId);
            
            $this->render('profile/edit-basic', [
                'user' => $user,
                'error' => $e->getMessage(),
                'page' => 'profile'
            ]);
        }
    }
    
    public function changePassword(): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }
        
        $this->render('profile/change-password', [
            'page' => 'profile'
        ]);
    }
    
    public function updatePassword(): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile/password');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $this->profileService->changePassword( // ✅ Serwis
                $userId,
                $_POST['current_password'],
                $_POST['new_password']
            );
            
            $_SESSION['flash_success'] = 'Hasło zostało zmienione';
            $this->redirect('profile/password');
            
        } catch (Exception $e) {
            $this->render('profile/change-password', [
                'error' => $e->getMessage(),
                'page' => 'profile'
            ]);
        }
    }
}
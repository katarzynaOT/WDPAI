<?php

require_once 'AppController.php';

class DashboardController extends AppController
{
    public function index(): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? null;

        if ($userRole === 'student' || $userRole === 'tutor') {
            $this->renderHtml('dashboard');
        } else {
            $this->redirect('');
        }
    }

}
<?php

require_once __DIR__ . '/BaseDashboardController.php';

class StudentDashboardController extends BaseDashboardController
{
    public function index(): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? null;
        if ($userRole !== 'student') 
        {
            $this->redirect('login');
            return;
        }

        $this->renderDashboard('student/dashboard', 'student');
    }
}

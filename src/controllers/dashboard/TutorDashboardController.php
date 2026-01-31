<?php

require_once __DIR__ . '/BaseDashboardController.php';

class TutorDashboardController extends BaseDashboardController
{
    public function index(): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? null;
        if ($userRole !== 'tutor') 
        {
            $this->redirect('login');
            return;
        }

        $this->renderDashboard('tutor/dashboard', 'tutor');
    }
}

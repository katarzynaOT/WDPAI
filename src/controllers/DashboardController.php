<?php

require_once 'AppController.php';

class DashboardController extends AppController
{
    public function index(): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? null;

        if ($userRole === 'student') {
            $this->renderDashboard('student/dashboard', 'student');
        } elseif ($userRole === 'tutor') {
            $this->renderDashboard('tutor/dashboard', 'tutor');
        } else {
            $this->redirect('');
        }
    }

    protected function renderDashboard(string $centerView, string $role, array $params = []): void
    {
        // Top panel
        $topPanel = [
            'logo' => '/public/images/logo.png', 
            'showSearch' => false
        ];

        // Left menu
        $leftMenu = [
            ['label' => 'Lessons', 'path' => 'lessons'],
            ['label' => 'Profile', 'path' => 'profile'],
        ];

        // Role-specific menu
        if ($role === 'student') {
            $leftMenu[] = ['label' => 'Tutors', 'path' => 'tutors'];
        } elseif ($role === 'tutor') {
            $leftMenu[] = ['label' => 'Students', 'path' => 'students'];
        }

        $params = array_merge($params, [
            'topPanel' => $topPanel,
            'leftMenu' => $leftMenu,
            'centerView' => $centerView,
            'role' => $role
        ]);

        $this->render('dashboard/layout', $params);
    }


}
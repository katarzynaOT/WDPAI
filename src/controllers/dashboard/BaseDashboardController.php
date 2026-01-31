<?php

require_once __DIR__ . '/../AppController.php';

class BaseDashboardController extends AppController
{
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

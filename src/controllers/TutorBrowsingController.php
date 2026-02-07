<?php

require_once 'AppController.php';
require_once __DIR__ . '/../services/TutorSearchService.php';

class TutorBrowsingController extends AppController
{
    private TutorSearchService $tutorSearchService;
    
    public function __construct()
    {
        $this->tutorSearchService = new TutorSearchService();
    }

    public function list(): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }

        if ($_SESSION['user_role'] !== 'student') {
            $this->redirect('dashboard');
            return;
        }

        try {
            // filter param
            $filters = [];
            if (isset($_GET['subject_id']) && !empty($_GET['subject_id'])) {
                $filters['subject_id'] = $_GET['subject_id'];
            }
            if (isset($_GET['min_rating']) && !empty($_GET['min_rating'])) {
                $filters['min_rating'] = $_GET['min_rating'];
            }

            // filtered tutors
            $tutors = $this->tutorSearchService->filterTutors($filters);
            
            $subjects = $this->tutorSearchService->getAllSubjects();
            
            $selectedSubject = $_GET['subject_id'] ?? null;
            $selectedRating = $_GET['min_rating'] ?? null;

            $this->render('student/tutors', [
                'tutors' => $tutors,
                'subjects' => $subjects,
                'selectedSubject' => $selectedSubject,
                'selectedRating' => $selectedRating,
                'page' => 'tutors'
            ]);

        } catch (Exception $e) {
            $this->render('student/tutors', [
                'error' => $e->getMessage(),
                'tutors' => [],
                'subjects' => [],
                'page' => 'tutors'
            ]);
        }
    }

    public function profile(int $tutorId): void
    {
        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }

        if ($_SESSION['user_role'] !== 'student') {
            $this->redirect('student/dashboard');
            return;
        }

        try {
            // tutor profile details
            $profile = $this->tutorSearchService->getTutorById($tutorId);
            
            if (!$profile) {
                http_response_code(404);
                echo '404 - Tutor not found';
                return;
            }

            $this->render('tutor/profile-public', [
                'profile' => $profile,
                'page' => 'tutors'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }
}

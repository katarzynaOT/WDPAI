<?php
require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../services/ReviewService.php';

class ReviewController extends AppController
{
    private ReviewService $reviewService;

    public function __construct()
    {
        $this->reviewService = new ReviewService();
    }

    // formularz dodania recenzji
    public function create(int $tutorId): void
    {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== 'student') {
            $this->redirect('student/dashboard');
            return;
        }
        $this->render('review/create', ['tutor_id' => $tutorId]);
    }

    // ZAPISZ 
    public function store(int $tutorId): void
    {
        $this->requirePost();
        $this->requireLogin();
        if ($_SESSION['user_role'] !== 'student') {
            $this->redirect('student/dashboard');
            return;
        }
        $studentUserId = $_SESSION['user_id'];
        $formData = $this->getSanitizedPostData();
        try {
            $this->reviewService->addReview($studentUserId, $tutorId, $formData);
            $_SESSION['flash_success'] = 'Recenzja została dodana!';
            $this->redirect('tutor/' . $tutorId);
        } catch (Exception $e) {
            $this->render('review/create', [
                'error' => $e->getMessage(),
                'tutor_id' => $tutorId,
                'formData' => $formData
            ]);
        }
    }
}

<?php
require_once __DIR__ . '/../repositories/ReviewRepository.php';
require_once __DIR__ . '/../repositories/StudentRepository.php';

class ReviewService
{
    private ReviewRepository $reviewRepository;
    private StudentRepository $studentRepository;

    public function __construct()
    {
        $this->reviewRepository = new ReviewRepository();
        $this->studentRepository = new StudentRepository();
    }

    public function addReview(int $studentUserId, int $tutorId, array $data): void
    {
        $studentProfile = $this->studentRepository->findByUserId($studentUserId);
        if (!$studentProfile || !isset($studentProfile['id'])) {
            throw new Exception('Profil studenta nie został znaleziony');
        }
        $studentProfileId = $studentProfile['id'];
        if ($this->hasReview($studentProfileId, $tutorId)) {
            throw new Exception('Już dodałeś recenzję dla tego korepetytora.');
        }
        $rating = (int)($data['rating'] ?? 0);
        $content = trim($data['content'] ?? '');
        if ($rating < 1 || $rating > 5) {
            throw new Exception('Ocena musi być od 1 do 5');
        }
        if (strlen($content) < 5) {
            throw new Exception('Komentarz jest za krótki');
        }
        $this->reviewRepository->addReview($tutorId, $studentProfileId, $rating, $content);
    }

    public function hasReview(int $studentProfileId, int $tutorId): bool
    {
        return $this->reviewRepository->hasReview($studentProfileId, $tutorId);
    }
}

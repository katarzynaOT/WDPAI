<?php

require_once 'Repository.php';

class ReviewRepository extends Repository
{
    public function addReview(int $tutorId, int $studentId, int $rating, string $content): void
    {
        $stmt = $this->db->prepare("INSERT INTO reviews (tutor_id, student_id, rating, content, created_at) VALUES (:tutor_id, :student_id, :rating, :content, NOW())");
        $stmt->execute([
            'tutor_id' => $tutorId,
            'student_id' => $studentId,
            'rating' => $rating,
            'content' => $content
        ]);
    }

    public function hasReview(int $studentId, int $tutorId): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM reviews WHERE student_id = :student_id AND tutor_id = :tutor_id LIMIT 1");
        $stmt->execute([
            'student_id' => $studentId,
            'tutor_id' => $tutorId
        ]);
        return (bool)$stmt->fetchColumn();
    }
}

<?php

require_once 'Repository.php';

class LessonRepository extends Repository 
{
    public function getAllLessons(): array
    {
        $stmt = $this->db->query("SELECT * FROM lessons ORDER BY scheduled_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLessonById(int $lessonId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $lessonId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        return $lesson ?: null;
    }

    public function addHomework(int $lessonId, array $homework): void
    {
        // dane lekcji, dla tutor_id i student_id
        $stmt = $this->db->prepare("SELECT tutor_id, student_id FROM lessons WHERE id = :lesson_id LIMIT 1");
        $stmt->execute(['lesson_id' => $lessonId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$lesson) throw new Exception('Lekcja nie znaleziona');

        $stmt = $this->db->prepare("INSERT INTO homework (lesson_id, tutor_id, student_id, title, description, assigned_date, due_date, created_at) VALUES (:lesson_id, :tutor_id, :student_id, :title, :description, :assigned_date, :due_date, NOW())");
        $stmt->execute([
            'lesson_id' => $lessonId,
            'tutor_id' => $lesson['tutor_id'],
            'student_id' => $lesson['student_id'],
            'title' => $homework['title'],
            'description' => $homework['description'],
            'assigned_date' => date('Y-m-d H:i:s'),
            'due_date' => $homework['deadline'] ?? date('Y-m-d H:i:s', strtotime('+7 days'))
        ]);
    }
}

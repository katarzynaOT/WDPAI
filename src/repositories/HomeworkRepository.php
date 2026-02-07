<?php

require_once 'Repository.php';

class HomeworkRepository extends Repository
{
    public function addHomework(int $lessonId, array $homework): void
    {
        $stmt = $this->db->prepare("INSERT INTO homeworks (lesson_id, title, description, deadline, created_at) VALUES (:lesson_id, :title, :description, :deadline, NOW())");
        $stmt->execute([
            'lesson_id' => $lessonId,
            'title' => $homework['title'],
            'description' => $homework['description'],
            'deadline' => $homework['deadline'] ?? null
        ]);
    }
}

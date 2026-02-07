<?php

require_once __DIR__ . '/../repositories/LessonRepository.php';

class LessonService {
    public function getAllLessons(): array
    {
        return $this->lessonRepository->getAllLessons();
    }

    public function getLessonById(int $lessonId): ?array
    {
        return $this->lessonRepository->getLessonById($lessonId);
    }


    private LessonRepository $lessonRepository;

    public function __construct()
    {
        $this->lessonRepository = new LessonRepository();
    }

    public function updateLesson(int $lessonId, array $data): void
    {
        $fields = [];
        if (isset($data['price'])) {
            $fields['price'] = (float)$data['price'];
        }
        if (isset($data['meeting_url'])) {
            $fields['meeting_url'] = trim($data['meeting_url']);
        }
        if (isset($data['payment_status'])) {
            $fields['payment_status'] = $data['payment_status'];
        }
        if (!empty($fields)) {
            $this->lessonRepository->updateLessonFields($lessonId, $fields);
        }
    }

    public function addHomework(int $lessonId, array $data): void
    {
        $homework = [
            'title' => trim($data['title'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'deadline' => $data['deadline'] ?? null
        ];
        $this->lessonRepository->addHomework($lessonId, $homework);
    }
}

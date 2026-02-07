<?php
require_once __DIR__ . '/../services/SubjectService.php';

class SubjectController
{
    private SubjectService $subjectService;

    public function __construct()
    {
        $this->subjectService = new SubjectService();
    }

    public function list(): void
    {
        $subjects = $this->subjectService->getAllSubjects();
        $result = [];
        foreach ($subjects as $subject) {
            $result[] = [
                'id' => $subject['id'],
                'name' => $subject['name']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}

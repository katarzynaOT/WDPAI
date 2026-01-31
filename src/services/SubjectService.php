<?php

require_once __DIR__ . '/../repositories/SubjectRepository.php';

class SubjectService
{
    private SubjectRepository $subjectRepository;
    
    public function __construct()
    {
        $this->subjectRepository = new SubjectRepository();
    }
    
    public function getAllSubjects(): array
    {
        return $this->subjectRepository->findAll();
    }
    
    public function getSubjectById(int $id): ?array
    {
        return $this->subjectRepository->findById($id);
    }
    
    public function getSubjectsByCategory(string $category): array
    {
        return $this->subjectRepository->findByCategory($category);
    }
    
    public function getPopularSubjects(int $limit = 10): array
    {
        return $this->subjectRepository->findPopular($limit);
    }
}
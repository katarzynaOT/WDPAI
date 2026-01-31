<?php

require_once __DIR__ . '/../repositories/TutorRepository.php';
require_once __DIR__ . '/../repositories/SubjectRepository.php';

class TutorSearchService
{
    private TutorRepository $tutorRepository;
    private SubjectRepository $subjectRepository;
    
    public function __construct()
    {
        $this->tutorRepository = new TutorRepository();
        $this->subjectRepository = new SubjectRepository();
    }

    public function getAllTutors(): array
    {
        return $this->tutorRepository->findAllWithDetails();
    }

    public function searchBySubject(int $subjectId): array
    {
        return $this->tutorRepository->findBySubject($subjectId);
    }
    public function getAllSubjects(): array
    {
        return $this->subjectRepository->findAll();
    }

    public function filterTutors(array $filters = []): array
    {
        $allTutors = $this->getAllTutors();
        
        // Filter by subject if provided
        if (!empty($filters['subject_id'])) {
            $subjectId = (int)$filters['subject_id'];
            $allTutors = array_filter($allTutors, function($tutor) use ($subjectId) {
                foreach ($tutor['subjects'] as $subject) {
                    if ($subject['subject_id'] == $subjectId) {
                        return true;
                    }
                }
                return false;
            });
        }
        
        // Filter by rating if provided
        if (!empty($filters['min_rating'])) {
            $minRating = (float)$filters['min_rating'];
            $allTutors = array_filter($allTutors, function($tutor) use ($minRating) {
                return ($tutor['rating'] ?? 0) >= $minRating;
            });
        }
        
        // Sort by rating (highest first)
        usort($allTutors, function($a, $b) {
            $ratingA = $a['rating'] ?? 0;
            $ratingB = $b['rating'] ?? 0;
            return $ratingB <=> $ratingA;
        });
        
        return $allTutors;
    }
    public function getTutorById(int $tutorId): ?array
    {
        $tutors = $this->getAllTutors();
        foreach ($tutors as $tutor) {
            if ($tutor['profile_id'] == $tutorId) {
                return $tutor;
            }
        }
        return null;
    }
}

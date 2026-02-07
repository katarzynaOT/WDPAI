<?php

require_once 'Repository.php';

class TutorRepository extends Repository
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO tutor_profiles 
            (user_id, bio, education, experience_years, description)
            VALUES (:user_id, :bio, :education, :experience_years, :description)
            RETURNING id
        ");
        
        $stmt->execute([
            'user_id' => $data['user_id'],
            'bio' => $data['bio'],
            'education' => $data['education'] ?? null,
            'experience_years' => $data['experience_years'] ?? 0,
            'description' => $data['description'] ?? null
        ]);
        
        $result = $stmt->fetch();
        return $result['id'];
    }
    
    public function addSubject(int $tutorId, int $subjectId, string $expertiseLevel = 'intermediate'): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO tutor_subjects 
            (tutor_id, subject_id, expertise_level)
            VALUES (:tutor_id, :subject_id, :expertise_level)
            ON CONFLICT (tutor_id, subject_id) 
            DO UPDATE SET expertise_level = EXCLUDED.expertise_level
        ");
        
        $stmt->execute([
            'tutor_id' => $tutorId,
            'subject_id' => $subjectId,
            'expertise_level' => $expertiseLevel
        ]);
    }
    
    public function removeSubject(int $tutorId, int $subjectId): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM tutor_subjects 
            WHERE tutor_id = :tutor_id AND subject_id = :subject_id
        ");
        
        $stmt->execute([
            'tutor_id' => $tutorId,
            'subject_id' => $subjectId
        ]);
    }
    
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                tp.id,
                tp.user_id,
                tp.bio,
                tp.education,
                tp.experience_years,
                tp.description,
                tp.rating,
                tp.total_reviews,
                ARRAY_AGG(
                    JSON_BUILD_OBJECT(
                        'subject_id', ts.subject_id,
                        'expertise_level', ts.expertise_level,
                        'years_experience', ts.years_experience
                    )
                ) as subjects
            FROM tutor_profiles tp
            LEFT JOIN tutor_subjects ts ON tp.id = ts.tutor_id
            WHERE tp.user_id = :user_id
            GROUP BY tp.id
            LIMIT 1
        ");
        
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        // Rozpakuj JSON subjects
        $row['subjects'] = $row['subjects'] ? json_decode($row['subjects'], true) : [];
        
        return $row;
    }
    
    public function findById(int $profileId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                tp.id,
                tp.user_id,
                tp.bio,
                tp.education,
                tp.experience_years,
                tp.description,
                tp.rating,
                tp.total_reviews,
                ARRAY_AGG(
                    JSON_BUILD_OBJECT(
                        'subject_id', ts.subject_id,
                        'expertise_level', ts.expertise_level,
                        'years_experience', ts.years_experience
                    )
                ) as subjects
            FROM tutor_profiles tp
            LEFT JOIN tutor_subjects ts ON tp.id = ts.tutor_id
            WHERE tp.id = :id
            GROUP BY tp.id
            LIMIT 1
        ");
        
        $stmt->execute(['id' => $profileId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        // unpakc JSON subjects
        $row['subjects'] = $row['subjects'] ? json_decode($row['subjects'], true) : [];
        
        return $row;
    }
    
    public function findAllWithDetails(): array
    {
        $stmt = $this->db->query("
            SELECT 
                tp.id as profile_id,
                tp.bio,
                tp.education,
                tp.experience_years,
                tp.description,
                tp.rating,
                tp.total_reviews,
                u.id as user_id,
                u.email,
                u.first_name,
                u.last_name,
                u.phone,
                u.hourly_rate,
                u.created_at,
                ARRAY_AGG(
                    JSON_BUILD_OBJECT(
                        'subject_id', s.id,
                        'subject_name', s.name,
                        'category', s.category,
                        'expertise_level', ts.expertise_level
                    ) ORDER BY s.id
                ) as subjects
            FROM tutor_profiles tp
            JOIN users u ON tp.user_id = u.id
            LEFT JOIN tutor_subjects ts ON tp.id = ts.tutor_id
            LEFT JOIN subjects s ON ts.subject_id = s.id
            GROUP BY tp.id, u.id
            ORDER BY tp.rating DESC NULLS LAST, u.created_at DESC
        ");
        
        $results = $stmt->fetchAll();
        
        // Przetwórz JSON subjects dla każdego rekordu i usuń duplikaty
        foreach ($results as &$row) {
            $subjects = [];
            if ($row['subjects']) {
                $decoded = json_decode($row['subjects'], true);
                $subjects = is_array($decoded) ? $decoded : [];
            }
            
            // Usuń duplikaty i null wartości na podstawie subject_id
            $uniqueSubjects = [];
            $seenIds = [];
            foreach ($subjects as $subject) {
                if ($subject && isset($subject['subject_id']) && !in_array($subject['subject_id'], $seenIds)) {
                    $uniqueSubjects[] = $subject;
                    $seenIds[] = $subject['subject_id'];
                }
            }
            $row['subjects'] = $uniqueSubjects;
        }
        
        return $results;
    }
    
    public function findBySubject(int $subjectId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                tp.id as profile_id,
                tp.bio,
                tp.rating,
                tp.total_reviews,
                u.id as user_id,
                u.first_name,
                u.last_name,
                u.hourly_rate,
                ts.expertise_level
            FROM tutor_profiles tp
            JOIN users u ON tp.user_id = u.id
            JOIN tutor_subjects ts ON tp.id = ts.tutor_id
            WHERE ts.subject_id = :subject_id
            ORDER BY tp.rating DESC NULLS LAST
        ");
        
        $stmt->execute(['subject_id' => $subjectId]);
        return $stmt->fetchAll();
    }
    
    public function updateRating(int $tutorId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE tutor_profiles tp
            SET 
                rating = (
                    SELECT COALESCE(AVG(r.rating), 0.0)
                    FROM reviews r
                    WHERE r.tutor_id = tp.id
                ),
                total_reviews = (
                    SELECT COUNT(*)
                    FROM reviews r
                    WHERE r.tutor_id = tp.id
                )
            WHERE tp.id = :tutor_id
        ");
        
        $stmt->execute(['tutor_id' => $tutorId]);
    }

    public function update(int $profileId, array $data): void
    {
        $fields = [];
        $params = ['id' => $profileId];

        if (isset($data['bio'])) {
            $fields[] = 'bio = :bio';
            $params['bio'] = $data['bio'];
        }

        if (isset($data['education'])) {
            $fields[] = 'education = :education';
            $params['education'] = $data['education'];
        }

        if (isset($data['experience_years'])) {
            $fields[] = 'experience_years = :experience_years';
            $params['experience_years'] = $data['experience_years'];
        }

        if (isset($data['description'])) {
            $fields[] = 'description = :description';
            $params['description'] = $data['description'];
        }

        if (empty($fields)) {
            return;
        }

        $sql = "UPDATE tutor_profiles SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function getSubjects(int $tutorId): array
    {
        $stmt = $this->db->prepare(
            "SELECT subject_id, expertise_level, years_experience
             FROM tutor_subjects
             WHERE tutor_id = :tutor_id"
        );

        $stmt->execute(['tutor_id' => $tutorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get user_id by tutor profile_id
     */
    public function getUserIdByProfileId(int $profileId): ?int
    {
        $stmt = $this->db->prepare("
            SELECT user_id FROM tutor_profiles WHERE id = :id LIMIT 1
        ");
        
        $stmt->execute(['id' => $profileId]);
        $result = $stmt->fetch();
        
        return $result ? (int)$result['user_id'] : null;
    }
}
<?php

require_once 'Repository.php';

class SubjectRepository extends Repository
{
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT id, name, category, description
            FROM subjects
            ORDER BY name
        ");
        
        return $stmt->fetchAll();
    }
    
    public function findByCategory(string $category): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, category, description
            FROM subjects
            WHERE category = :category
            ORDER BY name
        ");
        
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, category, description
            FROM subjects
            WHERE id = :id
            LIMIT 1
        ");
        
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, category, description
            FROM subjects
            WHERE name = :name
            LIMIT 1
        ");
        
        $stmt->execute(['name' => $name]);
        return $stmt->fetch() ?: null;
    }
    
    public function create(string $name, string $category, ?string $description = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO subjects (name, category, description)
            VALUES (:name, :category, :description)
            RETURNING id
        ");
        
        $stmt->execute([
            'name' => $name,
            'category' => $category,
            'description' => $description
        ]);
        
        $result = $stmt->fetch();
        return $result['id'];
    }
    
    public function findPopular(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                s.id,
                s.name,
                s.category,
                COUNT(ts.tutor_id) as tutor_count
            FROM subjects s
            LEFT JOIN tutor_subjects ts ON s.id = ts.subject_id
            GROUP BY s.id, s.name, s.category
            ORDER BY tutor_count DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
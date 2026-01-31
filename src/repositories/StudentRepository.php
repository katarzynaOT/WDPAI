<?php

require_once 'Repository.php';

class StudentRepository extends Repository
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO student_profiles 
            (user_id, level, learning_goals)
            VALUES (:user_id, :level, :learning_goals)
            RETURNING id
        ");
        
        $stmt->execute([
            'user_id' => $data['user_id'],
            'level' => $data['level'],
            'learning_goals' => $data['learning_goals'] ?? null
        ]);
        
        $result = $stmt->fetch();
        return $result['id'];
    }
    
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, level, learning_goals
            FROM student_profiles 
            WHERE user_id = :user_id
            LIMIT 1
        ");
        
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        
        return $row ?: null;
    }
    
    public function findById(int $profileId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, level, learning_goals
            FROM student_profiles 
            WHERE id = :id
            LIMIT 1
        ");
        
        $stmt->execute(['id' => $profileId]);
        $row = $stmt->fetch();
        
        return $row ?: null;
    }
    
    public function update(int $profileId, array $data): void
    {
        $fields = [];
        $params = ['id' => $profileId];
        
        if (isset($data['level'])) {
            $fields[] = 'level = :level';
            $params['level'] = $data['level'];
        }
        
        if (isset($data['learning_goals'])) {
            $fields[] = 'learning_goals = :learning_goals';
            $params['learning_goals'] = $data['learning_goals'];
        }
        
        if (empty($fields)) {
            return;
        }
        
        $sql = "UPDATE student_profiles SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function findAllWithUserData(): array
    {
        $stmt = $this->db->query("
            SELECT 
                sp.id as profile_id,
                sp.level,
                sp.learning_goals,
                u.id as user_id,
                u.email,
                u.first_name,
                u.last_name,
                u.phone,
                u.created_at
            FROM student_profiles sp
            JOIN users u ON sp.user_id = u.id
            ORDER BY u.created_at DESC
        ");
        
        return $stmt->fetchAll();
    }
}
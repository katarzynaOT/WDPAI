<?php

require_once __DIR__ . '/../models/User.php';
require_once "Repository.php";

class UserRepository extends Repository 
{
    private static ?UserRepository $instance = null;
    
    public function __construct()
    {
        parent::__construct(); 
    }

    public static function getInstance(): UserRepository 
    {
        if (self::$instance === null) 
        {
            self::$instance = new UserRepository();
        }
        return self::$instance;
    }

    public function findByEmail(string $email): ?User 
    {
        $stmt = $this->db->prepare(
            "SELECT id, email, password, role, first_name, last_name
             FROM users WHERE email = :email LIMIT 1"
        );

        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if (!$row) return null;

        //return $this->mapToUser($row);

        $user = new User();
        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->passwordHash = $row['password'];
        $user->role = $row['role'];
        $user->firstName = $row['first_name'];
        $user->lastName = $row['last_name'];

        return $user;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare(
            "SELECT id, email, password, role, first_name, last_name, phone, hourly_rate, created_at, last_login
             FROM users WHERE id = :id LIMIT 1"
        );

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return $this->mapToUser($row);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            " INSERT INTO users 
             (email, password, first_name, last_name, phone, role, hourly_rate)
             VALUES (:email, :password, :first_name, :last_name, :phone, :role, :hourly_rate)
             RETURNING id"
        );
        
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'hourly_rate' => $data['hourly_rate'] ?? null
        ]);
        
        $result = $stmt->fetch();
        return $result['id'];
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count 
             FROM users WHERE email = :email"
        );
        
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        
        return $row['count'] > 0;
    }

    public function updateLastLogin(int $userId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET last_login = NOW() 
             WHERE id = :id"
        );
        
        $stmt->execute(['id' => $userId]);
    }

    // TODO: ChangePassword, finByrole

    private function mapToUser(array $row, bool $includePassword = true): User
    {
        $user = new User();
        $user->id = (int)$row['id'];
        $user->email = $row['email'];
        
        if ($includePassword) {
            $user->passwordHash = $row['password'];
        }
        
        $user->role = $row['role'];
        $user->firstName = $row['first_name'];
        $user->lastName = $row['last_name'];
        $user->phone = $row['phone'];
        $user->hourlyRate = $row['hourly_rate'] ? (float)$row['hourly_rate'] : null;
        $user->createdAt = $row['created_at'];
        $user->lastLogin = $row['last_login'];
        
        return $user;
    }
}

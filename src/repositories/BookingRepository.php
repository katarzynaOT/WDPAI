<?php

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../models/Booking.php';

class BookingRepository extends Repository
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO lessons 
            (tutor_id, student_id, subject_id, title, description, scheduled_date, duration_minutes, status, price, payment_status, notes)
            VALUES (:tutor_id, :student_id, :subject_id, :title, :description, :scheduled_date, :duration_minutes, :status::lesson_status, :price, :payment_status::payment_status, :notes)
            RETURNING id
        ");

        $stmt->execute([
            'tutor_id' => $data['tutor_id'],
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'] ?? null,
            'title' => $data['title'] ?? 'Lekcja',
            'description' => $data['description'] ?? null,
            'scheduled_date' => $data['scheduled_date'],
            'duration_minutes' => $data['duration_minutes'] ?? 60,
            'status' => $data['status'] ?? 'pending',
            'price' => $data['price'] ?? 0.00,
            'payment_status' => $data['payment_status'] ?? 'pending',
            'notes' => $data['notes'] ?? null
        ]);

        $result = $stmt->fetch();
        return $result['id'];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                b.id,
                b.student_id,
                b.tutor_id,
                b.subject_id,
                b.title,
                b.description,
                b.scheduled_date,
                b.duration_minutes,
                b.status,
                b.notes,
                b.price,
                b.payment_status,
                b.meeting_url,
                b.cancelled_at,
                b.created_at,
                b.updated_at,
                u_student.first_name as student_first_name,
                u_student.last_name as student_last_name,
                u_student.email as student_email,
                u_tutor.first_name as tutor_first_name,
                u_tutor.last_name as tutor_last_name,
                u_tutor.email as tutor_email,
                s.name as subject_name
            FROM lessons b
            JOIN student_profiles sp ON b.student_id = sp.id
            JOIN tutor_profiles tp ON b.tutor_id = tp.id
            JOIN users u_student ON sp.user_id = u_student.id
            JOIN users u_tutor ON tp.user_id = u_tutor.id
            LEFT JOIN subjects s ON b.subject_id = s.id
            WHERE b.id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByStudentId(int $studentId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                b.id,
                b.student_id,
                b.tutor_id,
                b.subject_id,
                b.title,
                b.scheduled_date,
                b.duration_minutes,
                b.status,
                b.price,
                b.payment_status,
                b.created_at,
                b.updated_at,
                u_tutor.first_name as tutor_first_name,
                u_tutor.last_name as tutor_last_name,
                u_tutor.email as tutor_email,
                tp.bio as tutor_bio,
                tp.rating as tutor_rating,
                s.name as subject_name
            FROM lessons b
            JOIN tutor_profiles tp ON b.tutor_id = tp.id
            JOIN users u_tutor ON tp.user_id = u_tutor.id
            LEFT JOIN subjects s ON b.subject_id = s.id
            WHERE b.student_id = :student_id
            ORDER BY b.scheduled_date DESC
        ");

        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function findByTutorId(int $tutorId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                b.id,
                b.student_id,
                b.tutor_id,
                b.subject_id,
                b.title,
                b.scheduled_date,
                b.duration_minutes,
                b.status,
                b.price,
                b.payment_status,
                b.created_at,
                b.updated_at,
                u_student.first_name as student_first_name,
                u_student.last_name as student_last_name,
                u_student.email as student_email,
                s.name as subject_name
            FROM lessons b
            JOIN student_profiles sp ON b.student_id = sp.id
            JOIN users u_student ON sp.user_id = u_student.id
            LEFT JOIN subjects s ON b.subject_id = s.id
            WHERE b.tutor_id = :tutor_id
            ORDER BY b.scheduled_date DESC
        ");

        $stmt->execute(['tutor_id' => $tutorId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare("
            UPDATE lessons 
            SET status = :status::lesson_status, updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    public function cancel(int $id, int $cancelledBy): void
    {
        $stmt = $this->db->prepare("
            UPDATE lessons 
            SET status = 'cancelled'::lesson_status, cancelled_at = NOW(), cancelled_by = :cancelled_by, updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'cancelled_by' => $cancelledBy
        ]);
    }

    public function hasActiveBooking(int $studentId, int $tutorId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM lessons
            WHERE student_id = :student_id 
            AND tutor_id = :tutor_id
            AND status IN ('pending'::lesson_status, 'scheduled'::lesson_status)
        ");

        $stmt->execute([
            'student_id' => $studentId,
            'tutor_id' => $tutorId
        ]);

        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getUpcomingForStudent(int $studentId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                b.id,
                b.student_id,
                b.tutor_id,
                b.subject_id,
                b.title,
                b.scheduled_date,
                b.duration_minutes,
                b.status,
                b.price,
                b.payment_status,
                u_tutor.first_name as tutor_first_name,
                u_tutor.last_name as tutor_last_name,
                tp.rating as tutor_rating,
                s.name as subject_name
            FROM lessons b
            JOIN tutor_profiles tp ON b.tutor_id = tp.id
            JOIN users u_tutor ON tp.user_id = u_tutor.id
            LEFT JOIN subjects s ON b.subject_id = s.id
            WHERE b.student_id = :student_id
            AND b.status IN ('pending'::lesson_status, 'scheduled'::lesson_status)
            AND b.scheduled_date > NOW()
            ORDER BY b.scheduled_date ASC
            LIMIT :limit
        ");

        $stmt->bindValue('student_id', $studentId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

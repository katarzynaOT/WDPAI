<?php

require_once __DIR__ . '/../repositories/BookingRepository.php';
require_once __DIR__ . '/../repositories/StudentRepository.php';
require_once __DIR__ . '/../repositories/TutorRepository.php';

class BookingService
{
    private BookingRepository $bookingRepository;
    private StudentRepository $studentRepository;
    private TutorRepository $tutorRepository;

    public function __construct()
    {
        $this->bookingRepository = new BookingRepository();
        $this->studentRepository = new StudentRepository();
        $this->tutorRepository = new TutorRepository();
    }

    public function requestBooking(int $studentProfileId, int $tutorProfileId, string $scheduledAt, int $durationMinutes = 60, ?string $notes = null): int
    {
        // Walidacja danych wejściowych
        $studentProfile = $this->studentRepository->findById($studentProfileId);
        $tutorProfile = $this->tutorRepository->findById($tutorProfileId);

        if (!$studentProfile || !$tutorProfile) {
            throw new Exception('Student lub tutor nie znaleziony');
        }

        // Walidacja daty i czasu
        $scheduledTime = new DateTime($scheduledAt);
        $now = new DateTime();
        if ($scheduledTime <= $now) {
            throw new Exception('Lekcja musi być zaplanowana na przyszłość');
        }

        if ($durationMinutes < 15 || $durationMinutes > 240) {
            throw new Exception('Czas trwania musi być między 15 a 240 minut');
        }

        // TWORZENIE REZERWACJI
        $bookingId = $this->bookingRepository->create([
            'student_id' => $studentProfileId,
            'tutor_id' => $tutorProfileId,
            'scheduled_date' => $scheduledAt,
            'duration_minutes' => $durationMinutes,
            'status' => 'pending',
            'notes' => $notes
        ]);

        return $bookingId;
    }

    public function confirmBooking(int $bookingId, int $tutorProfileId): void
    {
        $booking = $this->bookingRepository->findById($bookingId);

        if (!$booking) {
            throw new Exception('Rezerwacja nie została znaleziona');
        }

        if ($booking['tutor_id'] != $tutorProfileId) {
            throw new Exception('Nie masz uprawnień do potwierdzenia tej rezerwacji');
        }

        if ($booking['status'] !== 'pending') {
            throw new Exception('Ta rezerwacja ma już status: ' . $booking['status']);
        }

        $this->bookingRepository->updateStatus($bookingId, 'scheduled');
    }

    public function cancelBooking(int $bookingId, int $userProfileId, string $userRole): void
    {
        $booking = $this->bookingRepository->findById($bookingId);

        if (!$booking) {
            throw new Exception('Rezerwacja nie została znaleziona');
        }

        // Tylko właściciele rezerwacji mogą ją anulować
        $isStudent = $userRole === 'student' && $booking['student_id'] == $userProfileId;
        $isTutor = $userRole === 'tutor' && $booking['tutor_id'] == $userProfileId;
        
        if (!$isStudent && !$isTutor) {
            throw new Exception('Nie masz uprawnień do anulowania tej rezerwacji');
        }

        if ($booking['status'] === 'cancelled') {
            throw new Exception('Ta rezerwacja została już anulowana');
        }

        if ($booking['status'] === 'completed') {
            throw new Exception('Nie można anulować ukończoną lekcję');
        }

        $this->bookingRepository->cancel($bookingId, $userProfileId);
    }

    public function completeBooking(int $bookingId, int $tutorProfileId): void
    {
        $booking = $this->bookingRepository->findById($bookingId);

        if (!$booking) {
            throw new Exception('Rezerwacja nie została znaleziona');
        }

        if ($booking['tutor_id'] != $tutorProfileId) {
            throw new Exception('Nie masz uprawnień do ukończenia tej rezerwacji');
        }

        if ($booking['status'] !== 'scheduled') {
            throw new Exception('Tylko potwierdzone rezerwacje mogą być ukończone');
        }

        $this->bookingRepository->updateStatus($bookingId, 'completed');
    }

    // GETTERY

    public function getBooking(int $bookingId): ?array
    {
        return $this->bookingRepository->findById($bookingId);
    }

    // Pobierz lekcję z weryfikacją uprawnień użytkownika
    public function getBookingForUser(int $bookingId, int $userId, string $userRole): array
    {
        $booking = $this->getBooking($bookingId);
        if (!$booking) {
            throw new Exception('Lekcja nie została znaleziona.');
        }

        if ($userRole === 'student') {
            $studentProfile = $this->studentRepository->findByUserId($userId);
            if (!$studentProfile || !isset($studentProfile['id']) || $booking['student_id'] !== $studentProfile['id']) {
                throw new Exception('Nie masz uprawnień do wyświetlenia tej lekcji.');
            }
        } elseif ($userRole === 'tutor') {
            $tutorProfile = $this->tutorRepository->findByUserId($userId);
            if (!$tutorProfile || !isset($tutorProfile['id']) || $booking['tutor_id'] !== $tutorProfile['id']) {
                throw new Exception('Nie masz uprawnień do wyświetlenia tej lekcji.');
            }
        } else {
            throw new Exception('Nieznana rola użytkownika.');
        }

        return $booking;
    }

    public function getStudentBookings(int $studentId): array
    {
        return $this->bookingRepository->findByStudentId($studentId);
    }

    public function getTutorBookings(int $tutorId): array
    {
        return $this->bookingRepository->findByTutorId($tutorId);
    }

    public function getUpcomingBookings(int $studentId, int $limit = 10): array
    {
        return $this->bookingRepository->getUpcomingForStudent($studentId, $limit);
    }
}

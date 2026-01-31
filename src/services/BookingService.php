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

    /**
     * Request new booking (lesson)
     * @param int $studentProfileId ID from student_profiles table
     * @param int $tutorProfileId ID from tutor_profiles table
     */
    public function requestBooking(int $studentProfileId, int $tutorProfileId, string $scheduledAt, int $durationMinutes = 60, ?string $notes = null): int
    {
        // Validate student and tutor profiles exist
        $studentProfile = $this->studentRepository->findById($studentProfileId);
        $tutorProfile = $this->tutorRepository->findById($tutorProfileId);

        if (!$studentProfile || !$tutorProfile) {
            throw new Exception('Student lub tutor nie został znaleziony');
        }

        // Check if already has active booking
        if ($this->bookingRepository->hasActiveBooking($studentProfileId, $tutorProfileId)) {
            throw new Exception('Masz już aktywną rezerwację z tym tutorem');
        }

        // Validate scheduled_date is in future
        $scheduledTime = new DateTime($scheduledAt);
        $now = new DateTime();
        if ($scheduledTime <= $now) {
            throw new Exception('Lekcja musi być zaplanowana na przyszłość');
        }

        // Validate duration
        if ($durationMinutes < 15 || $durationMinutes > 240) {
            throw new Exception('Czas trwania musi być między 15 a 240 minut');
        }

        // Create booking with pending status
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

    /**
     * Confirm booking (tutor accepts)
     */
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

    /**
     * Cancel booking
     */
    public function cancelBooking(int $bookingId, int $userProfileId, string $userRole): void
    {
        $booking = $this->bookingRepository->findById($bookingId);

        if (!$booking) {
            throw new Exception('Rezerwacja nie została znaleziona');
        }

        // Only student or tutor of this booking can cancel
        $isStudent = $userRole === 'student' && $booking['student_id'] == $userProfileId;
        $isTutor = $userRole === 'tutor' && $booking['tutor_id'] == $userProfileId;
        
        if (!$isStudent && !$isTutor) {
            throw new Exception('Nie masz uprawnień do anulowania tej rezerwacji');
        }

        if ($booking['status'] === 'cancelled') {
            throw new Exception('Ta rezerwacja została już anulowana');
        }

        if ($booking['status'] === 'completed') {
            throw new Exception('Nie możesz anulować ukończoną lekcję');
        }

        $this->bookingRepository->cancel($bookingId, $userProfileId);
    }

    /**
     * Mark booking as completed
     */
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

    /**
     * Get booking details
     */
    public function getBooking(int $bookingId): ?array
    {
        return $this->bookingRepository->findById($bookingId);
    }

    /**
     * Get student's bookings
     */
    public function getStudentBookings(int $studentId): array
    {
        return $this->bookingRepository->findByStudentId($studentId);
    }

    /**
     * Get tutor's bookings
     */
    public function getTutorBookings(int $tutorId): array
    {
        return $this->bookingRepository->findByTutorId($tutorId);
    }

    /**
     * Get upcoming bookings for student
     */
    public function getUpcomingBookings(int $studentId, int $limit = 10): array
    {
        return $this->bookingRepository->getUpcomingForStudent($studentId, $limit);
    }
}

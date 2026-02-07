<?php

require_once 'AppController.php';
require_once __DIR__ . '/../services/BookingService.php';
require_once __DIR__ . '/../services/TutorSearchService.php';
require_once __DIR__ . '/../repositories/TutorRepository.php';
require_once __DIR__ . '/../repositories/StudentRepository.php';

class BookingController extends AppController
{
    private BookingService $bookingService;

    public function __construct()
    {
        $this->bookingService = new BookingService();
    }

    public function create(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') {
            $this->redirect('login');
            return;
        }

        try {
            $tutorId = isset($_GET['tutor']) ? (int)$_GET['tutor'] : null;

            if (!$tutorId) {
                throw new Exception('ID tutora jest wymagane');
            }

            // tutor details
            $tutorService = new TutorSearchService();
            $tutor = $tutorService->getTutorById($tutorId);

            if (!$tutor) {
                throw new Exception('Tutor nie został znaleziony');
            }

            $this->render('booking/create', [
                'tutor' => $tutor,
                'page' => 'booking'
            ]);

        } catch (Exception $e) {
            $this->render('booking/create', [
                'error' => $e->getMessage(),
                'page' => 'booking'
            ]);
        }
    }

    public function store(): void
    {
        $this->requirePost();

        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') {
            $this->redirect('login');
            return;
        }

        $studentUserId = $_SESSION['user_id'];
        $tutorProfileId = (int)($_POST['tutor_id'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? '';
        $durationMinutes = (int)($_POST['duration_minutes'] ?? 60);
        $notes = $_POST['notes'] ?? null;

        try {
            if (!$tutorProfileId || !$scheduledAt) {
                throw new Exception('Wszystkie pola są wymagane');
            }

            $studentRepository = new StudentRepository();
            $studentProfile = $studentRepository->findByUserId($studentUserId);
            
            if (!$studentProfile || !isset($studentProfile['id'])) {
                throw new Exception('Profil studenta nie został znaleziony');
            }

            $studentProfileId = $studentProfile['id'];
            $tutorRepository = new TutorRepository();
            $tutorUserId = $tutorRepository->getUserIdByProfileId($tutorProfileId);

            if (!$tutorUserId) {
                throw new Exception('Tutor nie został znaleziony');
            }

            $bookingId = $this->bookingService->requestBooking(
                $studentProfileId,
                $tutorProfileId,
                $scheduledAt,
                $durationMinutes,
                $notes
            );

            $_SESSION['flash_success'] = 'Rezerwacja została wysłana! Czekaj na potwierdzenie tutora.';
            $this->redirect('bookings');

        } catch (Exception $e) {
            $tutor = new TutorSearchService();
            $tutorData = $tutor->getTutorById($tutorProfileId);

            $this->render('booking/create', [
                'error' => $e->getMessage(),
                'tutor' => $tutorData,
                'posted_data' => $_POST,
                'page' => 'booking'
            ]);
        }
    }

    public function listBookings(): void
    {
        $this->requireLogin();

        $userRole = $_SESSION['user_role'] ?? null;

        if ($userRole === 'student') {
            $this->listStudentBookings();
        } elseif ($userRole === 'tutor') {
            $this->listTutorBookings();
        } else {
            $this->redirect('');
        }
    }

    public function listStudentBookings(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'student') {
            $this->redirect('login');
            return;
        }

        try {
            $studentUserId = $_SESSION['user_id'];
            $studentRepository = new StudentRepository();
            $studentProfile = $studentRepository->findByUserId($studentUserId);
            
            if (!$studentProfile || !isset($studentProfile['id'])) {
                throw new Exception('Profil studenta nie został znaleziony');
            }
            
            $studentProfileId = $studentProfile['id'];
            $bookings = $this->bookingService->getStudentBookings($studentProfileId);
            $upcomingBookings = $this->bookingService->getUpcomingBookings($studentProfileId, 5);

            $this->render('booking/student-list', [
                'bookings' => $bookings,
                'upcomingBookings' => $upcomingBookings,
                'page' => 'bookings'
            ]);

        } catch (Exception $e) {
            $this->render('booking/student-list', [
                'error' => $e->getMessage(),
                'bookings' => [],
                'upcomingBookings' => [],
                'page' => 'bookings'
            ]);
        }
    }

    public function listTutorBookings(): void
    {
        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            $this->redirect('login');
            return;
        }

        try {
            $tutorUserId = $_SESSION['user_id'];
            $tutorRepository = new TutorRepository();
            $tutorProfile = $tutorRepository->findByUserId($tutorUserId);
            
            if (!$tutorProfile || !isset($tutorProfile['id'])) {
                throw new Exception('Profil tutora nie został znaleziony');
            }
            
            $tutorProfileId = $tutorProfile['id'];
            $bookings = $this->bookingService->getTutorBookings($tutorProfileId);

            $this->render('booking/tutor-list', [
                'bookings' => $bookings,
                'page' => 'bookings'
            ]);

        } catch (Exception $e) {
            $this->render('booking/tutor-list', [
                'error' => $e->getMessage(),
                'bookings' => [],
                'page' => 'bookings'
            ]);
        }
    }

    public function confirm(): void
    {
        $this->requirePost();

        if (!$this->isLogged() || $_SESSION['user_role'] !== 'tutor') {
            $this->redirect('login');
            return;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $tutorUserId = $_SESSION['user_id'];

        try {
            if (!$bookingId) {
                throw new Exception('ID rezerwacji jest wymagane');
            }

            $tutorRepository = new TutorRepository();
            $tutorProfile = $tutorRepository->findByUserId($tutorUserId);
            
            if (!$tutorProfile || !isset($tutorProfile['id'])) {
                throw new Exception('Profil tutora nie został znaleziony');
            }
            
            $tutorProfileId = $tutorProfile['id'];
            $this->bookingService->confirmBooking($bookingId, $tutorProfileId);

            $_SESSION['flash_success'] = 'Rezerwacja została potwierdzona!';
            $this->redirect('bookings');

        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('bookings');
        }
    }

    public function cancel(): void
    {
        $this->requirePost();

        if (!$this->isLogged()) {
            $this->redirect('login');
            return;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        try {
            if (!$bookingId) {
                throw new Exception('ID rezerwacji jest wymagane');
            }

            // Get profile ID based on user role
            if ($userRole === 'student') {
                $studentRepository = new StudentRepository();
                $profile = $studentRepository->findByUserId($userId);
            } else {
                $tutorRepository = new TutorRepository();
                $profile = $tutorRepository->findByUserId($userId);
            }
            
            if (!$profile || !isset($profile['id'])) {
                throw new Exception('Profil użytkownika nie został znaleziony');
            }
            
            $profileId = $profile['id'];
            $this->bookingService->cancelBooking($bookingId, $profileId, $userRole);

            $_SESSION['flash_success'] = 'Rezerwacja została anulowana';
            
            $this->redirect('bookings');

        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            
            $this->redirect('bookings');
        }
    }

}

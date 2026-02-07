<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../services/BookingService.php';
require_once __DIR__ . '/../services/LessonService.php';

class LessonController extends AppController {
        // Endpoint do pobierania listy lekcji jako JSON
        public function lessonsData(): void
        {
            $this->requireLogin();
            $lessons = $this->lessonService->getAllLessons();
            $result = [];
            foreach ($lessons as $lesson) {
                $result[] = [
                    'id' => $lesson['id'],
                    'title' => $lesson['title'],
                    'date' => isset($lesson['scheduled_date']) ? date('Y-m-d', strtotime($lesson['scheduled_date'])) : '',
                    'status' => $lesson['status']
                ];
            }
            header('Content-Type: application/json');
            echo json_encode(['lessons' => $result]);
            exit;
        }
    private BookingService $bookingService;
    private LessonService $lessonService;

    public function __construct()
    {
        $this->bookingService = new BookingService();
        $this->lessonService = new LessonService();
    }

    public function listHtml(): void
    {
        $this->requireLogin();
        $this->renderHtml('lessons');
    }

    // Widok szczegółów lekcji (HTML placeholder)
    public function detailsHtml(int $id): void
    {
        $this->requireLogin();
        $lesson = $this->lessonService->getLessonById($id);
        $this->renderHtml('lesson-details', [
            'lessonId' => $id,
            'lessonTitle' => $lesson['title'] ?? ''
        ]);
    }

    // lesson detail 
    public function show(int $id): void
    {
        $this->requireLogin();
        $userRole = $_SESSION['user_role'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        try {
            $booking = $this->bookingService->getBookingForUser($id, $userId, $userRole);
            if ($userRole === 'student') {
                $this->render('booking/student-lesson', ['booking' => $booking]);
            } elseif ($userRole === 'tutor') {
                $this->render('booking/tutor-lesson', ['booking' => $booking]);
            } else {
                $this->redirect('dashboard');
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('bookings');
        }
    }

    // Edycja lekcji przez tutora
    public function edit(int $id): void
    {
        $this->requireLogin();
        $userRole = $_SESSION['user_role'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        try {
            $booking = $this->bookingService->getBookingForUser($id, $userId, $userRole);
            if ($userRole !== 'tutor') {
                throw new Exception('Brak uprawnień do edycji lekcji.');
            }
            $this->render('lesson/edit', ['booking' => $booking]);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('bookings');
        }
    }

    public function update(int $id): void
    {
        $this->requirePost();
        $userRole = $_SESSION['user_role'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        try {
            $booking = $this->bookingService->getBookingForUser($id, $userId, $userRole);
            if ($userRole !== 'tutor') {
                throw new Exception('Brak uprawnień do edycji lekcji.');
            }
            $formData = $this->getSanitizedPostData();
            $this->lessonService->updateLesson($id, $formData);
            $_SESSION['flash_success'] = 'Lekcja została zaktualizowana.';
            $this->redirect('lesson/' . $id);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('lesson/' . $id . '/edit');
        }
    }

    // Dodawanie pracy domowej
    public function homework(int $id): void
    {
        $this->requireLogin();
        $userRole = $_SESSION['user_role'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        try {
            $booking = $this->bookingService->getBookingForUser($id, $userId, $userRole);
            if ($userRole !== 'tutor') {
                throw new Exception('Brak uprawnień do dodania pracy domowej.');
            }
            if (($booking['status'] ?? '') === 'cancelled') {
                throw new Exception('Nie można dodać pracy domowej do anulowanej lekcji.');
            }
            $this->render('lesson/homework', ['booking' => $booking]);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('lesson/' . $id);
        }
    }

    public function storeHomework(int $id): void
    {
        $this->requirePost();
        $userRole = $_SESSION['user_role'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        try {
            $booking = $this->bookingService->getBookingForUser($id, $userId, $userRole);
            if ($userRole !== 'tutor') {
                throw new Exception('Brak uprawnień do dodania pracy domowej.');
            }
            $formData = $this->getSanitizedPostData();
            $this->lessonService->addHomework($id, $formData);
            $_SESSION['flash_success'] = 'Praca domowa została dodana.';
            $this->redirect('lesson/' . $id);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('lesson/' . $id . '/homework');
        }
    }

    // czyszczenie danych z POST
    private function getSanitizedPostData(): array
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $data[$key] = array_map('trim', $value);
                $data[$key] = array_map('htmlspecialchars', $data[$key]);
            } else {
                $data[$key] = trim($value);
                $data[$key] = htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8');
            }
        }
        return $data;
    }
}

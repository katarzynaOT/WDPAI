<?php

class Booking {
    public int $id;
    public int $student_id;
    public int $tutor_id;
    public string $scheduled_at;           // Data i godzina zaplanowanej lekcji
    public int $duration_minutes = 60;     // Domyślnie 60 minut
    public string $status = 'pending';     // pending, confirmed, completed, cancelled
    public ?string $notes;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $cancelled_at;
}

<?php
$bookings = $bookings ?? [];
$upcomingBookings = $upcomingBookings ?? [];
$error = $error ?? null;
?>

<div class="container bookings-container">
    <div class="bookings-header">
        <h1><i class="fas fa-calendar-check"></i> Moje rezerwacje</h1>
        <p>Zarządzaj swoimi zaplanowanymi lekcjami</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <!-- Upcoming Bookings -->
    <?php if (!empty($upcomingBookings)): ?>
        <div class="bookings-section upcoming">
            <h2><i class="fas fa-hourglass-start"></i> Najbliższe lekcje</h2>
            
            <div class="bookings-list upcoming-list">
                <?php foreach ($upcomingBookings as $booking): ?>
                    <div class="booking-item upcoming-item">
                        <div class="booking-datetime">
                            <div class="date">
                                <?= date('d.m', strtotime($booking['scheduled_date'])) ?>
                            </div>
                            <div class="time">
                                <?= date('H:i', strtotime($booking['scheduled_date'])) ?>
                            </div>
                        </div>

                        <div class="booking-details">
                            <h3>
                                <i class="fas fa-user-circle"></i>
                                <?= htmlspecialchars($booking['tutor_first_name'] ?? '') ?> 
                                <?= htmlspecialchars($booking['tutor_last_name'] ?? '') ?>
                            </h3>
                            
                            <div class="booking-meta">
                                <span class="duration">
                                    <i class="fas fa-clock"></i> <?= $booking['duration_minutes'] ?> minut
                                </span>
                                <?php if (isset($booking['tutor_rating'])): ?>
                                    <span class="rating">
                                        <i class="fas fa-star"></i> <?= number_format($booking['tutor_rating'], 1) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($booking['notes'])): ?>
                                <p class="booking-notes">
                                    <strong>Notatka:</strong> <?= htmlspecialchars(substr($booking['notes'], 0, 80)) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="booking-status">
                            <span class="status-badge status-<?= htmlspecialchars($booking['status']) ?>">
                                <?php
                                    $statusNames = [
                                        'pending' => 'Oczekująca',
                                        'confirmed' => 'Potwierdzona',
                                        'completed' => 'Ukończona',
                                        'cancelled' => 'Anulowana'
                                    ];
                                    echo $statusNames[$booking['status']] ?? ucfirst($booking['status']);
                                ?>
                            </span>
                        </div>

                        <div class="booking-actions">
                            <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
                                <form method="POST" action="/booking/cancel" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Na pewno chcesz anulować rezerwację?')">
                                        <i class="fas fa-times"></i> Anuluj
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- All Bookings -->
    <div class="bookings-section">
        <h2><i class="fas fa-list"></i> Wszystkie rezerwacje</h2>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Brak rezerwacji</h3>
                <p>Nie masz żadnych zaplanowanych lekcji</p>
                <a href="/student/tutors" class="btn btn-primary">
                    <i class="fas fa-search"></i> Wyszukaj tutora
                </a>
            </div>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-datetime">
                            <div class="date">
                                <?= date('d.m.Y', strtotime($booking['scheduled_date'])) ?>
                            </div>
                            <div class="time">
                                <?= date('H:i', strtotime($booking['scheduled_date'])) ?>
                            </div>
                        </div>

                        <div class="booking-details">
                            <h3>
                                <i class="fas fa-user-circle"></i>
                                <?= htmlspecialchars($booking['tutor_first_name'] ?? '') ?> 
                                <?= htmlspecialchars($booking['tutor_last_name'] ?? '') ?>
                            </h3>
                            
                            <div class="booking-meta">
                                <span class="duration">
                                    <i class="fas fa-clock"></i> <?= $booking['duration_minutes'] ?> minut
                                </span>
                                <?php if (isset($booking['tutor_rating'])): ?>
                                    <span class="rating">
                                        <i class="fas fa-star"></i> <?= number_format($booking['tutor_rating'], 1) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($booking['notes'])): ?>
                                <p class="booking-notes">
                                    <strong>Notatka:</strong> <?= htmlspecialchars(substr($booking['notes'], 0, 100)) ?>...
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="booking-status">
                            <span class="status-badge status-<?= htmlspecialchars($booking['status']) ?>">
                                <?php
                                    $statusNames = [
                                        'pending' => 'Oczekująca',
                                        'confirmed' => 'Potwierdzona',
                                        'completed' => 'Ukończona',
                                        'cancelled' => 'Anulowana'
                                    ];
                                    echo $statusNames[$booking['status']] ?? ucfirst($booking['status']);
                                ?>
                            </span>
                        </div>

                        <div class="booking-actions">
                            <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
                                <form method="POST" action="/booking/cancel" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Na pewno chcesz anulować rezerwację?')">
                                        <i class="fas fa-times"></i> Anuluj
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.bookings-container {
    padding: 30px 20px;
    max-width: 900px;
    margin: 0 auto;
}

.bookings-header {
    text-align: center;
    margin-bottom: 40px;
}

.bookings-header h1 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.bookings-header p {
    color: #7f8c8d;
    font-size: 16px;
}

.bookings-section {
    margin-bottom: 40px;
}

.bookings-section h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 20px;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.bookings-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.booking-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto;
    gap: 20px;
    align-items: center;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s;
}

.booking-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #3498db;
}

.booking-item.upcoming-item {
    background: #f0f8ff;
    border-color: #3498db;
    border-left: 4px solid #3498db;
}

.booking-datetime {
    text-align: center;
    background: #ecf0f1;
    border-radius: 6px;
    padding: 15px 10px;
}

.booking-datetime .date {
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
    margin-bottom: 5px;
}

.booking-datetime .time {
    font-size: 18px;
    color: #3498db;
    font-weight: bold;
}

.booking-details h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.booking-details h3 i {
    color: #3498db;
}

.booking-meta {
    display: flex;
    gap: 15px;
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 10px;
}

.booking-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.booking-meta i {
    color: #3498db;
}

.booking-notes {
    margin: 10px 0 0 0;
    color: #555;
    font-size: 13px;
    line-height: 1.5;
}

.booking-status {
    text-align: center;
}

.status-badge {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.status-completed {
    background: #cfe2ff;
    color: #084298;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.booking-actions {
    display: flex;
    gap: 10px;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 12px;
}

.btn-danger {
    background: #e74c3c;
    color: white;
    border: none;
    cursor: pointer;
}

.btn-danger:hover {
    background: #c0392b;
}

.empty-state {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border-radius: 8px;
    border: 2px dashed #ddd;
}

.empty-state i {
    font-size: 48px;
    color: #bdc3c7;
    margin-bottom: 20px;
    display: block;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    color: #7f8c8d;
    margin-bottom: 20px;
}

.upcoming {
    background: #f0f8ff;
    border: 1px solid #e3f2fd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .booking-item {
        grid-template-columns: 70px 1fr;
    }

    .booking-status,
    .booking-actions {
        grid-column: 1 / -1;
        margin-top: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bookings-section h2 {
        font-size: 18px;
    }
}
</style>

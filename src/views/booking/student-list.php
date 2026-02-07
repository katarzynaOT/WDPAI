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
                            <a href="/lesson/<?= (int)$booking['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Wyświetl szczegóły
                            </a>
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

    <!-- Bookings -->
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

                        <div class="booking-actions">                            <a href="/lesson/<?= (int)$booking['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Wyświetl szczegóły
                            </a>                            <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
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

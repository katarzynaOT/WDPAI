<?php
$booking = $booking ?? [];
?>

<div class="container lesson-detail-container">
    <div class="lesson-header">
        <h1><i class="fas fa-calendar-check"></i> Szczegóły lekcji</h1>
        <a href="/bookings" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Powrót do rezerwacji</a>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <div class="lesson-card">
        <div class="lesson-datetime">
            <div class="date">
                <i class="fas fa-calendar"></i>
                <?= date('d.m.Y', strtotime($booking['scheduled_date'])) ?>
            </div>
            <div class="time">
                <i class="fas fa-clock"></i>
                <?= date('H:i', strtotime($booking['scheduled_date'])) ?> (<?= $booking['duration_minutes'] ?> minut)
            </div>
        </div>

        <div class="lesson-participants">
            <div class="tutor-info">
                <h3><i class="fas fa-user-circle"></i> Korepetytor</h3>
                <p><strong>Imię i nazwisko:</strong> <?= htmlspecialchars($booking['tutor_first_name'] ?? '') ?> <?= htmlspecialchars($booking['tutor_last_name'] ?? '') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($booking['tutor_email'] ?? '') ?></p>
                <?php if (isset($booking['tutor_rating'])): ?>
                    <p><strong>Ocena:</strong> <i class="fas fa-star"></i> <?= number_format($booking['tutor_rating'], 1) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="lesson-details">
            <h3><i class="fas fa-info-circle"></i> Szczegóły lekcji</h3>
            <p><strong>Status:</strong>
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
            </p>
            <?php if (!empty($booking['notes'])): ?>
                <p><strong>Notatka:</strong> <?= htmlspecialchars($booking['notes']) ?></p>
            <?php endif; ?>
        </div>

        <div class="lesson-actions">
            <?php if ($booking['status'] === 'confirmed'): ?>
                <p><em>Lekcja została potwierdzona. Skontaktuj się z korepetytorem jeśli potrzebujesz zmian.</em></p>
            <?php elseif ($booking['status'] === 'pending'): ?>
                <p><em>Oczekuje na potwierdzenie przez korepetytora.</em></p>
            <?php elseif ($booking['status'] === 'completed'): ?>
                <p><em>Lekcja została ukończona.</em></p>
            <?php elseif ($booking['status'] === 'cancelled'): ?>
                <p><em>Lekcja została anulowana.</em></p>
            <?php endif; ?>

            <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
                <form method="POST" action="/booking/cancel" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Na pewno chcesz anulować tę lekcję?')">
                        <i class="fas fa-times"></i> Anuluj lekcję
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
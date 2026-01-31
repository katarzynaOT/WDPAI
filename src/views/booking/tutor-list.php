<?php
$bookings = $bookings ?? [];
$error = $error ?? null;
?>

<div class="container tutor-bookings-container">
    <div class="bookings-header">
        <h1><i class="fas fa-tasks"></i> Rezerwacje moich lekcji</h1>
        <p>Zarządzaj rezerwacjami od uczniów</p>
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

    <!-- Pending Bookings -->
    <?php 
    $pendingBookings = array_filter($bookings, fn($b) => $b['status'] === 'pending');
    if (!empty($pendingBookings)): 
    ?>
        <div class="bookings-section pending-section">
            <h2><i class="fas fa-hourglass-half"></i> Oczekujące rezerwacje (<?= count($pendingBookings) ?>)</h2>
            
            <div class="bookings-list">
                <?php foreach ($pendingBookings as $booking): ?>
                    <div class="booking-item pending">
                        <div class="booking-datetime">
                            <div class="date"><?= date('d.m.Y', strtotime($booking['scheduled_date'])) ?></div>
                            <div class="time"><?= date('H:i', strtotime($booking['scheduled_date'])) ?></div>
                        </div>

                        <div class="booking-details">
                            <h3>
                                <i class="fas fa-user-graduate"></i>
                                <?= htmlspecialchars($booking['student_first_name'] ?? '') ?> 
                                <?= htmlspecialchars($booking['student_last_name'] ?? '') ?>
                            </h3>
                            
                            <div class="booking-meta">
                                <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($booking['student_email'] ?? '') ?></span>
                                <span><i class="fas fa-clock"></i> <?= $booking['duration_minutes'] ?> minut</span>
                            </div>

                            <?php if (!empty($booking['notes'])): ?>
                                <div class="booking-notes">
                                    <strong>Notatka ucznia:</strong>
                                    <p><?= htmlspecialchars($booking['notes']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="booking-actions">
                            <form method="POST" action="/booking/confirm" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Potwierdź
                                </button>
                            </form>
                            <form method="POST" action="/booking/cancel" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Anulować tę rezerwację?')">
                                    <i class="fas fa-times"></i> Anuluj
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Confirmed Bookings -->
    <?php 
    $confirmedBookings = array_filter($bookings, fn($b) => $b['status'] === 'confirmed');
    if (!empty($confirmedBookings)): 
    ?>
        <div class="bookings-section confirmed-section">
            <h2><i class="fas fa-check-circle"></i> Potwierdzone rezerwacje (<?= count($confirmedBookings) ?>)</h2>
            
            <div class="bookings-list">
                <?php foreach ($confirmedBookings as $booking): ?>
                    <div class="booking-item confirmed">
                        <div class="booking-datetime">
                            <div class="date"><?= date('d.m.Y', strtotime($booking['scheduled_date'])) ?></div>
                            <div class="time"><?= date('H:i', strtotime($booking['scheduled_date'])) ?></div>
                        </div>

                        <div class="booking-details">
                            <h3>
                                <i class="fas fa-user-graduate"></i>
                                <?= htmlspecialchars($booking['student_first_name'] ?? '') ?> 
                                <?= htmlspecialchars($booking['student_last_name'] ?? '') ?>
                            </h3>
                            
                            <div class="booking-meta">
                                <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($booking['student_email'] ?? '') ?></span>
                                <span><i class="fas fa-clock"></i> <?= $booking['duration_minutes'] ?> minut</span>
                            </div>

                            <?php if (!empty($booking['notes'])): ?>
                                <div class="booking-notes">
                                    <strong>Notatka ucznia:</strong>
                                    <p><?= htmlspecialchars($booking['notes']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="booking-actions">
                            <form method="POST" action="/booking/cancel" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Anulować tę rezerwację?')">
                                    <i class="fas fa-times"></i> Anuluj
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Completed & Cancelled -->
    <?php 
    $otherBookings = array_filter($bookings, fn($b) => in_array($b['status'], ['completed', 'cancelled']));
    if (!empty($otherBookings)): 
    ?>
        <div class="bookings-section">
            <h2><i class="fas fa-history"></i> Historia (<?= count($otherBookings) ?>)</h2>
            
            <div class="bookings-list">
                <?php foreach ($otherBookings as $booking): ?>
                    <div class="booking-item other">
                        <div class="booking-datetime">
                            <div class="date"><?= date('d.m.Y', strtotime($booking['scheduled_date'])) ?></div>
                            <div class="time"><?= date('H:i', strtotime($booking['scheduled_date'])) ?></div>
                        </div>

                        <div class="booking-details">
                            <h3>
                                <i class="fas fa-user-graduate"></i>
                                <?= htmlspecialchars($booking['student_first_name'] ?? '') ?> 
                                <?= htmlspecialchars($booking['student_last_name'] ?? '') ?>
                            </h3>
                            
                            <div class="booking-meta">
                                <span><i class="fas fa-clock"></i> <?= $booking['duration_minutes'] ?> minut</span>
                            </div>
                        </div>

                        <div class="status-badge status-<?= htmlspecialchars($booking['status']) ?>">
                            <?php
                                $statusNames = [
                                    'completed' => 'Ukończona',
                                    'cancelled' => 'Anulowana'
                                ];
                                echo $statusNames[$booking['status']] ?? ucfirst($booking['status']);
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Brak rezerwacji</h3>
            <p>Czekaj aż uczniowie zarezerwują Twoją lekcję</p>
            <a href="/tutor/profile" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edytuj swój profil
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.tutor-bookings-container {
    padding: 30px 20px;
    max-width: 1000px;
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
    margin-bottom: 30px;
}

.bookings-section h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 20px;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.pending-section,
.confirmed-section {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
}

.pending-section {
    border-left: 4px solid #f39c12;
}

.confirmed-section {
    border-left: 4px solid #27ae60;
}

.bookings-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.booking-item {
    display: grid;
    grid-template-columns: 90px 1fr auto;
    gap: 20px;
    align-items: start;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s;
}

.booking-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.booking-item.pending {
    border-left: 4px solid #f39c12;
}

.booking-item.confirmed {
    border-left: 4px solid #27ae60;
}

.booking-item.other {
    opacity: 0.7;
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
    font-size: 14px;
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
    font-size: 13px;
    margin-bottom: 10px;
    flex-wrap: wrap;
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
    margin-top: 15px;
    padding: 12px;
    background: #f0f0f0;
    border-radius: 6px;
    border-left: 3px solid #3498db;
}

.booking-notes strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 13px;
}

.booking-notes p {
    margin: 0;
    color: #555;
    font-size: 13px;
    line-height: 1.5;
}

.booking-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
}

.btn-success {
    background: #27ae60;
    color: white;
}

.btn-success:hover {
    background: #229954;
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 12px;
}

.status-badge {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
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

.btn-primary {
    background: #3498db;
    color: white;
    padding: 12px 30px;
}

.btn-primary:hover {
    background: #2980b9;
}

@media (max-width: 768px) {
    .booking-item {
        grid-template-columns: 1fr;
    }

    .booking-actions {
        grid-column: 1 / -1;
        margin-top: 10px;
    }
}
</style>

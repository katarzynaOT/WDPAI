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
                            <a href="/lesson/<?= (int)$booking['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Wyświetl szczegóły
                            </a>
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
                            <a href="/lesson/<?= (int)$booking['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Wyświetl szczegóły
                            </a>
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

                        <div class="booking-actions">
                            <a href="/lesson/<?= (int)$booking['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Wyświetl szczegóły
                            </a>
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
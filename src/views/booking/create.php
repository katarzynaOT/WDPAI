<?php
$tutor = $tutor ?? [];
$error = $error ?? null;
$posted_data = $posted_data ?? [];
?>

<div class="container booking-container">
    <div class="booking-header">
        <h1><i class="fas fa-calendar-plus"></i> Zarezerwuj lekcję</h1>
        <p>Umów się na lekcję z wybranym tutorem</p>
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

    <div class="booking-content">
        <!-- Tutor Info -->
        <div class="tutor-info-panel">
            <div class="tutor-card-compact">
                <h3><?= htmlspecialchars($tutor['first_name'] ?? '') ?> <?= htmlspecialchars($tutor['last_name'] ?? '') ?></h3>
                
                <?php if (isset($tutor['rating'])): ?>
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= round($tutor['rating']) ? 'filled' : '' ?>"></i>
                        <?php endfor; ?>
                        <span><?= number_format($tutor['rating'] ?? 0, 1) ?>/5.0</span>
                    </div>
                <?php endif; ?>

                <p class="tutor-bio"><?= htmlspecialchars(substr($tutor['bio'] ?? '', 0, 100)) ?>...</p>

                <?php if (!empty($tutor['subjects'])): ?>
                    <div class="subjects-compact">
                        <strong>Przedmioty:</strong>
                        <div class="subject-list">
                            <?php foreach (array_slice($tutor['subjects'], 0, 3) as $subject): ?>
                                <span class="subject-tag-small">
                                    <?= htmlspecialchars($subject['subject_name'] ?? '') ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="booking-form-panel">
            <form method="POST" action="/booking/store" class="booking-form">
                <input type="hidden" name="tutor_id" value="<?= (int)($tutor['profile_id'] ?? 0) ?>">

                <div class="form-group">
                    <label for="scheduled_at">
                        <i class="fas fa-calendar"></i> Data i godzina lekcji *
                    </label>
                    <input type="datetime-local" 
                           id="scheduled_at" 
                           name="scheduled_at" 
                           required
                           min="<?= date('Y-m-d\TH:i') ?>"
                           value="<?= htmlspecialchars($posted_data['scheduled_at'] ?? '') ?>"
                           class="form-input">
                    <small class="form-text">Wybierz przyszłą datę i godzinę</small>
                </div>

                <div class="form-group">
                    <label for="duration_minutes">
                        <i class="fas fa-clock"></i> Czas trwania (minuty) *
                    </label>
                    <select id="duration_minutes" name="duration_minutes" required class="form-select">
                        <option value="">-- Wybierz czas --</option>
                        <option value="30" <?= ($posted_data['duration_minutes'] ?? '') === '30' ? 'selected' : '' ?>>30 minut</option>
                        <option value="45" <?= ($posted_data['duration_minutes'] ?? '') === '45' ? 'selected' : '' ?>>45 minut</option>
                        <option value="60" <?= ($posted_data['duration_minutes'] ?? 60) === '60' ? 'selected' : '' ?>>60 minut (1 godzina)</option>
                        <option value="90" <?= ($posted_data['duration_minutes'] ?? '') === '90' ? 'selected' : '' ?>>90 minut (1,5 godziny)</option>
                        <option value="120" <?= ($posted_data['duration_minutes'] ?? '') === '120' ? 'selected' : '' ?>>120 minut (2 godziny)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">
                        <i class="fas fa-sticky-note"></i> Wiadomość dla tutora
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="4"
                              placeholder="Napisz czego chciałbyś się nauczyć, jakie masz problemy, czy masz jakieś specjalne życzenia..."
                              class="form-textarea"><?= htmlspecialchars($posted_data['notes'] ?? '') ?></textarea>
                    <small class="form-text">Opcjonalnie - pomóż tutorowi przygotować się do lekcji</small>
                </div>

                <div class="booking-info-box">
                    <h4><i class="fas fa-info-circle"></i> Informacja:</h4>
                    <ul>
                        <li>Po wysłaniu rezerwacji tutor będzie musiał ją potwierdzić</li>
                        <li>Otrzymasz powiadomienie gdy tutor potwierdzi twoją rezerwację</li>
                        <li>Możesz anulować rezerwację do momentu potwierdzenia</li>
                    </ul>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Wyślij rezerwację
                    </button>
                    <a href="/student/tutors" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Powrót
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.booking-container {
    padding: 30px 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.booking-header {
    text-align: center;
    margin-bottom: 40px;
}

.booking-header h1 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.booking-header p {
    color: #7f8c8d;
    font-size: 16px;
}

.booking-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    align-items: start;
}

.tutor-info-panel {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.tutor-card-compact {
    background: white;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e0e0e0;
}

.tutor-card-compact h3 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 20px;
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    font-weight: 600;
    color: #2c3e50;
}

.rating-stars .fa-star {
    font-size: 16px;
    color: #ddd;
}

.rating-stars .fa-star.filled {
    color: #f39c12;
}

.tutor-bio {
    color: #555;
    line-height: 1.6;
    margin: 15px 0;
    font-size: 14px;
}

.subjects-compact {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.subjects-compact strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 14px;
}

.subject-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.subject-tag-small {
    display: inline-block;
    padding: 4px 10px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

.booking-form-panel {
    background: white;
    border-radius: 12px;
    padding: 30px;
    border: 1px solid #e0e0e0;
}

.booking-form-panel h2 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 20px;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group label i {
    color: #3498db;
    margin-right: 5px;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-text {
    display: block;
    font-size: 12px;
    color: #7f8c8d;
    margin-top: 5px;
}

.booking-info-box {
    background: #e8f5e9;
    border-left: 4px solid #4caf50;
    padding: 15px;
    border-radius: 6px;
    margin: 25px 0;
}

.booking-info-box h4 {
    margin: 0 0 10px 0;
    color: #2e7d32;
    font-size: 14px;
}

.booking-info-box ul {
    margin: 0;
    padding-left: 20px;
    color: #555;
}

.booking-info-box li {
    margin: 8px 0;
    font-size: 13px;
    line-height: 1.5;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #bdc3c7;
    color: white;
}

.btn-secondary:hover {
    background: #95a5a6;
}

@media (max-width: 768px) {
    .booking-content {
        grid-template-columns: 1fr;
    }

    .tutor-info-panel {
        position: static;
        order: 2;
    }

    .booking-form-panel {
        order: 1;
    }
}
</style>

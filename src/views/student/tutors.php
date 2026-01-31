<?php
$tutors = $tutors ?? [];
$subjects = $subjects ?? [];
$selectedSubject = $selectedSubject ?? null;
$selectedRating = $selectedRating ?? null;
$error = $error ?? null;
?>

<div class="container tutors-container">
    <div class="tutors-header">
        <h1><i class="fas fa-search"></i> Wyszukaj korepetytora</h1>
        <p>Znajdź idealnego korepetytora do swoich potrzeb</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="tutors-filters">
        <form method="GET" action="/student/tutors" class="filter-form">
            <div class="filter-group">
                <label for="subject_id">
                    <i class="fas fa-book"></i> Przedmiot
                </label>
                <select id="subject_id" name="subject_id" class="form-select">
                    <option value="">-- Wszystkie przedmioty --</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>" 
                                <?= $selectedSubject == $subject['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="min_rating">
                    <i class="fas fa-star"></i> Minimum ocena
                </label>
                <select id="min_rating" name="min_rating" class="form-select">
                    <option value="">-- Wszystkie oceny --</option>
                    <option value="4.5" <?= $selectedRating == '4.5' ? 'selected' : '' ?>>4.5+ ⭐</option>
                    <option value="4.0" <?= $selectedRating == '4.0' ? 'selected' : '' ?>>4.0+ ⭐</option>
                    <option value="3.5" <?= $selectedRating == '3.5' ? 'selected' : '' ?>>3.5+ ⭐</option>
                    <option value="3.0" <?= $selectedRating == '3.0' ? 'selected' : '' ?>>3.0+ ⭐</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary filter-btn">
                <i class="fas fa-filter"></i> Filtruj
            </button>

            <?php if ($selectedSubject || $selectedRating): ?>
                <a href="/student/tutors" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Wyczyść filtry
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tutors List -->
    <div class="tutors-list">
        <?php if (empty($tutors)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Brak korepetytorów</h3>
                <p>Niestety nie znaleźliśmy korepetytorów spełniających Twoje kryteria.</p>
                <a href="/student/tutors" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Wyczyść filtry
                </a>
            </div>
        <?php else: ?>
            <div class="tutors-grid">
                <?php foreach ($tutors as $tutor): ?>
                    <div class="tutor-card">
                        <div class="tutor-header">
                            <div class="tutor-name">
                                <h3><?= htmlspecialchars($tutor['first_name'] ?? '') ?> 
                                    <?= htmlspecialchars($tutor['last_name'] ?? '') ?></h3>
                                <span class="tutor-email">
                                    <i class="fas fa-envelope"></i> 
                                    <?= htmlspecialchars($tutor['email'] ?? '') ?>
                                </span>
                            </div>

                            <div class="tutor-rating">
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= round($tutor['rating'] ?? 0) ? 'filled' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-value">
                                    <?= number_format($tutor['rating'] ?? 0, 1) ?>/5.0
                                </span>
                                <span class="reviews-count">
                                    (<?= $tutor['total_reviews'] ?? 0 ?> opinii)
                                </span>
                            </div>
                        </div>

                        <div class="tutor-bio">
                            <p><?= htmlspecialchars(substr($tutor['bio'] ?? '', 0, 150)) ?>...</p>
                        </div>

                        <div class="tutor-details">
                            <?php if (!empty($tutor['experience_years'])): ?>
                                <div class="detail-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?= (int)$tutor['experience_years'] ?> lat doświadczenia</span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($tutor['hourly_rate'])): ?>
                                <div class="detail-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span><?= number_format($tutor['hourly_rate'], 2) ?> zł/h</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tutor-subjects">
                            <?php if (!empty($tutor['subjects'])): ?>
                                <div class="subjects-list">
                                    <?php 
                                    $subjectCount = 0;
                                    foreach ($tutor['subjects'] as $subject): 
                                        if ($subjectCount < 3):
                                    ?>
                                        <span class="subject-tag">
                                            <?= htmlspecialchars($subject['subject_name'] ?? '') ?>
                                        </span>
                                    <?php 
                                        $subjectCount++;
                                        endif;
                                    endforeach; 
                                    ?>
                                    <?php if (count($tutor['subjects']) > 3): ?>
                                        <span class="subject-tag more">
                                            +<?= count($tutor['subjects']) - 3 ?> więcej
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tutor-actions">
                            <a href="/tutor/<?= (int)$tutor['profile_id'] ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> Profil
                            </a>
                            <a href="/booking/new?tutor=<?= (int)$tutor['profile_id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar"></i> Zarezerwuj
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="tutors-count">
                <p><strong><?= count($tutors) ?></strong> korepetytorów spełnia Twoje kryteria</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.tutors-container {
    padding: 30px 20px;
}

.tutors-header {
    text-align: center;
    margin-bottom: 40px;
}

.tutors-header h1 {
    font-size: 32px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.tutors-header p {
    color: #7f8c8d;
    font-size: 16px;
}

.tutors-filters {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-select {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
}

.form-select:hover {
    border-color: #3498db;
}

.form-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.filter-btn {
    padding: 12px 30px;
}

.tutors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.tutor-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.tutor-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: #3498db;
    transform: translateY(-2px);
}

.tutor-header {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.tutor-name h3 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-size: 18px;
}

.tutor-email {
    font-size: 13px;
    color: #7f8c8d;
}

.tutor-rating {
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stars {
    display: flex;
    gap: 3px;
}

.stars .fa-star {
    color: #ddd;
    font-size: 14px;
}

.stars .fa-star.filled {
    color: #f39c12;
}

.rating-value {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.reviews-count {
    font-size: 12px;
    color: #7f8c8d;
}

.tutor-bio {
    padding: 15px 20px;
    flex-grow: 1;
}

.tutor-bio p {
    margin: 0;
    color: #555;
    font-size: 14px;
    line-height: 1.6;
}

.tutor-details {
    padding: 0 20px;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 14px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #555;
}

.detail-item i {
    color: #3498db;
    width: 16px;
}

.tutor-subjects {
    padding: 15px 20px;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}

.subjects-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.subject-tag {
    display: inline-block;
    padding: 5px 12px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.subject-tag.more {
    background: #f0f0f0;
    color: #666;
}

.tutor-actions {
    padding: 15px 20px;
    display: flex;
    gap: 10px;
}

.btn-sm {
    padding: 10px 15px;
    font-size: 14px;
}

.btn-outline {
    background: white;
    color: #3498db;
    border: 1px solid #3498db;
}

.btn-outline:hover {
    background: #e3f2fd;
}

.empty-state {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border-radius: 12px;
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

.tutors-count {
    text-align: center;
    padding: 20px;
    color: #7f8c8d;
    font-size: 14px;
}

@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr;
    }

    .tutors-grid {
        grid-template-columns: 1fr;
    }

    .tutor-actions {
        flex-direction: column;
    }

    .btn-sm {
        width: 100%;
        text-align: center;
    }
}
</style>

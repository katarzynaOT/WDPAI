<?php
$profile = $profile ?? [];
?>

<div class="container public-profile">
    <div class="profile-header public">
        <div class="tutor-main-info">
            <div class="tutor-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="tutor-details">
                <h1><?= htmlspecialchars($_SESSION['user_first_name'] ?? '') ?> <?= htmlspecialchars($_SESSION['user_last_name'] ?? '') ?></h1>
                <p class="tutor-title">Korepetytor</p>
                
                <?php if (isset($profile['rating'])): ?>
                    <div class="rating-badge">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= round($profile['rating']) ? 'filled' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-value"><?= number_format($profile['rating'] ?? 0, 1) ?></span>
                        <span class="reviews-count">(<?= $profile['total_reviews'] ?? 0 ?> opinii)</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="/booking/new?tutor=<?= (int)($profile['profile_id'] ?? $profile['id'] ?? 0) ?>" class="btn btn-primary btn-book">
                <i class="fas fa-calendar-plus"></i> Umów lekcję
            </a>
            <button class="btn btn-secondary" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Wróć
            </button>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-section">
            <h3><i class="fas fa-user-edit"></i> O mnie</h3>
            <div class="section-content">
                <?= nl2br(htmlspecialchars($profile['bio'] ?? 'Brak opisu')) ?>
            </div>
        </div>

        <?php if (!empty($profile['education'])): ?>
            <div class="profile-section">
                <h3><i class="fas fa-university"></i> Wykształcenie</h3>
                <div class="section-content">
                    <?= nl2br(htmlspecialchars($profile['education'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-section">
            <h3><i class="fas fa-briefcase"></i> Doświadczenie</h3>
            <div class="section-content">
                <div class="experience-item">
                    <i class="fas fa-clock"></i>
                    <span><?= $profile['experience_years'] ?? 0 ?> lat doświadczenia</span>
                </div>
            </div>
        </div>

        <?php if (!empty($profile['description'])): ?>
            <div class="profile-section">
                <h3><i class="fas fa-info-circle"></i> Dodatkowe informacje</h3>
                <div class="section-content">
                    <?= nl2br(htmlspecialchars($profile['description'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($profile['subjects'])): ?>
            <div class="profile-section">
                <h3><i class="fas fa-book"></i> Przedmioty</h3>
                <div class="subjects-tags">
                    <?php foreach ($profile['subjects'] as $subject): ?>
                        <?php if (isset($subject['subject_name'])): ?>
                            <span class="subject-tag">
                                <?= htmlspecialchars($subject['subject_name']) ?>
                                <span class="expertise-level"><?= htmlspecialchars($subject['expertise_level'] ?? '') ?></span>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Miejsce na opinie uczniów -->
        <div class="profile-section">
            <h3><i class="fas fa-comments"></i> Opinie uczniów</h3>
            <div class="reviews-placeholder">
                <p>Tutaj będą wyświetlane opinie uczniów o tym korepetytorze.</p>
                <!-- TODO: Dodaj system opinii -->
            </div>
        </div>
    </div>
</div>

<style>
.public-profile {
    max-width: 1000px;
}

.profile-header.public {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 30px;
    padding-bottom: 30px;
    border-bottom: 2px solid #eee;
}

.tutor-main-info {
    display: flex;
    gap: 25px;
    align-items: center;
}

.tutor-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2c3e50);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 48px;
}

.tutor-details h1 {
    font-size: 32px;
    margin-bottom: 5px;
    color: #2c3e50;
}

.tutor-title {
    color: #7f8c8d;
    font-size: 18px;
    margin-bottom: 15px;
}

.rating-badge {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stars .fa-star {
    color: #ddd;
    font-size: 16px;
}

.stars .fa-star.filled {
    color: #f39c12;
}

.rating-value {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}

.reviews-count {
    color: #7f8c8d;
}

.profile-actions {
    display: flex;
    gap: 15px;
    align-self: center;
}

.btn-book {
    padding: 12px 30px;
    font-size: 16px;
}

.profile-content {
    margin-top: 40px;
}

.profile-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.profile-section:last-child {
    border-bottom: none;
}

.profile-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
}

.section-content {
    line-height: 1.8;
    color: #555;
    font-size: 16px;
}

.experience-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}
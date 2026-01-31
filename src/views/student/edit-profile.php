<?php
$profile = $profile ?? [];
$error = $error ?? null;
?>

<div class="container">
    <div class="profile-header">
        <h1><i class="fas fa-graduation-cap"></i> Profil studenta</h1>
        <p>Skonfiguruj swoje preferencje nauki</p>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/student/profile/update" class="profile-form">
        <div class="form-single">
            <div class="form-group">
                <label for="level">
                    <i class="fas fa-school"></i> Poziom nauki *
                </label>
                <select id="level" name="level" required class="form-select">
                    <option value="">-- Wybierz poziom --</option>
                    <option value="podstawówka" <?= ($profile['level'] ?? '') === 'podstawówka' ? 'selected' : '' ?>>Szkoła podstawowa</option>
                    <option value="liceum" <?= ($profile['level'] ?? '') === 'liceum' ? 'selected' : '' ?>>Liceum</option>
                    <option value="technikum" <?= ($profile['level'] ?? '') === 'technikum' ? 'selected' : '' ?>>Technikum</option>
                    <option value="studia" <?= ($profile['level'] ?? '') === 'studia' ? 'selected' : '' ?>>Studia</option>
                    <option value="inny" <?= ($profile['level'] ?? '') === 'inny' ? 'selected' : '' ?>>Inny</option>
                </select>
                <small class="form-text">Pomoże to korepetytorom w dopasowaniu materiałów</small>
            </div>

            <div class="form-group">
                <label for="learning_goals">
                    <i class="fas fa-bullseye"></i> Cele naukowe
                </label>
                <textarea id="learning_goals" 
                          name="learning_goals" 
                          rows="5"
                          placeholder="Np. Przygotowanie do matury, poprawa ocen, nauka do egzaminu..."
                          class="form-textarea"><?= htmlspecialchars($profile['learning_goals'] ?? '') ?></textarea>
                <small class="form-text">Opisz czego chcesz się nauczyć (opcjonalnie)</small>
            </div>

            <div class="student-info-box">
                <h4><i class="fas fa-info-circle"></i> Informacje dla korepetytorów:</h4>
                <p>Twoje cele naukowe pomogą korepetytorom przygotować odpowiednie materiały i plan nauki.</p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Zapisz profil
            </button>
            <a href="/dashboard" class="btn btn-secondary">
                <i class="fas fa-times"></i> Anuluj
            </a>
        </div>
    </form>
</div>

<style>
.form-select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    background: white;
    cursor: pointer;
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    font-family: inherit;
    resize: vertical;
    min-height: 120px;
}

.student-info-box {
    margin-top: 30px;
    padding: 20px;
    background: #e8f4fc;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.student-info-box h4 {
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.student-info-box p {
    color: #555;
    line-height: 1.6;
}
</style>
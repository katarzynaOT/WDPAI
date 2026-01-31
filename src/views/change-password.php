<div class="container">
    <div class="profile-header">
        <h1><i class="fas fa-key"></i> Zmiana hasła</h1>
        <p>Zabezpiecz swoje konto silnym hasłem</p>
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

    <form method="POST" action="/profile/password/update" class="profile-form">
        <div class="form-single">
            <div class="form-group">
                <label for="current_password">
                    <i class="fas fa-lock"></i> Obecne hasło *
                </label>
                <input type="password" 
                       id="current_password" 
                       name="current_password" 
                       required
                       placeholder="Wprowadź obecne hasło">
            </div>

            <div class="form-group">
                <label for="new_password">
                    <i class="fas fa-lock"></i> Nowe hasło *
                </label>
                <input type="password" 
                       id="new_password" 
                       name="new_password" 
                       required
                       placeholder="Minimum 8 znaków">
                <small class="form-text">Hasło musi zawierać co najmniej 8 znaków, w tym cyfrę i wielką literę</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-lock"></i> Potwierdź nowe hasło *
                </label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       required
                       placeholder="Powtórz nowe hasło">
            </div>

            <div class="password-requirements">
                <h4>Wymagania bezpiecznego hasła:</h4>
                <ul>
                    <li><i class="fas fa-check"></i> Minimum 8 znaków</li>
                    <li><i class="fas fa-check"></i> Co najmniej jedna wielka litera</li>
                    <li><i class="fas fa-check"></i> Co najmniej jedna cyfra</li>
                    <li><i class="fas fa-check"></i> Nie używaj prostych haseł jak "123456"</li>
                </ul>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Zmień hasło
            </button>
            <a href="/profile/basic" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Wróć do profilu
            </a>
        </div>
    </form>
</div>

<style>
.form-single {
    max-width: 500px;
    margin: 0 auto;
}

.password-requirements {
    margin-top: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.password-requirements h4 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.password-requirements ul {
    list-style: none;
    padding: 0;
}

.password-requirements li {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #555;
}

.password-requirements .fa-check {
    color: #27ae60;
}
</style>
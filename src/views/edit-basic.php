<?php
$user = $user ?? null;
$error = $error ?? null;
?>

<div class="container">
    <div class="profile-header">
        <h1><i class="fas fa-user-edit"></i> Edycja danych podstawowych</h1>
        <p>Aktualizuj swoje dane kontaktowe</p>
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

    <form method="POST" action="/profile/basic/update" class="profile-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="first_name">
                    <i class="fas fa-signature"></i> Imię *
                </label>
                <input type="text" 
                       id="first_name" 
                       name="first_name" 
                       value="<?= htmlspecialchars($user->firstName ?? '') ?>" 
                       required
                       placeholder="Twoje imię">
            </div>

            <div class="form-group">
                <label for="last_name">
                    <i class="fas fa-signature"></i> Nazwisko *
                </label>
                <input type="text" 
                       id="last_name" 
                       name="last_name" 
                       value="<?= htmlspecialchars($user->lastName ?? '') ?>" 
                       required
                       placeholder="Twoje nazwisko">
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email *
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars($user->email ?? '') ?>" 
                       required
                       placeholder="twój@email.com">
                <small class="form-text">Na ten adres będą przychodzić powiadomienia</small>
            </div>

            <div class="form-group">
                <label for="phone">
                    <i class="fas fa-phone"></i> Telefon
                </label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="<?= htmlspecialchars($user->phone ?? '') ?>" 
                       placeholder="+48 123 456 789">
                <small class="form-text">Opcjonalnie, do kontaktu z korepetytorem/uczniem</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Zapisz zmiany
            </button>
            <a href="/dashboard" class="btn btn-secondary">
                <i class="fas fa-times"></i> Anuluj
            </a>
        </div>
    </form>

    <div class="profile-links">
        <a href="/profile/password" class="btn-link">
            <i class="fas fa-key"></i> Zmień hasło
        </a>
        
        <?php if ($_SESSION['user_role'] === 'student'): ?>
            <a href="/student/profile" class="btn-link">
                <i class="fas fa-graduation-cap"></i> Edytuj profil studenta
            </a>
        <?php elseif ($_SESSION['user_role'] === 'tutor'): ?>
            <a href="/tutor/profile" class="btn-link">
                <i class="fas fa-chalkboard-teacher"></i> Edytuj profil korepetytora
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.profile-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.profile-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.profile-header p {
    color: #7f8c8d;
    font-size: 16px;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    transition: border 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-text {
    display: block;
    margin-top: 5px;
    color: #7f8c8d;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 40px;
    padding-top: 25px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #ecf0f1;
    color: #2c3e50;
}

.btn-secondary:hover {
    background: #d5dbdb;
}

.profile-links {
    margin-top: 40px;
    padding-top: 25px;
    border-top: 1px solid #eee;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.btn-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    padding: 10px;
    border-radius: 5px;
    transition: background 0.3s;
}

.btn-link:hover {
    background: #f8f9fa;
}
</style>
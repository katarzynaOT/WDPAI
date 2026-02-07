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

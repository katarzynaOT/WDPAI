<h2>Rejestracja Ucznia</h2>

<?php if (isset($error)): ?>
    <div style="color: red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="/register/student">
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
    </div>
    <div>
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="confirm_password">Powtórz hasło:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <div>
        <label for="first_name">Imię:</label>
        <input type="text" id="first_name" name="first_name" required value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>">
    </div>
    <div>
        <label for="last_name">Nazwisko:</label>
        <input type="text" id="last_name" name="last_name" required value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>">
    </div>
    <div>
        <label for="phone">Telefon:</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
    </div>
    <div>
        <label for="level">Poziom:</label>
        <select id="level" name="level" required>
            <option value="">-- Wybierz poziom --</option>
            <option value="beginner" <?= ($formData['level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Początkujący</option>
            <option value="intermediate" <?= ($formData['level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Średniozaawansowany</option>
            <option value="advanced" <?= ($formData['level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Zaawansowany</option>
        </select>
    </div>
    <div>
        <label for="learning_goals">Cele nauki:</label>
        <textarea id="learning_goals" name="learning_goals"><?= htmlspecialchars($formData['learning_goals'] ?? '') ?></textarea>
    </div>
    <button type="submit">Zarejestruj się</button>
</form>
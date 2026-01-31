<h2>Rejestracja Korepetytora</h2>

<?php if (isset($error)): ?>
    <div style="color: red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="/register/tutor">
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
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" required><?= htmlspecialchars($formData['bio'] ?? '') ?></textarea>
    </div>
    <div>
        <label for="education">Wykształcenie:</label>
        <input type="text" id="education" name="education" value="<?= htmlspecialchars($formData['education'] ?? '') ?>">
    </div>
    <div>
        <label for="experience_years">Lata doświadczenia:</label>
        <input type="number" id="experience_years" name="experience_years" min="0" value="<?= htmlspecialchars($formData['experience_years'] ?? '') ?>">
    </div>
    <div>
        <label for="description">Opis:</label>
        <textarea id="description" name="description"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
    </div>
    <!-- TODO: Dodać wybór przedmiotów -->
    <button type="submit">Zarejestruj się</button>
</form>
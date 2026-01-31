<h2>Logowanie</h2>

<?php if (isset($error)): ?>
    <div style="color: red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="/login">
    <div>
        <label>Email:</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Hasło:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Zaloguj</button>
</form>
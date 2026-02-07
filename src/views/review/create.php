<div class="container">
    <h2>Dodaj recenzję dla korepetytora</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-error"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <form method="POST" action="/tutor/<?= (int)$tutor_id ?>/review/store">
        <label>Ocena:
            <select name="rating" required>
                <option value="">Wybierz...</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </label><br>
        <label>Komentarz:
            <textarea name="content" rows="4" required><?= htmlspecialchars($formData['content'] ?? '') ?></textarea>
        </label><br>
        <button type="submit" class="btn btn-primary">Dodaj recenzję</button>
    </form>
</div>

<?php $booking = $booking ?? []; ?>
<div class="container">
    <h1>Dodaj pracę domową</h1>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error"> <?= htmlspecialchars($_SESSION['flash_error']) ?> </div>
    <?php endif; ?>
    <form method="POST" action="/lesson/<?= (int)$booking['id'] ?>/homework/store">
        <label>Tytuł:
            <input type="text" name="title" required>
        </label><br>
        <label>Opis:
            <textarea name="description" rows="4" required></textarea>
        </label><br>
        <label>Termin oddania:
            <input type="date" name="deadline">
        </label><br>
        <button type="submit" class="btn btn-primary">Dodaj pracę domową</button>
    </form>
</div>

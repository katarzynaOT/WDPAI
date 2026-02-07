<?php $booking = $booking ?? []; ?>
<div class="container">
    <h1>Edycja lekcji</h1>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error"> <?= htmlspecialchars($_SESSION['flash_error']) ?> </div>
    <?php endif; ?>
    <form method="POST" action="/lesson/<?= (int)$booking['id'] ?>/update">
        <label>Cena:
            <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($booking['price'] ?? '') ?>" required>
        </label><br>
        <label>Link do spotkania:
            <input type="text" name="meeting_url" value="<?= htmlspecialchars($booking['meeting_url'] ?? '') ?>">
        </label><br>
        <label>Status płatności:
            <select name="payment_status">
                <option value="pending" <?= ($booking['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Oczekująca</option>
                <option value="paid" <?= ($booking['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Opłacona</option>
                <option value="cancelled" <?= ($booking['payment_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Anulowana</option>
            </select>
        </label><br>
        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
    </form>
</div>

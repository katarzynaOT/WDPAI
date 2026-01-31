<h2>Rejestracja</h2>

<p>Wybierz swoją rolę, aby przejść do formularza rejestracji.</p>

<form id="roleForm">
    <div>
        <label for="role">Rola:</label>
        <select id="role" name="role" required>
            <option value="">-- Wybierz rolę --</option>
            <option value="student">Uczeń</option>
            <option value="tutor">Korepetytor</option>
        </select>
    </div>
    <button type="button" onclick="redirectToRegister()">Przejdź do rejestracji</button>
</form>

<script>
function redirectToRegister() {
    const role = document.getElementById('role').value;
    if (role === 'student') {
        window.location.href = '/register/student';
    } else if (role === 'tutor') {
        window.location.href = '/register/tutor';
    } else {
        alert('Wybierz rolę');
    }
}
</script>

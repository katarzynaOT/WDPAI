document.addEventListener('DOMContentLoaded', function() {
    // Pobierz dane studenta i wypełnij formularz
    fetch('/student/profile/data')
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById('firstName').value = (data.name || '').split(' ')[0] || '';
                document.getElementById('lastName').value = (data.name || '').split(' ')[1] || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone').value = data.phone || '';
                // Ustaw poziom edukacji w select
                if (data.level) {
                    document.getElementById('education_level').value = data.level;
                }
                document.getElementById('learning_goals').value = data.learning_goals || '';
            }
        });

    // Obsługa wysyłki formularza AJAX
    document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/student/profile/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit-profile-message').textContent = data.success ? 'Profil zaktualizowany!' : (data.error || 'Błąd zapisu');
            // Odśwież dane na stronie po zapisie
            if (data.success) {
                setTimeout(() => window.location.href = '/student/profile', 1000);
            }
        })
        .catch(() => {
            document.getElementById('edit-profile-message').textContent = 'Błąd połączenia.';
        });
    });
});

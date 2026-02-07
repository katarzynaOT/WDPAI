document.addEventListener('DOMContentLoaded', function() {
    fetch('/student/profile/data')
        .then(response => {
            return response.json();
        })
        .then(data => {
            if (data.error) {
                var profileInfo = document.getElementById('profile-info');
                if (profileInfo) {
                    profileInfo.innerHTML = '<p>Błąd: ' + data.error + '</p>';
                }
                return;
            }
            var profileInfo = document.getElementById('profile-info');
            if (profileInfo) {
                document.getElementById('student-name').textContent = data.name || '-';
                document.getElementById('student-email').textContent = data.email || '-';
                document.getElementById('student-phone').textContent = data.phone || '-';
                document.getElementById('student-class').textContent = data.class || '-';
                document.getElementById('student-goals').textContent = data.learning_goals || '-';
            }
        })
        .catch(() => {
            var profileInfo = document.getElementById('profile-info');
            if (profileInfo) {
                profileInfo.innerHTML = '<p>Błąd ładowania danych profilu.</p>';
            }
        });
});

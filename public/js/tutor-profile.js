document.addEventListener('DOMContentLoaded', function() {
    fetch('/tutor/profile/data')
        .then(response => response.json())
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
                document.getElementById('tutor-name').textContent = data.name || '-';
                document.getElementById('tutor-email').textContent = data.email || '-';
                document.getElementById('tutor-phone').textContent = data.phone || '-';
                document.getElementById('tutor-subjects').textContent = data.subjects || '-';
                document.getElementById('tutor-description').textContent = data.description || '-';
            }
        })
        .catch(() => {
            var profileInfo = document.getElementById('profile-info');
            if (profileInfo) {
                profileInfo.innerHTML = '<p>Błąd ładowania danych profilu.</p>';
            }
        });
});

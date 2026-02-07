document.addEventListener('DOMContentLoaded', function() {
    const profileLink = document.getElementById('profile-link');
    const lessonsLink = document.getElementById('lessons-link');
    const logoutLink = document.getElementById('logout-link');
    if (profileLink) {
        profileLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/student/profile';
        });
    }
    if (lessonsLink) {
        lessonsLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/lessons';
        });
    }
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/logout';
        });
    }
});
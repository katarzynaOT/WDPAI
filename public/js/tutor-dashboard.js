document.addEventListener('DOMContentLoaded', function() {
    const profileLinkTutor = document.getElementById('profile-link-tutor');
    const lessonsLinkTutor = document.getElementById('lessons-link-tutor');
    const logoutLinkTutor = document.getElementById('logout-link-tutor');
    if (profileLinkTutor) {
        profileLinkTutor.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/tutor/profile';
        });
    }
    if (lessonsLinkTutor) {
        lessonsLinkTutor.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/lessons';
        });
    }
    if (logoutLinkTutor) {
        logoutLinkTutor.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/logout';
        });
    }
});
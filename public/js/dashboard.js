document.addEventListener('DOMContentLoaded', function() {
    // Student dashboard
    const profileLink = document.getElementById('profile-link');
    const lessonsLink = document.getElementById('lessons-link');
    const logoutLink = document.getElementById('logout-link');
    if (profileLink) {
        profileLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = ' ';
        });
    }
    if (lessonsLink) {
        lessonsLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = ' ';
        });
    }
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/logout';
        });
    }

    // Tutor dashboard
    const profileLinkTutor = document.getElementById('profile-link-tutor');
    const lessonsLinkTutor = document.getElementById('lessons-link-tutor');
    const logoutLinkTutor = document.getElementById('logout-link-tutor');
    if (profileLinkTutor) {
        profileLinkTutor.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = ' ';
        });
    }
    if (lessonsLinkTutor) {
        lessonsLinkTutor.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = ' ';
        });
    }
    if (logoutLinkTutor) {
        logoutLinkTutor.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/logout';
        });
    }
});

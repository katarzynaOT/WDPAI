document.addEventListener('DOMContentLoaded', function() {
    // Najpierw pobierz listę dostępnych przedmiotów, potem dane tutora
    fetch('/subjects/list')
        .then(response => response.json())
        .then(subjects => {
            const select = document.getElementById('subjects');
            select.innerHTML = '';
            for (const subject of subjects) {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                select.appendChild(option);
            }
            // Teraz pobierz dane tutora i ustaw wartości
            fetch('/tutor/profile/data')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('bio').value = data.bio || '';
                        document.getElementById('education').value = data.education || '';
                        document.getElementById('experience_years').value = data.experience_years || '';
                        document.getElementById('description').value = data.description || '';
                        // Ustaw przedmioty
                        if (Array.isArray(data.subjects)) {
                            for (const subj of data.subjects) {
                                const option = document.querySelector(`#subjects option[value='${subj.id}']`);
                                if (option) option.selected = true;
                            }
                        }
                    }
                });
        });

    // Obsługa wysyłki formularza AJAX
    document.getElementById('edit-tutor-profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        // Zbierz wybrane przedmioty
        const selectedSubjects = Array.from(document.getElementById('subjects').selectedOptions).map(opt => opt.value);
        formData.delete('subjects[]');
        for (const subjId of selectedSubjects) {
            formData.append('subjects[]', subjId);
        }
        fetch('/tutor/profile/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit-tutor-profile-message').textContent = data.success ? 'Profil zaktualizowany!' : (data.error || 'Błąd zapisu');
            if (data.success) {
                setTimeout(() => window.location.href = '/tutor/profile', 1000);
            }
        })
        .catch(() => {
            document.getElementById('edit-tutor-profile-message').textContent = 'Błąd połączenia.';
        });
    });
});

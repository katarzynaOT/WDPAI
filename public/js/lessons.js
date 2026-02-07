document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#lessons-table tbody');
    if (tableBody) {
        fetch('/lessons/data')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    tableBody.innerHTML = `<tr><td colspan="4">Błąd: ${data.error}</td></tr>`;
                    return;
                }
                if (!Array.isArray(data.lessons)) {
                    tableBody.innerHTML = `<tr><td colspan="4">Brak danych o lekcjach</td></tr>`;
                    return;
                }
                data.lessons.forEach(lesson => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${lesson.title || '-'}</td>
                        <td>${lesson.date || '-'}</td>
                        <td>${lesson.status || '-'}</td>
                        <td><button class="view-details-btn" data-id="${lesson.id}">Szczegóły</button></td>
                    `;
                    tableBody.appendChild(tr);
                });
            })
            .catch(() => {
                tableBody.innerHTML = `<tr><td colspan="4">Błąd ładowania lekcji</td></tr>`;
            });

        tableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('view-details-btn')) {
                const lessonId = e.target.getAttribute('data-id');
                window.location.href = `/lessons/${lessonId}`;
            }
        });
    }
});

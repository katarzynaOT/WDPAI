document.addEventListener('DOMContentLoaded', function() {
    // 1. Poczekaj aż cały DOM się załaduje
    const studentFields = document.getElementById('student-fields');
    const tutorFields = document.getElementById('tutor-fields');
    const roleRadios = document.querySelectorAll('input[name="role"]');
    
    // 2. Sprawdź czy elementy istnieją
    if (!studentFields || !tutorFields || roleRadios.length === 0) {
        console.error('Required elements not found');
        return;
    }
    
    // 3. Funkcja pokazująca/ukrywająca pola
    function toggleFields() {
        const selectedRole = document.querySelector('input[name="role"]:checked');
        
        if (!selectedRole) {
            // Jeśli nic nie wybrano - ukryj oba
            studentFields.style.display = 'none';
            tutorFields.style.display = 'none';
            studentFields.querySelectorAll('input').forEach(input => input.required = false);
            tutorFields.querySelectorAll('input').forEach(input => input.required = false);
            return;
        }
        
        if (selectedRole.value === 'student') {
            // Pokaż student, ukryj tutor
            studentFields.style.display = 'block';
            tutorFields.style.display = 'none';
            
            // Wymagaj pól studenta, nie wymagaj pól tutora
            studentFields.querySelectorAll('input').forEach(input => input.required = true);
            tutorFields.querySelectorAll('input').forEach(input => input.required = false);
            
        } else if (selectedRole.value === 'tutor') {
            // Pokaż tutor, ukryj student
            studentFields.style.display = 'none';
            tutorFields.style.display = 'block';
            
            tutorFields.querySelectorAll('input').forEach(input => input.required = true);
            studentFields.querySelectorAll('input').forEach(input => input.required = false);
        }
    }
    
    // 4. Ukryj oba na start
    studentFields.style.display = 'none';
    tutorFields.style.display = 'none';
    
    // 5. Sprawdź czy któryś radio jest już zaznaczony (np. po odświeżeniu)
    const initiallyChecked = document.querySelector('input[name="role"]:checked');
    if (initiallyChecked) {
        toggleFields(); // Pokaż odpowiednie pola
    }
    
    // 6. Dodaj event listener do każdego radio
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleFields);
    });
});
<?php
$profile = $profile ?? [];
$subjects = $subjects ?? [];
$error = $error ?? null;
?>

<div class="container">
    <div class="profile-header">
        <h1><i class="fas fa-chalkboard-teacher"></i> Profil korepetytora</h1>
        <p>Stwórz atrakcyjny profil dla potencjalnych uczniów</p>
        <?php if (isset($profile['rating'])): ?>
            <div class="tutor-rating">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?= $i <= round($profile['rating']) ? 'filled' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-text">Ocena: <?= number_format($profile['rating'] ?? 0, 1) ?>/5.0 
                (<?= $profile['total_reviews'] ?? 0 ?> opinii)</span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/tutor/profile/update" class="profile-form" id="tutorProfileForm">
        <div class="form-single">
            <div class="form-group">
                <label for="bio">
                    <i class="fas fa-user-edit"></i> Opis (bio) *
                    <span class="char-counter" id="bio-counter">0/50</span>
                </label>
                <textarea id="bio" 
                          name="bio" 
                          rows="6"
                          required
                          minlength="50"
                          placeholder="Opisz siebie, swoje podejście do nauczania, doświadczenie..."
                          class="form-textarea bio-textarea"
                          oninput="updateCounter('bio-counter', this)"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                <small class="form-text">Minimum 50 znaków. To pierwsze co zobaczą uczniowie!</small>
            </div>

            <div class="form-group">
                <label for="education">
                    <i class="fas fa-university"></i> Wykształcenie
                </label>
                <textarea id="education" 
                          name="education" 
                          rows="4"
                          placeholder="Twoje wykształcenie, ukończone szkoły, certyfikaty..."
                          class="form-textarea"><?= htmlspecialchars($profile['education'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="experience_years">
                    <i class="fas fa-briefcase"></i> Doświadczenie (lata)
                </label>
                <input type="number" 
                       id="experience_years" 
                       name="experience_years" 
                       min="0" 
                       max="50"
                       value="<?= htmlspecialchars($profile['experience_years'] ?? 0) ?>"
                       class="form-input">
                <small class="form-text">Liczba lat doświadczenia w nauczaniu</small>
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-info-circle"></i> Dodatkowy opis
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          placeholder="Specjalizacje, metody nauczania, dodatkowe informacje..."
                          class="form-textarea"><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-book"></i> Przedmioty *
                </label>
                <div class="subjects-container">
                    <div class="subjects-grid">
                        <?php foreach ($subjects as $subject): ?>
                            <?php 
                            $isSelected = false;
                            $subjectExpertise = 'intermediate';
                            
                            if (isset($profile['subjects']) && is_array($profile['subjects'])) {
                                foreach ($profile['subjects'] as $profileSubject) {
                                    if (isset($profileSubject['subject_id']) && $profileSubject['subject_id'] == $subject['id']) {
                                        $isSelected = true;
                                        $subjectExpertise = $profileSubject['expertise_level'] ?? 'intermediate';
                                        break;
                                    }
                                }
                            }
                            ?>
                            <div class="subject-item">
                                <label class="subject-checkbox">
                                    <input type="checkbox" 
                                           name="subjects[]" 
                                           value="<?= $subject['id'] ?>"
                                           <?= $isSelected ? 'checked' : '' ?>
                                           class="subject-input">
                                    <span class="subject-name"><?= htmlspecialchars($subject['name']) ?></span>
                                    <span class="subject-category">(<?= htmlspecialchars($subject['category']) ?>)</span>
                                </label>
                                
                                <select name="expertise_level[<?= $subject['id'] ?>]" 
                                        class="expertise-select" 
                                        <?= !$isSelected ? 'disabled' : '' ?>>
                                    <option value="beginner" <?= $subjectExpertise === 'beginner' ? 'selected' : '' ?>>Początkujący</option>
                                    <option value="intermediate" <?= $subjectExpertise === 'intermediate' ? 'selected' : '' ?>>Średniozaawansowany</option>
                                    <option value="expert" <?= $subjectExpertise === 'expert' ? 'selected' : '' ?>>Ekspert</option>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="form-text">Wybierz przynajmniej jeden przedmiot. Możesz też określić poziom zaawansowania.</small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Zapisz profil
            </button>
            <a href="/dashboard" class="btn btn-secondary">
                <i class="fas fa-times"></i> Anuluj
            </a>
            <?php if (isset($profile['id'])): ?>
                <a href="/tutor/<?= $profile['id'] ?>" class="btn btn-outline" target="_blank">
                    <i class="fas fa-eye"></i> Podgląd profilu
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<style>
.tutor-rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-top: 15px;
}

.stars {
    display: flex;
    gap: 5px;
}

.stars .fa-star {
    color: #ddd;
    font-size: 18px;
}

.stars .fa-star.filled {
    color: #f39c12;
}

.rating-text {
    color: #7f8c8d;
    font-weight: 500;
}

.char-counter {
    float: right;
    font-size: 14px;
    color: #7f8c8d;
}

.bio-textarea {
    min-height: 150px;
}

.subjects-container {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 20px;
    background: #f9f9f9;
}

.subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
    margin-bottom: 15px;
}

.subject-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    background: white;
    border-radius: 5px;
    border: 1px solid #eee;
}

.subject-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    flex-grow: 1;
}

.subject-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.subject-name {
    font-weight: 500;
    color: #2c3e50;
}

.subject-category {
    font-size: 12px;
    color: #7f8c8d;
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 10px;
}

.expertise-select {
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

.expertise-select:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-outline {
    background: white;
    color: #3498db;
    border: 1px solid #3498db;
}

.btn-outline:hover {
    background: #3498db;
    color: white;
}
</style>

<script>
function updateCounter(counterId, textarea) {
    const counter = document.getElementById(counterId);
    const count = textarea.value.length;
    counter.textContent = count + '/50';
    
    if (count < 50) {
        counter.style.color = '#e74c3c';
    } else {
        counter.style.color = '#27ae60';
    }
}

// Inicjalizacja licznika
document.addEventListener('DOMContentLoaded', function() {
    const bioTextarea = document.getElementById('bio');
    if (bioTextarea) {
        updateCounter('bio-counter', bioTextarea);
    }
    
    // Włącz/wyłącz select poziomu gdy checkbox się zmienia
    document.querySelectorAll('.subject-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const subjectId = this.value;
            const expertiseSelect = document.querySelector(`select[name="expertise_level[${subjectId}]"]`);
            if (expertiseSelect) {
                expertiseSelect.disabled = !this.checked;
            }
        });
    });
    
    // Walidacja przed wysłaniem
    document.getElementById('tutorProfileForm').addEventListener('submit', function(e) {
        const bio = document.getElementById('bio').value;
        const checkedSubjects = document.querySelectorAll('.subject-input:checked').length;
        
        if (bio.length < 50) {
            e.preventDefault();
            alert('Opis musi mieć co najmniej 50 znaków!');
            return;
        }
        
        if (checkedSubjects === 0) {
            e.preventDefault();
            alert('Wybierz przynajmniej jeden przedmiot!');
            return;
        }
    });
});
</script>
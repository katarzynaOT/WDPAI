DROP TABLE IF EXISTS 
    homework, 
    files, 
    payments, 
    reviews, 
    lessons, 
    tutor_subjects, 
    subjects, 
    student_profiles, 
    tutor_profiles, 
    users CASCADE;

DROP TYPE IF EXISTS 
    user_role, 
    lesson_status, 
    payment_status, 
    file_type CASCADE;


--CREATE TYPE proba AS ENUM ('aa');
CREATE TYPE user_role AS ENUM ('student', 'tutor', 'admin');
CREATE TYPE lesson_status AS ENUM ('scheduled', 'completed', 'cancelled', 'pending');
CREATE TYPE payment_status AS ENUM ('pending', 'paid', 'failed', 'refunded');
CREATE TYPE file_type AS ENUM ('material', 'homework', 'plan', 'other');

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    role user_role NOT NULL DEFAULT 'student'::user_role,
    hourly_rate NUMERIC(8,2),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
	last_login TIMESTAMP
);

CREATE TABLE tutor_profiles (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	bio TEXT,
    education TEXT,
	experience_years INTEGER DEFAULT 0,
    description TEXT,
    rating DECIMAL(3,2) DEFAULT 0.0,
    total_reviews INTEGER DEFAULT 0
);

CREATE TABLE student_profiles (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    level VARCHAR(50),
	learning_goals TEXT
);

CREATE TABLE subjects (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(100),
    description TEXT
);

CREATE TABLE tutor_subjects (
    tutor_id INT NOT NULL REFERENCES tutor_profiles(id) ON DELETE CASCADE,
    subject_id INT NOT NULL REFERENCES subjects(id) ON DELETE CASCADE,
    expertise_level VARCHAR(100), -- 'beginner', 'intermediate', 'expert'
    years_experience INTEGER DEFAULT 0,
    PRIMARY KEY (tutor_id, subject_id)
);

CREATE TABLE lessons (
    id SERIAL PRIMARY KEY,
    tutor_id INT NOT NULL REFERENCES tutor_profiles(id) ON DELETE CASCADE,
    student_id INT NOT NULL REFERENCES student_profiles(id) ON DELETE CASCADE,
    subject_id INT REFERENCES subjects(id),
    title VARCHAR(200) NOT NULL,
    description TEXT,
    scheduled_date TIMESTAMP NOT NULL,
    duration_minutes INTEGER NOT NULL DEFAULT 60,
    status lesson_status DEFAULT 'scheduled',
    meeting_url TEXT, -- link do Zoom/Teams/Google Meet
    price NUMERIC(8,2) NOT NULL,
    payment_status payment_status DEFAULT 'pending',
    notes TEXT, -- notatki po lekcji
    cancelled_at TIMESTAMP,
    cancelled_by INT REFERENCES users(id),
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);


CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    tutor_id INT NOT NULL REFERENCES tutor_profiles(id) ON DELETE CASCADE,
    student_id INT NOT NULL REFERENCES student_profiles(id) ON DELETE CASCADE,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(student_id, tutor_id)
);

CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    lesson_id INT REFERENCES lessons(id),
    student_id INT NOT NULL REFERENCES student_profiles(id),
    tutor_id INT NOT NULL REFERENCES tutor_profiles(id),
    amount NUMERIC(8,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'PLN',
    status payment_status DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255) UNIQUE,
    paid_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE files (
    id SERIAL PRIMARY KEY,
    uploader_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    lesson_id INT REFERENCES lessons(id) ON DELETE SET NULL,
    filename VARCHAR(255) NOT NULL,
    file_path TEXT NOT NULL,
    file_type file_type NOT NULL,
    file_size INTEGER, -- w bajtach
    mime_type VARCHAR(100),
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    downloads_count INTEGER DEFAULT 0,
    uploaded_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE homework (
    id SERIAL PRIMARY KEY,
    lesson_id INT REFERENCES lessons(id) ON DELETE CASCADE,
    tutor_id INT NOT NULL REFERENCES tutor_profiles(id),
    student_id INT NOT NULL REFERENCES student_profiles(id),
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    assigned_date TIMESTAMP NOT NULL DEFAULT NOW(),
    due_date TIMESTAMP NOT NULL,
    is_submitted BOOLEAN DEFAULT FALSE,
    submitted_at TIMESTAMP,
    grade INTEGER CHECK (grade >= 1 AND grade <= 5) NULL,
    tutor_feedback TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

INSERT INTO subjects (name, category) VALUES
    ('Matematyka', 'Ścisłe'),
    ('Fizyka', 'Ścisłe'),
    ('Chemia', 'Ścisłe'),
    ('Język polski', 'Humanistyczne'),
    ('Historia', 'Humanistyczne'),
    ('Angielski', 'Języki obce'),
    ('Niemiecki', 'Języki obce'),
    ('Informatyka', 'Ścisłe'),
    ('Biologia', 'Przyrodnicze'),
    ('Geografia', 'Przyrodnicze')
ON CONFLICT (name) DO NOTHING;



CREATE OR REPLACE FUNCTION get_account_age(user_id_param INT)
RETURNS INTERVAL AS 
$$
DECLARE
    account_created TIMESTAMPTZ;
BEGIN
    SELECT created_at INTO account_created
    FROM users WHERE id = user_id_param;
    
    RETURN NOW() - account_created;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION calculate_tutor_rating(tutor_id_param INT)
RETURNS DECIMAL(3,2) AS 
$$
DECLARE
    avg_rating DECIMAL(3,2);
BEGIN
    SELECT COALESCE(AVG(r.rating), 0.0) INTO avg_rating
    FROM reviews r
    WHERE r.tutor_id = tutor_id_param;
    
    RETURN ROUND(avg_rating, 2);
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION update_tutor_rating_on_review()
RETURNS TRIGGER AS 
$$
BEGIN
    UPDATE tutor_profiles 
    SET 
        rating = calculate_tutor_rating(NEW.tutor_id),
        total_reviews = (
            SELECT COUNT(*) 
            FROM reviews 
            WHERE tutor_id = NEW.tutor_id
        ),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.tutor_id;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE VIEW v_tutors_full AS
SELECT 
    u.id as user_id,
    u.email,
    u.first_name,
    u.last_name,
    u.phone,
    u.hourly_rate as default_hourly_rate,
    u.created_at as account_created,
    u.last_login,
    tp.id as tutor_profile_id,
    tp.bio,
    tp.education,
    tp.experience_years,
    tp.description,
    tp.rating,
    tp.total_reviews,
    -- lista przedmiotów
    ARRAY(
        SELECT json_build_object(
            'id', s.id,
            'name', s.name,
            'category', s.category,
            'expertise_level', ts.expertise_level
        )
        FROM tutor_subjects ts
        JOIN subjects s ON ts.subject_id = s.id
        WHERE ts.tutor_id = tp.id
    ) as subjects
FROM users u
JOIN tutor_profiles tp ON u.id = tp.user_id
WHERE u.role = 'tutor';

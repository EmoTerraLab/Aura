-- =============================================================================
-- Aura — seeds.sql
-- Datos iniciales basados en el documento de Análisis
-- =============================================================================

-- Contraseñas hasheadas. Para pruebas, asumimos que 'aura2026' y 'password'
-- están representadas aquí (se recomienda reemplazar los hashes por bcrypt reales 
-- en la capa de la app o simplemente usar hashes pre-generados)
-- Usaremos un texto plano o un hash de 'password' o 'aura2026' para facilidad de test,
-- idealmente el registro o el Database::seed de la app se encargará de hashear si esto fuera texto plano, 
-- pero como SQLite no tiene bcrypt, pondremos un hash de 'aura2026' por defecto, 
-- o insertamos las contraseñas en claro si la app las hashea al hacer login (no recomendado).
-- Asumiremos que el login verifica con password_verify(), así que insertamos el hash bcrypt de 'aura2026'
-- password_hash('aura2026', PASSWORD_DEFAULT) -> $2y$10$wO9Pq8BfXG/...

-- 1. Usuarios
INSERT INTO users (id, name, email, role, password) VALUES
(1, 'Administrador de Aura', 'admin@aura.local', 'admin', '$2y$12$qOm.Fh8uFXrU/XdF5lGa3u3z205gvYjZimzdN4ibqsZjms/8cXqpS'), -- aura2026
(2, 'Orientadora Lucía', 'orientador@aura.test', 'orientador', '$2y$12$qOm.Fh8uFXrU/XdF5lGa3u3z205gvYjZimzdN4ibqsZjms/8cXqpS'),
(3, 'Profesor Tutor', 'profesor@aura.test', 'profesor', '$2y$12$qOm.Fh8uFXrU/XdF5lGa3u3z205gvYjZimzdN4ibqsZjms/8cXqpS'),
(4, 'Juan Alumno', 'alumno@aura.test', 'alumno', NULL),
(5, 'Marina Alumna', 'marina@aura.test', 'alumno', NULL);

-- 2. Aulas
INSERT INTO classrooms (id, name, tutor_id) VALUES
(1, '3º ESO A', 3);

-- 3. Perfiles de estudiantes (necesario para la relación de reports)
INSERT INTO student_profiles (id, user_id, classroom_id, anonymized_id) VALUES
(1, 4, 1, 'anon-uuid-juan'),
(2, 5, 1, 'anon-uuid-marina');

-- 4. Reportes
INSERT INTO reports (id, student_id, classroom_id, content, target, urgency_level, is_anonymous, status) VALUES
(1, 1, 1, 'Me siento un poco solo en el recreo últimamente.', 'yo_mismo', 'low', 0, 'new'),
(2, 2, 1, 'He visto que alguien ha pintado la mesa de mi compañera con insultos.', 'otro', 'high', 1, 'new');


<?php
/**
 * Migración: Datos iniciales de demostración
 */
class Migration_2024_01_11_000000_seed_demo_data
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $hash = password_hash('aura2026', PASSWORD_DEFAULT);
        
        // 1. Usuarios
        $this->db->exec("
            INSERT OR IGNORE INTO users (id, name, email, role, password) VALUES
            (1, 'Administrador de Aura', 'admin@aura.local', 'admin', '$hash'),
            (2, 'Orientadora Lucía', 'orientador@aura.test', 'orientador', '$hash'),
            (3, 'Profesor Tutor', 'profesor@aura.test', 'profesor', '$hash'),
            (4, 'Juan Alumno', 'alumno@aura.test', 'alumno', NULL),
            (5, 'Marina Alumna', 'marina@aura.test', 'alumno', NULL);
        ");

        // 2. Aulas
        $this->db->exec("
            INSERT OR IGNORE INTO classrooms (id, name, tutor_id) VALUES
            (1, '3º ESO A', 3);
        ");

        // 3. Perfiles de estudiantes
        $this->db->exec("
            INSERT OR IGNORE INTO student_profiles (id, user_id, classroom_id, anonymized_id) VALUES
            (1, 4, 1, 'anon-uuid-juan'),
            (2, 5, 1, 'anon-uuid-marina');
        ");

        // 4. Reportes
        $this->db->exec("
            INSERT OR IGNORE INTO reports (id, student_id, classroom_id, content, target, urgency_level, is_anonymous, status) VALUES
            (1, 1, 1, 'Me siento un poco solo en el recreo últimamente.', 'yo_mismo', 'low', 0, 'new'),
            (2, 2, 1, 'He visto que alguien ha pintado la mesa de mi compañera con insultos.', 'otro', 'high', 1, 'new');
        ");
    }

    public function down(): void
    {
        // No revertimos los seeds en down para no romper nada en producción
    }
}

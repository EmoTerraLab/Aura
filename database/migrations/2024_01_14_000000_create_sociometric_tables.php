<?php
/**
 * Migración: Sistema de Sociogramas (CESC)
 */
class Migration_2024_01_14_000000_create_sociometric_tables
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        // Tabla de Encuestas (Cabecera)
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS sociometric_surveys (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                classroom_id INTEGER NOT NULL,
                created_by INTEGER NOT NULL,
                status VARCHAR(20) DEFAULT 'active', -- active, closed
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (classroom_id) REFERENCES classrooms(id),
                FOREIGN KEY (created_by) REFERENCES users(id)
            )
        ");

        // Tabla de Respuestas (Nominaciones individuales)
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS sociometric_responses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                survey_id INTEGER NOT NULL,
                student_id INTEGER NOT NULL, -- Quien nomina
                nominated_student_id INTEGER, -- A quien nomina (null si nadie)
                question_type VARCHAR(50) NOT NULL, -- positive_affinity, negative_affinity, victimization_target
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (survey_id) REFERENCES sociometric_surveys(id) ON DELETE CASCADE,
                FOREIGN KEY (student_id) REFERENCES users(id),
                FOREIGN KEY (nominated_student_id) REFERENCES users(id)
            )
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS sociometric_responses;");
        $this->db->exec("DROP TABLE IF EXISTS sociometric_surveys;");
    }
}

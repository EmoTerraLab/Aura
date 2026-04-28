<?php
/**
 * Migración: Seguimiento y Cierre de Protocolo
 */
class Migration_2024_01_13_000000_add_followups_and_closure
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        // Tabla de seguimientos
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS protocol_followups (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                protocol_case_id INTEGER NOT NULL,
                target_type VARCHAR(50) NOT NULL, -- victima, agressor, familia, grup_classe
                session_date DATETIME NOT NULL,
                notes TEXT,
                created_by INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (protocol_case_id) REFERENCES protocol_cases(id) ON DELETE CASCADE,
                FOREIGN KEY (created_by) REFERENCES users(id)
            )
        ");

        // Checklist de cierre en protocol_cases
        $this->db->exec("ALTER TABLE protocol_cases ADD COLUMN closure_checks JSON DEFAULT '{\"eradicated\":false,\"reparation\":false,\"students_confirm\":false,\"teachers_valorate\":false}'");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS protocol_followups;");
    }
}

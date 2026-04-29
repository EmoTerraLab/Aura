<?php
/**
 * Migración: Tabla de Custodia de Evidencias Legales
 */
class Migration_2026_04_28_125000_create_protocol_evidence_table
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS protocol_evidence (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                protocol_case_id INTEGER NOT NULL,
                filename VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                mime_type VARCHAR(100),
                uploaded_by INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (protocol_case_id) REFERENCES protocol_cases(id) ON DELETE CASCADE,
                FOREIGN KEY (uploaded_by) REFERENCES users(id)
            )
        ");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_evidence_case ON protocol_evidence(protocol_case_id)");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS protocol_evidence");
    }
}

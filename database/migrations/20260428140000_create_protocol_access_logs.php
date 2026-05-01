<?php
/**
 * Migración: Registro de Accesos a Casos Sensibles (Audit Trail)
 */
class Migration_20260428140000_create_protocol_access_logs
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS protocol_access_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                protocol_case_id INTEGER, -- Permite NULL para accesos generales
                user_id INTEGER NOT NULL,
                accessed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(45),
                user_agent TEXT,
                FOREIGN KEY (protocol_case_id) REFERENCES protocol_cases(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS protocol_access_logs;");
    }
}

<?php
/**
 * Migración: Registro de Accesos a Casos Sensibles (Audit Trail)
 */
class Migration_2026_04_28_140000_create_protocol_access_logs
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS protocol_access_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                protocol_case_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                ip_address TEXT,
                user_agent TEXT,
                accessed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (protocol_case_id) REFERENCES protocol_cases(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_access_case ON protocol_access_logs(protocol_case_id)");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS protocol_access_logs");
    }
}

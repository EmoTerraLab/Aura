<?php

class Migration_2026_05_11_180000_create_audit_logs_table
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                action VARCHAR(255) NOT NULL,
                entity_type VARCHAR(255) NULL,
                entity_id INTEGER NULL,
                ip_address VARCHAR(45) NULL,
                details TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");
        
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_audit_logs_user ON audit_logs(user_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_audit_logs_action ON audit_logs(action)");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS audit_logs");
    }
}

<?php
/**
 * Migration: Create Password Resets Table
 */
class Migration_2024_01_09_000000_create_password_resets_table
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email VARCHAR(150) NOT NULL,
                token VARCHAR(64) NOT NULL UNIQUE,
                used INTEGER DEFAULT 0,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_token ON password_resets(token);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_email ON password_resets(email);");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS password_resets;");
    }
}

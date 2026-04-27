<?php
/**
 * Migración: Tabla códigos recuperación TOTP
 */
class Migration_2024_01_06_000000_create_totp_recovery_codes_table
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS totp_recovery_codes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                code VARCHAR(255) NOT NULL,
                used INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }
    public function down(): void {}
}

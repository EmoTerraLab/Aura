<?php
/**
 * Migración: Tabla de configuración
 */
class Migration_2024_01_02_000000_create_settings_table
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key VARCHAR(100) NOT NULL UNIQUE,
                value TEXT NOT NULL,
                type VARCHAR(20) DEFAULT 'text',
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    public function down(): void {}
}

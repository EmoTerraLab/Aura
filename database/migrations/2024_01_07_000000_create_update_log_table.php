<?php
/**
 * Migración: Tabla de log de actualizaciones
 */
class Migration_2024_01_07_000000_create_update_log_table
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS update_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                version_from VARCHAR(20),
                version_to VARCHAR(20),
                status VARCHAR(20) DEFAULT 'pending',
                migrations_run INTEGER DEFAULT 0,
                error_message TEXT,
                backup_file VARCHAR(200),
                started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                finished_at DATETIME
            )
        ");
    }
    public function down(): void {}
}

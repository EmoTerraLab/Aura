<?php
declare(strict_types=1);

class Migration_20260502000000_create_rate_limits_table {
    private \PDO $db;
    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS rate_limits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip TEXT NOT NULL UNIQUE,
                attempts INTEGER DEFAULT 1,
                last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $this->db->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_rate_limits_ip ON rate_limits(ip)");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS rate_limits");
    }
}

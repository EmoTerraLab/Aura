<?php
/**
 * Migración: Tabla WebAuthn
 */
class Migration_2024_01_04_000000_create_webauthn_credentials_table
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS webauthn_credentials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                credential_id TEXT NOT NULL UNIQUE,
                public_key TEXT NOT NULL,
                sign_count INTEGER DEFAULT 0,
                device_name VARCHAR(100) DEFAULT 'Mi dispositivo',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                last_used_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        $stmt = $this->db->query("PRAGMA table_info(users)");
        $columns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');
        if (!in_array('webauthn_handle', $columns)) {
            $this->db->exec("ALTER TABLE users ADD COLUMN webauthn_handle VARCHAR(64) DEFAULT NULL");
        }
    }
    public function down(): void {}
}

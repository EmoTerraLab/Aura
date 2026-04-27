<?php
/**
 * Migración: Campos TOTP en users
 */
class Migration_2024_01_05_000000_add_totp_fields_to_users
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $stmt = $this->db->query("PRAGMA table_info(users)");
        $columns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');
        
        $fields = [
            'totp_secret' => 'VARCHAR(64) DEFAULT NULL',
            'totp_enabled' => 'INTEGER DEFAULT 0',
            'totp_verified_at' => 'DATETIME DEFAULT NULL'
        ];

        foreach ($fields as $field => $def) {
            if (!in_array($field, $columns)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN $field $def");
            }
        }
    }
    public function down(): void {}
}

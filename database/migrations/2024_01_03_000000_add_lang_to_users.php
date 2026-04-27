<?php
/**
 * Migración: Añadir columna lang a users
 */
class Migration_2024_01_03_000000_add_lang_to_users
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $stmt = $this->db->query("PRAGMA table_info(users)");
        $columns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');
        if (!in_array('lang', $columns)) {
            $this->db->exec("ALTER TABLE users ADD COLUMN lang VARCHAR(5) DEFAULT NULL");
        }
    }
    public function down(): void {}
}

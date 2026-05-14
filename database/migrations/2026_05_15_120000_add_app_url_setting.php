<?php
/**
 * Migración: Agregar setting app_url
 */
class Migration_2026_05_15_120000_add_app_url_setting
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO settings (key, value, type) VALUES (?, ?, ?)");
        $stmt->execute(['app_url', '', 'text']);
    }
    public function down(): void {}
}

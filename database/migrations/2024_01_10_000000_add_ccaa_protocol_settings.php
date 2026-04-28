<?php
/**
 * Migración: Añadir ajustes para protocolos CCAA
 */
class Migration_2024_01_10_000000_add_ccaa_protocol_settings
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $stmt = $this->db->prepare('INSERT OR IGNORE INTO settings (key, value, type) VALUES (?, ?, ?)');
        $stmt->execute(['ccaa_code', '', 'select']);
        $stmt->execute(['ccaa_protocol_active', '1', 'boolean']);
        $stmt->execute(['ccaa_show_to_students', '1', 'boolean']);
    }

    public function down(): void
    {
        $this->db->exec("DELETE FROM settings WHERE key IN ('ccaa_code', 'ccaa_protocol_active', 'ccaa_show_to_students')");
    }
}

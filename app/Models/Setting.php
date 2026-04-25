<?php
namespace App\Models;

use App\Core\Database;

/**
 * Setting - Modelo para gestionar configuraciones globales del sistema.
 */
class Setting
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function get(string $key): ?string
    {
        $stmt = $this->db->prepare('SELECT value FROM settings WHERE key = ? LIMIT 1');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : null;
    }

    public function set(string $key, string $value): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (key, value) VALUES (?, ?)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = CURRENT_TIMESTAMP'
        );
        return $stmt->execute([$key, $value]);
    }
}

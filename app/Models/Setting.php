<?php
namespace App\Models;

use App\Core\Database;

/**
 * Setting - Modelo para gestionar configuraciones globales del sistema con caché en memoria.
 */
class Setting
{
    private $db;
    private static array $cache = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function get(string $key, string $default = ''): string
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        $stmt = $this->db->prepare('SELECT value FROM settings WHERE key = ? LIMIT 1');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $value = $row ? (string)$row['value'] : $default;
        self::$cache[$key] = $value;
        return $value;
    }

    public function set(string $key, string $value): bool
    {
        self::$cache[$key] = $value;
        $stmt = $this->db->prepare(
            'INSERT INTO settings (key, value) VALUES (?, ?)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = CURRENT_TIMESTAMP'
        );
        return $stmt->execute([$key, $value]);
    }

    public function setMany(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (!$this->set($key, $value)) return false;
        }
        return true;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT key, value, type FROM settings ORDER BY key');
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = ['value' => $row['value'], 'type' => $row['type']];
            self::$cache[$row['key']] = $row['value']; // Popular caché
        }
        return $result;
    }
}

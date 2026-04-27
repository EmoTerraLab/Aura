<?php
namespace App\Core;

/**
 * MaintenanceMode — Controla el modo mantenimiento de la aplicación
 *
 * Usa un archivo flag en storage/maintenance/
 * para activar/desactivar el modo sin tocar la BD
 * (la BD podría estar en proceso de migración)
 */
class MaintenanceMode
{
    private static function getFlagFile(): string {
        return __DIR__ . '/../../storage/maintenance/.maintenance';
    }

    private static function getDataFile(): string {
        return __DIR__ . '/../../storage/maintenance/data.json';
    }

    /**
     * Activa el modo mantenimiento
     */
    public static function enable(string $message = '', string $estimatedEnd = ''): void
    {
        $flagFile = self::getFlagFile();
        $dataFile = self::getDataFile();

        $dir = dirname($flagFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        touch($flagFile);

        $data = [
            'enabled_at'    => date('Y-m-d H:i:s'),
            'message'       => $message ?: 'El sistema está en mantenimiento. Volveremos pronto.',
            'message_en'    => 'The system is under maintenance. We will be back soon.',
            'estimated_end' => $estimatedEnd,
            'enabled_by'    => (defined('CLI') && CLI) ? 'cli' : ((\App\Core\Auth::check()) ? \App\Core\Auth::user()['email'] : 'system')
        ];

        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Desactiva el modo mantenimiento
     */
    public static function disable(): void
    {
        if (file_exists(self::getFlagFile())) unlink(self::getFlagFile());
        if (file_exists(self::getDataFile())) unlink(self::getDataFile());
    }

    /**
     * Comprueba si el modo mantenimiento está activo
     */
    public static function isActive(): bool
    {
        return file_exists(self::getFlagFile());
    }

    /**
     * Devuelve los datos del modo mantenimiento actual
     */
    public static function getData(): array
    {
        $dataFile = self::getDataFile();
        if (!file_exists($dataFile)) {
            return ['message' => 'Sistema en mantenimiento', 'enabled_at' => ''];
        }
        return json_decode(file_get_contents($dataFile), true) ?? [];
    }
}

<?php
namespace App\Core;

/**
 * Config - Acceso rápido a settings del sistema desde cualquier parte
 * Uso en vistas: <?= Config::get('school_name') ?>
 */
class Config
{
    private static ?\App\Models\Setting $model = null;

    public static function init(\App\Models\Setting $model): void
    {
        self::$model = $model;
        // Precargar caché si es posible
        self::$model->getAll();
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$model ? self::$model->get($key, $default) : $default;
    }
}

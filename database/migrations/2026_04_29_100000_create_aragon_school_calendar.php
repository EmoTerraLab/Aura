<?php
declare(strict_types=1);

/**
 * Migración para crear la tabla de calendario escolar de Aragón.
 * Necesaria para el cálculo de plazos en días lectivos.
 */
class Migration_2026_04_29_100000_create_aragon_school_calendar
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        // Tabla para marcar días lectivos y no lectivos
        $this->db->exec("CREATE TABLE IF NOT EXISTS aragon_school_calendar (
            calendar_date DATE PRIMARY KEY,
            is_school_day INTEGER NOT NULL DEFAULT 1,
            description VARCHAR(100) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Insertar algunos días de ejemplo para que el sistema no use el fallback si hay datos
        // En producción se cargará un CSV o se gestionará desde el panel de admin.
        $today = date('Y-m-d');
        $this->db->exec("INSERT OR IGNORE INTO aragon_school_calendar (calendar_date, is_school_day, description) VALUES ('$today', 1, 'Hoy')");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS aragon_school_calendar");
    }
}

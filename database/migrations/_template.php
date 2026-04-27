<?php
/**
 * Migración: [DESCRIPCIÓN AQUÍ]
 * Versión app: 1.6.2-stable
 * Fecha: 2026-04-27
 */
class Migration_YYYY_MM_DD_HHMMSS_nombre_descriptivo
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Aplicar migración (cambios hacia adelante)
     * SIEMPRE usar helpers para ser idempotente
     */
    public function up(): void
    {
        // Ejemplo: crear tabla
        // $this->createTableIfNotExists("CREATE TABLE IF NOT EXISTS mi_tabla (...)");

        // Ejemplo: añadir columna
        // $this->addColumnIfNotExists('mi_tabla', 'mi_columna', 'VARCHAR(100) DEFAULT NULL');

        // Ejemplo: insertar datos iniciales
        // $stmt = $this->db->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)');
        // $stmt->execute(['mi_clave', 'mi_valor']);
    }

    /**
     * Revertir migración
     * Implementar siempre que sea posible
     */
    public function down(): void
    {
        // NOTA: SQLite no soporta DROP COLUMN en versiones < 3.35
        // Para revertir cambios de columna: reconstruir la tabla
        // Para DROP TABLE: $this->db->exec('DROP TABLE IF EXISTS mi_tabla');
    }

    // ---- Helpers de idempotencia ----

    protected function addColumnIfNotExists(string $table, string $column, string $definition): void
    {
        $stmt = $this->db->query("PRAGMA table_info($table)");
        $columns = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'name');
        if (!in_array($column, $columns, true)) {
            $this->db->exec("ALTER TABLE \"$table\" ADD COLUMN $column $definition");
        }
    }

    protected function createTableIfNotExists(string $sql): void
    {
        $this->db->exec($sql);
    }

    protected function tableExists(string $table): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name=?"
        );
        $stmt->execute([$table]);
        return (bool)$stmt->fetchColumn();
    }

    protected function columnExists(string $table, string $column): bool
    {
        $stmt = $this->db->query("PRAGMA table_info($table)");
        $columns = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'name');
        return in_array($column, $columns, true);
    }
}

<?php
/**
 * Migración: Comunicaciones y Mapa de Seguridad
 */
class Migration_2024_01_12_000000_add_comms_and_security_map
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        // Añadir comunicaciones a protocol_cases
        $this->db->exec("ALTER TABLE protocol_cases ADD COLUMN communications JSON DEFAULT '{}'");

        // Crear tabla security_maps
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS security_maps (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                protocol_case_id INTEGER NOT NULL UNIQUE,
                espais_segurs TEXT,
                espais_de_risc TEXT,
                persones_de_suport TEXT,
                mesures_urgencia JSON DEFAULT '[]',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (protocol_case_id) REFERENCES protocol_cases(id) ON DELETE CASCADE
            )
        ");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS security_maps;");
    }
}

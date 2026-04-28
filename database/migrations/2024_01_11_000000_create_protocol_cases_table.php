<?php
/**
 * Migración: Casos de Protocolo Legal (Catalunya 2024 / General)
 * Vincula los reportes de Aura con el flujo legal de las CCAA.
 */
class Migration_2024_01_11_000000_create_protocol_cases_table
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS protocol_cases (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                report_id INTEGER NOT NULL UNIQUE,
                ccaa_code VARCHAR(50) NOT NULL,
                current_phase VARCHAR(50) DEFAULT 'deteccion',
                valuation_team JSON, -- IDs de los profesionales asignados
                severity_preliminary VARCHAR(50), -- leve, grave, violencia_sexual
                classification VARCHAR(100), -- assetjament, odi, masclista, etc.
                deadline_at DATETIME, -- Límite de 48h/72h para valoración
                security_map JSON, -- Zonas seguras/inseguras definidas
                intervention_plan JSON, -- Tareas para víctima/agresor
                status VARCHAR(20) DEFAULT 'active', -- active, closed, bypassed
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
            )
        ");

        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_report ON protocol_cases(report_id);");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS protocol_cases;");
    }
}

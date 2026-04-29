<?php
/**
 * Migración: Índices de Rendimiento para Protocolos
 */
class Migration_2026_04_28_150000_add_performance_indexes
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        // Índices de Claves Foráneas
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_report ON protocol_cases(report_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_followups_case ON protocol_followups(protocol_case_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_security_maps_case ON security_maps(protocol_case_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_evidence_case ON protocol_evidence(protocol_case_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_access_logs_case ON protocol_access_logs(protocol_case_id);");
    }

    public function down(): void
    {
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_cases_report;");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_followups_case;");
        $this->db->exec("DROP INDEX IF EXISTS idx_security_maps_case;");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_evidence_case;");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_access_logs_case;");
    }
}

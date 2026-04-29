<?php
/**
 * Migración: Índices de Rendimiento para Protocolos
 */
class Migration_2026_04_28_140001_add_performance_indexes
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        // Índices para Sociometría
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_sociometric_responses_survey ON sociometric_responses(survey_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_sociometric_responses_nominated ON sociometric_responses(nominated_student_id);");
        
        // Índices para Seguimientos y Mapas
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_followups_case ON protocol_followups(protocol_case_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_security_maps_case ON security_maps(protocol_case_id);");
        
        // Índice para Logs de Acceso
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_access_logs_case ON protocol_access_logs(protocol_case_id);");
    }

    public function down(): void
    {
        $this->db->exec("DROP INDEX IF EXISTS idx_sociometric_responses_survey;");
        $this->db->exec("DROP INDEX IF EXISTS idx_sociometric_responses_nominated;");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_followups_case;");
        $this->db->exec("DROP INDEX IF EXISTS idx_security_maps_case;");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_access_logs_case;");
    }
}

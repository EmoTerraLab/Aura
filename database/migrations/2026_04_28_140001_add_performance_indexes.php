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
        // Índices para reportes y mensajes (Bandeja de entrada Staff)
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_reports_status_created ON reports(status, created_at)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_messages_report_sender ON report_messages(report_id, sender_id)");
        
        // Índices para el flujo de protocolo legal
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_status_deadline ON protocol_cases(ccaa_code, deadline_at)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_followups_case_date ON protocol_followups(protocol_case_id, session_date)");
    }

    public function down(): void
    {
        $this->db->exec("DROP INDEX IF EXISTS idx_reports_status_created");
        $this->db->exec("DROP INDEX IF EXISTS idx_messages_report_sender");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_status_deadline");
        $this->db->exec("DROP INDEX IF EXISTS idx_followups_case_date");
    }
}

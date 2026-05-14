<?php
/**
 * Migración: Índices de rendimiento adicionales para optimización global.
 * Fecha: 2026-05-15
 */
class Migration_2026_05_15_000000_add_missing_performance_indexes
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        // Índices para búsquedas frecuentes y filtrado en Protocol Cases
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_phase ON protocol_cases(current_phase);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_ccaa ON protocol_cases(ccaa_code);");
        
        // Índices para Report Messages (N+1 preventivo en hilos de mensajes)
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_report_messages_report ON report_messages(report_id);");
        
        // Índices de Claves Foráneas faltantes para optimizar JOINs
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_reports_student ON reports(student_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_reports_classroom ON reports(classroom_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_student_profiles_classroom ON student_profiles(classroom_id);");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_classrooms_tutor ON classrooms(tutor_id);");
        
        // Optimización de búsquedas por email (aunque sea UNIQUE, ayuda en planes de ejecución complejos)
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);");
    }

    public function down(): void
    {
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_cases_phase;");
        $this->db->exec("DROP INDEX IF EXISTS idx_protocol_cases_ccaa;");
        $this->db->exec("DROP INDEX IF EXISTS idx_report_messages_report;");
        $this->db->exec("DROP INDEX IF EXISTS idx_reports_student;");
        $this->db->exec("DROP INDEX IF EXISTS idx_reports_classroom;");
        $this->db->exec("DROP INDEX IF EXISTS idx_student_profiles_classroom;");
        $this->db->exec("DROP INDEX IF EXISTS idx_classrooms_tutor;");
        $this->db->exec("DROP INDEX IF EXISTS idx_users_email;");
    }
}

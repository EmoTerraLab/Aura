<?php
class Migration_2026_05_15_000000_add_missing_performance_indexes {
    public function up($db) {
        $db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_report_id ON protocol_cases(report_id);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_current_phase ON protocol_cases(current_phase);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_protocol_cases_ccaa_code ON protocol_cases(ccaa_code);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_report_messages_report_id ON report_messages(report_id);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);");
    }
    public function down($db) {
        $db->exec("DROP INDEX IF EXISTS idx_protocol_cases_report_id;");
        $db->exec("DROP INDEX IF EXISTS idx_protocol_cases_current_phase;");
        $db->exec("DROP INDEX IF EXISTS idx_protocol_cases_ccaa_code;");
        $db->exec("DROP INDEX IF EXISTS idx_report_messages_report_id;");
        $db->exec("DROP INDEX IF EXISTS idx_users_email;");
    }
}

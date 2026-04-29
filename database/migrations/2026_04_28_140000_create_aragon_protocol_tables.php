<?php
declare(strict_types=1);
class Migration_2026_04_28_140000_create_aragon_protocol_tables
 {
    private \PDO $db;
    public function __construct(\PDO $db) { $this->db = $db; }
    public function up(): void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS aragon_protocol_cases (id INTEGER PRIMARY KEY AUTOINCREMENT, report_id INTEGER NOT NULL UNIQUE, status VARCHAR(50) NOT NULL, victim_id INTEGER DEFAULT NULL, aggressor_id INTEGER DEFAULT NULL, start_date DATETIME DEFAULT NULL, is_sexual_violence INTEGER DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE, FOREIGN KEY (victim_id) REFERENCES users(id), FOREIGN KEY (aggressor_id) REFERENCES users(id))");
        $this->db->exec("CREATE TABLE IF NOT EXISTS aragon_protocol_annexes (id INTEGER PRIMARY KEY AUTOINCREMENT, protocol_case_id INTEGER NOT NULL, annex_type VARCHAR(10) NOT NULL, content TEXT NOT NULL, submitted_by INTEGER NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (protocol_case_id) REFERENCES aragon_protocol_cases(id) ON DELETE CASCADE, FOREIGN KEY (submitted_by) REFERENCES users(id))");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_aragon_case_report ON aragon_protocol_cases(report_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_aragon_annex_case ON aragon_protocol_annexes(protocol_case_id)");
    }
    public function down(): void {
        $this->db->exec("DROP TABLE IF EXISTS aragon_protocol_annexes");
        $this->db->exec("DROP TABLE IF EXISTS aragon_protocol_cases");
    }
}

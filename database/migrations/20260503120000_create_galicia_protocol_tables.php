<?php
declare(strict_types=1);

class Migration_20260503120000_create_galicia_protocol_tables {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function up(): void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS galicia_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            report_id INTEGER NOT NULL UNIQUE,
            status VARCHAR(50) NOT NULL DEFAULT 'deteccio_comunicacio',
            victim_id INTEGER DEFAULT NULL,
            aggressor_id INTEGER DEFAULT NULL,
            team_coordinator_id INTEGER DEFAULT NULL,
            start_date DATETIME DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
            FOREIGN KEY (victim_id) REFERENCES users(id),
            FOREIGN KEY (aggressor_id) REFERENCES users(id),
            FOREIGN KEY (team_coordinator_id) REFERENCES users(id)
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS galicia_protocol_annexes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            protocol_case_id INTEGER NOT NULL,
            annex_type VARCHAR(30) NOT NULL,
            content TEXT NOT NULL,
            submitted_by INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (protocol_case_id) REFERENCES galicia_protocol_cases(id) ON DELETE CASCADE,
            FOREIGN KEY (submitted_by) REFERENCES users(id)
        )");

        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_galicia_case_report ON galicia_protocol_cases(report_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_galicia_annex_case ON galicia_protocol_annexes(protocol_case_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_galicia_annex_type ON galicia_protocol_annexes(annex_type)");
    }

    public function down(): void {
        $this->db->exec("DROP TABLE IF EXISTS galicia_protocol_annexes");
        $this->db->exec("DROP TABLE IF EXISTS galicia_protocol_cases");
    }
}

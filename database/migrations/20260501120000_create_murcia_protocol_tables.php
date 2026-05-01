<?php
declare(strict_types=1);
class Migration_20260501120000_create_murcia_protocol_tables {
    private \PDO $db;
    public function __construct(\PDO $db) { $this->db = $db; }
    public function up(): void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS murcia_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            report_id INTEGER NOT NULL UNIQUE, 
            status VARCHAR(50) NOT NULL, 
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
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS murcia_protocol_annexes (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            protocol_case_id INTEGER NOT NULL, 
            annex_type VARCHAR(20) NOT NULL, 
            content TEXT NOT NULL, 
            submitted_by INTEGER NOT NULL, 
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
            FOREIGN KEY (protocol_case_id) REFERENCES murcia_protocol_cases(id) ON DELETE CASCADE, 
            FOREIGN KEY (submitted_by) REFERENCES users(id)
        )");
        
        $this->db->exec("CREATE INDEX idx_murcia_case_report ON murcia_protocol_cases(report_id)");
        $this->db->exec("CREATE INDEX idx_murcia_annex_case ON murcia_protocol_annexes(protocol_case_id)");
    }
    public function down(): void {
        $this->db->exec("DROP TABLE IF EXISTS murcia_protocol_annexes");
        $this->db->exec("DROP TABLE IF EXISTS murcia_protocol_cases");
    }
}

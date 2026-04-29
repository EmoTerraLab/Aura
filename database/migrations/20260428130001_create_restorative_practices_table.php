<?php

class Migration_20260428130001_create_restorative_practices_table
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS restorative_practices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            protocol_case_id INTEGER NOT NULL,
            practice_type VARCHAR(50) NOT NULL, -- conversa, reunio, cercle
            facilitator_id INTEGER NOT NULL,
            session_date DATE NOT NULL,
            participants TEXT,
            agreements TEXT,
            status VARCHAR(20) DEFAULT 'pending', -- pending, completed, failed
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (protocol_case_id) REFERENCES protocol_cases(id) ON DELETE CASCADE,
            FOREIGN KEY (facilitator_id) REFERENCES users(id)
        )";
        $this->db->exec($sql);
        $this->db->exec("CREATE INDEX idx_restorative_case ON restorative_practices(protocol_case_id)");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS restorative_practices");
    }
}

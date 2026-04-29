<?php

class Migration_2026_04_28_130000_add_acknowledgment_to_cases
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $this->db->exec("ALTER TABLE protocol_cases ADD COLUMN is_acknowledged INTEGER DEFAULT NULL");
    }

    public function down(): void
    {
        // SQLite no permite DROP COLUMN de forma sencilla en versiones antiguas, omitimos para estabilidad
    }
}

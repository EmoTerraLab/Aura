<?php

class Migration_20260428130000_add_acknowledgment_to_cases
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        // Añadir columna booleana (0/1) nullable
        $stmt = $this->db->query("PRAGMA table_info(protocol_cases)");
        $columns = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'name');
        if (!in_array('aggressor_acknowledges_facts', $columns, true)) {
            $this->db->exec("ALTER TABLE protocol_cases ADD COLUMN aggressor_acknowledges_facts INTEGER DEFAULT NULL");
        }
    }

    public function down(): void
    {
        // SQLite no permite borrar columnas fácilmente. En un sistema real se reconstruiría la tabla.
    }
}

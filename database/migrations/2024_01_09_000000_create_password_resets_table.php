<?php
/**
 * Migration: Create Password Resets Table
 */
return new class {
    public function up(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email VARCHAR(150) NOT NULL,
                token VARCHAR(64) NOT NULL UNIQUE,
                used INTEGER DEFAULT 0,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_token ON password_resets(token);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_email ON password_resets(email);");
    }

    public function down(PDO $db): void {
        $db->exec("DROP TABLE IF EXISTS password_resets;");
    }
};

<?php
namespace App\Core;

/**
 * Migrator — Sistema de migraciones de base de datos
 *
 * Gestiona la ejecución incremental de migraciones,
 * registro de versiones ejecutadas y rollback en caso de error.
 */
class Migrator
{
    private \PDO $db;
    private string $migrationsPath;
    private string $backupsPath;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->migrationsPath = __DIR__ . '/../../database/migrations/';
        $this->backupsPath    = __DIR__ . '/../../database/backups/';
        $this->ensureMigrationsTable();
    }

    /**
     * Devuelve las migraciones pendientes (no ejecutadas aún)
     */
    public function getPending(): array
    {
        $allFiles = $this->getAllMigrationFiles();
        $executed = $this->getExecutedVersions();
        $pending  = [];

        foreach ($allFiles as $file) {
            $version = $this->extractVersion($file);
            if (!in_array($version, $executed)) {
                $pending[] = [
                    'version'  => $version,
                    'filename' => $file,
                    'description' => $this->extractDescription($file)
                ];
            }
        }

        return $pending;
    }

    /**
     * Ejecuta todas las migraciones pendientes
     * Devuelve array con resultados de cada migración
     */
    public function runPending(): array
    {
        $pending = $this->getPending();
        $results = [];

        foreach ($pending as $migration) {
            $result = $this->runSingle($migration);
            $results[] = $result;
            if (!$result['success']) {
                break; // Detener en el primer error
            }
        }

        return $results;
    }

    /**
     * Ejecuta una sola migración y registra el resultado
     */
    private function runSingle(array $migration): array
    {
        $startTime = microtime(true);
        $filepath  = $this->migrationsPath . $migration['filename'];

        try {
            require_once $filepath;
            $className = $this->fileToClassName($migration['filename']);
            if (!class_exists($className)) {
                 throw new \RuntimeException("Clase $className no encontrada en $filepath");
            }
            $instance  = new $className($this->db);

            $this->db->beginTransaction();
            $instance->up();
            $this->db->commit();

            $timeMs = (int)((microtime(true) - $startTime) * 1000);
            $this->recordMigration($migration, $timeMs);

            error_log("[Migrator] OK: {$migration['version']} ({$timeMs}ms)");

            return [
                'version'     => $migration['version'],
                'description' => $migration['description'],
                'success'     => true,
                'time_ms'     => $timeMs
            ];

        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log("[Migrator] ERROR: {$migration['version']} — " . $e->getMessage());

            return [
                'version'     => $migration['version'],
                'description' => $migration['description'],
                'success'     => false,
                'error'       => $e->getMessage()
            ];
        }
    }

    /**
     * Crea un backup de la BD SQLite antes de migrar
     * Devuelve la ruta del archivo de backup
     */
    public function createBackup(): string
    {
        if (!is_dir($this->backupsPath)) {
            mkdir($this->backupsPath, 0750, true);
        }

        $dbPath    = __DIR__ . '/../../database/aura.sqlite';
        $timestamp = date('Y-m-d_His');
        $backupPath = $this->backupsPath . "aura_backup_{$timestamp}.sqlite";

        if (!copy($dbPath, $backupPath)) {
            throw new \RuntimeException('No se pudo crear el backup de la base de datos');
        }

        // Mantener solo los últimos 10 backups
        $this->cleanOldBackups(10);

        return $backupPath;
    }

    /**
     * Restaura la BD desde un backup
     */
    public function restoreBackup(string $backupPath): void
    {
        $dbPath = __DIR__ . '/../../database/aura.sqlite';
        if (!file_exists($backupPath)) {
            throw new \RuntimeException('Archivo de backup no encontrado: ' . $backupPath);
        }
        if (!copy($backupPath, $dbPath)) {
            throw new \RuntimeException('No se pudo restaurar el backup');
        }
    }

    /**
     * Verifica la integridad de la BD tras las migraciones
     */
    public function verifyIntegrity(): array
    {
        $checks = [];

        // 1. Verificar integridad SQLite
        $result = $this->db->query('PRAGMA integrity_check')->fetchAll();
        $checks['sqlite_integrity'] = [
            'ok'     => ($result[0]['integrity_check'] ?? '') === 'ok',
            'detail' => $result[0]['integrity_check'] ?? 'error'
        ];

        // 2. Verificar que las tablas críticas existen
        $requiredTables = ['users', 'migrations', 'settings', 'update_log'];
        $existingTables = $this->db->query(
            "SELECT name FROM sqlite_master WHERE type='table'"
        )->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($requiredTables as $table) {
            $checks['table_' . $table] = [
                'ok'     => in_array($table, $existingTables),
                'detail' => in_array($table, $existingTables) ? 'exists' : 'MISSING'
            ];
        }

        // 3. Verificar foreign keys
        $this->db->exec('PRAGMA foreign_keys = ON');
        $fkCheck = $this->db->query('PRAGMA foreign_key_check')->fetchAll();
        $checks['foreign_keys'] = [
            'ok'     => empty($fkCheck),
            'detail' => empty($fkCheck) ? 'ok' : 'violations: ' . count($fkCheck)
        ];

        return $checks;
    }

    public function getExecutedMigrations(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM migrations ORDER BY executed_at ASC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getBackups(): array
    {
        if (!is_dir($this->backupsPath)) return [];
        $files = glob($this->backupsPath . '*.sqlite');
        rsort($files);
        return array_map(function($f) {
            return [
                'filename' => basename($f),
                'path'     => $f,
                'size_mb'  => round(filesize($f) / 1048576, 2),
                'date'     => date('d/m/Y H:i:s', filemtime($f))
            ];
        }, $files);
    }

    // ---- Privados ----

    private function ensureMigrationsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                version VARCHAR(20) NOT NULL UNIQUE,
                filename VARCHAR(200) NOT NULL,
                description TEXT,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                execution_time_ms INTEGER,
                checksum VARCHAR(64)
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS update_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                version_from VARCHAR(20),
                version_to VARCHAR(20),
                status VARCHAR(20) DEFAULT 'pending',
                migrations_run INTEGER DEFAULT 0,
                error_message TEXT,
                backup_file VARCHAR(200),
                started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                finished_at DATETIME
            )
        ");
    }

    private function getAllMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) return [];
        $files = glob($this->migrationsPath . '*.php');
        sort($files); // Orden cronológico por nombre
        return array_map('basename', $files);
    }

    private function getExecutedVersions(): array
    {
        $stmt = $this->db->query('SELECT version FROM migrations');
        return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    private function extractVersion(string $filename): string
    {
        // De '2024_03_20_093000_add_lang_to_users.php' extrae '2024_03_20_093000'
        preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})/', $filename, $m);
        return $m[1] ?? $filename;
    }

    private function extractDescription(string $filename): string
    {
        // De '2024_03_20_093000_add_lang_to_users.php' extrae 'add lang to users'
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $filename);
        $name = str_replace(['_', '.php'], [' ', ''], $name);
        return ucfirst($name);
    }

    private function fileToClassName(string $filename): string
    {
        $name = str_replace('.php', '', $filename);
        // '2024_03_20_093000_add_lang_to_users' -> 'Migration_2024_03_20_093000_add_lang_to_users'
        return 'Migration_' . $name;
    }

    private function recordMigration(array $migration, int $timeMs): void
    {
        $stmt = $this->db->prepare(
            'INSERT OR IGNORE INTO migrations (version, filename, description, execution_time_ms, checksum)
             VALUES (?, ?, ?, ?, ?)'
        );
        $checksum = md5_file($this->migrationsPath . $migration['filename']);
        $stmt->execute([
            $migration['version'],
            $migration['filename'],
            $migration['description'],
            $timeMs,
            $checksum
        ]);
    }

    private function cleanOldBackups(int $keep): void
    {
        $files = glob($this->backupsPath . '*.sqlite');
        rsort($files);
        foreach (array_slice($files, $keep) as $old) {
            unlink($old);
        }
    }
}

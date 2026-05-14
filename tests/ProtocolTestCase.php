<?php
namespace Tests;

use App\Core\Database;
use App\Models\ProtocolCase;
use App\Models\Report;
use PDO;
use ReflectionClass;

require_once __DIR__ . '/../vendor/autoload.php';

abstract class ProtocolTestCase
{
    protected PDO $db;

    public function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Inject this PDO instance into the Database singleton using reflection
        $reflection = new ReflectionClass(Database::class);
        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        
        // Create a fake Database object if needed or just mock the static instance
        // Since getInstance() creates it, we can pre-set it.
        // But Database is private constructor, so we need to instantiate it via reflection too or modify it.
        // Actually, let's just create an anonymous class or a dummy that has the pdo property.
        
        $databaseWrapper = $reflection->newInstanceWithoutConstructor();
        $pdoProperty = $reflection->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($databaseWrapper, $this->db);
        
        $instanceProperty->setValue(null, $databaseWrapper);

        $this->createSchema();
    }

    protected function createSchema(): void
    {
        // Minimal schema for tests
        $this->db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT, role TEXT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS classrooms (id INTEGER PRIMARY KEY, name TEXT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS reports (id INTEGER PRIMARY KEY, student_id INTEGER, classroom_id INTEGER, content TEXT, status TEXT, created_at DATETIME, updated_at DATETIME)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS report_messages (id INTEGER PRIMARY KEY, report_id INTEGER, sender_id INTEGER, message TEXT, is_internal INTEGER, created_at DATETIME)");
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            report_id INTEGER NOT NULL UNIQUE,
            ccaa_code VARCHAR(50) NOT NULL,
            current_phase VARCHAR(50) DEFAULT 'deteccion',
            status VARCHAR(20) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS aragon_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            report_id INTEGER NOT NULL UNIQUE, 
            status VARCHAR(50) NOT NULL, 
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS murcia_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            report_id INTEGER NOT NULL UNIQUE,
            status VARCHAR(50) NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS galicia_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            report_id INTEGER NOT NULL UNIQUE,
            status VARCHAR(50) NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Cataluña y Valencia if needed
        $this->db->exec("CREATE TABLE IF NOT EXISTS cataluna_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            report_id INTEGER NOT NULL UNIQUE,
            status VARCHAR(50) NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS comunidad_valenciana_protocol_cases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            report_id INTEGER NOT NULL UNIQUE,
            status VARCHAR(50) NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    protected function createCase(string $ccaa, string $phase): int
    {
        // 1. Create a report first
        $this->db->prepare("INSERT INTO reports (classroom_id, content, status) VALUES (1, 'Test content', 'new')")
                 ->execute();
        $reportId = (int)$this->db->lastInsertId();

        // 2. Create the protocol case
        $this->db->prepare("INSERT INTO protocol_cases (report_id, ccaa_code, current_phase) VALUES (?, ?, ?)")
                 ->execute([$reportId, $ccaa, $phase]);
        $caseId = (int)$this->db->lastInsertId();

        // 3. Sync with regional table
        $tableName = strtolower($ccaa);
        if ($ccaa === 'ARA') $tableName = 'aragon';
        if ($ccaa === 'MUR') $tableName = 'murcia';
        if ($ccaa === 'GAL') $tableName = 'galicia';
        if ($ccaa === 'CAT') $tableName = 'cataluna';
        if ($ccaa === 'VAL') $tableName = 'comunidad_valenciana';
        
        $this->db->prepare("INSERT INTO {$tableName}_protocol_cases (report_id, status) VALUES (?, ?)")
                 ->execute([$reportId, $phase]);

        return $caseId;
    }

    protected function getCase(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM protocol_cases WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    protected function getRegionalCase(string $ccaa, int $reportId): ?array
    {
        $tableName = strtolower($ccaa);
        if ($ccaa === 'ARA') $tableName = 'aragon';
        if ($ccaa === 'MUR') $tableName = 'murcia';
        if ($ccaa === 'GAL') $tableName = 'galicia';
        if ($ccaa === 'CAT') $tableName = 'cataluna';
        if ($ccaa === 'VAL') $tableName = 'comunidad_valenciana';

        $stmt = $this->db->prepare("SELECT * FROM {$tableName}_protocol_cases WHERE report_id = ?");
        $stmt->execute([$reportId]);
        return $stmt->fetch() ?: null;
    }

    protected function assertEquals($expected, $actual, $message = ''): void
    {
        if ($expected !== $actual) {
            $msg = $message ?: "Expected " . var_export($expected, true) . " but got " . var_export($actual, true);
            throw new \Exception($msg);
        }
    }

    protected function assertTrue($condition, $message = ''): void
    {
        if ($condition !== true) {
            $msg = $message ?: "Expected true but got " . var_export($condition, true);
            throw new \Exception($msg);
        }
    }

    protected function assertIsArray($actual, $message = ''): void
    {
        if (!is_array($actual)) {
            $msg = $message ?: "Expected array but got " . gettype($actual);
            throw new \Exception($msg);
        }
    }

    protected function assertNotEmpty($actual, $message = ''): void
    {
        if (empty($actual)) {
            $msg = $message ?: "Expected non-empty value";
            throw new \Exception($msg);
        }
    }
}

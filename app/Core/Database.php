<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dbPath = __DIR__ . '/../../database/aura.sqlite';
        $isNew = !file_exists($dbPath);

        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Configuraciones de rendimiento y seguridad para SQLite
            $this->pdo->exec('PRAGMA foreign_keys = ON;');
            $this->pdo->exec('PRAGMA journal_mode = WAL;');
            $this->pdo->exec('PRAGMA synchronous = NORMAL;');
            $this->pdo->exec('PRAGMA busy_timeout = 5000;');

            if ($isNew) {
                $this->initSchema();
            }
        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new \RuntimeException("Error de conexión a la base de datos. Revise la configuración.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    private function initSchema() {
        $schemaPath = __DIR__ . '/../../database/migrations.sql';
        $seedsPath = __DIR__ . '/../../database/seeds.sql';

        if (file_exists($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            $this->pdo->exec($sql);
        }

        if (file_exists($seedsPath)) {
            $sql = file_get_contents($seedsPath);
            $this->pdo->exec($sql);
        }
    }
}

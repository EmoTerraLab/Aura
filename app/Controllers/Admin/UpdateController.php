<?php
namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\MaintenanceMode;
use App\Core\Migrator;
use App\Core\Database;
use App\Core\View;

class UpdateController
{
    private Migrator $migrator;

    public function __construct()
    {
        $this->migrator = new Migrator(Database::getInstance());
    }

    /**
     * GET /admin/update
     * Panel principal de actualizaciones
     */
    public function index(): void
    {
        $pending          = $this->migrator->getPending();
        $executed         = $this->migrator->getExecutedMigrations();
        $backups          = $this->migrator->getBackups();
        $maintenanceActive = MaintenanceMode::isActive();
        $maintenanceData  = MaintenanceMode::getData();
        $currentVersion   = $this->getCurrentVersion();
        
        View::render('admin/update/index', [
            'title' => 'Actualizaciones del Sistema',
            'pending' => $pending,
            'executed' => $executed,
            'backups' => $backups,
            'maintenanceActive' => $maintenanceActive,
            'maintenanceData' => $maintenanceData,
            'currentVersion' => $currentVersion
        ], 'app');
    }

    /**
     * POST /admin/update/run
     * Ejecuta el proceso completo de actualización
     */
    public function run(): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');

        // Aumentar límites para el proceso de migración
        set_time_limit(300);        // 5 minutos máximo
        ini_set('memory_limit', '256M');
        ignore_user_abort(true);    // Continuar aunque el usuario cierre el navegador

        $logId = $this->startUpdateLog();

        try {
            $pending = $this->migrator->getPending();
            if (empty($pending)) {
                echo json_encode(['success' => true, 'message' => 'No hay migraciones pendientes', 'migrations' => []]);
                return;
            }

            // 1. Activar modo mantenimiento
            $estimatedTime = count($pending) . ' migración(es)';
            MaintenanceMode::enable(
                'Actualizando el sistema. Por favor espera.',
                $estimatedTime
            );

            // 2. Crear backup
            $backupPath = $this->migrator->createBackup();
            $this->updateLog($logId, ['backup_file' => basename($backupPath)]);

            // 3. Ejecutar migraciones
            $results = $this->migrator->runPending();
            $success = !in_array(false, array_column($results, 'success'));
            $migrationsRun = count(array_filter($results, fn($r) => $r['success']));

            if (!$success) {
                // Encontrar el primer error
                $errorResult = array_filter($results, fn($r) => !$r['success']);
                $error = reset($errorResult);

                // Restaurar backup
                $this->migrator->restoreBackup($backupPath);

                $this->updateLog($logId, [
                    'status'         => 'failed',
                    'migrations_run' => $migrationsRun,
                    'error_message'  => $error['error'] ?? 'Error desconocido',
                    'finished_at'    => date('Y-m-d H:i:s')
                ]);

                // Mantener modo mantenimiento activo con mensaje de error
                MaintenanceMode::enable(
                    'Error durante la actualización. El administrador debe revisar los logs.',
                    'Pendiente de revisión manual'
                );

                echo json_encode([
                    'success'     => false,
                    'error'       => $error['error'],
                    'version'     => $error['version'],
                    'migrations'  => $results,
                    'backup_used' => true,
                    'backup_file' => basename($backupPath)
                ]);
                return;
            }

            // 4. Verificar integridad
            $integrity = $this->migrator->verifyIntegrity();
            $integrityOk = !in_array(false, array_column($integrity, 'ok'));

            if (!$integrityOk) {
                $this->migrator->restoreBackup($backupPath);
                MaintenanceMode::enable('Error de integridad tras actualización. Datos restaurados.');

                echo json_encode([
                    'success'   => false,
                    'error'     => 'Fallo en verificación de integridad',
                    'integrity' => $integrity
                ]);
                return;
            }

            // 5. Todo OK — desactivar mantenimiento
            MaintenanceMode::disable();

            $this->updateLog($logId, [
                'status'         => 'success',
                'version_to'     => $this->getCurrentVersion(),
                'migrations_run' => $migrationsRun,
                'finished_at'    => date('Y-m-d H:i:s')
            ]);

            echo json_encode([
                'success'    => true,
                'migrations' => $results,
                'integrity'  => $integrity,
                'message'    => "$migrationsRun migración(es) ejecutada(s) correctamente"
            ]);

        } catch (\Throwable $e) {
            error_log('[UpdateController] Error crítico: ' . $e->getMessage());
            $this->updateLog($logId, [
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at'   => date('Y-m-d H:i:s')
            ]);

            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * POST /admin/update/maintenance/enable
     */
    public function enableMaintenance(): void
    {
        Csrf::validateRequest();
        $message = trim($_POST['message'] ?? '');
        $estimated = trim($_POST['estimated_end'] ?? '');
        MaintenanceMode::enable($message, $estimated);
        header('Location: /admin/update?maintenance=enabled');
        exit;
    }

    /**
     * POST /admin/update/maintenance/disable
     */
    public function disableMaintenance(): void
    {
        Csrf::validateRequest();
        MaintenanceMode::disable();
        header('Location: /admin/update?maintenance=disabled');
        exit;
    }

    /**
     * Alterna el modo mantenimiento con un secreto (sin login)
     */
    public function secretToggleMaintenance(string $secret = 'Ceuta2000'): void
    {
        if ($secret !== 'Ceuta2000') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }

        if (MaintenanceMode::isActive()) {
            MaintenanceMode::disable();
            echo "<h1>Aura PDP</h1>";
            echo "<p>Modo mantenimiento <strong>DESACTIVADO</strong> correctamente.</p>";
            echo "<a href='/login'>Ir al inicio</a>";
        } else {
            MaintenanceMode::enable('Mantenimiento activado mediante acceso secreto.', 'Indefinido');
            echo "<h1>Aura PDP</h1>";
            echo "<p>Modo mantenimiento <strong>ACTIVADO</strong> correctamente.</p>";
            echo "<a href='/'>Ver estado</a>";
        }
        exit;
    }

    /**
     * POST /admin/update/backup/create
     */
    public function createBackupManual(): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $path = $this->migrator->createBackup();
            echo json_encode(['success' => true, 'message' => 'Backup creado: ' . basename($path)]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * POST /admin/update/backup/restore
     */
    public function restoreBackup(): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        
        // Confirmación extra requerida
        $confirmation = $data['confirmation'] ?? '';
        if ($confirmation !== 'RESTAURAR') {
            http_response_code(400);
            echo json_encode(['error' => 'Debes escribir RESTAURAR para confirmar']);
            return;
        }

        $filename = basename($data['filename'] ?? '');
        if (!$filename || !preg_match('/^aura_backup_[\d_-]+\.sqlite$/', $filename)) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre de archivo no válido']);
            return;
        }

        try {
            // 1. Crear backup del estado actual ANTES de restaurar
            $preRestoreBackup = $this->migrator->createBackup();

            // 2. Activar mantenimiento
            MaintenanceMode::enable('Restaurando backup. Sistema no disponible.');

            // 3. Restaurar
            $backupPath = __DIR__ . '/../../../database/backups/' . $filename;
            $this->migrator->restoreBackup($backupPath);

            // 4. NO desactivar mantenimiento automáticamente
            echo json_encode([
                'success' => true,
                'message' => 'Backup restaurado. Se ha creado un backup del estado anterior en: ' . basename($preRestoreBackup) . '. Verifica el sistema y desactiva el mantenimiento manualmente.',
                'pre_restore_backup' => basename($preRestoreBackup)
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * GET /admin/update/integrity
     */
    public function checkIntegrity(): void
    {
        header('Content-Type: application/json');
        $checks = $this->migrator->verifyIntegrity();
        $allOk  = !in_array(false, array_column($checks, 'ok'));
        echo json_encode(['success' => true, 'all_ok' => $allOk, 'checks' => $checks]);
    }

    private function getCurrentVersion(): string
    {
        $composerFile = __DIR__ . '/../../../composer.json';
        if (file_exists($composerFile)) {
            $data = json_decode(file_get_contents($composerFile), true);
            return $data['version'] ?? '1.0.0';
        }
        return '1.0.0';
    }

    private function startUpdateLog(): int
    {
        $stmt = Database::getInstance()->prepare(
            'INSERT INTO update_log (version_from, status) VALUES (?, ?)'
        );
        $stmt->execute([$this->getCurrentVersion(), 'running']);
        return (int)Database::getInstance()->lastInsertId();
    }

    private function updateLog(int $id, array $data): void
    {
        $sets   = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        Database::getInstance()->prepare("UPDATE update_log SET $sets WHERE id = ?")->execute($values);
    }
}

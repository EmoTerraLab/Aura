#!/usr/bin/env php
<?php
/**
 * CLI Migration Runner — Aura
 * Uso: php migrate.php [comando]
 *
 * Comandos:
 *   php migrate.php status     — Ver migraciones pendientes y ejecutadas
 *   php migrate.php run        — Ejecutar migraciones pendientes
 *   php migrate.php backup     — Crear backup de la BD
 *   php migrate.php integrity  — Verificar integridad de la BD
 *   php migrate.php maintenance:on  [mensaje]  — Activar modo mantenimiento
 *   php migrate.php maintenance:off             — Desactivar modo mantenimiento
 */

define('CLI', true);

// Autoloader simple para CLI
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Suprimir warnings de sesión en CLI (pueden ocurrir por Auth::check() en MaintenanceMode)
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

$db       = App\Core\Database::getInstance();
$migrator = new App\Core\Migrator($db);
$cmd      = $argv[1] ?? 'status';

echo "\n🔷 Aura — Migration CLI\n";
echo str_repeat('─', 50) . "\n\n";

switch ($cmd) {
    case 'status':
        $pending  = $migrator->getPending();
        $executed = $migrator->getExecutedMigrations();
        echo "✅ Ejecutadas: " . count($executed) . "\n";
        echo "⏳ Pendientes: " . count($pending) . "\n\n";
        if ($pending) {
            echo "Pendientes:\n";
            foreach ($pending as $m) echo "  • {$m['version']} — {$m['description']}\n";
        } else {
            echo "✓ El sistema está actualizado\n";
        }
        break;

    case 'run':
        $pending = $migrator->getPending();
        if (empty($pending)) { echo "✓ No hay migraciones pendientes\n"; break; }

        echo "Creando backup...\n";
        $backup = $migrator->createBackup();
        echo "✓ Backup: " . basename($backup) . "\n\n";

        App\Core\MaintenanceMode::enable('Actualización en progreso (CLI)');
        echo "🔧 Modo mantenimiento activado\n\n";

        $results = $migrator->runPending();
        $allOk   = true;

        foreach ($results as $r) {
            if ($r['success']) {
                echo "  ✓ {$r['version']} ({$r['time_ms']}ms)\n";
            } else {
                echo "  ✗ {$r['version']} — ERROR: {$r['error']}\n";
                $allOk = false;
                break;
            }
        }

        if ($allOk) {
            $integrity = $migrator->verifyIntegrity();
            $intOk = !in_array(false, array_column($integrity, 'ok'));
            echo $intOk ? "\n✅ Integridad OK\n" : "\n⚠️  Problemas de integridad detectados\n";
            App\Core\MaintenanceMode::disable();
            echo "✓ Modo mantenimiento desactivado\n";
        } else {
            $migrator->restoreBackup($backup);
            echo "\n🔄 Backup restaurado automáticamente\n";
            echo "⚠️  Modo mantenimiento permanece activo. Revisar logs.\n";
        }
        break;

    case 'backup':
        $path = $migrator->createBackup();
        echo "✓ Backup creado: " . basename($path) . "\n";
        break;

    case 'integrity':
        $checks = $migrator->verifyIntegrity();
        foreach ($checks as $name => $check) {
            $icon = $check['ok'] ? '✓' : '✗';
            echo "  $icon $name: {$check['detail']}\n";
        }
        break;

    case 'maintenance:on':
        $msg = $argv[2] ?? 'Sistema en mantenimiento';
        App\Core\MaintenanceMode::enable($msg);
        echo "✓ Modo mantenimiento activado\n";
        break;

    case 'maintenance:off':
        App\Core\MaintenanceMode::disable();
        echo "✓ Modo mantenimiento desactivado\n";
        break;

    default:
        echo "Comando no reconocido: $cmd\n";
        echo "Comandos: status, run, backup, integrity, maintenance:on, maintenance:off\n";
}

echo "\n";

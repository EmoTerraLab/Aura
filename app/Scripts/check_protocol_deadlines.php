<?php
/**
 * Script: Control de Plazos de Protocolo (Cron Job)
 * Revisa que no se excedan las 48h de valoración y que haya seguimientos semanales.
 */

require __DIR__ . '/../../vendor/autoload.php';

// Simular entorno si se ejecuta por CLI
if (!defined('APP_ENV')) define('APP_ENV', 'production');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;

echo "[CRON] Revisant terminis de protocols...\n";

$db = Database::getInstance();
$now = date('Y-m-d H:i:s');

// 1. Alerta de Valoración (> 48h en detección sin clasificar)
$sqlVal = "SELECT c.id, u.name 
           FROM protocol_cases c
           JOIN reports r ON c.report_id = r.id
           JOIN student_profiles sp ON r.student_id = sp.id
           JOIN users u ON sp.user_id = u.id
           WHERE c.current_phase = 'deteccion' 
           AND c.severity_preliminary IS NULL
           AND julianday('now') - julianday(c.created_at) > 2";
$overdueVal = $db->query($sqlVal)->fetchAll();

foreach ($overdueVal as $row) {
    echo "⚠️ ALERTA: Cas #{$row['id']} ({$row['name']}) porta més de 48h sense valoración.\n";
    // Aquí podrías insertar en una tabla de 'notifications' si existiera
}

// 2. Alerta de Seguimiento (Fase 4/5 sin seguimiento en los últimos 7 días)
$sqlFol = "SELECT c.id, u.name 
           FROM protocol_cases c
           JOIN reports r ON c.report_id = r.id
           JOIN student_profiles sp ON r.student_id = sp.id
           JOIN users u ON sp.user_id = u.id
           WHERE (c.current_phase = 'intervencio' OR c.current_phase = 'seguiment_tancament')
           AND NOT EXISTS (
               SELECT 1 FROM protocol_followups f 
               WHERE f.protocol_case_id = c.id 
               AND julianday('now') - julianday(f.session_date) < 7
           )";
$overdueFol = $db->query($sqlFol)->fetchAll();

foreach ($overdueFol as $row) {
    echo "📅 SEGUIMENT: Cas #{$row['id']} ({$row['name']}) requereix sessió de control setmanal.\n";
}

echo "[DONE] Revisió finalitzada.\n";

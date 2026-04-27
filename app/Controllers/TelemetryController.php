<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Report;
use App\Models\Classroom;
use App\Core\Config;

class TelemetryController {
    public function getStats() {
        header('Content-Type: application/json');
        
        try {
            $userModel = new User();
            $reportModel = new Report();
            $classroomModel = new Classroom();

            $stats = [
                'ok' => true,
                'app' => [
                    'name' => 'Aura',
                    'version' => trim(file_get_contents(__DIR__ . '/../../VERSION')),
                    'environment' => APP_ENV ?? 'prod',
                    'license' => 'MIT'
                ],
                'server' => [
                    'php_version' => PHP_VERSION,
                    'os' => PHP_OS,
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
                ],
                'usage' => [
                    'total_users' => $userModel->count(),
                    'total_reports' => $reportModel->count(),
                    'total_classrooms' => $classroomModel->count()
                ],
                'timestamp' => time()
            ];

            echo json_encode($stats, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'error' => 'No se pudieron recopilar las métricas.',
                'message' => $e->getMessage()
            ]);
        }
    }
}

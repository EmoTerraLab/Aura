<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Lang;
use App\Core\Auth;
use App\Models\ProtocolCase;

class ProtocolDashboardController
{
    public function index(): void
    {
        $db = Database::getInstance();

        // Audit Trail: Registro de acceso al panel global
        $this->logAccess();

        // 1. KPIs
        $totalActive = $db->query("SELECT COUNT(*) FROM protocol_cases WHERE current_phase != 'tancament'")->fetchColumn();
        $totalBarnahus = $db->query("SELECT COUNT(*) FROM protocol_cases WHERE current_phase = 'violencia_sexual_actiu'")->fetchColumn();
        
        // Seguimientos esta semana
        $startOfWeek = date('Y-m-d H:i:s', strtotime('monday this week'));
        $totalFollowups = $db->query("SELECT COUNT(*) FROM protocol_followups WHERE session_date >= '$startOfWeek'")->fetchColumn();

        // 2. Tabla de Expedientes (Optimizado con JOINs necesarios)
        $sql = "SELECT c.id, c.report_id, c.current_phase, c.created_at, 
                r.content as initial_content, u.name as student_name, cl.name as classroom_name,
                (julianday('now') - julianday(c.created_at)) as days_active
                FROM protocol_cases c
                JOIN reports r ON c.report_id = r.id
                JOIN student_profiles sp ON r.student_id = sp.id
                JOIN users u ON sp.user_id = u.id
                JOIN classrooms cl ON r.classroom_id = cl.id
                WHERE c.current_phase != 'tancament'
                ORDER BY c.created_at DESC";
        $cases = $db->query($sql)->fetchAll();

        // 3. Alertas de plazos
        $alerts = [];
        foreach ($cases as $case) {
            if ($case['current_phase'] === 'deteccion' && $case['days_active'] > 2) {
                $alerts[] = Lang::t('protocol.alert_valoracion_pending', ['id' => $case['id']]);
            }
        }

        View::render('protocol/dashboard', [
            'title' => Lang::t('protocol.dashboard_title'),
            'totalActive' => $totalActive,
            'totalBarnahus' => $totalBarnahus,
            'totalFollowups' => $totalFollowups,
            'cases' => $cases,
            'alerts' => $alerts
        ], 'app');
    }

    private function logAccess(): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO protocol_access_logs (protocol_case_id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            0, // Indica acceso al listado general
            Auth::id(),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }
}

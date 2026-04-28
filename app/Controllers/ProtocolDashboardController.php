<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Lang;
use App\Models\ProtocolCase;

class ProtocolDashboardController
{
    public function index(): void
    {
        $db = Database::getInstance();

        // 1. KPIs
        $totalActive = $db->query("SELECT COUNT(*) FROM protocol_cases WHERE current_phase != 'tancament'")->fetchColumn();
        $totalBarnahus = $db->query("SELECT COUNT(*) FROM protocol_cases WHERE current_phase = 'violencia_sexual_actiu'")->fetchColumn();
        
        // Seguimientos esta semana
        $startOfWeek = date('Y-m-d H:i:s', strtotime('monday this week'));
        $totalFollowups = $db->query("SELECT COUNT(*) FROM protocol_followups WHERE session_date >= '$startOfWeek'")->fetchColumn();

        // 2. Tabla de Expedientes
        $sql = "SELECT c.*, r.content as initial_content, u.name as student_name, cl.name as classroom_name,
                (julianday('now') - julianday(c.created_at)) as days_active
                FROM protocol_cases c
                JOIN reports r ON c.report_id = r.id
                JOIN student_profiles sp ON r.student_id = sp.id
                JOIN users u ON sp.user_id = u.id
                JOIN classrooms cl ON r.classroom_id = cl.id
                WHERE c.current_phase != 'tancament'
                ORDER BY c.created_at DESC";
        $cases = $db->query($sql)->fetchAll();

        // 3. Alertas de plazos (Simulación rápida para el dashboard)
        $alerts = [];
        foreach ($cases as $case) {
            if ($case['current_phase'] === 'deteccion' && $case['days_active'] > 2) {
                $alerts[] = "Cas #{$case['id']} excedeix les 48h de Valoració.";
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
}

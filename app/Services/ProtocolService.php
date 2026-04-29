<?php
namespace App\Services;

use App\Models\ProtocolCase;
use App\Models\ProtocolFollowup;
use App\Models\SecurityMap;
use App\Core\Auth;
use App\Core\Database;

class ProtocolService
{
    private ProtocolCase $caseModel;
    private ProtocolFollowup $followupModel;
    private SecurityMap $mapModel;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->followupModel = new ProtocolFollowup();
        $this->mapModel = new SecurityMap();
    }

    /**
     * Obtiene métricas agregadas para el dashboard evitando N+1
     */
    public function getDashboardMetrics(): array
    {
        $db = Database::getInstance();
        $startOfWeek = date('Y-m-d H:i:s', strtotime('monday this week'));

        return [
            'total_active' => $db->query("SELECT COUNT(*) FROM protocol_cases WHERE current_phase != 'tancament'")->fetchColumn(),
            'total_barnahus' => $db->query("SELECT COUNT(*) FROM protocol_cases WHERE current_phase = 'violencia_sexual_actiu'")->fetchColumn(),
            'weekly_followups' => $db->query("SELECT COUNT(*) FROM protocol_followups WHERE session_date >= '$startOfWeek'")->fetchColumn()
        ];
    }

    /**
     * Obtiene el listado consolidado de casos activos con optimización de JOINs
     */
    public function getActiveCasesWithDetails(): array
    {
        $db = Database::getInstance();
        $sql = "SELECT c.id, c.report_id, c.current_phase, c.created_at, 
                r.content as initial_content, u.name as student_name, cl.name as classroom_name,
                (julianday('now') - julianday(c.created_at)) as days_active,
                (SELECT COUNT(*) FROM protocol_followups f WHERE f.protocol_case_id = c.id) as total_followups
                FROM protocol_cases c
                JOIN reports r ON c.report_id = r.id
                JOIN student_profiles sp ON r.student_id = sp.id
                JOIN users u ON sp.user_id = u.id
                JOIN classrooms cl ON r.classroom_id = cl.id
                WHERE c.current_phase != 'tancament'
                ORDER BY c.created_at DESC";
        
        return $db->query($sql)->fetchAll();
    }

    /**
     * Registro de auditoría de acceso a datos sensibles
     */
    public function logSensitiveAccess(int $caseId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO protocol_access_logs (protocol_case_id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $caseId,
            Auth::id(),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }
}

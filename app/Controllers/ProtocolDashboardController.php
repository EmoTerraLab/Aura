<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Lang;
use App\Services\ProtocolService;

class ProtocolDashboardController
{
    private ProtocolService $protocolService;

    public function __construct()
    {
        $this->protocolService = new ProtocolService();
    }

    public function index(): void
    {
        // Audit Trail: Registro de acceso al panel global
        $this->protocolService->logSensitiveAccess(0); // 0 indica acceso general

        $metrics = $this->protocolService->getDashboardMetrics();
        $cases = $this->protocolService->getActiveCasesWithDetails();

        // Alertas de plazos
        $alerts = [];
        foreach ($cases as $case) {
            if ($case['current_phase'] === 'deteccion' && $case['days_active'] > 2) {
                $alerts[] = Lang::t('protocol.alert_valoracion_pending', ['id' => $case['id']]);
            }
        }

        View::render('protocol/dashboard', [
            'title' => Lang::t('protocol.dashboard_title'),
            'totalActive' => $metrics['total_active'],
            'totalBarnahus' => $metrics['total_barnahus'],
            'totalFollowups' => $metrics['weekly_followups'],
            'cases' => $cases,
            'alerts' => $alerts
        ], 'app');
    }
}

<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Lang;
use App\Core\View;
use App\Models\ProtocolCase;
use App\Models\Report;
use App\Models\SecurityMap;
use App\Models\ProtocolFollowup;
use App\Services\ProtocolStateService;
use App\Services\RevaExportService;
use App\Services\ProtocolService;

class ProtocolWorkflowController
{
    private ProtocolCase $caseModel;
    private Report $reportModel;
    private SecurityMap $mapModel;
    private ProtocolFollowup $followupModel;
    private ProtocolStateService $stateService;
    private RevaExportService $revaService;
    private ProtocolService $protocolService;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->reportModel = new Report();
        $this->mapModel = new SecurityMap();
        $this->followupModel = new ProtocolFollowup();
        $this->stateService = new ProtocolStateService();
        $this->revaService = new RevaExportService();
        $this->protocolService = new ProtocolService();
    }

    public function getCaseData($report_id): void
    {
        try {
            header('Content-Type: application/json');
            $report_id = (int)$report_id;
            $ccaa = Config::get('ccaa_code');
            
            if ($ccaa !== 'cataluna') {
                echo json_encode(['success' => true, 'case' => null, 'ccaa' => $ccaa]);
                return;
            }

            $case = $this->caseModel->findByReport($report_id);
            
            if (!$case && $ccaa === 'cataluna') {
                $this->stateService->transitionTo($report_id, ProtocolCase::PHASE_DETECCION); // El servicio maneja creación
                $case = $this->caseModel->findByReport($report_id);
            }

            if ($case) {
                if ($case['current_phase'] === ProtocolCase::PHASE_BARNAHUS || $case['severity_preliminary'] === 'violencia_sexual') {
                    $this->protocolService->logSensitiveAccess($case['id']);
                }

                $case['followups'] = $this->followupModel->findByCase($case['id']);
                $case['closure_checks'] = json_decode($case['closure_checks'] ?? '{}', true);
                $case['communications'] = json_decode($case['communications'] ?? '{}', true);
            }

            echo json_encode(['success' => true, 'case' => $case, 'ccaa' => $ccaa]);
        } catch (\Throwable $e) {
            error_log("Error en getCaseData: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function changePhase($id): void
    {
        header('Content-Type: application/json');
        if (!Auth::hasRole(['orientador', 'direccion', 'admin']) && !Auth::isCocobe()) {
            echo json_encode(['success' => false, 'error' => Lang::t('error.no_permission')]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $success = $this->stateService->transitionTo((int)$id, $data['phase'] ?? '');
            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function classify($id): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $success = $this->stateService->classify((int)$id, $data['severity'] ?? '', $data['classification'] ?? '');
            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getRevaSummary(int $id): void
    {
        header('Content-Type: application/json');
        $case = $this->caseModel->find($id);
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        echo json_encode(['success' => true, 'summary' => $this->revaService->generateSummary($case, $report)]);
    }

    // El resto delegan a modelos de forma simple
    public function addFollowup($id): void { /* ... */ }
    public function uploadEvidence($id): void { /* ... */ }
}

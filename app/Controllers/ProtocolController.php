<?php
namespace App\Controllers;

use App\Core\Config;
use App\Models\ProtocolCase;
use App\Models\Report;
use App\Services\Protocol\ProtocolFactory;
use App\Services\ProtocolStateService;
use App\Services\ProtocolService;

class ProtocolController
{
    private ProtocolCase $caseModel;
    private Report $reportModel;
    private ProtocolStateService $stateService;
    private ProtocolService $protocolService;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->reportModel = new Report();
        $this->stateService = new ProtocolStateService();
        $this->protocolService = new ProtocolService();
    }

    /**
     * Endpoint genérico para obtener metadatos y estado del protocolo de un reporte
     */
    public function getCaseData($report_id): void
    {
        try {
            header('Content-Type: application/json');
            $report_id = (int)$report_id;
            $ccaa = Config::get('ccaa_code', 'generic');
            
            $protocol = ProtocolFactory::make($ccaa);

            $case = $this->caseModel->findByReport($report_id);
            
            if (!$case) {
                $case = $this->stateService->createInitialCase($report_id, $ccaa, $protocol->getInitialState());
            }

            // Auto-reparar fase si no corresponde a la CCAA actual (ej. cambio de región o error inicial)
            if ($case) {
                $allStates = $protocol->getAllStates();
                if (!in_array($case['current_phase'], $allStates)) {
                    $newPhase = $protocol->getInitialState();
                    $this->caseModel->updatePhase($case['id'], $newPhase);
                    $this->caseModel->updateCcaa($case['id'], $ccaa);
                    $case['current_phase'] = $newPhase;
                    $case['ccaa_code'] = $ccaa;
                }
            }

            $protocol_meta = [
                'ccaa_code' => $protocol->getCcaaCode(),
                'ccaa_name' => $protocol->getCcaaName(),
                'legal_reference' => $protocol->getLegalReference(),
                'timeline_steps' => $protocol->getTimelineSteps(),
                'current_actions' => $protocol->getActionsForState($case['current_phase'] ?? '', $case ?: []),
                'exclusive_tools' => $protocol->getExclusiveTools(),
                'documents' => $protocol->getDocuments()
            ];

            if ($case) {
                $schoolDaysElapsed = $this->protocolService->calculateSchoolDays($case['created_at']);
                $case['school_days_count'] = $schoolDaysElapsed;
                $protocol_meta['deadline_alert'] = $protocol->getDeadlineAlert($case['current_phase'], $schoolDaysElapsed);
            }

            echo json_encode([
                'success' => true, 
                'case' => $case, 
                'ccaa' => $ccaa, 
                'protocol_meta' => $protocol_meta
            ]);
        } catch (\Throwable $e) {
            error_log("Error en getCaseData: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function changePhase($id): void
    {
        header('Content-Type: application/json');
        if (!\App\Core\Auth::hasRole(['orientador', 'direccion', 'admin']) && !\App\Core\Auth::isCocobe()) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado.']);
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
}

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
        // Suprimir errores HTML para que NUNCA se contaminen las respuestas JSON
        $prevDisplayErrors = ini_get('display_errors');
        ini_set('display_errors', '0');
        
        ob_start();
        try {
            header('Content-Type: application/json');
            $report_id = (int)$report_id;
            $ccaa = Config::get('ccaa_code', 'generic');
            
            $protocol = ProtocolFactory::make($ccaa);

            $case = $this->caseModel->findByReport($report_id);
            
            if (!$case && $protocol->isFullyImplemented()) {
                $case = $this->stateService->createInitialCase($report_id, $ccaa, $protocol->getInitialState());
            }

            // Auto-reparar fase si no corresponde a la CCAA actual (ej. cambio de región o error inicial)
            if ($case && $protocol->isFullyImplemented()) {
                $allStates = $protocol->getAllStates();
                if (!in_array($case['current_phase'], $allStates)) {
                    $newPhase = $protocol->getInitialState();
                    try {
                        $this->caseModel->updatePhase($case['id'], $newPhase);
                        $this->caseModel->updateCcaa($case['id'], $ccaa);
                        $case['current_phase'] = $newPhase;
                        $case['ccaa_code'] = $ccaa;
                    } catch (\Throwable $repairError) {
                        // Si falla la auto-reparación (ej. por bloqueo Barnahus), mantenemos la fase actual
                        error_log("Error auto-reparando caso protocol: " . $repairError->getMessage());
                    }
                }
            }

            $timeline = $protocol->isFullyImplemented() ? $protocol->getTimelineSteps() : [];
            $activeStepIndex = -1;
            $currentPhase = $case['current_phase'] ?? '';

            if ($protocol->isFullyImplemented()) {
                // Intentar encontrar el índice exacto
                $activeStepIndex = array_search($currentPhase, array_column($timeline, 'key'));

                // Fallbacks específicos por protocolo si no se encuentra el estado exacto en el timeline
                if ($activeStepIndex === false || $activeStepIndex === -1) {
                    if ($ccaa === 'ARA') {
                        if ($currentPhase === 'protocolo_no_iniciado') $activeStepIndex = 0;
                        elseif (in_array($currentPhase, ['contrato_conducta', 'expediente_disciplinario'])) $activeStepIndex = 3;
                        elseif ($currentPhase === 'reabierto') $activeStepIndex = 4;
                    }
                }
            }

            $protocol_meta = [
                'ccaa_code' => $protocol->getCode(),
                'ccaa_name' => $protocol->getName(),
                'is_fully_implemented' => $protocol->isFullyImplemented(),
                'manage_url' => $protocol->getManageUrl($report_id),
                'timeline_steps' => $timeline,
                'active_step_index' => $activeStepIndex !== false ? $activeStepIndex : -1,
                'current_actions' => $protocol->isFullyImplemented() ? $protocol->getActionsForState($currentPhase, $case ?: []) : [],
                'exclusive_tools' => $protocol->isFullyImplemented() ? $protocol->getExclusiveTools() : [],
                'documents' => $protocol->isFullyImplemented() ? $protocol->getDocuments() : []
            ];

            if ($case) {
                $schoolDaysElapsed = $this->protocolService->calculateSchoolDays($case['created_at']);
                $case['school_days_count'] = $schoolDaysElapsed;
                
                // Solo si el protocolo está implementado buscamos alertas
                if ($protocol->isFullyImplemented() && method_exists($protocol, 'getDeadlineAlert')) {
                    $protocol_meta['deadline_alert'] = $protocol->getDeadlineAlert($case['current_phase'], $schoolDaysElapsed);
                }
            }

            $response = [
                'success' => true, 
                'case' => $case, 
                'ccaa' => $ccaa, 
                'protocol_meta' => $protocol_meta
            ];

            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                throw new \Exception("Error encoding JSON: " . json_last_error_msg());
            }
            
            // CAPTURAR RUIDO PARA DEBUG
            $noise = ob_get_contents();
            if (!empty($noise)) {
                file_put_contents(__DIR__ . '/../../storage/logs/debug_protocol.log', "NOISE DETECTED: " . $noise . "\n", FILE_APPEND);
            }

            // Forzar limpieza absoluta de cualquier aviso PHP previo (incluso fuera de esta clase)
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            header('Content-Type: application/json');
            echo $json;
            exit;
        } catch (\Throwable $e) {
            $errorMsg = "EXCEPTION: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString();
            file_put_contents(__DIR__ . '/../../storage/logs/debug_protocol.log', $errorMsg . "\n", FILE_APPEND);
            
            while (ob_get_level()) {
                ob_end_clean();
            }
            error_log("Error en getCaseData: " . $e->getMessage());
            if (!headers_sent()) http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function changePhase($id): void
    {
        \App\Core\Csrf::validateRequest();
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
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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

<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Lang;
use App\Core\View;
use App\Core\Database;
use App\Models\ProtocolCase;
use App\Models\Report;
use App\Models\SecurityMap;
use App\Models\ProtocolFollowup;
use App\Models\ReportMessage;
use App\Services\ProtocolStateService;
use App\Services\RevaExportService;
use App\Services\ProtocolService;

class ProtocolWorkflowController
{
    private ProtocolCase $caseModel;
    private Report $reportModel;
    private SecurityMap $mapModel;
    private ProtocolFollowup $followupModel;
    private ReportMessage $messageModel;
    private ProtocolStateService $stateService;
    private RevaExportService $revaService;
    private ProtocolService $protocolService;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->reportModel = new Report();
        $this->mapModel = new SecurityMap();
        $this->followupModel = new ProtocolFollowup();
        $this->messageModel = new ReportMessage();
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
            
            $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);

            $case = $this->caseModel->findByReport($report_id);
            
            if (!$case && $protocol->getInitialState() !== 'no_implementado') {
                $case = $this->stateService->createInitialCase($report_id, $ccaa, $protocol->getInitialState());
            }

            $protocol_meta = [
                'ccaa_name' => $protocol->getCcaaName(),
                'legal_reference' => $protocol->getLegalReference(),
                'timeline_steps' => $protocol->getTimelineSteps(),
                'current_actions' => [],
                'exclusive_tools' => $protocol->getExclusiveTools(),
                'deadline_alert' => null
            ];

            if ($case) {
                if ($case['current_phase'] === ProtocolCase::PHASE_BARNAHUS || $case['severity_preliminary'] === 'violencia_sexual') {
                    $this->protocolService->logSensitiveAccess($case['id']);
                }

                $case['followups'] = $this->followupModel->findByCase($case['id']);
                $case['closure_checks'] = json_decode($case['closure_checks'] ?? '{}', true);
                $case['communications'] = json_decode($case['communications'] ?? '{}', true);
                
                $schoolDaysElapsed = 0;
                if ($ccaa === 'aragon') {
                    $schoolDaysElapsed = $this->protocolService->calculateSchoolDays($case['created_at']);
                    $case['school_days_count'] = $schoolDaysElapsed;
                }

                $protocol_meta['current_actions'] = $protocol->getActionsForState($case['current_phase'], $case);
                $protocol_meta['deadline_alert'] = $protocol->getDeadlineAlert($case['current_phase'], $schoolDaysElapsed);
            }

            echo json_encode(['success' => true, 'case' => $case, 'ccaa' => $ccaa, 'protocol_meta' => $protocol_meta]);
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

    public function addFollowup($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        
        $success = $this->followupModel->create([
            'protocol_case_id' => $id,
            'target_type'      => $data['target_type'],
            'session_date'     => $data['session_date'],
            'notes'            => $data['notes'],
            'created_by'       => Auth::id()
        ]);

        if ($success) {
            $case = $this->caseModel->find($id);
            $this->logAction($case['report_id'], "Nou seguiment registrat: " . strtoupper($data['target_type']));
        }
        echo json_encode(['success' => $success]);
    }

    public function uploadEvidence($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        
        if (!isset($_FILES['evidence'])) {
            echo json_encode(['success' => false, 'error' => 'No se recibió archivo.']);
            return;
        }

        $file = $_FILES['evidence'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'error' => 'Extensión no permitida.']);
            return;
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = __DIR__ . '/../../storage/evidence/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO protocol_evidence (protocol_case_id, filename, original_name, mime_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $newName, $file['name'], $file['type'], Auth::id()]);
            $this->logAction($id, "Nova evidència pujada en custòdia: " . $file['name']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error moviendo el archivo.']);
        }
    }

    public function exportTemplate($id, $templateName): void
    {
        $case = $this->caseModel->find((int)$id);
        if (!$case) die("Cas no trobat");
        
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        $schoolName = Config::get('school_name', 'Aura PDP');

        View::render("protocol/templates/cataluna/{$templateName}", [
            'case' => $case,
            'report' => $report,
            'schoolName' => $schoolName
        ], 'app');
    }
    
    public function exportPdf($id): void
    {
        $case = $this->caseModel->find((int)$id);
        if (!$case) die("Cas no trobat");
        
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        $map = $this->mapModel->findByCase($case['id']);
        $followups = $this->followupModel->findByCase($case['id']);
        $case['closure_checks'] = json_decode($case['closure_checks'] ?? '{}', true);
        $case['communications'] = json_decode($case['communications'] ?? '{}', true);
        
        $this->logAction($case['report_id'], "Generat informe PDF consolidat.");
        View::render('protocol/pdf_export', ['case' => $case, 'report' => $report, 'map' => $map, 'followups' => $followups], 'app');
    }

    public function getSecurityMap($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $map = $this->mapModel->findByCase($id);
        if ($map) $map['mesures_urgencia'] = json_decode($map['mesures_urgencia'] ?? '[]', true);
        echo json_encode(['success' => true, 'map' => $map]);
    }

    public function saveSecurityMapFull($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        $payload = $data['map'] ?? [];
        $payload['protocol_case_id'] = $id;
        
        $success = $this->mapModel->upsert($payload);
        if ($success) {
            $case = $this->caseModel->find($id);
            $this->logAction($case['report_id'], "Mapa de Seguretat actualitzat.");
        }
        echo json_encode(['success' => $success]);
    }

    public function updateComms($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        $case = $this->caseModel->find($id);
        $success = $this->caseModel->updateCommunications($id, $data['comms'] ?? []);
        
        if ($success) {
            $this->logAction($case['report_id'], "Actualització de comunicacions oficials.");
        }
        echo json_encode(['success' => $success]);
    }

    public function updateClosure($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        $case = $this->caseModel->find($id);
        $success = $this->caseModel->updateClosureChecks($id, $data['checks'] ?? []);
        
        if ($success) {
            $this->logAction($case['report_id'], "Checklist de tancament actualitzat.");
        }
        echo json_encode(['success' => $success]);
    }

    private function logAction(int $reportId, string $message): void
    {
        $this->messageModel->create([
            'report_id' => $reportId,
            'sender_id' => Auth::id(),
            'message' => "📋 [REGISTRE LEGAL] " . $message,
            'is_internal' => 1
        ]);
    }
}

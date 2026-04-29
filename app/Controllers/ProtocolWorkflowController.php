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

class ProtocolWorkflowController
{
    private ProtocolCase $caseModel;
    private Report $reportModel;
    private SecurityMap $mapModel;
    private ProtocolFollowup $followupModel;
    private ReportMessage $messageModel;
    private ProtocolStateService $stateService;
    private RevaExportService $revaService;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->reportModel = new Report();
        $this->mapModel = new SecurityMap();
        $this->followupModel = new ProtocolFollowup();
        $this->messageModel = new ReportMessage();
        $this->stateService = new ProtocolStateService();
        $this->revaService = new RevaExportService();
    }

    private function logAccess(int $caseId): void
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

    private function logAction(int $reportId, string $message): void
    {
        $this->messageModel->create([
            'report_id' => $reportId,
            'sender_id' => Auth::id(),
            'message' => "📋 [REGISTRE LEGAL] " . $message,
            'is_internal' => 1
        ]);
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
                $deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));
                $this->caseModel->create([
                    'report_id' => $report_id,
                    'ccaa_code' => $ccaa,
                    'deadline_at' => $deadline
                ]);
                $case = $this->caseModel->findByReport($report_id);
                $this->logAction($report_id, "Protocol de Catalunya activat. Termini de valoració: 48h.");
            }

            if ($case) {
                // Audit Trail para casos Barnahus
                if ($case['current_phase'] === ProtocolCase::PHASE_BARNAHUS || $case['severity_preliminary'] === 'violencia_sexual') {
                    $this->logAccess($case['id']);
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
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!Auth::hasRole(['orientador', 'direccion', 'admin']) && !Auth::isCocobe()) {
            echo json_encode(['success' => false, 'error' => 'No tienes permiso.']);
            return;
        }

        try {
            $success = $this->stateService->transitionTo($id, $data['phase'] ?? '');
            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function classify($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $success = $this->stateService->classify($id, $data['severity'] ?? '', $data['classification'] ?? '');
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
        
        $summary = $this->revaService->generateSummary($case, $report);
        echo json_encode(['success' => true, 'summary' => $summary]);
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
        
        // Validación básica de extensiones
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
}

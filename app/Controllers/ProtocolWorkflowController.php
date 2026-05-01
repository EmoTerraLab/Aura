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
use App\Models\RestorativePractice;
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
    private RestorativePractice $restorativeModel;
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
        $this->restorativeModel = new RestorativePractice();
        $this->stateService = new ProtocolStateService();
        $this->revaService = new RevaExportService();
        $this->protocolService = new ProtocolService();
    }

    public function assignTeam($id): void
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Funcionalidad de equipo no disponible en esta versión.']);
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
            $this->logAction($case['report_id'], "Nuevo seguimiento registrado: " . strtoupper($data['target_type']));
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
            
            $case = $this->caseModel->find($id);
            if ($case) {
                $this->logAction($case['report_id'], "Nova evidència pujada en custòdia: " . $file['name']);
            }
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error moviendo el archivo.']);
        }
    }

    public function exportTemplate($id, $templateName): void
    {
        $case = $this->caseModel->find((int)$id);
        if (!$case) die("Caso no encontrado");
        
        $ccaa = $case['ccaa_code'];
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        $schoolName = Config::get('school_name', 'Aura');

        View::render("protocol/templates/{$ccaa}/{$templateName}", [
            'case' => $case,
            'report' => $report,
            'schoolName' => $schoolName
        ], 'app');
    }
    
    public function exportPdf($id): void
    {
        $case = $this->caseModel->find((int)$id);
        if (!$case) die("Caso no encontrado");
        
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        $map = $this->mapModel->findByCase($case['id']);
        $followups = $this->followupModel->findByCase($case['id']);
        $case['closure_checks'] = json_decode($case['closure_checks'] ?? '{}', true);
        $case['communications'] = json_decode($case['communications'] ?? '{}', true);
        
        $this->logAction($case['report_id'], "Generado informe PDF consolidado.");
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

    public function saveAcknowledgment(int $id): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $ack = isset($data['acknowledged']) ? (int)$data['acknowledged'] : null;
        
        $success = $this->caseModel->updateAcknowledgment($id, $ack);
        if ($success) {
            $case = $this->caseModel->find($id);
            $msg = ($ack === 1) ? "L'alumne RECONEIX els fets." : "L'alumne NO reconeix els fets.";
            $this->logAction($case['report_id'], $msg);
        }
        echo json_encode(['success' => $success]);
    }

    public function getRestorativeData(int $id): void
    {
        header('Content-Type: application/json');
        $case = $this->caseModel->find($id);
        $practices = $this->restorativeModel->findByCase($id);
        
        echo json_encode([
            'success' => true, 
            'acknowledged' => $case['aggressor_acknowledges_facts'] ?? null,
            'practices' => $practices
        ]);
    }

    public function addRestorativePractice(int $id): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        $practiceData = [
            'protocol_case_id' => $id,
            'practice_type'    => $data['practice_type'],
            'facilitator_id'   => Auth::id(),
            'session_date'     => $data['session_date'],
            'participants'     => $data['participants'],
            'agreements'       => $data['agreements'],
            'status'           => 'pending'
        ];

        $newId = $this->restorativeModel->create($practiceData);
        if ($newId) {
            $case = $this->caseModel->find($id);
            $this->logAction($case['report_id'], "Nova pràctica restaurativa programada: " . strtoupper($data['practice_type']));
        }
        echo json_encode(['success' => (bool)$newId]);
    }

    public function updatePracticeStatus(int $id): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $success = $this->restorativeModel->updateStatus((int)$id, $data['status']);
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

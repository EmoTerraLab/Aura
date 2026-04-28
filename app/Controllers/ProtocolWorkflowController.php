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
use App\Models\ReportMessage;

class ProtocolWorkflowController
{
    private ProtocolCase $caseModel;
    private Report $reportModel;
    private SecurityMap $mapModel;
    private ProtocolFollowup $followupModel;
    private ReportMessage $messageModel;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->reportModel = new Report();
        $this->mapModel = new SecurityMap();
        $this->followupModel = new ProtocolFollowup();
        $this->messageModel = new ReportMessage();
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
            $case = $this->caseModel->findByReport($report_id);
            
            if (!$case) {
                $ccaa = Config::get('ccaa_code');
                if ($ccaa && $ccaa !== '') {
                    $deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));
                    $this->caseModel->create([
                        'report_id' => $report_id,
                        'ccaa_code' => $ccaa,
                        'deadline_at' => $deadline
                    ]);
                    $case = $this->caseModel->findByReport($report_id);
                    $this->logAction($report_id, "Protocol activat automàticament (CCAA: $ccaa). Termini de valoració: 48h.");
                }
            }

            if ($case) {
                $case['followups'] = $this->followupModel->findByCase($case['id']);
                $case['closure_checks'] = json_decode($case['closure_checks'] ?? '{}', true);
                $case['communications'] = json_decode($case['communications'] ?? '{}', true);
            }

            echo json_encode(['success' => true, 'case' => $case]);
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
        $newPhase = $data['phase'] ?? '';

        if (!in_array(Auth::role(), ['orientador', 'direccion', 'admin'])) {
            echo json_encode(['success' => false, 'error' => 'No tienes permiso.']);
            return;
        }

        try {
            $case = $this->caseModel->find($id);
            $oldPhase = $case['current_phase'];
            $success = $this->caseModel->updatePhase($id, $newPhase);
            if ($success) {
                $this->logAction($case['report_id'], "Canvi de fase: de " . strtoupper($oldPhase) . " a " . strtoupper($newPhase));
            }
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
        $severity = $data['severity'] ?? '';
        $classification = $data['classification'] ?? '';

        $case = $this->caseModel->find($id);
        $success = $this->caseModel->updateClassification($id, $severity, $classification);
        
        if ($success) {
            $this->logAction($case['report_id'], "Tipificació actualitzada: Categoria [$classification], Gravetat [$severity]");
            
            // Avanzar automáticamente a la siguiente fase si es un caso normal
            if ($severity !== 'violencia_sexual') {
                $this->caseModel->updatePhase($id, ProtocolCase::PHASE_VALORACION);
                $this->logAction($case['report_id'], "Indicis confirmats. El cas avança a fase de VALORACIÓ.");
            }
        }

        if ($severity === 'violencia_sexual') {
            $this->caseModel->updatePhase($id, ProtocolCase::PHASE_BARNAHUS);
            $this->logAction($case['report_id'], "🔒 BARNAHUS ACTIVAT: Pas directe a CREURE i PROTEGIR. Diagnosi interna bloquejada.");
        }
        echo json_encode(['success' => $success]);
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
            $this->logAction($case['report_id'], "Nou seguiment registrat amb: " . strtoupper($data['target_type']));
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
            $this->logAction($case['report_id'], "Mapa de Seguretat actualitzat/dissenyat.");
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

    public function exportPdf($id): void
    {
        $case = $this->caseModel->find((int)$id);
        if (!$case) die("Cas no trobat");
        $this->logAction($case['report_id'], "S'ha generat/exportat l'informe oficial en PDF.");
        
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        $map = $this->mapModel->findByCase($case['id']);
        $followups = $this->followupModel->findByCase($case['id']);
        $case['closure_checks'] = json_decode($case['closure_checks'] ?? '{}', true);
        $case['communications'] = json_decode($case['communications'] ?? '{}', true);
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

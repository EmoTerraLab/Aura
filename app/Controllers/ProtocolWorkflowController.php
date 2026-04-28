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

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->reportModel = new Report();
        $this->mapModel = new SecurityMap();
        $this->followupModel = new ProtocolFollowup();
    }

    /**
     * GET /api/protocol/case/{report_id}
     */
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
            $success = $this->caseModel->updatePhase($id, $newPhase);
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
        $success = $this->caseModel->updateClassification($id, $severity, $classification);
        
        if ($severity === 'violencia_sexual') {
            $this->caseModel->updatePhase($id, ProtocolCase::PHASE_BARNAHUS);
            $case = $this->caseModel->find($id);
            $messageModel = new ReportMessage();
            $messageModel->create([
                'report_id' => $case['report_id'],
                'sender_id' => Auth::id(),
                'message' => "🔒 [BARNAHUS] Pas: CREURE i PROTEGIR. Derivació obligatòria.",
                'is_internal' => 1
            ]);
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
        echo json_encode(['success' => $success]);
    }

    public function updateClosure($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        $success = $this->caseModel->updateClosureChecks($id, $data['checks'] ?? []);
        echo json_encode(['success' => $success]);
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

        View::render('protocol/pdf_export', [
            'case' => $case,
            'report' => $report,
            'map' => $map,
            'followups' => $followups
        ], 'app'); // Usamos el layout app pero el CSS ocultará menús al imprimir
    }

    public function saveSecurityMapFull($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        $payload = $data['map'] ?? [];
        $payload['protocol_case_id'] = $id;
        $success = $this->mapModel->upsert($payload);
        echo json_encode(['success' => $success]);
    }

    public function getSecurityMap($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $map = $this->mapModel->findByCase($id);
        if ($map) $map['mesures_urgencia'] = json_decode($map['mesures_urgencia'] ?? '[]', true);
        echo json_encode(['success' => true, 'map' => $map]);
    }

    public function updateComms($id): void
    {
        header('Content-Type: application/json');
        $id = (int)$id;
        $data = json_decode(file_get_contents('php://input'), true);
        $success = $this->caseModel->updateCommunications($id, $data['comms'] ?? []);
        echo json_encode(['success' => $success]);
    }
}

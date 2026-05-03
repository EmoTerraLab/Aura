<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Core\Database;
use App\Core\Config;
use App\Models\Report;
use App\Models\AragonProtocolCase;
use App\Models\AragonAnnex;

class AragonProtocolController
{
    private Report $reportModel;
    private AragonProtocolCase $caseModel;
    private AragonAnnex $annexModel;

    public function __construct()
    {
        if (Config::get('ccaa_code') !== 'ARA') {
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'El protocolo de Aragón no está habilitado en este centro']);
            } else {
                http_response_code(403);
                echo 'El protocolo de Aragón no está habilitado en este centro';
            }
            exit;
        }
        $this->reportModel = new Report();
        $this->caseModel = new AragonProtocolCase();
        $this->annexModel = new AragonAnnex();
    }

    public function createAnexo1a(): void
    {
        View::render('protocol/aragon/anexo_1a', ['title' => 'Anexo I-a'], 'app');
    }

    public function storeAnexo1a(): void
    {
        Csrf::validateRequest();
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $reportId = $this->reportModel->createStaffReport([
                'title' => 'Protocolo Aragón',
                'description' => $_POST['facts_summary'] ?? '',
                'target_student_id' => null,
                'created_by' => Auth::id(),
                'created_by_role' => Auth::role(),
                'category' => 'acoso',
                'urgency' => isset($_POST['is_sexual_violence']) ? 'urgente' : 'alta',
                'is_confidential' => 1,
                'status' => 'open'
            ]);
            $isSexualViolence = isset($_POST['is_sexual_violence']) ? 1 : 0;
            $caseId = $this->caseModel->createCase([
                'report_id' => $reportId,
                'status' => $isSexualViolence ? AragonProtocolCase::STATE_VIOLENCIA_SEXUAL_ACTIVA : AragonProtocolCase::STATE_COMUNICACION_RECIBIDA,
                'is_sexual_violence' => $isSexualViolence
            ]);
            $annexContent = [
                'reporter_info' => ['role' => $_POST['reporter_role'] ?? 'anonimo', 'name' => $_POST['reporter_name'] ?? 'Anónimo'],
                'victim_data' => $_POST['victim_data'] ?? '',
                'aggressor_data' => $_POST['aggressor_data'] ?? '',
                'facts_summary' => $_POST['facts_summary'] ?? '',
                'is_sexual_violence' => (bool)$isSexualViolence,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            $this->annexModel->createAnnex($caseId, 'I-a', $annexContent, Auth::id());
            $db->commit();
            header('Location: /staff/inbox?success=annex_filed');
            exit;
        } catch (\Exception $e) {
            $db->rollBack();
            header('Location: /protocol/aragon/anexo-1a?error=processing_failed');
            exit;
        }
    }

    public function showCaseByReport(int $reportId): void
    {
        $case = $this->caseModel->findByReport($reportId);
        if (!$case) {
            // Si no existe el caso, lo creamos (Anexo I-a ya existe si llegamos aquí normalmente)
            // O redirigimos al formulario de creación del Anexo I-a si es necesario
            header("Location: /protocol/aragon/anexo-1a?report_id=$reportId");
            exit;
        }
        header("Location: /protocol/aragon/case/" . $case['id']);
        exit;
    }

    public function showCase(int $id): void
    {
        $case = $this->caseModel->find($id);
        if (!$case) {
            http_response_code(404);
            echo "Expediente no encontrado.";
            return;
        }
        $annexes = $this->annexModel->findByCase($id);
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, name FROM users WHERE role != 'alumno'");
        $staff = $stmt->fetchAll();
        View::render('protocol/aragon/case_detail', ['title' => 'Gestión Aragón', 'case' => $case, 'annexes' => $annexes, 'staff' => $staff], 'app');
    }

    public function processDecision(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        if (Auth::role() !== 'admin') { echo json_encode(['success' => false, 'error' => 'Solo Dirección puede decidir.']); return; }
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $decision = $_POST['decision'] ?? 'no_iniciar';
            $newStatus = ($decision === 'iniciar') ? AragonProtocolCase::STATE_PROTOCOLO_INICIADO : AragonProtocolCase::STATE_PROTOCOLO_NO_INICIADO;
            $this->annexModel->createAnnex($id, 'I-b', ['decision' => $decision, 'motivation' => $_POST['justification'] ?? '', 'date' => date('Y-m-d H:i:s')], Auth::id());
            if ($decision === 'iniciar') {
                $this->annexModel->createAnnex($id, 'II', ['measures' => $_POST['measures'] ?? [], 'other_measures' => $_POST['other_measures'] ?? ''], Auth::id());
            }
            
            $case = $this->caseModel->find($id);
            $stateService = new \App\Services\ProtocolStateService();
            $stateService->transitionTo($case['report_id'], $newStatus);
            
            $db->commit();
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function constituteTeam(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $this->annexModel->createAnnex($id, 'III', ['team_ids' => $_POST['team_ids'] ?? [], 'constitution_date' => date('Y-m-d')], Auth::id());
            
            $case = $this->caseModel->find($id);
            $stateService = new \App\Services\ProtocolStateService();
            $stateService->transitionTo($case['report_id'], AragonProtocolCase::STATE_EN_VALORACION);
            
            $db->commit();
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function addInterview(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("SELECT id, content FROM aragon_protocol_annexes WHERE protocol_case_id = ? AND annex_type = 'V' LIMIT 1");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $interviews = $existing ? json_decode($existing['content'], true) : [];
            $interviews[] = [
                'id' => uniqid('int_'), 'date' => $_POST['date'] ?? date('Y-m-d'), 'profile' => $_POST['profile'] ?? 'familia',
                'attendees' => htmlspecialchars($_POST['attendees'] ?? ''), 'notes' => htmlspecialchars($_POST['notes'] ?? ''),
                'created_by' => Auth::id(), 'created_at' => date('Y-m-d H:i:s')
            ];
            if ($existing) {
                $upd = $db->prepare("UPDATE aragon_protocol_annexes SET content = ? WHERE id = ?");
                $upd->execute([json_encode($interviews, JSON_UNESCAPED_UNICODE), $existing['id']]);
            } else {
                $this->annexModel->createAnnex($id, 'V', $interviews, Auth::id());
            }
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function saveIndicators(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $indicators = $_POST['indicators'] ?? [];
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id FROM aragon_protocol_annexes WHERE protocol_case_id = ? AND annex_type = 'VI' LIMIT 1");
            $stmt->execute([$id]);
            $existingId = $stmt->fetchColumn();
            if ($existingId) {
                $upd = $db->prepare("UPDATE aragon_protocol_annexes SET content = ? WHERE id = ?");
                $upd->execute([json_encode($indicators, JSON_UNESCAPED_UNICODE), $existingId]);
            } else {
                $this->annexModel->createAnnex($id, 'VI', $indicators, Auth::id());
            }
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function processResolution(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        if (Auth::role() !== 'admin') { echo json_encode(['success' => false, 'error' => 'Permiso denegado.']); return; }
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $res = [
                'characteristics' => ['desigualdad' => $_POST['char_desigualdad'] ?? '', 'recurrencia' => $_POST['char_recurrencia'] ?? '', 'intencionalidad' => $_POST['char_intencionalidad'] ?? ''],
                'conclusion' => $_POST['conclusion'] ?? 'no_acreditado', 'justification' => htmlspecialchars($_POST['justification'] ?? ''),
                'measures' => htmlspecialchars($_POST['measures'] ?? ''), 'date_resolution' => date('Y-m-d H:i:s'), 'signed_by' => Auth::id()
            ];
            $this->annexModel->createAnnex($id, 'VIII', $res, Auth::id());
            
            $case = $this->caseModel->find($id);
            $stateService = new \App\Services\ProtocolStateService();
            $stateService->transitionTo($case['report_id'], AragonProtocolCase::STATE_VALORADO);
            
            $db->commit();
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function startFollowUp(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $case = $this->caseModel->find($id);
            $stateService = new \App\Services\ProtocolStateService();
            $success = $stateService->transitionTo($case['report_id'], AragonProtocolCase::STATE_EN_SEGUIMIENTO);
            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function addFollowUp(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        $db = Database::getInstance();
        try {
            $case = $this->caseModel->find($id);
            if ($case['status'] === AragonProtocolCase::STATE_CERRADO) throw new \Exception("Caso cerrado.");
            $stmt = $db->prepare("SELECT id, content FROM aragon_protocol_annexes WHERE protocol_case_id = ? AND annex_type = 'IX' LIMIT 1");
            $stmt->execute([$id]);
            $annex = $stmt->fetch();
            $history = $annex ? json_decode($annex['content'], true) : [];
            $history[] = [
                'id' => uniqid('seq_'), 'date' => $_POST['date'] ?? date('Y-m-d'), 'situation_status' => $_POST['situation_status'] ?? 'sin_incidentes',
                'family_info' => htmlspecialchars($_POST['family_info'] ?? ''), 'observations' => htmlspecialchars($_POST['observations'] ?? ''),
                'created_by' => Auth::id(), 'timestamp' => time()
            ];
            if ($annex) {
                $upd = $db->prepare("UPDATE aragon_protocol_annexes SET content = ? WHERE id = ?");
                $upd->execute([json_encode($history, JSON_UNESCAPED_UNICODE), $annex['id']]);
            } else { $this->annexModel->createAnnex($id, 'IX', $history, Auth::id()); }
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { echo json_encode(['success' => false, 'error' => $e->getMessage()]); }
    }

    public function closeCase(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        if (Auth::role() !== 'admin') { echo json_encode(['success' => false, 'error' => 'Solo Dirección.']); return; }
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $closureData = ['evolution_favorable' => isset($_POST['evolution_favorable']), 'can_be_closed' => isset($_POST['can_be_closed']), 'justification' => htmlspecialchars($_POST['justification'] ?? ''), 'closure_date' => date('Y-m-d H:i:s')];
            $this->annexModel->createAnnex($id, 'X', $closureData, Auth::id());
            
            $case = $this->caseModel->find($id);
            $stateService = new \App\Services\ProtocolStateService();
            $stateService->transitionTo($case['report_id'], AragonProtocolCase::STATE_CERRADO);
            
            $db->commit();
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function exportAnnex(int $id, string $type): void
    {
        $case = $this->caseModel->find($id);
        if (!$case) {
            http_response_code(404);
            echo "Expediente no encontrado.";
            return;
        }
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM aragon_protocol_annexes WHERE protocol_case_id = ? AND annex_type = ? LIMIT 1");
        $stmt->execute([$id, $type]);
        $annex = $stmt->fetch();
        if (!$annex) {
            http_response_code(404);
            echo "El Anexo solicitado (" . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . ") no ha sido generado aún.";
            return;
        }
        $report = $this->reportModel->find($case['report_id']);
        View::render('protocol/aragon/print_annex', ['case' => $case, 'report' => $report, 'annex' => $annex, 'type' => $type, 'content' => json_decode($annex['content'], true), 'school_name' => Config::get('school_name', 'Centro')], null);
    }
}

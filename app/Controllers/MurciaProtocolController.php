<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Core\Database;
use App\Core\Config;
use App\Models\Report;
use App\Models\ProtocolCase;
use App\Models\MurciaProtocolCase;
use App\Models\MurciaAnnex;

class MurciaProtocolController
{
    private Report $reportModel;
    private MurciaProtocolCase $caseModel;
    private MurciaAnnex $annexModel;

    public function __construct()
    {
        if (Config::get('ccaa_code') !== 'MUR') {
            http_response_code(403);
            die('El protocolo de Murcia no está habilitado');
        }
        $this->reportModel = new Report();
        $this->caseModel = new MurciaProtocolCase();
        $this->annexModel = new MurciaAnnex();
    }

    public function showCase(int $id): void
    {
        // El ID que viene puede ser el del caso de Murcia o el report_id
        $case = $this->caseModel->find($id);
        if (!$case) {
            $case = $this->caseModel->findByReportId($id);
        }
        
        if (!$case) {
            http_response_code(404);
            echo "Expediente no encontrado.";
            return;
        }
        $annexes = $this->annexModel->findByCase($case['id']);
        
        $db = Database::getInstance();
        $staff = $db->query("SELECT id, name FROM users WHERE role != 'alumno'")->fetchAll();
        
        View::render('protocol/murcia/case_detail', [
            'title' => 'Gestión Murcia',
            'case' => $case,
            'annexes' => $annexes,
            'staff' => $staff
        ], 'app');
    }

    public function storeDesignation(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $coordinatorId = (int)$_POST['coordinator_id'];
            $teamMembers = $_POST['team_members'] ?? [];
            
            $this->caseModel->updateCoordinator($id, $coordinatorId);
            $this->annexModel->createAnnex($id, 'designacio', [
                'coordinator_id' => $coordinatorId,
                'team_members' => $teamMembers,
                'date' => date('Y-m-d')
            ], Auth::id());
            
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function storeUrgencyMeasures(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $measures = $_POST['measures'] ?? '';
            $this->annexModel->createAnnex($id, 'medides_urgencia', [
                'measures' => $measures,
                'date' => date('Y-m-d H:i:s')
            ], Auth::id());
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function storeAnexoI(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $this->annexModel->createAnnex($id, 'anexo_i', [
                'sent_to_inspeccion' => true,
                'sent_to_ordenacion' => true,
                'date' => date('Y-m-d H:i:s')
            ], Auth::id());
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function addInterview(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $type = $_POST['type'] ?? 'victima';
            $notes = $_POST['notes'] ?? '';
            $this->annexModel->createAnnex($id, "entrevista_$type", [
                'notes' => $notes,
                'date' => $_POST['date'] ?? date('Y-m-d')
            ], Auth::id());
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function storeAnexoIV(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $content = $_POST['content'] ?? '';
            $this->annexModel->createAnnex($id, 'anexo_iv', [
                'report_content' => $content,
                'date' => date('Y-m-d')
            ], Auth::id());
            $this->caseModel->updateStatus($id, ProtocolCase::PHASE_MUR_VALORACION);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function storeValuation(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $conclusion = $_POST['conclusion'] ?? 'no_evidencias';
            $measures = $_POST['measures'] ?? [];
            $actaNotes = $_POST['acta_notes'] ?? '';
            
            $this->annexModel->createAnnex($id, 'acta_reunio', [
                'notes' => $actaNotes,
                'date' => date('Y-m-d H:i:s')
            ], Auth::id());

            $this->annexModel->createAnnex($id, 'anexo_v', [
                'conclusion' => $conclusion,
                'measures' => $measures,
                'date' => date('Y-m-d')
            ], Auth::id());
            
            if ($conclusion === 'no_evidencias') {
                $this->annexModel->createAnnex($id, 'anexo_vi', [
                    'followup_plan' => $_POST['followup_plan'] ?? '',
                    'date' => date('Y-m-d')
                ], Auth::id());
            }
            
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function storeLegalCommunication(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $ageGroup = $_POST['age_group'] ?? '';
            $entity = match($ageGroup) {
                'menor_14' => 'Servicios Sociales',
                'mayor_14' => 'Fiscalía de Menores',
                'adulto' => 'Fuerzas y Cuerpos de Seguridad del Estado',
                default => 'Otras autoridades'
            };
            
            $this->annexModel->createAnnex($id, 'comunicacio_legal', [
                'age_group' => $ageGroup,
                'entity' => $entity,
                'date' => date('Y-m-d H:i:s'),
                'notes' => $_POST['notes'] ?? ''
            ], Auth::id());
            
            echo json_encode(['success' => true, 'entity' => $entity]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

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
use App\Models\GaliciaProtocolCase;
use App\Models\GaliciaAnnex;
use App\Services\ProtocolStateService;
use App\Services\Protocol\GaliciaProtocol;

class GaliciaProtocolController
{
    private Report $reportModel;
    private GaliciaProtocolCase $caseModel;
    private GaliciaAnnex $annexModel;

    public function __construct()
    {
        if (Config::get('ccaa_code') !== 'GAL') {
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'O protocolo de Galicia non está habilitado neste centro']);
            } else {
                http_response_code(403);
                echo 'O protocolo de Galicia non está habilitado neste centro';
            }
            exit;
        }
        $this->reportModel = new Report();
        $this->caseModel = new GaliciaProtocolCase();
        $this->annexModel = new GaliciaAnnex();
    }

    // ─────────────────────────────────────────────────
    // Visualización del caso
    // ─────────────────────────────────────────────────

    public function showCase(int $id): void
    {
        $case = $this->caseModel->find($id);
        if (!$case) {
            $case = $this->caseModel->findByReportId($id);
        }

        if (!$case) {
            http_response_code(404);
            echo "Expediente non atopado.";
            return;
        }

        $annexes = $this->annexModel->findByCase($case['id']);

        $protocolCaseModel = new ProtocolCase();
        $generalCase = $protocolCaseModel->findByReport($case['report_id']);

        $db = Database::getInstance();
        $staff = $db->query("SELECT id, name FROM users WHERE role != 'alumno'")->fetchAll();

        View::render('protocol/galicia/case_detail', [
            'title'       => 'Xestión Galicia',
            'case'        => $case,
            'generalCase' => $generalCase,
            'annexes'     => $annexes,
            'staff'       => $staff
        ], 'app');
    }

    // ─────────────────────────────────────────────────
    // Anexo genérico: guardar cualquier anexo (1-16)
    // ─────────────────────────────────────────────────

    public function storeAnnex(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $annexType = $_POST['annex_type'] ?? '';
            if (empty($annexType) || !preg_match('/^anexo_\d{1,2}$/', $annexType)) {
                echo json_encode(['success' => false, 'message' => 'Tipo de anexo non válido.']);
                return;
            }

            // Recoger contenido del formulario (excepto campos de control)
            $content = $_POST;
            unset($content['csrf_token'], $content['annex_type']);

            $this->annexModel->createAnnex($id, $annexType, $content, Auth::id());
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────
    // Herramienta exclusiva: Medidas Urxentes
    // ─────────────────────────────────────────────────

    public function storeMedidasUrxentes(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $measures = $_POST['measures'] ?? [];
            $details = htmlspecialchars($_POST['measures_details'] ?? '', ENT_QUOTES, 'UTF-8');
            $familiasInformadas = isset($_POST['familias_informadas']);

            $this->annexModel->createAnnex($id, 'medidas_urxentes', [
                'measures'             => $measures,
                'measures_details'     => $details,
                'familias_informadas'  => $familiasInformadas,
                'date'                 => date('Y-m-d H:i:s')
            ], Auth::id());

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────
    // Herramienta exclusiva: Actuación Ciberacoso
    // ─────────────────────────────────────────────────

    public function storeCiberacoso(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        try {
            $platforms     = $_POST['platforms'] ?? [];
            $agresionType  = htmlspecialchars($_POST['agresion_type'] ?? '', ENT_QUOTES, 'UTF-8');
            $evidences     = $_POST['evidences'] ?? [];
            $derivacion    = isset($_POST['derivacion_fcse']);
            $notes         = htmlspecialchars($_POST['technical_notes'] ?? '', ENT_QUOTES, 'UTF-8');

            $this->annexModel->createAnnex($id, 'actuacion_ciberacoso', [
                'platforms'       => $platforms,
                'agresion_type'   => $agresionType,
                'evidences'       => $evidences,
                'derivacion_fcse' => $derivacion,
                'technical_notes' => $notes,
                'date'            => date('Y-m-d H:i:s')
            ], Auth::id());

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────
    // Transición de fase (delegada al servicio central)
    // ─────────────────────────────────────────────────

    public function transitionState(int $id): void
    {
        Csrf::validateRequest();
        header('Content-Type: application/json');

        if (!Auth::hasRole(['orientador', 'direccion', 'admin'])) {
            echo json_encode(['success' => false, 'message' => 'Permiso denegado.']);
            return;
        }

        try {
            $newPhase = $_POST['new_phase'] ?? '';
            if (empty($newPhase)) {
                echo json_encode(['success' => false, 'message' => 'Fase destino non especificada.']);
                return;
            }

            $galiciaCase = $this->caseModel->find($id);
            if (!$galiciaCase) {
                echo json_encode(['success' => false, 'message' => 'Caso non atopado.']);
                return;
            }

            // Actualizar tabla propia de Galicia
            $this->caseModel->updateStatus($id, $newPhase);

            // Sincronizar con tabla genérica protocol_cases via servicio centralizado
            $stateService = new ProtocolStateService();
            $stateService->transitionTo($galiciaCase['report_id'], $newPhase);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────
    // Exportar/imprimir anexo
    // ─────────────────────────────────────────────────

    public function exportAnnex(int $id, string $type): void
    {
        $case = $this->caseModel->find($id);
        if (!$case) {
            http_response_code(404);
            echo "Expediente non atopado.";
            return;
        }

        $annex = $this->annexModel->findLatestByType($id, $type);
        if (!$annex) {
            http_response_code(404);
            echo "O documento solicitado (" . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . ") non foi xerado aínda.";
            return;
        }

        $content = json_decode($annex['content'], true) ?: [];

        $db = Database::getInstance();
        $submitterStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
        $submitterStmt->execute([$annex['submitted_by']]);
        $submitter = $submitterStmt->fetch();

        // Validar que o template existe
        $templateName = basename($type);
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $templateName)) {
            http_response_code(400);
            echo "Nome de plantilla non válido.";
            return;
        }

        $templatePath = "protocol/templates/galicia/{$templateName}";
        $fullPath = __DIR__ . '/../Views/' . $templatePath . '.php';

        if (!file_exists($fullPath)) {
            // Fallback: render genérico JSON
            header('Content-Type: application/json');
            echo json_encode(['case' => $case, 'annex' => $annex, 'content' => $content], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return;
        }

        View::render($templatePath, [
            'case'              => $case,
            'annex'             => $annex,
            'type'              => $type,
            'content'           => $content,
            'school_name'       => Config::get('school_name', 'Centro Educativo'),
            'submitted_by_name' => $submitter['name'] ?? 'Persoal autorizado'
        ], null);
    }
}

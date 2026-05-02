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

class ComunidadValencianaController
{
    private Report $reportModel;
    private ProtocolCase $caseModel;

    public function __construct()
    {
        if (Config::get('ccaa_code') !== 'VAL') {
            http_response_code(403);
            echo 'El protocolo de la Comunitat Valenciana no está habilitado';
            exit;
        }
        $this->reportModel = new Report();
        $this->caseModel = new ProtocolCase();
    }

    public function showCase(int $id): void
    {
        // En el modelo general, el ID suele ser el report_id o el id del caso.
        // ProtocolCase::find busca por ID de tabla.
        // Pero a veces recibimos el report_id.
        $case = $this->caseModel->findByReport($id);
        if (!$case) {
            // Intentar buscar por ID directo si no es report_id
            $case = $this->caseModel->find($id);
        }
        
        if (!$case) {
            http_response_code(404);
            echo "Expediente no encontrado.";
            return;
        }
        
        $report = $this->reportModel->findByIdWithDetails($case['report_id'], Auth::id(), Auth::role());
        
        $db = Database::getInstance();
        $staff = $db->query("SELECT id, name, role FROM users WHERE role != 'alumno' ORDER BY name ASC")->fetchAll();
        
        // Obtener mensajes de auditoría interna
        $stmt = $db->prepare("SELECT m.*, u.name as sender_name FROM report_messages m LEFT JOIN users u ON m.sender_id = u.id WHERE m.report_id = ? AND m.is_internal = 1 ORDER BY m.created_at DESC");
        $stmt->execute([$case['report_id']]);
        $auditLog = $stmt->fetchAll();

        View::render('protocol/valencia/case_detail', [
            'title' => 'Gestió Protocol VAL',
            'case' => $case,
            'report' => $report,
            'staff' => $staff,
            'auditLog' => $auditLog
        ], 'app');
    }
}

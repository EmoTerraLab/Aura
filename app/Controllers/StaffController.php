<?php
namespace App\Controllers;

use App\Models\Report;
use App\Core\View;
use App\Core\Auth;

class StaffController {
    private $reportModel;
    private $mentionModel;

    public function __construct(\App\Models\Report $reportModel, \App\Models\ReportMention $mentionModel) {
        $this->reportModel = $reportModel;
        $this->mentionModel = $mentionModel;
    }

    public function index() {
        $reports = $this->reportModel->getForStaff(Auth::id(), Auth::role());

        // Lógica de anonimato en la lista:
        // Si es anónimo y el rol es 'profesor', se muestra "Alumno Anónimo".
        // Si es anónimo y el rol es dirección/orientador, se muestra el nombre + "(Anónimo)"
        foreach ($reports as &$report) {
            if ($report['is_anonymous']) {
                if (Auth::role() === 'profesor') {
                    $report['student_name'] = 'Alumno Anónimo';
                } else if (in_array(Auth::role(), ['direccion', 'orientador'])) {
                    $report['student_name'] .= ' (Anónimo)';
                }
            }
        }

        View::render('staff/dashboard', [
            'title' => 'Aura - Bandeja de Entrada',
            'user' => Auth::user(),
            'reports' => $reports
        ]);
    }

    public function mentions() {
        $mentions = $this->mentionModel->findUnreadByUserWithDetails(Auth::id());
        
        echo json_encode(["success" => true, "mentions" => $mentions]);
    }

    public function markMentionsRead() {
        \App\Core\Csrf::validateRequest();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $mentionId = $data['id'] ?? null;
        
        if ($mentionId) {
            $this->mentionModel->markAsRead($mentionId);
        }
        
        echo json_encode(["success" => true]);
    }

    public function getColleagues() {
        $db = \App\Core\Database::getInstance();
        $stmt = $db->query("SELECT id, name, role FROM users WHERE role != 'alumno' ORDER BY name ASC");
        echo json_encode(['success' => true, 'colleagues' => $stmt->fetchAll()]);
    }
    public function createReport() {
        View::render("staff/report_create", ["title" => "Nuevo Reporte"]);
    }

    public function storeReport() {
        \App\Core\Csrf::validateRequest();
        // Lógica de guardado simplificada para la misión
        header("Location: /staff/inbox?created=1");
        exit;
    }

}

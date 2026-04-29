<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\ReportMention;
use App\Core\View;
use App\Core\Auth;
use App\Core\Lang;
use App\Services\ReportService;

class StaffController {
    private $reportModel;
    private $mentionModel;
    private $reportService;

    public function __construct(Report $reportModel, ReportMention $mentionModel) {
        $this->reportModel = $reportModel;
        $this->mentionModel = $mentionModel;
        $this->reportService = new ReportService();
    }

    public function index() {
        $reports = $this->reportService->getStaffDashboardReports(Auth::id(), Auth::role());

        View::render('staff/dashboard', [
            'title' => 'Aura - ' . Lang::t('staff.inbox_title'),
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
        View::render("staff/report_create", ["title" => Lang::t('nav.new_report')]);
    }

    public function storeReport() {
        \App\Core\Csrf::validateRequest();
        header("Location: /staff/inbox?created=1");
        exit;
    }
}

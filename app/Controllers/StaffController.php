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
        header('Content-Type: application/json');
        $mentions = $this->mentionModel->findUnreadByUserWithDetails(Auth::id());
        echo json_encode(["success" => true, "mentions" => $mentions]);
    }

    public function markMentionsRead() {
        \App\Core\Csrf::validateRequest();
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $mentionId = $data['id'] ?? null;
        
        if ($mentionId) {
            $this->mentionModel->markAsRead($mentionId);
        }
        echo json_encode(["success" => true]);
    }

    public function getColleagues() {
        header('Content-Type: application/json');
        $db = \App\Core\Database::getInstance();
        $stmt = $db->query("SELECT id, name, role FROM users WHERE role != 'alumno' ORDER BY name ASC");
        echo json_encode(['success' => true, 'colleagues' => $stmt->fetchAll()]);
    }

    public function createReport() {
        View::render("staff/report_create", ["title" => Lang::t('nav.new_report')]);
    }

    public function storeReport() {
        \App\Core\Csrf::validateRequest();

        $content = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? 'acoso');
        $urgency = trim($_POST['urgency'] ?? 'medium');
        $isConfidential = isset($_POST['is_confidential']) ? 1 : 0;
        $targetStudentId = !empty($_POST['target_student_id']) ? (int)$_POST['target_student_id'] : null;
        $classroomId = !empty($_POST['classroom_id']) ? (int)$_POST['classroom_id'] : null;

        if (empty($content)) {
            header("Location: /staff/reports/create?error=empty_content");
            exit;
        }

        try {
            $this->reportModel->createStaffReport([
                'title'             => $category,
                'description'       => $content,
                'target_student_id' => $targetStudentId,
                'classroom_id'      => $classroomId,
                'created_by'        => Auth::id(),
                'created_by_role'   => Auth::role(),
                'category'          => $category,
                'urgency'           => $urgency,
                'is_confidential'   => $isConfidential,
                'status'            => 'new'
            ]);
            header("Location: /staff/inbox?created=1");
        } catch (\Exception $e) {
            error_log("[StaffController] Error creating report: " . $e->getMessage());
            header("Location: /staff/reports/create?error=creation_failed");
        }
        exit;
    }

    public function showPasswordForm() {
        View::render('staff/password_change', [
            'title' => 'Aura - ' . Lang::t('auth.change_password')
        ]);
    }

    public function updatePassword() {
        \App\Core\Csrf::validateRequest();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = Auth::user();
        
        if (!password_verify($currentPassword, $user['password'])) {
            header('Location: /profile/password?error=current_invalid');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            header('Location: /profile/password?error=mismatch');
            exit;
        }

        if (strlen($newPassword) < 8) {
            header('Location: /profile/password?error=too_short');
            exit;
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hashed, $user['id']]);

        header('Location: /staff/inbox?password_changed=1');
        exit;
    }
}

<?php
namespace App\Services;

use App\Models\Report;
use App\Models\ReportMention;
use App\Core\Auth;

class ReportService {
    private Report $reportModel;
    private ReportMention $mentionModel;

    public function __construct() {
        $this->reportModel = new Report();
        $this->mentionModel = new ReportMention();
    }

    /**
     * Obtiene y anonimiza los reportes para el dashboard del staff según su rol.
     */
    public function getStaffDashboardReports(int $userId, string $role): array {
        $reports = $this->reportModel->getForStaff($userId, $role);

        foreach ($reports as &$report) {
            if ($report['is_anonymous']) {
                if ($role === 'profesor') {
                    $report['student_name'] = 'Alumno Anónimo';
                } else if (in_array($role, ['direccion', 'orientador'])) {
                    $report['student_name'] .= ' (Anónimo)';
                }
            }
        }

        return $reports;
    }
}

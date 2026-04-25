<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\StudentProfile;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Telemetry;

class ReportController {
    private $reportModel;
    private $profileModel;

    public function __construct(\App\Models\Report $reportModel, \App\Models\StudentProfile $profileModel) {
        $this->reportModel = $reportModel;
        $this->profileModel = $profileModel;
    }

    public function store() {
        Csrf::validateRequest();

        $data = json_decode(file_get_contents('php://input'), true);
        $content = trim($data['content'] ?? '');
        $target = $data['target'] ?? 'yo_mismo';
        $urgency = $data['urgency_level'] ?? 'low';
        $isAnonymous = isset($data['is_anonymous']) ? (bool)$data['is_anonymous'] : true;

        if (strlen($content) < 5) {
            echo json_encode(['success' => false, 'error' => 'Por favor, cuéntanos un poco más sobre lo ocurrido.']);
            return;
        }

        $userId = Auth::id();
        $profile = $this->profileModel->findByUser($userId);

        if (!$profile || !$profile['classroom_id']) {
            echo json_encode(['success' => false, 'error' => 'No tienes un aula asignada. Por favor, contacta con tu profesor.']);
            return;
        }

        $reportId = $this->reportModel->create([
            'student_id' => $profile['id'],
            'classroom_id' => $profile['classroom_id'],
            'content' => $content,
            'target' => $target,
            'urgency_level' => $urgency,
            'is_anonymous' => $isAnonymous ? 1 : 0
        ]);

        // Cumplimiento: Telemetría post-dispatch
        Telemetry::dispatch('report_created', [
            'report_id' => $reportId,
            'urgency' => $urgency
        ]);

        echo json_encode(['success' => true, 'report_id' => $reportId]);
    }
}
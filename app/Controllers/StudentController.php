<?php
namespace App\Controllers;

use App\Models\Report;
use App\Core\View;
use App\Core\Auth;
use App\Models\StudentProfile;

class StudentController {
    private $reportModel;
    private $profileModel;
    private $messageModel;

    public function __construct(\App\Models\Report $reportModel, \App\Models\StudentProfile $profileModel, \App\Models\ReportMessage $messageModel) {
        $this->reportModel = $reportModel;
        $this->profileModel = $profileModel;
        $this->messageModel = $messageModel;
    }

    public function index() {
        $userId = Auth::id();
        
        // Obtener el perfil de estudiante
        $profile = $this->profileModel->findByUser($userId);

        $reports = [];
        if ($profile) {
            $reports = $this->reportModel->findByStudent($profile['id']);
        }

        View::render('alumno/dashboard', [
            'title' => 'Aura - Dashboard Alumno',
            'reports' => $reports
        ]);
    }

    public function show($id) {
        $userId = Auth::id();
        $profile = $this->profileModel->findByUser($userId);

        if (!$profile) {
            http_response_code(403);
            echo json_encode(['error' => 'Perfil de estudiante no encontrado.']);
            return;
        }

        $report = $this->reportModel->find($id);

        if (!$report || $report['student_id'] !== $profile['id']) {
            http_response_code(404);
            echo json_encode(['error' => 'Reporte no encontrado o no tienes acceso.']);
            return;
        }

        // Fetch messages, but EXCLUDE internal notes
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT m.*, u.name as sender_name 
            FROM report_messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.report_id = :report_id AND m.is_internal = 0
            ORDER BY m.created_at ASC
        ");
        $stmt->execute(['report_id' => $id]);
        $messages = $stmt->fetchAll();

        foreach ($messages as &$msg) {
            $msg['is_current_user'] = ($msg['sender_id'] == $userId);
        }

        // Return the report details and the public messages
        echo json_encode([
            'report' => $report,
            'messages' => $messages,
            'current_user_id' => $userId
        ]);
    }

    public function addMessage($id) {
        \App\Core\Csrf::validateRequest();

        $userId = Auth::id();
        $profile = $this->profileModel->findByUser($userId);

        $report = $this->reportModel->find($id);

        // Validate ownership and status
        if (!$report || $report['student_id'] !== $profile['id']) {
            http_response_code(404);
            echo json_encode(['error' => 'Reporte no encontrado.']);
            return;
        }

        if ($report['status'] === 'resolved') {
            http_response_code(403);
            echo json_encode(['error' => 'El caso está resuelto, no se pueden enviar más mensajes.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $message = trim($data['message'] ?? '');

        if (empty($message)) {
            echo json_encode(['error' => 'El mensaje no puede estar vacío.']);
            return;
        }

        $messageId = $this->messageModel->create([
            'report_id' => $id,
            'sender_id' => $userId,
            'message' => $message,
            'is_internal' => 0 // Students can NEVER send internal notes
        ]);

        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT m.*, u.name as sender_name FROM report_messages m JOIN users u ON m.sender_id = u.id WHERE m.id = :id");
        $stmt->execute(['id' => $messageId]);
        $msg = $stmt->fetch();

        echo json_encode([
            'id' => $msg['id'],
            'sender_name' => $msg['sender_name'],
            'message' => $msg['message'],
            'created_at' => $msg['created_at'],
            'is_internal' => 0,
            'is_current_user' => true
        ]);
    }
}

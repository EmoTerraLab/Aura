<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\ReportMessage;
use App\Core\Auth;
use App\Core\AuditLogger;

class ReportManagementController {
    private $reportModel;
    private $messageModel;

    public function __construct(\App\Models\Report $reportModel, \App\Models\ReportMessage $messageModel) {
        $this->reportModel = $reportModel;
        $this->messageModel = $messageModel;
    }

    public function show($id) {
        $id = (int)$id;
        $report = $this->reportModel->findByIdWithDetails($id, Auth::id(), Auth::role());

        if (!$report) {
            http_response_code(404);
            header('Content-Type: application/json'); echo json_encode(['error' => 'Reporte no encontrado o no tienes acceso.']);
            return;
        }

        // Si no es una petición AJAX/JSON, redirigir al dashboard con el reporte seleccionado
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') 
               || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

        if (!$isAjax && !isset($_GET['json'])) {
            header("Location: /staff/inbox?report_id=$id");
            exit;
        }

        // Anonimato
        if ($report['is_anonymous']) {
            if (Auth::role() === 'profesor') {
                $report['student_name'] = 'Alumno Anónimo';
            } else if (in_array(Auth::role(), ['direccion', 'orientador', 'admin'])) {
                $name = $report['student_name'] ?? 'Alumno';
                $report['student_name'] = $name . ' (Anónimo)';
            }
        }

        $messages = $this->messageModel->findByReport($id);
        
        $currentUserId = Auth::id();
        foreach ($messages as &$msg) {
            $msg['is_current_user'] = ($msg['sender_id'] == $currentUserId);
        }

        header('Content-Type: application/json');
        header('Content-Type: application/json'); echo json_encode([
            'report' => $report,
            'messages' => $messages,
            'current_user_id' => $currentUserId
        ]);
    }
    public function updateStatus($id) {
        \App\Core\Csrf::validateRequest();
        header('Content-Type: application/json');

        $id = (int)$id;
        $report = $this->reportModel->findByIdWithDetails($id, Auth::id(), Auth::role());
        if (!$report) {
            http_response_code(403);
            header('Content-Type: application/json'); echo json_encode(['error' => 'No tienes permiso para modificar este reporte.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $status = $data['status'] ?? null;
        $summary = $data['resolution_summary'] ?? null;

        if (!in_array($status, ['new', 'in_progress', 'resolved'])) {
            header('Content-Type: application/json'); echo json_encode(['error' => 'Estado inválido.']);
            return;
        }

        $this->reportModel->updateStatus($id, $status, $summary);
        AuditLogger::log('REPORT_STATUS_UPDATED', 'report', $id, ['status' => $status]);

        header('Content-Type: application/json'); echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
    }

    public function addMessage($id) {
        \App\Core\Csrf::validateRequest();
        header('Content-Type: application/json');

        $id = (int)$id;
        $report = $this->reportModel->findByIdWithDetails($id, Auth::id(), Auth::role());
        if (!$report) {
            http_response_code(403);
            header('Content-Type: application/json'); echo json_encode(['error' => 'No tienes permiso para comentar en este reporte.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $message = trim($data['message'] ?? '');
        $isInternal = isset($data['is_internal']) ? (bool)$data['is_internal'] : false;

        if (empty($message)) {
            header('Content-Type: application/json'); echo json_encode(['error' => 'El mensaje no puede estar vacío.']);
            return;
        }

        $messageId = $this->messageModel->create([
            'report_id' => $id,
            'sender_id' => Auth::id(),
            'message' => $message,
            'is_internal' => $isInternal ? 1 : 0
        ]);

        // Cumplimiento: Menciones @nombre en notas internas
        if ($isInternal) {
            preg_match_all('/@([a-zA-Z0-9_]+)/', $message, $matches);
            if (!empty($matches[1])) {
                $db = \App\Core\Database::getInstance();
                $mentionModel = new \App\Models\ReportMention();
                
                $conditions = [];
                $params = [];
                foreach ($matches[1] as $username) {
                    $conditions[] = "name LIKE ?";
                    $params[] = '%' . $username . '%';
                }
                
                $sql = "SELECT id FROM users WHERE (" . implode(' OR ', $conditions) . ") AND role != 'alumno'";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $mentionedUsers = $stmt->fetchAll();

                $mentionsToCreate = [];
                foreach ($mentionedUsers as $mentionedUser) {
                    $mentionsToCreate[] = [
                        'report_id' => $id,
                        'message_id' => $messageId,
                        'user_id' => $mentionedUser['id']
                    ];
                }
                $mentionModel->createMany($mentionsToCreate);
            }
        }

        $msg = $this->messageModel->find($messageId);

        header('Content-Type: application/json'); echo json_encode([
            'id' => $msg['id'],
            'sender_name' => Auth::user()['name'],
            'message' => $msg['message'],
            'created_at' => $msg['created_at'],
            'is_internal' => $msg['is_internal'],
            'is_current_user' => true
        ]);
    }
}

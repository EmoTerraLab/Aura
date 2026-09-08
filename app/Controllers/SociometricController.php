<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Core\Lang;
use App\Services\SociometricService;

class SociometricController
{
    private $db;
    private SociometricService $sociometricService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->sociometricService = new SociometricService();
    }

    public function survey(): void
    {
        $userId = Auth::id();
        
        $stmt = $this->db->prepare("SELECT classroom_id FROM student_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
        
        if (!$profile || !$profile['classroom_id']) {
            echo "No estás asignado a ninguna clase.";
            return;
        }
        
        $classroomId = $profile['classroom_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM sociometric_surveys WHERE classroom_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$classroomId]);
        $survey = $stmt->fetch();
        
        if (!$survey) {
            View::render('errors/error', [
                'code' => '404',
                'title_key' => 'Dinámica',
                'message_key' => 'No hay dinámicas activas',
                'description_key' => 'Tu profesor no ha activado ninguna dinámica grupal por el momento.',
                'icon' => 'hourglass_empty'
            ], 'app');
            return;
        }

        $stmt = $this->db->prepare("SELECT u.id, u.name FROM users u JOIN student_profiles sp ON u.id = sp.user_id WHERE sp.classroom_id = ? AND u.id != ? ORDER BY u.name ASC");
        $stmt->execute([$classroomId, $userId]);
        $classmates = $stmt->fetchAll();

        View::render('alumno/sociometric_survey', [
            'title' => 'Dinámica de Grupo',
            'survey' => $survey,
            'classmates' => $classmates
        ], 'app');
    }

    public function submitResponse(): void
    {
        \App\Core\Csrf::validateRequest();
        $data = json_decode(file_get_contents('php://input'), true);
        $surveyId = $data['survey_id'] ?? null;
        $userId = Auth::id();

        if (!$surveyId || !isset($data['positive'])) {
            http_response_code(400);
            header('Content-Type: application/json'); echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        // Verificar si ya respondió
        $stmt = $this->db->prepare("SELECT id FROM sociometric_responses WHERE survey_id = ? AND student_id = ? LIMIT 1");
        $stmt->execute([$surveyId, $userId]);
        if ($stmt->fetch()) {
            header('Content-Type: application/json'); echo json_encode(['success' => false, 'error' => 'Ya has participado en esta dinámica.']);
            return;
        }

        $success = $this->sociometricService->processResponses($surveyId, $userId, $data);
        if ($success) {
            header('Content-Type: application/json'); echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            header('Content-Type: application/json'); echo json_encode(['error' => 'Error al guardar los datos']);
        }
    }
    public function demo(): void
    {
        $survey = [
            'id' => 0,
            'title' => 'Análisis de Cohesión Grupal - 3º ESO A',
            'classroom_name' => '3º ESO A',
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'closed'
        ];

        $metrics = [
            ['id' => 101, 'name' => 'Laura Sánchez', 'pos_count' => 12, 'neg_count' => 0, 'victim_count' => 0],
            ['id' => 102, 'name' => 'Pedro Gómez', 'pos_count' => 1, 'neg_count' => 5, 'victim_count' => 4],
            ['id' => 103, 'name' => 'Miguel Díaz', 'pos_count' => 8, 'neg_count' => 1, 'victim_count' => 0],
            ['id' => 104, 'name' => 'Lucía López', 'pos_count' => 10, 'neg_count' => 0, 'victim_count' => 0],
            ['id' => 105, 'name' => 'Carlos Martínez', 'pos_count' => 0, 'neg_count' => 2, 'victim_count' => 1],
            ['id' => 106, 'name' => 'Carmen Ruiz', 'pos_count' => 6, 'neg_count' => 0, 'victim_count' => 0],
            ['id' => 107, 'name' => 'Juan Alumno', 'pos_count' => 4, 'neg_count' => 1, 'victim_count' => 0],
            ['id' => 108, 'name' => 'Marina Alumna', 'pos_count' => 5, 'neg_count' => 0, 'victim_count' => 0],
            ['id' => 109, 'name' => 'Javier Torres', 'pos_count' => 3, 'neg_count' => 4, 'victim_count' => 2],
            ['id' => 110, 'name' => 'Elena Gil', 'pos_count' => 7, 'neg_count' => 0, 'victim_count' => 0],
        ];

        View::render('staff/sociometric_results', [
            'title' => Lang::t('sociogram.analysis_header'),
            'survey' => $survey,
            'metrics' => $metrics
        ], 'app');
    }

    /**
     * GET /staff/sociogramas/{id}
     * Dashboard de resultats optimitzat (Sense N+1).
     */
    public function results($id): void
    {
        $id = (int)$id;
        
        // 1. Dades de l'enquesta
        $stmtSurvey = $this->db->prepare("SELECT s.*, c.name as classroom_name FROM sociometric_surveys s JOIN classrooms c ON s.classroom_id = c.id WHERE s.id = ?");
        $stmtSurvey->execute([$id]);
        $survey = $stmtSurvey->fetch();

        if (!$survey) {
            http_response_code(404);
            echo "Enquesta no trobada.";
            return;
        }

        // 2. Consulta agrupada de métriques (Optimitzada)
        $sql = "SELECT u.id, u.name,
                SUM(CASE WHEN r.question_type = 'positive_affinity' THEN 1 ELSE 0 END) as pos_count,
                SUM(CASE WHEN r.question_type = 'negative_affinity' THEN 1 ELSE 0 END) as neg_count,
                SUM(CASE WHEN r.question_type = 'victimization_target' THEN 1 ELSE 0 END) as victim_count
                FROM users u
                JOIN student_profiles sp ON u.id = sp.user_id
                LEFT JOIN sociometric_responses r ON u.id = r.nominated_student_id AND r.survey_id = ?
                WHERE sp.classroom_id = ?
                GROUP BY u.id, u.name
                ORDER BY pos_count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $survey['classroom_id']]);
        $metrics = $stmt->fetchAll();

        View::render('staff/sociometric_results', [
            'title' => Lang::t('sociogram.analysis_header'),
            'survey' => $survey,
            'metrics' => $metrics
        ], 'app');
    }
}

<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Core\Lang;

class SociometricController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * GET /alumno/sociograma
     * Muestra la encuesta activa para el alumno.
     */
    public function survey(): void
    {
        $userId = Auth::id();
        
        // Obtener el aula del alumno
        $stmtClass = $this->db->prepare("SELECT classroom_id FROM student_profiles WHERE user_id = ?");
        $stmtClass->execute([$userId]);
        $profile = $stmtClass->fetch();

        if (!$profile) {
            header('Location: /alumno/dashboard');
            exit;
        }

        // Buscar encuesta activa para esa aula
        $stmtSurvey = $this->db->prepare("SELECT * FROM sociometric_surveys WHERE classroom_id = ? AND status = 'active' LIMIT 1");
        $stmtSurvey->execute([$profile['classroom_id']]);
        $survey = $stmtSurvey->fetch();

        if (!$survey) {
            header('Location: /alumno/dashboard');
            exit;
        }

        // Verificar si ya respondió
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM sociometric_responses WHERE survey_id = ? AND student_id = ?");
        $stmtCheck->execute([$survey['id'], $userId]);
        if ($stmtCheck->fetchColumn() > 0) {
            header('Location: /alumno/dashboard?survey_done=1');
            exit;
        }

        // Obtener compañeros de clase (excluyéndose a sí mismo)
        $stmtStudents = $this->db->prepare("
            SELECT u.id, u.name 
            FROM users u
            JOIN student_profiles sp ON u.id = sp.user_id
            WHERE sp.classroom_id = ? AND u.id != ?
            ORDER BY u.name ASC
        ");
        $stmtStudents->execute([$profile['classroom_id'], $userId]);
        $classmates = $stmtStudents->fetchAll();

        View::render('alumno/sociometric_survey', [
            'title' => $survey['title'],
            'survey' => $survey,
            'classmates' => $classmates
        ], 'app');
    }

    /**
     * POST /api/sociometric/respond
     */
    public function submitResponse(): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        $surveyId = (int)$data['survey_id'];
        $userId = Auth::id();

        try {
            $this->db->beginTransaction();

            // Guardar Afinidad Positiva
            foreach (($data['positive'] ?? []) as $sid) {
                $this->saveNomination($surveyId, $userId, $sid, 'positive_affinity');
            }

            // Guardar Afinidad Negativa
            foreach (($data['negative'] ?? []) as $sid) {
                $this->saveNomination($surveyId, $userId, $sid, 'negative_affinity');
            }

            // Guardar Detección de Víctimas
            foreach (($data['victims'] ?? []) as $sid) {
                $this->saveNomination($surveyId, $userId, $sid, 'victimization_target');
            }

            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function saveNomination($surveyId, $studentId, $nominatedId, $type)
    {
        $stmt = $this->db->prepare("INSERT INTO sociometric_responses (survey_id, student_id, nominated_student_id, question_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$surveyId, $studentId, $nominatedId, $type]);
    }

    /**
     * GET /staff/sociogramas/{id}
     * Dashboard de resultados para el Staff
     */
    public function results($id): void
    {
        $id = (int)$id;
        // 1. Obtener datos básicos de la encuesta
        $stmtSurvey = $this->db->prepare("SELECT s.*, c.name as classroom_name FROM sociometric_surveys s JOIN classrooms c ON s.classroom_id = c.id WHERE s.id = ?");
        $stmtSurvey->execute([$id]);
        $survey = $stmtSurvey->fetch();

        // 2. Cálculo de métricas (Agregados)
        $sql = "SELECT u.name, u.id,
                (SELECT COUNT(*) FROM sociometric_responses WHERE nominated_student_id = u.id AND question_type = 'positive_affinity' AND survey_id = ?) as pos_count,
                (SELECT COUNT(*) FROM sociometric_responses WHERE nominated_student_id = u.id AND question_type = 'negative_affinity' AND survey_id = ?) as neg_count,
                (SELECT COUNT(*) FROM sociometric_responses WHERE nominated_student_id = u.id AND question_type = 'victimization_target' AND survey_id = ?) as victim_count
                FROM users u
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE sp.classroom_id = ?
                ORDER BY pos_count DESC";

        $stmtMetrics = $this->db->prepare($sql);
        $stmtMetrics->execute([$id, $id, $id, $survey['classroom_id']]);
        $metrics = $stmtMetrics->fetchAll();

        View::render('staff/sociometric_results', [
            'title' => 'Anàlisi Sociomètric: ' . $survey['title'],
            'survey' => $survey,
            'metrics' => $metrics
        ], 'app');
    }
}

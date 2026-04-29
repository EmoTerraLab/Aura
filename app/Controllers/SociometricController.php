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
        $stmtClass = $this->db->prepare("SELECT classroom_id FROM student_profiles WHERE user_id = ?");
        $stmtClass->execute([$userId]);
        $profile = $stmtClass->fetch();

        if (!$profile) {
            header('Location: /alumno/dashboard');
            exit;
        }

        $stmtSurvey = $this->db->prepare("SELECT * FROM sociometric_surveys WHERE classroom_id = ? AND status = 'active' LIMIT 1");
        $stmtSurvey->execute([$profile['classroom_id']]);
        $survey = $stmtSurvey->fetch();

        if (!$survey) {
            header('Location: /alumno/dashboard');
            exit;
        }

        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM sociometric_responses WHERE survey_id = ? AND student_id = ?");
        $stmtCheck->execute([$survey['id'], $userId]);
        if ($stmtCheck->fetchColumn() > 0) {
            header('Location: /alumno/dashboard?survey_done=1');
            exit;
        }

        $stmtStudents = $this->db->prepare("
            SELECT u.id, u.name 
            FROM users u
            JOIN student_profiles sp ON u.id = sp.user_id
            WHERE sp.classroom_id = ? AND u.id != ?
            ORDER BY u.name ASC
        ");
        $stmtStudents->execute([$profile['classroom_id'], $userId]);

        View::render('alumno/sociometric_survey', [
            'title' => $survey['title'],
            'survey' => $survey,
            'classmates' => $stmtStudents->fetchAll()
        ], 'app');
    }

    public function submitResponse(): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        $success = $this->sociometricService->processResponses(
            (int)$data['survey_id'], 
            Auth::id(), 
            $data
        );

        echo json_encode(['success' => $success]);
    }

    public function results($id): void
    {
        $id = (int)$id;
        $stmtSurvey = $this->db->prepare("SELECT s.*, c.name as classroom_name FROM sociometric_surveys s JOIN classrooms c ON s.classroom_id = c.id WHERE s.id = ?");
        $stmtSurvey->execute([$id]);
        $survey = $stmtSurvey->fetch();

        if (!$survey) {
            die("Enquesta no trobada.");
        }

        $metrics = $this->sociometricService->getSurveyAnalysis($id, (int)$survey['classroom_id']);

        View::render('staff/sociometric_results', [
            'title' => Lang::t('sociogram.analysis_header') . ': ' . $survey['title'],
            'survey' => $survey,
            'metrics' => $metrics
        ], 'app');
    }
}

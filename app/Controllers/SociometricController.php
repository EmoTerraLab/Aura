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

    // ... altres mètodes (survey, submitResponse) es mantenen igual ...

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

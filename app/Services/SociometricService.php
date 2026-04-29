<?php
namespace App\Services;

use App\Core\Database;

class SociometricService
{
    /**
     * Obtiene el análisis de métricas para un sociograma evitando consultas N+1
     */
    public function getSurveyAnalysis(int $surveyId, int $classroomId): array
    {
        $db = Database::getInstance();
        $sql = "SELECT u.id, u.name,
                COUNT(CASE WHEN r.question_type = 'positive_affinity' THEN 1 END) as pos_count,
                COUNT(CASE WHEN r.question_type = 'negative_affinity' THEN 1 END) as neg_count,
                COUNT(CASE WHEN r.question_type = 'victimization_target' THEN 1 END) as victim_count
                FROM users u
                JOIN student_profiles sp ON u.id = sp.user_id
                LEFT JOIN sociometric_responses r ON u.id = r.nominated_student_id AND r.survey_id = ?
                WHERE sp.classroom_id = ?
                GROUP BY u.id, u.name
                ORDER BY pos_count DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$surveyId, $classroomId]);
        return $stmt->fetchAll();
    }

    /**
     * Registra las nominaciones de un alumno
     */
    public function processResponses(int $surveyId, int $studentId, array $data): bool
    {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO sociometric_responses (survey_id, student_id, nominated_student_id, question_type) VALUES (?, ?, ?, ?)");
            
            $mappings = [
                'positive' => 'positive_affinity',
                'negative' => 'negative_affinity',
                'victims'  => 'victimization_target'
            ];

            foreach ($mappings as $key => $type) {
                foreach (($data[$key] ?? []) as $nominatedId) {
                    $stmt->execute([$surveyId, $studentId, (int)$nominatedId, $type]);
                }
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Error procesando sociograma: " . $e->getMessage());
            return false;
        }
    }
}

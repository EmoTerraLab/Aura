<?php
namespace App\Services;

class RevaExportService
{
    /**
     * Genera un resumen estructurado para el Registro de Violencias (REVA).
     */
    public function generateSummary(array $case, array $report): string
    {
        $summary = "--- RESUM PER AL REVA (Generalitat de Catalunya) ---\n";
        $summary .= "ID Cas: #" . $case['id'] . "\n";
        $summary .= "Tipificació: " . strtoupper($case['classification'] ?? 'PENDENT') . "\n";
        $summary .= "Gravetat: " . strtoupper($case['severity_preliminary'] ?? 'BAIXA') . "\n";
        $summary .= "Alumnat afectat: " . $report['student_name'] . "\n";
        $summary .= "Grup: " . $report['classroom_name'] . "\n";
        $summary .= "Data inici: " . date('d/m/Y', strtotime($case['created_at'])) . "\n";
        $summary .= "Iniciativa: " . $report['target'] . "\n";
        
        $comms = json_decode($case['communications'] ?? '{}', true);
        $summary .= "Comunicacions: " . (($comms['inspeccio'] ?? false) ? 'SI' : 'NO') . " a Inspecció\n";
        
        $summary .= "--------------------------------------------------";
        
        return $summary;
    }
}

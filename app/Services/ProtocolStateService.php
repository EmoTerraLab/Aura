<?php
namespace App\Services;

use App\Models\ProtocolCase;
use App\Models\ReportMessage;
use App\Core\Auth;
use App\Core\Config;

class ProtocolStateService
{
    private ProtocolCase $caseModel;
    private ReportMessage $messageModel;

    public function __construct()
    {
        $this->caseModel = new ProtocolCase();
        $this->messageModel = new ReportMessage();
    }

    /**
     * Gestiona la transició de fase mitjançant el mòdul de CCAA.
     * Soporta tanto caseId (id de protocol_cases) como reportId para mayor robustez.
     */
    public function transitionTo(int $id, string $newPhase): bool
    {
        // Intentar encontrar por ID de caso primero, luego por report_id
        $case = $this->caseModel->find($id);
        if (!$case) {
            $case = $this->caseModel->findByReport($id);
        }

        if (!$case) {
            throw new \Exception("Expediente de protocolo no encontrado (ID: $id).");
        }

        $caseId = $case['id'];
        $reportId = $case['report_id'];
        $oldPhase = $case['current_phase'];
        $ccaa = $case['ccaa_code'];
        
        $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);
        
        $canTransition = $protocol->canTransition($oldPhase, $newPhase, $case);
        if ($canTransition !== true) {
            throw new \Exception(is_string($canTransition) ? $canTransition : "TRANSICIÓN INVÁLIDA.");
        }

        $success = $this->caseModel->updatePhase($caseId, $newPhase);

        if ($success) {
            // Sincronización con tablas específicas de CCAA
            $db = \App\Core\Database::getInstance();
            if ($ccaa === 'MUR') {
                $db->prepare("UPDATE murcia_protocol_cases SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE report_id = ?")
                   ->execute([$newPhase, $reportId]);
            } elseif ($ccaa === 'ARA') {
                $db->prepare("UPDATE aragon_protocol_cases SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE report_id = ?")
                   ->execute([$newPhase, $reportId]);
            } elseif ($ccaa === 'GAL') {
                $db->prepare("UPDATE galicia_protocol_cases SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE report_id = ?")
                   ->execute([$newPhase, $reportId]);
            }

            $this->logInternalAudit($reportId, "Canvi de fase legal: de " . strtoupper($oldPhase) . " a " . strtoupper($newPhase));
        }

        return $success;
    }

    /**
     * Crea el registro inicial del caso legal.
     */
    public function createInitialCase(int $reportId, string $ccaa, ?string $initialPhase = null): ?array
    {
        $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);
        
        if (!$protocol->isFullyImplemented()) {
            return null; // No crear casos legales para protocolos no implementados
        }

        if ($initialPhase === null) {
            $initialPhase = $protocol->getInitialState();
        }

        // El deadline inicial por defecto son 48h si no especifica el protocolo
        $deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));

        $this->caseModel->create([
            'report_id' => $reportId,
            'ccaa_code' => $ccaa,
            'current_phase' => $initialPhase,
            'deadline_at' => $deadline
        ]);
        
        $case = $this->caseModel->findByReport($reportId);
        if ($case) {
            $this->logInternalAudit($reportId, "Protocol de $ccaa activat. Fase inicial: $initialPhase.");
        }
        return $case;
    }

    /**
     * Tipificació i activació automàtica segons CCAA.
     */
    public function classify(int $caseId, string $severity, string $classification): bool
    {
        $case = $this->caseModel->find($caseId);
        if (!$case) return false;

        $ccaa = $case['ccaa_code'];
        $success = $this->caseModel->updateClassification($caseId, $severity, $classification);

        if ($success) {
            $this->logInternalAudit($case['report_id'], "Tipificació actualitzada: [$classification] amb gravetat [$severity]");
            
            // Automatismes según CCAA
            if ($ccaa === 'CAT') {
                if ($severity === 'violencia_sexual') {
                    $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_BARNAHUS);
                    $this->logInternalAudit($case['report_id'], "🔒 BARNAHUS ACTIVAT: Pas directe a CREURE i PROTEGIR.");
                } else {
                    if ($case['current_phase'] === ProtocolCase::PHASE_DETECCION) {
                        $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_VALORACION);
                    }
                }
            } elseif ($ccaa === 'ARA') {
                if ($severity === 'violencia_sexual') {
                    $this->logInternalAudit($case['report_id'], "⚠️ ALERTA: Cas de violència sexual detectat. Notificar immediatament a Inspecció i FCSE.");
                }
            }
        }

        return $success;
    }

    private function logInternalAudit(int $reportId, string $message): void
    {
        $this->messageModel->create([
            'report_id' => $reportId,
            'sender_id' => Auth::id(),
            'message' => "📋 [AUDITORIA LEGAL] " . $message,
            'is_internal' => 1
        ]);
    }
}

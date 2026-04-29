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
     * Gestiona la transició de fase amb bloquejos per Violència Sexual (Barnahus).
     */
    public function transitionTo(int $caseId, string $newPhase): bool
    {
        $case = $this->caseModel->find($caseId);
        if (!$case) return false;

        $oldPhase = $case['current_phase'];
        $ccaa = $case['ccaa_code'];
        
        $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);
        
        $canTransition = $protocol->canTransition($oldPhase, $newPhase, $case);
        if ($canTransition !== true) {
            throw new \Exception(is_string($canTransition) ? $canTransition : "TRANSICIÓN INVÁLIDA.");
        }

        $success = $this->caseModel->updatePhase($caseId, $newPhase);

        if ($success) {
            $this->logInternalAudit($case['report_id'], "Canvi de fase legal: de " . strtoupper($oldPhase) . " a " . strtoupper($newPhase));
        }

        return $success;
    }

    /**
     * Crea el registro inicial del caso legal.
     */
    public function createInitialCase(int $reportId, string $ccaa, string $initialPhase = null): ?array
    {
        if ($initialPhase === null) {
            $initialPhase = ($ccaa === 'aragon') ? ProtocolCase::PHASE_AR_COMUNICACION : ProtocolCase::PHASE_DETECCION;
        }

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
     * Tipificació i activació automàtica del protocol de Catalunya.
     */
    public function classify(int $caseId, string $severity, string $classification): bool
    {
        $case = $this->caseModel->find($caseId);
        if (!$case) return false;

        $ccaa = $case['ccaa_code'];
        $success = $this->caseModel->updateClassification($caseId, $severity, $classification);

        if ($success) {
            $this->logInternalAudit($case['report_id'], "Tipificació actualitzada: [$classification] amb gravetat [$severity]");
            
            if ($ccaa === 'cataluna') {
                // Automatismes de Catalunya
                if ($severity === 'violencia_sexual') {
                    $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_BARNAHUS);
                    $this->logInternalAudit($case['report_id'], "🔒 BARNAHUS ACTIVAT: Pas directe a CREURE i PROTEGIR.");
                } else {
                    // Avançar automàticament de detecció a valoració en casos ordinaris
                    if ($case['current_phase'] === ProtocolCase::PHASE_DETECCION) {
                        $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_VALORACION);
                    }
                }
            } elseif ($ccaa === 'aragon') {
                // Automatismes d'Aragó
                if ($severity === 'violencia_sexual') {
                    // En Aragón la violencia sexual no tiene bypass Barnahus como tal en la máquina de estados,
                    // pero se registra y se notifica a inspección. Mantenemos fase actual o avanzamos según proceda.
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

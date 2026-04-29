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
        $isSexualViolence = $case['severity_preliminary'] === 'violencia_sexual';

        // Regla d'or Catalunya 2024: Si és violència sexual, només es permeten fases de derivació/tancament
        if ($isSexualViolence) {
            $allowed = [ProtocolCase::PHASE_BARNAHUS, ProtocolCase::PHASE_COMUNICACION, ProtocolCase::PHASE_CIERRE];
            if (!in_array($newPhase, $allowed)) {
                throw new \Exception("PROTOCOL BLOQUEJAT: Els casos de violència sexual requereixen derivació directa a Barnahus.");
            }
        }

        $success = $this->caseModel->updatePhase($caseId, $newPhase);

        if ($success) {
            $this->logInternalAudit($case['report_id'], "Canvi de fase legal: de " . strtoupper($oldPhase) . " a " . strtoupper($newPhase));
        }

        return $success;
    }

    /**
     * Tipificació i activació automàtica del protocol de Catalunya.
     */
    public function classifyCase(int $caseId, string $severity, string $classification): bool
    {
        $case = $this->caseModel->find($caseId);
        if (!$case) return false;

        $success = $this->caseModel->updateClassification($caseId, $severity, $classification);

        if ($success) {
            $this->logInternalAudit($case['report_id'], "Tipificació actualitzada: [$classification] amb gravetat [$severity]");
            
            // Automatismes de Catalunya
            if ($severity === 'violencia_sexual' && Config::get('ccaa_code') === 'cataluna') {
                $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_BARNAHUS);
                $this->logInternalAudit($case['report_id'], "🔒 BARNAHUS ACTIVAT: Pas directe a CREURE i PROTEGIR.");
            } else {
                // Avançar automàticament de detecció a valoració en casos ordinaris
                if ($case['current_phase'] === ProtocolCase::PHASE_DETECCION) {
                    $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_VALORACION);
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

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

    private array $aragon_transitions = [
        ProtocolCase::PHASE_AR_COMUNICACION => [ProtocolCase::PHASE_AR_INICIADO, ProtocolCase::PHASE_AR_NO_INICIADO],
        ProtocolCase::PHASE_AR_INICIADO    => [ProtocolCase::PHASE_AR_VALORACION],
        ProtocolCase::PHASE_AR_NO_INICIADO => [ProtocolCase::PHASE_AR_COMUNICACION],
        ProtocolCase::PHASE_AR_VALORACION  => [ProtocolCase::PHASE_AR_VALORADO],
        ProtocolCase::PHASE_AR_VALORADO    => [ProtocolCase::PHASE_AR_SEGUIMIENTO, ProtocolCase::PHASE_AR_CONTRATO, ProtocolCase::PHASE_AR_EXPEDIENTE],
        ProtocolCase::PHASE_AR_CONTRATO    => [ProtocolCase::PHASE_AR_SEGUIMIENTO],
        ProtocolCase::PHASE_AR_EXPEDIENTE  => [ProtocolCase::PHASE_AR_SEGUIMIENTO],
        ProtocolCase::PHASE_AR_SEGUIMIENTO => [ProtocolCase::PHASE_AR_CERRADO, ProtocolCase::PHASE_AR_REABIERTO],
        ProtocolCase::PHASE_AR_REABIERTO   => [ProtocolCase::PHASE_AR_SEGUIMIENTO, ProtocolCase::PHASE_AR_EXPEDIENTE],
        ProtocolCase::PHASE_AR_CERRADO     => [],
    ];

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

        if ($ccaa === 'cataluna') {
            $isSexualViolence = $case['severity_preliminary'] === 'violencia_sexual';

            // Regla d'or Catalunya 2024: Si és violència sexual, només es permeten fases de derivació/tancament
            if ($isSexualViolence) {
                $allowed = [ProtocolCase::PHASE_BARNAHUS, ProtocolCase::PHASE_COMUNICACION, ProtocolCase::PHASE_CIERRE];
                if (!in_array($newPhase, $allowed)) {
                    throw new \Exception("PROTOCOL BLOQUEJAT: Els casos de violència sexual requereixen derivació directa a Barnahus.");
                }
            }
        } elseif ($ccaa === 'aragon') {
            $allowed = $this->aragon_transitions[$oldPhase] ?? [];
            if (!in_array($newPhase, $allowed)) {
                throw new \Exception("TRANSICIÓN INVÁLIDA: No se puede pasar de '$oldPhase' a '$newPhase' en el protocolo de Aragón.");
            }
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
    public function createInitialCase(int $reportId, string $ccaa): ?array
    {
        $deadline = date('Y-m-d H:i:s', strtotime('+48 hours'));
        $initialPhase = ProtocolCase::PHASE_DETECCION;

        if ($ccaa === 'aragon') {
            $initialPhase = ProtocolCase::PHASE_AR_COMUNICACION;
        }

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

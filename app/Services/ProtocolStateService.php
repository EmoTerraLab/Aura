<?php
namespace App\Services;

use App\Models\ProtocolCase;
use App\Models\ReportMessage;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;

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
     * Gestiona la transició de fase mitjançant el mòdul de CCAA usando el reportId.
     * Esta es la forma recomendada para evitar colisiones de IDs entre tablas.
     */
    public function transitionByReportId(int $reportId, string $newPhase): bool
    {
        $case = $this->caseModel->findByReport($reportId);
        if (!$case) {
            throw new \Exception("Expediente de protocolo no encontrado para la alerta #$reportId.");
        }
        return $this->performTransition($case, $newPhase);
    }

    /**
     * Gestiona la transició de fase mitjançant el mòdul de CCAA.
     * @deprecated Usar transitionByReportId siempre que sea posible.
     */
    public function transitionTo(int $id, string $newPhase): bool
    {
        $case = $this->caseModel->find($id);

        if (!$case) {
            throw new \Exception("Expediente de protocolo no encontrado (ID: $id).");
        }

        return $this->performTransition($case, $newPhase);
    }

    private function performTransition(array $case, string $newPhase): bool
    {
        $db = Database::getInstance();
        $isAlreadyInTransaction = $db->inTransaction();
        if (!$isAlreadyInTransaction) $db->beginTransaction();

        try {
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
                // Sincronización con tablas específicas de CCAA delegada a la estrategia
                $protocol->syncState($reportId, $newPhase);

                $userName = Auth::user()['name'] ?? 'Usuario desconocido';
                // SEC-018: Auditoría obligatoria con formato legal requerido
                $this->logInternalAudit($reportId, "Estado cambiado de " . strtoupper($oldPhase) . " a " . strtoupper($newPhase) . " por " . $userName);
            }

            if (!$isAlreadyInTransaction) $db->commit();
            return $success;
        } catch (\Exception $e) {
            if (!$isAlreadyInTransaction && $db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /**
     * Crea el registro inicial del caso legal.
     */
    public function createInitialCase(int $reportId, string $ccaa, ?string $initialPhase = null): ?array
    {
        $db = Database::getInstance();
        $isAlreadyInTransaction = $db->inTransaction();
        if (!$isAlreadyInTransaction) $db->beginTransaction();

        try {
            $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);
            
            if (!$protocol->isFullyImplemented()) {
                if (!$isAlreadyInTransaction) $db->rollBack();
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
                $userName = Auth::user()['name'] ?? 'Sistema';
                $this->logInternalAudit($reportId, "Protocol de $ccaa activat. Fase inicial: $initialPhase. Iniciat per: $userName");

                // Sincronización con tablas específicas de CCAA delegada a la estrategia
                $protocol->syncState($reportId, $initialPhase);
            }
            
            if (!$isAlreadyInTransaction) $db->commit();
            return $case;
        } catch (\Exception $e) {
            if (!$isAlreadyInTransaction && $db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    /**
     * Tipificació i activació automàtica segons CCAA.
     */
    public function classify(int $caseId, string $severity, string $classification): bool
    {
        $db = Database::getInstance();
        $isAlreadyInTransaction = $db->inTransaction();
        if (!$isAlreadyInTransaction) $db->beginTransaction();

        try {
            $case = $this->caseModel->find($caseId);
            if (!$case) {
                if (!$isAlreadyInTransaction) $db->rollBack();
                return false;
            }

            $ccaa = $case['ccaa_code'];
            $success = $this->caseModel->updateClassification($caseId, $severity, $classification);

            if ($success) {
                $userName = Auth::user()['name'] ?? 'Usuario desconocido';
                $this->logInternalAudit($case['report_id'], "Tipificació actualitzada: [$classification] amb gravetat [$severity] por $userName");
                
                // Automatismes según CCAA
                if ($ccaa === 'CAT') {
                    if ($severity === 'violencia_sexual') {
                        $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_BARNAHUS);
                        $this->logInternalAudit($case['report_id'], "🔒 BARNAHUS ACTIVAT: Pas directe a CREURE i PROTEGIR.");
                        $this->logInternalAudit($case['report_id'], "Estado cambiado de " . strtoupper($case['current_phase']) . " a " . strtoupper(ProtocolCase::PHASE_BARNAHUS) . " por sistema (Automatismo Barnahus)");
                        // Sincronización si existiera tabla regional
                        $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);
                        $protocol->syncState($case['report_id'], ProtocolCase::PHASE_BARNAHUS);
                    } else {
                        if ($case['current_phase'] === ProtocolCase::PHASE_DETECCION) {
                            $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_VALORACION);
                            $this->logInternalAudit($case['report_id'], "Estado cambiado de " . strtoupper($case['current_phase']) . " a " . strtoupper(ProtocolCase::PHASE_VALORACION) . " por sistema (Automatismo Clasificación)");
                            $protocol = \App\Services\Protocol\ProtocolFactory::make($ccaa);
                            $protocol->syncState($case['report_id'], ProtocolCase::PHASE_VALORACION);
                        }
                    }
                } elseif ($ccaa === 'ARA') {
                    if ($severity === 'violencia_sexual') {
                        $this->logInternalAudit($case['report_id'], "⚠️ ALERTA: Cas de violència sexual detectat. Notificar immediatament a Inspecció i FCSE.");
                    }
                }
            }

            if (!$isAlreadyInTransaction) $db->commit();
            return $success;
        } catch (\Exception $e) {
            if (!$isAlreadyInTransaction && $db->inTransaction()) $db->rollBack();
            throw $e;
        }
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

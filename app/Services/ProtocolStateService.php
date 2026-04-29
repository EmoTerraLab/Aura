<?php
namespace App\Services;

use App\Models\ProtocolCase;
use App\Models\ReportMessage;
use App\Core\Auth;

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
     * Cambia la fase del protocolo con validaciones de negocio.
     */
    public function transitionTo(int $caseId, string $newPhase): bool
    {
        $case = $this->caseModel->find($caseId);
        if (!$case) return false;

        $oldPhase = $case['current_phase'];
        
        // Bloqueo Barnahus (Ya validado en el modelo, pero reforzamos aquí)
        if ($case['severity_preliminary'] === 'violencia_sexual') {
            $allowed = [ProtocolCase::PHASE_BARNAHUS, ProtocolCase::PHASE_COMUNICACION, ProtocolCase::PHASE_CIERRE];
            if (!in_array($newPhase, $allowed)) {
                throw new \Exception("PROTOCOL BLOQUEJAT: Els casos de violència sexual requereixen derivació directa a Barnahus.");
            }
        }

        $success = $this->caseModel->updatePhase($caseId, $newPhase);

        if ($success) {
            $this->logAction($case['report_id'], "Canvi de fase: de " . strtoupper($oldPhase) . " a " . strtoupper($newPhase));
        }

        return $success;
    }

    /**
     * Clasifica un caso y maneja automatismos (ej. Barnahus).
     */
    public function classify(int $caseId, string $severity, string $classification): bool
    {
        $case = $this->caseModel->find($caseId);
        if (!$case) return false;

        $success = $this->caseModel->updateClassification($caseId, $severity, $classification);

        if ($success) {
            $this->logAction($case['report_id'], "Tipificació: [$classification], Gravetat [$severity]");
            
            // Automatismos
            if ($severity === 'violencia_sexual') {
                $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_BARNAHUS);
                $this->logAction($case['report_id'], "🔒 BARNAHUS ACTIVAT: Pas directe a CREURE i PROTEGIR. Diagnosi interna bloquejada.");
            } else {
                // Avance automático a valoración para casos normales
                $this->caseModel->updatePhase($caseId, ProtocolCase::PHASE_VALORACION);
                $this->logAction($case['report_id'], "Indicis confirmats. El cas avança a fase de VALORACIÓ.");
            }
        }

        return $success;
    }

    private function logAction(int $reportId, string $message): void
    {
        $this->messageModel->create([
            'report_id' => $reportId,
            'sender_id' => Auth::id(),
            'message' => "📋 [REGISTRE LEGAL] " . $message,
            'is_internal' => 1
        ]);
    }
}

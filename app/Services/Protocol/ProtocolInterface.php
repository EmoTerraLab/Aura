<?php
namespace App\Services\Protocol;

/**
 * ProtocolInterface - Contrato obligatorio para todos los flujos de CCAA.
 */
interface ProtocolInterface {
    // Metadatos del protocolo
    public function getCcaaCode(): string;
    public function getCcaaName(): string;
    public function getLegalReference(): string;

    // Máquina de estados
    public function getInitialState(): string;
    public function getValidTransitions(string $currentState): array;
    public function getStateLabel(string $state): string;
    public function getAllStates(): array;

    // Timeline para la UI
    public function getTimelineSteps(): array;  // [{key, label, icon, deadline_days}]

    // Acciones disponibles por fase
    public function getActionsForState(string $state, array $case): array;  // [{key, label, style, onclick}]

    // Documentos del protocolo
    public function getDocuments(): array;  // [{code, name, annex_table, required_state}]

    // Plazos en días lectivos (null si no aplica)
    public function getDeadlineForState(string $state): ?int;

    // Alertas de plazo
    public function getDeadlineAlert(string $state, int $schoolDaysElapsed): ?array;  // {level: 'ok'|'warning'|'danger'|'overdue', message}

    // Herramientas exclusivas (REVA, Barnahus, etc.)
    public function getExclusiveTools(): array;  // ['reva', 'barnahus', 'addenda_compromis', etc.]

    // Validaciones antes de transición
    public function canTransition(string $fromState, string $toState, array $case): bool|string;  // true o mensaje de error
}

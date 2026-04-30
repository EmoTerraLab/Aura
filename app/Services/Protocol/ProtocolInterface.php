<?php
namespace App\Services\Protocol;

/**
 * ProtocolInterface - Contrato obligatorio para todos los flujos de CCAA.
 */
interface ProtocolInterface {
    // Métodos obligatorios para el Interruptor Maestro
    public function getCode(): string;
    public function getName(): string;
    public function isFullyImplemented(): bool;
    public function getManageUrl(int $caseId): string;

    // Métodos de lógica interna (Máquina de estados)
    public function getInitialState(): string;
    public function getValidTransitions(string $currentState): array;
    public function getStateLabel(string $state): string;
    public function getAllStates(): array;

    // Timeline para la UI
    public function getTimelineSteps(): array;

    // Acciones disponibles por fase
    public function getActionsForState(string $state, array $case): array;

    // Documentos del protocolo
    public function getDocuments(): array;

    // Herramientas exclusivas (REVA, Barnahus, etc.)
    public function getExclusiveTools(): array;

    // Validaciones antes de transición
    public function canTransition(string $fromState, string $toState, array $case): bool|string;
}

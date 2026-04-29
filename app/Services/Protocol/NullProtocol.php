<?php
namespace App\Services\Protocol;

/**
 * NullProtocol - Módulo vacío para CCAA sin implementar.
 */
class NullProtocol implements ProtocolInterface {
    private string $ccaaCode;

    public function __construct(string $ccaaCode) {
        $this->ccaaCode = $ccaaCode;
    }

    public function getCcaaCode(): string {
        return $this->ccaaCode;
    }

    public function getCcaaName(): string {
        return ucfirst($this->ccaaCode);
    }

    public function getLegalReference(): string {
        return "Protocol oficial en fase d'implementació";
    }

    public function getInitialState(): string {
        return 'inicio';
    }

    public function getValidTransitions(string $currentState): array {
        return [];
    }

    public function getStateLabel(string $state): string {
        return $state;
    }

    public function getAllStates(): array {
        return ['inicio'];
    }

    public function getTimelineSteps(): array {
        return [];
    }

    public function getActionsForState(string $state, array $case): array {
        return [];
    }

    public function getDocuments(): array {
        return [];
    }

    public function getDeadlineForState(string $state): ?int {
        return null;
    }

    public function getDeadlineAlert(string $state, int $schoolDaysElapsed): ?array {
        return [
            'level' => 'warning',
            'message' => "El protocolo automatizado para " . $this->getCcaaName() . " está en fase de implementación."
        ];
    }

    public function getExclusiveTools(): array {
        return [];
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        return "Protocol en fase d'implementació";
    }
}

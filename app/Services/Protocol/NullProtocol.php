<?php
namespace App\Services\Protocol;

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
        return "En fase d'implementació";
    }

    public function getInitialState(): string {
        return 'no_implementado';
    }

    public function getValidTransitions(string $currentState): array {
        return [];
    }

    public function getStateLabel(string $state): string {
        return 'No Implementado';
    }

    public function getAllStates(): array {
        return ['no_implementado'];
    }

    public function getTimelineSteps(): array {
        return [];
    }

    public function getActionsForState(string $state, array $case): array {
        $nombre = ucfirst($this->ccaaCode);
        return [
            [
                'key' => 'not_implemented',
                'label' => "El protocolo automatizado para {$nombre} está en fase de implementación.",
                'style' => 'alert',
                'onclick' => ''
            ]
        ];
    }

    public function getDocuments(): array {
        return [];
    }

    public function getDeadlineForState(string $state): ?int {
        return null;
    }

    public function getDeadlineAlert(string $state, int $schoolDaysElapsed): ?array {
        return null;
    }

    public function getExclusiveTools(): array {
        return [];
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        return false;
    }
}

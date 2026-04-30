<?php
namespace App\Services\Protocol;

class CastillaLaManchaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'CLM'; }
    public function getName(): string { return 'CastillaLaMancha'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $caseId): string { return "/protocolo-acoso"; }

    public function getInitialState(): string { return 'inicio'; }
    public function getValidTransitions(string $currentState): array { return []; }
    public function getStateLabel(string $state): string { return $state; }
    public function getAllStates(): array { return ['inicio']; }
    public function getTimelineSteps(): array { return []; }
    public function getActionsForState(string $state, array $case): array { return []; }
    public function getDocuments(): array { return []; }
    public function getExclusiveTools(): array { return []; }
    public function canTransition(string $fromState, string $toState, array $case): bool|string { return true; }
}
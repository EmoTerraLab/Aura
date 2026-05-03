<?php
namespace App\Services\Protocol;

class GaliciaProtocol implements ProtocolInterface {
    public const STATE_DETECCIO_COMUNICACIO = 'gal_deteccion';
    public const STATE_RECOLLIDA_INFORMACION = 'gal_recollida';
    public const STATE_ANALISE_MEDIDAS      = 'gal_analise';
    public const STATE_SEGUIMENTO           = 'gal_seguimento';
    public const STATE_PECHE                = 'tancament';

    public function getCode(): string { return 'GAL'; }
    public function getName(): string { return 'Galicia'; }
    public function isFullyImplemented(): bool { return true; }
    public function getManageUrl(int $caseId): string { return "/protocol/galicia/case/{$caseId}"; }

    public function getInitialState(): string { return self::STATE_DETECCIO_COMUNICACIO; }

    public function getValidTransitions(string $currentState): array {
        return match($currentState) {
            self::STATE_DETECCIO_COMUNICACIO => [self::STATE_RECOLLIDA_INFORMACION],
            self::STATE_RECOLLIDA_INFORMACION => [self::STATE_ANALISE_MEDIDAS],
            self::STATE_ANALISE_MEDIDAS      => [self::STATE_SEGUIMENTO],
            self::STATE_SEGUIMENTO           => [self::STATE_PECHE],
            default => []
        };
    }

    public function getStateLabel(string $state): string {
        return match($state) {
            self::STATE_DETECCIO_COMUNICACIO => 'Detección e Comunicación',
            self::STATE_RECOLLIDA_INFORMACION => 'Recollida de Información',
            self::STATE_ANALISE_MEDIDAS      => 'Análise e Medidas',
            self::STATE_SEGUIMENTO           => 'Seguimento',
            self::STATE_PECHE                => 'Cerrado',
            default => $state
        };
    }

    public function getAllStates(): array {
        return [
            self::STATE_DETECCIO_COMUNICACIO,
            self::STATE_RECOLLIDA_INFORMACION,
            self::STATE_ANALISE_MEDIDAS,
            self::STATE_SEGUIMENTO,
            self::STATE_PECHE
        ];
    }

    public function getTimelineSteps(): array {
        return [
            ['key' => self::STATE_DETECCIO_COMUNICACIO,  'label' => 'Detección', 'icon' => 'search', 'deadline_days' => 0],
            ['key' => self::STATE_RECOLLIDA_INFORMACION, 'label' => 'Recollida', 'icon' => 'find_in_page', 'deadline_days' => 2],
            ['key' => self::STATE_ANALISE_MEDIDAS,      'label' => 'Análise', 'icon' => 'fact_check', 'deadline_days' => 10],
            ['key' => self::STATE_SEGUIMENTO,           'label' => 'Seguimento', 'icon' => 'visibility', 'deadline_days' => null]
        ];
    }

    public function getActionsForState(string $state, array $case): array {
        $actions = [];
        $cid = $case['id'] ?? 0;
        if (!$cid) return [];

        if ($state === self::STATE_DETECCIO_COMUNICACIO) {
            $actions[] = ['key' => 'next', 'label' => 'Pasar a Recollida de Información', 'style' => 'primary', 'onclick' => "nextPhase($cid, '" . self::STATE_RECOLLIDA_INFORMACION . "')"];
        } elseif ($state === self::STATE_RECOLLIDA_INFORMACION) {
            $actions[] = ['key' => 'next', 'label' => 'Pasar a Análise e Medidas', 'style' => 'primary', 'onclick' => "nextPhase($cid, '" . self::STATE_ANALISE_MEDIDAS . "')"];
        } elseif ($state === self::STATE_ANALISE_MEDIDAS) {
            $actions[] = ['key' => 'next', 'label' => 'Pasar a Seguimento', 'style' => 'primary', 'onclick' => "nextPhase($cid, '" . self::STATE_SEGUIMENTO . "')"];
        } elseif ($state === self::STATE_SEGUIMENTO) {
            $actions[] = ['key' => 'close', 'label' => 'Pechar Expediente', 'style' => 'success', 'onclick' => "nextPhase($cid, '" . self::STATE_PECHE . "')"];
        }

        return $actions;
    }

    public function getDocuments(): array { return []; }
    public function getExclusiveTools(): array { return []; }
    public function canTransition(string $fromState, string $toState, array $case): bool|string { return true; }
}
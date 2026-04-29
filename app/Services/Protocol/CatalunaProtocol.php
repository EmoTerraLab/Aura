<?php
namespace App\Services\Protocol;

use App\Models\ProtocolCase;

class CatalunaProtocol implements ProtocolInterface {
    
    public function getCcaaCode(): string {
        return 'cataluna';
    }

    public function getCcaaName(): string {
        return 'Catalunya';
    }

    public function getLegalReference(): string {
        return 'Resolució 2024 (Generat de REVA/Barnahus)';
    }

    public function getInitialState(): string {
        return ProtocolCase::PHASE_DETECCION;
    }

    public function getValidTransitions(string $currentState): array {
        // En Catalunya las fases son lineales mayormente
        return match($currentState) {
            ProtocolCase::PHASE_DETECCION => [ProtocolCase::PHASE_VALORACION, ProtocolCase::PHASE_BARNAHUS],
            ProtocolCase::PHASE_VALORACION => [ProtocolCase::PHASE_COMUNICACION],
            ProtocolCase::PHASE_COMUNICACION => [ProtocolCase::PHASE_INTERVENCION],
            ProtocolCase::PHASE_INTERVENCION => [ProtocolCase::PHASE_CIERRE],
            ProtocolCase::PHASE_SEGUIMIENTO_TANCAMENT => [ProtocolCase::PHASE_CIERRE],
            ProtocolCase::PHASE_BARNAHUS => [ProtocolCase::PHASE_COMUNICACION, ProtocolCase::PHASE_CIERRE],
            ProtocolCase::PHASE_CIERRE => [],
            default => []
        };
    }

    public function getStateLabel(string $state): string {
        return match($state) {
            ProtocolCase::PHASE_DETECCION => 'Detecció',
            ProtocolCase::PHASE_VALORACION => 'Valoració',
            ProtocolCase::PHASE_COMUNICACION => 'Comunicació',
            ProtocolCase::PHASE_INTERVENCION => 'Intervenció',
            ProtocolCase::PHASE_SEGUIMIENTO_TANCAMENT => 'Seguiment',
            ProtocolCase::PHASE_CIERRE => 'Tancament',
            ProtocolCase::PHASE_BARNAHUS => 'BARNAHUS',
            default => $state
        };
    }

    public function getAllStates(): array {
        return [
            ProtocolCase::PHASE_DETECCION,
            ProtocolCase::PHASE_VALORACION,
            ProtocolCase::PHASE_COMUNICACION,
            ProtocolCase::PHASE_INTERVENCION,
            ProtocolCase::PHASE_SEGUIMIENTO_TANCAMENT,
            ProtocolCase::PHASE_CIERRE,
            ProtocolCase::PHASE_BARNAHUS
        ];
    }

    public function getTimelineSteps(): array {
        return [
            ['id' => ProtocolCase::PHASE_DETECCION, 'label' => 'Detección', 'special' => false],
            ['id' => ProtocolCase::PHASE_VALORACION, 'label' => 'Valoración', 'special' => false],
            ['id' => ProtocolCase::PHASE_BARNAHUS, 'label' => 'BARNAHUS', 'special' => true],
            ['id' => ProtocolCase::PHASE_COMUNICACION, 'label' => 'Comunicación', 'special' => false],
            ['id' => ProtocolCase::PHASE_INTERVENCION, 'label' => 'Intervención', 'special' => false],
            ['id' => ProtocolCase::PHASE_CIERRE, 'label' => 'Cierre', 'special' => false]
        ];
    }

    public function getActionsForState(string $state, array $case): array {
        // En esta función construimos las acciones para la UI dinámicamente
        $actions = [];
        $cid = $case['id'];
        
        if ($state === ProtocolCase::PHASE_DETECCION) {
            $actions[] = ['key' => 'confirm', 'label' => 'Confirmar Indicios', 'style' => 'secondary', 'onclick' => "protocolClassify($cid, 'grave', 'bullying')"];
            $actions[] = ['key' => 'barnahus', 'label' => '⚠️ Violencia Sexual (Barnahus)', 'style' => 'danger', 'onclick' => "protocolClassify($cid, 'violencia_sexual', 'sexual')"];
        }
        elseif ($state === ProtocolCase::PHASE_VALORACION) {
            $actions[] = ['key' => 'assign_team', 'label' => 'Asignar Equipo', 'style' => 'primary', 'onclick' => "alert('Asignación de equipo disponible en la versión PRO')"];
            $actions[] = ['key' => 'finish_val', 'label' => 'Finalizar Valoración', 'style' => 'secondary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_COMUNICACION."')"];
        }
        elseif ($state === ProtocolCase::PHASE_COMUNICACION) {
            // Checkboxes will be handled specially in the UI, but we can pass the data
            $actions[] = ['key' => 'addenda', 'label' => '📄 Addenda Compromís', 'style' => 'link', 'onclick' => "window.open('/protocol/case/$cid/template/addenda_compromis', '_blank')"];
            $actions[] = ['key' => 'next_intervention', 'label' => 'Avançar a Intervenció', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_INTERVENCION."')"];
        }
        elseif ($state === ProtocolCase::PHASE_INTERVENCION || $state === ProtocolCase::PHASE_SEGUIMIENTO_TANCAMENT) {
            $actions[] = ['key' => 'mapa', 'label' => '🗺️ Mapa Seguretat', 'style' => 'success', 'onclick' => "openSecurityMap($cid)"];
            $actions[] = ['key' => 'reconeixement', 'label' => '📄 Reconeixement', 'style' => 'dark', 'onclick' => "window.open('/protocol/case/$cid/template/reconeixement_fets', '_blank')"];
            $actions[] = ['key' => 'seguiment', 'label' => '📅 Seguiment', 'style' => 'secondary', 'onclick' => "openFollowupModal($cid)"];
            $actions[] = ['key' => 'close_protocol', 'label' => 'Tancar Protocol', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_CIERRE."')"];
            $actions[] = ['key' => 'redefine', 'label' => 'Redefinir', 'style' => 'secondary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_INTERVENCION."')"];
        }
        elseif ($state === ProtocolCase::PHASE_BARNAHUS) {
            $actions[] = ['key' => 'fitxa', 'label' => '📄 Fitxa de Derivació', 'style' => 'danger', 'onclick' => "window.open('/protocol/case/$cid/template/derivacio_barnahus', '_blank')"];
            $actions[] = ['key' => 'done_derivacio', 'label' => 'He realitzat la derivació', 'style' => 'danger-outline', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_COMUNICACION."')"];
        }

        return $actions;
    }

    public function getDocuments(): array {
        return [
            ['code' => 'addenda_compromis', 'name' => 'Addenda Compromís', 'annex_table' => null, 'required_state' => ProtocolCase::PHASE_COMUNICACION],
            ['code' => 'reconeixement_fets', 'name' => 'Reconeixement Fets', 'annex_table' => null, 'required_state' => ProtocolCase::PHASE_INTERVENCION],
            ['code' => 'derivacio_barnahus', 'name' => 'Derivació Barnahus', 'annex_table' => null, 'required_state' => ProtocolCase::PHASE_BARNAHUS],
        ];
    }

    public function getDeadlineForState(string $state): ?int {
        return null; // Catalunya current logic has '48 hours' for valuation, but not in school days
    }

    public function getDeadlineAlert(string $state, int $schoolDaysElapsed): ?array {
        return null;
    }

    public function getExclusiveTools(): array {
        return ['reva', 'barnahus', 'addenda_compromis', 'reconeixement_fets', 'derivacio_barnahus'];
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        $isSexualViolence = ($case['severity_preliminary'] ?? '') === 'violencia_sexual';

        // Regla d'or Catalunya 2024: Si és violència sexual, només es permeten fases de derivació/tancament
        if ($isSexualViolence) {
            $allowed = [ProtocolCase::PHASE_BARNAHUS, ProtocolCase::PHASE_COMUNICACION, ProtocolCase::PHASE_CIERRE];
            if (!in_array($toState, $allowed)) {
                return "PROTOCOL BLOQUEJAT: Els casos de violència sexual requereixen derivació directa a Barnahus.";
            }
        }
        return true;
    }
}
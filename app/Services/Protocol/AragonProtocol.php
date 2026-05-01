<?php
namespace App\Services\Protocol;

use App\Models\ProtocolCase;

class AragonProtocol implements ProtocolInterface {

    public function getCode(): string {
        return 'ARA';
    }

    public function getName(): string {
        return 'Aragón';
    }

    public function isFullyImplemented(): bool {
        return true;
    }

    public function getManageUrl(int $caseId): string {
        return "/protocol/aragon/case/{$caseId}";
    }

    public function getInitialState(): string {
        return ProtocolCase::PHASE_AR_COMUNICACION;
    }

    public function getValidTransitions(string $currentState): array {
        return match($currentState) {
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
            default => []
        };
    }

    public function getStateLabel(string $state): string {
        return match($state) {
            ProtocolCase::PHASE_AR_COMUNICACION => 'Comunicación Recibida',
            ProtocolCase::PHASE_AR_INICIADO => 'Protocolo Iniciado',
            ProtocolCase::PHASE_AR_NO_INICIADO => 'Protocolo No Iniciado',
            ProtocolCase::PHASE_AR_VALORACION => 'En Valoración',
            ProtocolCase::PHASE_AR_VALORADO => 'Valorado',
            ProtocolCase::PHASE_AR_CONTRATO => 'Contrato de Conducta',
            ProtocolCase::PHASE_AR_EXPEDIENTE => 'Expediente Disciplinario',
            ProtocolCase::PHASE_AR_SEGUIMIENTO => 'En Seguimiento',
            ProtocolCase::PHASE_AR_REABIERTO => 'Reabierto',
            ProtocolCase::PHASE_AR_CERRADO => 'Cerrado',
            default => $state
        };
    }

    public function getAllStates(): array {
        return [
            ProtocolCase::PHASE_AR_COMUNICACION,
            ProtocolCase::PHASE_AR_INICIADO,
            ProtocolCase::PHASE_AR_NO_INICIADO,
            ProtocolCase::PHASE_AR_VALORACION,
            ProtocolCase::PHASE_AR_VALORADO,
            ProtocolCase::PHASE_AR_CONTRATO,
            ProtocolCase::PHASE_AR_EXPEDIENTE,
            ProtocolCase::PHASE_AR_SEGUIMIENTO,
            ProtocolCase::PHASE_AR_REABIERTO,
            ProtocolCase::PHASE_AR_CERRADO
        ];
    }

    public function getTimelineSteps(): array {
        return [
            ['key' => ProtocolCase::PHASE_AR_COMUNICACION, 'label' => 'Comunicación', 'icon' => 'envelope', 'deadline_days' => 1],
            ['key' => ProtocolCase::PHASE_AR_INICIADO, 'label' => 'Inicio Protocolo', 'icon' => 'flag', 'deadline_days' => 2],
            ['key' => ProtocolCase::PHASE_AR_VALORACION, 'label' => 'Valoración', 'icon' => 'clipboard-list', 'deadline_days' => 18],
            ['key' => ProtocolCase::PHASE_AR_VALORADO, 'label' => 'Resolución', 'icon' => 'gavel', 'deadline_days' => 22],
            ['key' => ProtocolCase::PHASE_AR_SEGUIMIENTO, 'label' => 'Seguimiento', 'icon' => 'eye', 'deadline_days' => null],
            ['key' => ProtocolCase::PHASE_AR_CERRADO, 'label' => 'Cierre', 'icon' => 'check-circle', 'deadline_days' => null]
        ];
    }

    public function getActionsForState(string $state, array $case): array {
        $actions = [];
        $cid = $case['id'];

        if ($state === ProtocolCase::PHASE_AR_COMUNICACION) {
            $actions[] = ['key' => 'manage', 'label' => 'Gestionar Fase Inicial (ANEXO I)', 'style' => 'primary', 'onclick' => "window.location.href='/protocol/aragon/case/$cid'"];
        }
        elseif ($state === ProtocolCase::PHASE_AR_INICIADO) {
            $actions[] = ['key' => 'manage', 'label' => 'Configurar Equipo (ANEXO III)', 'style' => 'primary', 'onclick' => "window.location.href='/protocol/aragon/case/$cid'"];
        }
        elseif ($state === ProtocolCase::PHASE_AR_VALORACION) {
            $actions[] = ['key' => 'manage', 'label' => 'Registrar Entrevistas / Indicadores', 'style' => 'indigo', 'onclick' => "window.location.href='/protocol/aragon/case/$cid'"];
            $actions[] = ['key' => 'finish_val', 'label' => 'Finalizar Valoración (Ir a Resolución)', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_VALORADO."')"];
        }
        elseif ($state === ProtocolCase::PHASE_AR_VALORADO) {
            $actions[] = ['key' => 'manage', 'label' => 'Generar Informe Oficial (ANEXO VIII)', 'style' => 'success', 'onclick' => "window.location.href='/protocol/aragon/case/$cid'"];
            $actions[] = ['key' => 'inspeccion', 'label' => 'Enviar a Inspección', 'style' => 'warning', 'onclick' => "alert('Funcionalidad de envío automático disponible próximamente. Por ahora, descargue el PDF y envíelo por los canales habituales.')"];
            $actions[] = ['key' => 'contrato', 'label' => 'Contrato Conducta', 'style' => 'secondary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_CONTRATO."')"];
            $actions[] = ['key' => 'expediente', 'label' => 'Expediente Disciplinario', 'style' => 'secondary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_EXPEDIENTE."')"];
            $actions[] = ['key' => 'seguimiento', 'label' => 'Avanzar a Seguimiento', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_SEGUIMIENTO."')"];
        }
        elseif ($state === ProtocolCase::PHASE_AR_CONTRATO || $state === ProtocolCase::PHASE_AR_EXPEDIENTE) {
            $actions[] = ['key' => 'to_seguimiento', 'label' => 'Avanzar a Seguimiento', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_SEGUIMIENTO."')"];
        }
        elseif ($state === ProtocolCase::PHASE_AR_SEGUIMIENTO || $state === ProtocolCase::PHASE_AR_REABIERTO) {
            $actions[] = ['key' => 'seguimiento_session', 'label' => '📅 Registrar Sesión de Seguimiento (ANEXO IX)', 'style' => 'secondary', 'onclick' => "openFollowupModalAragon($cid)"];
            $actions[] = ['key' => 'cerrar', 'label' => 'Cerrar Protocolo', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_CERRADO."')"];
            $actions[] = ['key' => 'reabrir', 'label' => 'Reabrir', 'style' => 'warning-outline', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_AR_REABIERTO."')"];
        }
        elseif ($state === ProtocolCase::PHASE_AR_CERRADO) {
            $actions[] = ['key' => 'manage', 'label' => 'Ver Acta de Cierre (ANEXO X)', 'style' => 'success', 'onclick' => "window.location.href='/protocol/aragon/case/$cid'"];
        }

        return $actions;
    }

    public function getDocuments(): array {
        return [
            ['code' => 'anexo_i_a', 'name' => 'Comunicación Inicial', 'annex_table' => null, 'required_state' => ProtocolCase::PHASE_AR_COMUNICACION],
            ['code' => 'anexo_i_b', 'name' => 'Inicio Protocolo', 'annex_table' => null, 'required_state' => ProtocolCase::PHASE_AR_INICIADO],
            ['code' => 'anexo_ii', 'name' => 'Medidas Provisionales', 'annex_table' => 'aragon_annex_ii_measures', 'required_state' => ProtocolCase::PHASE_AR_COMUNICACION],
            ['code' => 'anexo_iii', 'name' => 'Equipo Valoración', 'annex_table' => 'aragon_annex_iii_team', 'required_state' => ProtocolCase::PHASE_AR_INICIADO],
            ['code' => 'anexo_iv', 'name' => 'Libro Registro', 'annex_table' => 'aragon_protocol_log_book', 'required_state' => null],
            ['code' => 'anexo_v', 'name' => 'Entrevistas y Síntesis', 'annex_table' => 'aragon_annex_v_interviews', 'required_state' => ProtocolCase::PHASE_AR_VALORACION],
            ['code' => 'anexo_vi', 'name' => 'Indicadores', 'annex_table' => 'aragon_annex_vi_indicators', 'required_state' => ProtocolCase::PHASE_AR_VALORACION],
            ['code' => 'anexo_vii', 'name' => 'Resolución', 'annex_table' => 'aragon_annex_vii_viii_resolution', 'required_state' => ProtocolCase::PHASE_AR_VALORADO],
            ['code' => 'anexo_viii', 'name' => 'Informe Resumen', 'annex_table' => 'aragon_annex_vii_viii_resolution', 'required_state' => ProtocolCase::PHASE_AR_VALORADO],
            ['code' => 'anexo_ix', 'name' => 'Seguimiento', 'annex_table' => 'aragon_annex_ix_followup', 'required_state' => ProtocolCase::PHASE_AR_SEGUIMIENTO],
            ['code' => 'anexo_x', 'name' => 'Cierre', 'annex_table' => 'aragon_annex_x_closure', 'required_state' => ProtocolCase::PHASE_AR_CERRADO]
        ];
    }

    public function getDeadlineAlert(string $state, int $schoolDaysElapsed): ?array {
        if ($state === ProtocolCase::PHASE_AR_VALORACION) {
            if ($schoolDaysElapsed <= 15) {
                return ['level' => 'ok', 'message' => "Día lectivo $schoolDaysElapsed de 18"];
            } elseif ($schoolDaysElapsed <= 18) {
                return ['level' => 'warning', 'message' => "Día lectivo $schoolDaysElapsed — Límite de valoración próximo"];
            } else {
                return ['level' => 'danger', 'message' => "PLAZO SUPERADO — Límite de valoración era día 18"];
            }
        } elseif ($state === ProtocolCase::PHASE_AR_VALORADO) {
            if ($schoolDaysElapsed <= 20) {
                return ['level' => 'warning', 'message' => "Día lectivo $schoolDaysElapsed de 22"];
            } elseif ($schoolDaysElapsed <= 22) {
                return ['level' => 'danger', 'message' => "¡Envío a Inspección obligatorio! Día $schoolDaysElapsed de 22"];
            } else {
                return ['level' => 'overdue', 'message' => "PLAZO SUPERADO — Notificar a Inspección urgentemente"];
            }
        }
        return null;
    }

    public function getExclusiveTools(): array {
        return [
            'anexo_i_a', 'anexo_i_b', 'anexo_ii', 'anexo_iii', 'anexo_iv', 
            'anexo_v', 'anexo_vi', 'anexo_vii', 'anexo_viii', 'anexo_ix', 'anexo_x', 
            'contrato_conducta', 'fiscalia_menores'
        ];
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        $allowed = $this->getValidTransitions($fromState);
        if (!in_array($toState, $allowed)) {
            return "TRANSICIÓN INVÁLIDA: No se puede pasar de '$fromState' a '$toState' en el protocolo de Aragón.";
        }
        return true;
    }
}

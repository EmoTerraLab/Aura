<?php
namespace App\Services\Protocol;

use App\Models\ProtocolCase;

class MurciaProtocol implements ProtocolInterface {

    public function getCode(): string {
        return 'MUR';
    }

    public function getName(): string {
        return 'Región de Murcia';
    }

    public function isFullyImplemented(): bool {
        return true;
    }

    public function getManageUrl(int $caseId): string {
        return "/protocol/murcia/case/{$caseId}";
    }

    public function getInitialState(): string {
        return ProtocolCase::PHASE_MUR_INICIAL;
    }

    public function getValidTransitions(string $currentState): array {
        return match($currentState) {
            ProtocolCase::PHASE_MUR_INICIAL     => [ProtocolCase::PHASE_MUR_INTERVENCION],
            ProtocolCase::PHASE_MUR_INTERVENCION => [ProtocolCase::PHASE_MUR_INFORME],
            ProtocolCase::PHASE_MUR_INFORME      => [ProtocolCase::PHASE_MUR_VALORACION],
            ProtocolCase::PHASE_MUR_VALORACION   => [ProtocolCase::PHASE_MUR_CIERRE],
            ProtocolCase::PHASE_MUR_CIERRE       => [ProtocolCase::PHASE_MUR_INTERVENCION], // Para reapertura
            default => []
        };
    }

    public function getStateLabel(string $state): string {
        return match($state) {
            ProtocolCase::PHASE_MUR_INICIAL      => 'Designación y Medidas Urgentes',
            ProtocolCase::PHASE_MUR_INTERVENCION => 'Intervención y Entrevistas',
            ProtocolCase::PHASE_MUR_INFORME      => 'Emisión del Informe',
            ProtocolCase::PHASE_MUR_VALORACION   => 'Valoración y Decisión',
            ProtocolCase::PHASE_MUR_CIERRE       => 'Cierre y Actuaciones Posteriores',
            default => $state
        };
    }

    public function getAllStates(): array {
        return [
            ProtocolCase::PHASE_MUR_INICIAL,
            ProtocolCase::PHASE_MUR_INTERVENCION,
            ProtocolCase::PHASE_MUR_INFORME,
            ProtocolCase::PHASE_MUR_VALORACION,
            ProtocolCase::PHASE_MUR_CIERRE
        ];
    }

    public function getTimelineSteps(): array {
        return [
            ['key' => ProtocolCase::PHASE_MUR_INICIAL,      'label' => 'Inicio y Medidas', 'icon' => 'flag', 'deadline_days' => 0],
            ['key' => ProtocolCase::PHASE_MUR_INTERVENCION, 'label' => 'Entrevistas', 'icon' => 'groups', 'deadline_days' => 20],
            ['key' => ProtocolCase::PHASE_MUR_INFORME,      'label' => 'Informe (Anexo IV)', 'icon' => 'description', 'deadline_days' => 20],
            ['key' => ProtocolCase::PHASE_MUR_VALORACION,   'label' => 'Valoración', 'icon' => 'gavel', 'deadline_days' => null],
            ['key' => ProtocolCase::PHASE_MUR_CIERRE,       'label' => 'Cierre', 'icon' => 'check-circle', 'deadline_days' => null]
        ];
    }

    public function getActionsForState(string $state, array $case): array {
        $actions = [];
        $cid = $case['id'] ?? 0;
        if (!$cid) return [];

        if ($state === ProtocolCase::PHASE_MUR_INICIAL) {
            $actions[] = ['key' => 'designar_equipo', 'label' => 'Designar Equipo Intervención', 'style' => 'primary', 'onclick' => "openFollowupModal($cid, 'mur_designacio')"];
            $actions[] = ['key' => 'medidas_urgencia', 'label' => 'Adoptar Medidas Urgencia', 'style' => 'warning', 'onclick' => "openFollowupModal($cid, 'mur_medides_urgencia')"];
            $actions[] = ['key' => 'enviar_anexo_i', 'label' => 'Enviar Anexo I (Inspección)', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_comunicacio_inspeccio')"];
            $actions[] = ['key' => 'pasar_intervencion', 'label' => 'Pasar a Intervención', 'style' => 'success', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_MUR_INTERVENCION."')"];
        }
        elseif ($state === ProtocolCase::PHASE_MUR_INTERVENCION) {
            $actions[] = ['key' => 'entrevista_victima', 'label' => '1º Entrevista Víctima', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_entrevista_victima')"];
            $actions[] = ['key' => 'entrevista_observadores', 'label' => '2º Entrevista Observadores', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_entrevista_observadors')"];
            $actions[] = ['key' => 'entrevista_familia_victima', 'label' => '3º Entrevista Familia Víctima', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_entrevista_familia_victima')"];
            $actions[] = ['key' => 'entrevista_familia_agresor', 'label' => '4º Entrevista Familia Agresor', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_entrevista_familia_agresor')"];
            $actions[] = ['key' => 'entrevista_agresor', 'label' => '5º Entrevista Agresor(es)', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_entrevista_agresor')"];
            $actions[] = ['key' => 'pedir_asesoramiento', 'label' => 'Pedir Asesoramiento EOEP', 'style' => 'secondary', 'onclick' => "openFollowupModal($cid, 'mur_assessorament_eoep')"];
            $actions[] = ['key' => 'pasar_informe', 'label' => 'Redactar Informe (Anexo IV)', 'style' => 'success', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_MUR_INFORME."')"];
        }
        elseif ($state === ProtocolCase::PHASE_MUR_INFORME) {
            $actions[] = ['key' => 'generar_anexo_iv', 'label' => 'Cargar Informe Equipo (Anexo IV)', 'style' => 'primary', 'onclick' => "openFollowupModal($cid, 'mur_anexo_iv')"];
            $actions[] = ['key' => 'pasar_valoracion', 'label' => 'Convocar Reunión Valoración', 'style' => 'success', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_MUR_VALORACION."')"];
        }
        elseif ($state === ProtocolCase::PHASE_MUR_VALORACION) {
            $actions[] = ['key' => 'acta_reunion', 'label' => 'Levantar Acta de Reunión', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_acta_reunio')"];
            $actions[] = ['key' => 'determinar_acoso', 'label' => 'Determinar Evidencias (Anexo V)', 'style' => 'primary', 'onclick' => "openFollowupModal($cid, 'mur_anexo_v')"];
            $actions[] = ['key' => 'plan_seguimiento', 'label' => 'Plan Seguimiento (Anexo VI)', 'style' => 'secondary', 'onclick' => "openFollowupModal($cid, 'mur_anexo_vi')"];
            $actions[] = ['key' => 'pasar_cierre', 'label' => 'Ir a Cierre y Comunicaciones', 'style' => 'success', 'onclick' => "nextPhase($cid, '".ProtocolCase::PHASE_MUR_CIERRE."')"];
        }
        elseif ($state === ProtocolCase::PHASE_MUR_CIERRE) {
            $actions[] = ['key' => 'entregar_anexo_v', 'label' => 'Entregar Anexo V a Familias', 'style' => 'indigo', 'onclick' => "openFollowupModal($cid, 'mur_entrega_anexo_v')"];
            $actions[] = ['key' => 'comunicacion_fiscalia', 'label' => 'Comunicación Legal (Edad)', 'style' => 'danger', 'onclick' => "openFollowupModal($cid, 'mur_comunicacio_legal')"];
            $actions[] = ['key' => 'medidas_reparacion', 'label' => 'Medidas Reparación', 'style' => 'warning', 'onclick' => "openFollowupModal($cid, 'mur_mesures_reparacio')"];
            $actions[] = ['key' => 'tancar', 'label' => 'Tancar Protocol', 'style' => 'success', 'onclick' => "nextPhase($cid, 'tancament')"];
        }

        return $actions;
    }

    public function getDocuments(): array {
        return [
            ['code' => 'mur_anexo_i',  'name' => 'Anexo I - Notificación Inicio', 'required_state' => ProtocolCase::PHASE_MUR_INICIAL],
            ['code' => 'mur_anexo_iv', 'name' => 'Anexo IV - Informe Equipo',    'required_state' => ProtocolCase::PHASE_MUR_INFORME],
            ['code' => 'mur_acta',     'name' => 'Acta de Reunión Conjunta',     'required_state' => ProtocolCase::PHASE_MUR_VALORACION],
            ['code' => 'mur_anexo_v',  'name' => 'Anexo V - Medidas y Conclusión', 'required_state' => ProtocolCase::PHASE_MUR_VALORACION],
            ['code' => 'mur_anexo_vi', 'name' => 'Anexo VI - Plan Seguimiento',  'required_state' => ProtocolCase::PHASE_MUR_VALORACION]
        ];
    }

    public function getExclusiveTools(): array {
        return [
            'mur_designacio', 'mur_medides_urgencia', 'mur_entrevistes_ordre', 
            'mur_assessorament_eoep', 'mur_anexo_iv', 'mur_acta_reunio', 
            'mur_anexo_v', 'mur_anexo_vi', 'mur_comunicacio_legal_edat'
        ];
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        $allowed = $this->getValidTransitions($fromState);
        if (!in_array($toState, $allowed) && $toState !== 'tancament') {
            return "Transició no permesa: de '$fromState' a '$toState' en el protocol de Murcia.";
        }
        return true;
    }
}

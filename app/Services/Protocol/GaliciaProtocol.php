<?php
declare(strict_types=1);

namespace App\Services\Protocol;

/**
 * GaliciaProtocol — Protocolo educativo para a prevención e actuación
 * ante o acoso escolar na Comunidade Autónoma de Galicia.
 * 4 Fases + 16 Anexos.
 *
 * @package Aura PDP v2.22
 */
class GaliciaProtocol implements ProtocolInterface {

    public const STATE_DETECCIO_COMUNICACIO = 'deteccio_comunicacio';
    public const STATE_RECOLLIDA_INFORMACION = 'recollida_informacion';
    public const STATE_ANALISE_MEDIDAS = 'analise_medidas';
    public const STATE_SEGUIMENTO = 'seguimento';
    public const STATE_PECHE_CON_ACOSO = 'peche_con_acoso';
    public const STATE_PECHE_SEN_ACOSO = 'peche_sen_acoso';

    public function getCode(): string {
        return 'GAL';
    }

    public function getName(): string {
        return 'Galicia';
    }

    public function isFullyImplemented(): bool {
        return true;
    }

    public function getManageUrl(int $caseId): string {
        return "/protocol/galicia/case/{$caseId}";
    }

    public function getLegalReference(): string {
        return 'Protocolo educativo para a prevención e actuación ante o acoso escolar na Comunidade Autónoma de Galicia';
    }

    public function getInitialState(): string {
        return self::STATE_DETECCIO_COMUNICACIO;
    }

    public function getAllStates(): array {
        return [
            self::STATE_DETECCIO_COMUNICACIO,
            self::STATE_RECOLLIDA_INFORMACION,
            self::STATE_ANALISE_MEDIDAS,
            self::STATE_SEGUIMENTO,
            self::STATE_PECHE_CON_ACOSO,
            self::STATE_PECHE_SEN_ACOSO
        ];
    }

    public function getValidTransitions(string $currentState): array {
        return match($currentState) {
            self::STATE_DETECCIO_COMUNICACIO  => [self::STATE_RECOLLIDA_INFORMACION],
            self::STATE_RECOLLIDA_INFORMACION => [self::STATE_ANALISE_MEDIDAS],
            self::STATE_ANALISE_MEDIDAS       => [self::STATE_SEGUIMENTO, self::STATE_PECHE_CON_ACOSO, self::STATE_PECHE_SEN_ACOSO],
            self::STATE_SEGUIMENTO            => [self::STATE_PECHE_CON_ACOSO, self::STATE_PECHE_SEN_ACOSO],
            self::STATE_PECHE_CON_ACOSO       => [self::STATE_SEGUIMENTO],
            self::STATE_PECHE_SEN_ACOSO       => [],
            default => []
        };
    }

    public function getStateLabel(string $state): string {
        return match($state) {
            self::STATE_DETECCIO_COMUNICACIO  => 'Fase 1: Detección e comunicación',
            self::STATE_RECOLLIDA_INFORMACION => 'Fase 2: Recollida de información',
            self::STATE_ANALISE_MEDIDAS       => 'Fase 3: Análise e medidas',
            self::STATE_SEGUIMENTO            => 'Fase 4: Seguimento',
            self::STATE_PECHE_CON_ACOSO       => 'Peche con acoso',
            self::STATE_PECHE_SEN_ACOSO       => 'Peche sen acoso',
            default => $state
        };
    }

    public function getTimelineSteps(): array {
        return [
            ['key' => self::STATE_DETECCIO_COMUNICACIO,  'label' => 'Detección',   'icon' => 'flag',        'deadline_days' => 2],
            ['key' => self::STATE_RECOLLIDA_INFORMACION, 'label' => 'Información', 'icon' => 'folder_open', 'deadline_days' => 10],
            ['key' => self::STATE_ANALISE_MEDIDAS,       'label' => 'Análise',     'icon' => 'gavel',       'deadline_days' => 15],
            ['key' => self::STATE_SEGUIMENTO,            'label' => 'Seguimento',  'icon' => 'timeline',    'deadline_days' => 30],
            ['key' => self::STATE_PECHE_CON_ACOSO,       'label' => 'Peche (Con Acoso)', 'icon' => 'check_circle', 'deadline_days' => null]
        ];
    }

    public function getActionsForState(string $state, array $case): array {
        $actions = [];
        $cid = $case['id'] ?? 0;
        if (!$cid) return [];

        if ($state === self::STATE_DETECCIO_COMUNICACIO) {
            $actions[] = ['key' => 'comunicar_inspeccion', 'label' => 'Anexo 1: Comunicación Inspección',   'style' => 'primary', 'onclick' => "openFollowupModal(cid, 'gal_anexo_1')"];
            $actions[] = ['key' => 'comunicar_familias',   'label' => 'Anexo 2: Comunicación Familias',     'style' => 'indigo',  'onclick' => "openFollowupModal(cid, 'gal_anexo_2')"];
            $actions[] = ['key' => 'medidas_urxentes',     'label' => 'Medidas Urxentes',                   'style' => 'warning', 'onclick' => "openFollowupModal(cid, 'gal_medidas_urxentes')"];
            $actions[] = ['key' => 'pasar_recollida',      'label' => 'Pasar a Fase 2',                     'style' => 'success', 'onclick' => "nextPhase(cid, '".self::STATE_RECOLLIDA_INFORMACION."')"];
        }
        elseif ($state === self::STATE_RECOLLIDA_INFORMACION) {
            $actions[] = ['key' => 'entrevista_victima',      'label' => 'Anexo 4/5: Entrevista Vítima',             'style' => 'indigo',    'onclick' => "openFollowupModal(cid, 'gal_anexo_4')"];
            $actions[] = ['key' => 'entrevista_agresor',      'label' => 'Anexo 6/7: Entrevista Presunto Acosador',  'style' => 'indigo',    'onclick' => "openFollowupModal(cid, 'gal_anexo_6')"];
            $actions[] = ['key' => 'entrevista_observadores', 'label' => 'Anexo 8/9: Entrevista Observadores',       'style' => 'indigo',    'onclick' => "openFollowupModal(cid, 'gal_anexo_8')"];
            $actions[] = ['key' => 'informe_titor',           'label' => 'Anexo 10: Informe Titoría',                'style' => 'secondary', 'onclick' => "openFollowupModal(cid, 'gal_anexo_10')"];
            $actions[] = ['key' => 'pasar_analise',           'label' => 'Pasar a Fase 3',                           'style' => 'success',   'onclick' => "nextPhase(cid, '".self::STATE_ANALISE_MEDIDAS."')"];
        }
        elseif ($state === self::STATE_ANALISE_MEDIDAS) {
            $actions[] = ['key' => 'informe_valoracion', 'label' => 'Anexo 11: Informe de Valoración', 'style' => 'primary', 'onclick' => "openFollowupModal(cid, 'gal_anexo_11')"];
            $actions[] = ['key' => 'plan_intervencion',  'label' => 'Anexo 12: Plan de Intervención',  'style' => 'warning', 'onclick' => "openFollowupModal(cid, 'gal_anexo_12')"];
            $actions[] = ['key' => 'pasar_seguimento',   'label' => 'Pasar a Fase 4 (Seguimento)',     'style' => 'success', 'onclick' => "nextPhase(cid, '".self::STATE_SEGUIMENTO."')"];
            $actions[] = ['key' => 'pechar_sen_acoso',   'label' => 'Pechar sen Acoso',                'style' => 'danger',  'onclick' => "nextPhase(cid, '".self::STATE_PECHE_SEN_ACOSO."')"];
        }
        elseif ($state === self::STATE_SEGUIMENTO) {
            $actions[] = ['key' => 'acta_seguimento',  'label' => 'Anexo 13/14: Seguimento', 'style' => 'indigo', 'onclick' => "openFollowupModal(cid, 'gal_anexo_13')"];
            $actions[] = ['key' => 'pechar_con_acoso', 'label' => 'Pechar con Acoso',        'style' => 'danger', 'onclick' => "nextPhase(cid, '".self::STATE_PECHE_CON_ACOSO."')"];
        }
        elseif ($state === self::STATE_PECHE_CON_ACOSO || $state === self::STATE_PECHE_SEN_ACOSO) {
            $actions[] = ['key' => 'anexo_16_peche', 'label' => 'Anexo 16: Informe final de Peche', 'style' => 'primary', 'onclick' => "openFollowupModal(cid, 'gal_anexo_16')"];
            $actions[] = ['key' => 'tancar',         'label' => 'Finalizar',                        'style' => 'success', 'onclick' => "nextPhase(cid, 'tancament')"];
        }

        return $actions;
    }

    public function getDocuments(): array {
        return [
            ['code' => 'anexo_1',  'name' => 'Anexo 1 - Comunicación á Inspección Educativa',                          'required_state' => self::STATE_DETECCIO_COMUNICACIO],
            ['code' => 'anexo_2',  'name' => 'Anexo 2 - Comunicación ás familias',                                     'required_state' => self::STATE_DETECCIO_COMUNICACIO],
            ['code' => 'anexo_3',  'name' => 'Anexo 3 - Designación de profesorado para a recollida de información',   'required_state' => self::STATE_DETECCIO_COMUNICACIO],
            ['code' => 'anexo_4',  'name' => 'Anexo 4 - Entrevista co alumno/a presuntamente acosado/a',               'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_5',  'name' => 'Anexo 5 - Entrevista coa familia do alumno/a presuntamente acosado/a',   'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_6',  'name' => 'Anexo 6 - Entrevista co alumno/a presunto/a acosador/a',                 'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_7',  'name' => 'Anexo 7 - Entrevista coa familia do alumno/a presunto/a acosador/a',     'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_8',  'name' => 'Anexo 8 - Entrevista co alumnado observador',                            'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_9',  'name' => 'Anexo 9 - Entrevista coa familia do alumnado observador',                'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_10', 'name' => 'Anexo 10 - Informe do titor ou titora',                                  'required_state' => self::STATE_RECOLLIDA_INFORMACION],
            ['code' => 'anexo_11', 'name' => 'Anexo 11 - Informe de valoración e análise da información',              'required_state' => self::STATE_ANALISE_MEDIDAS],
            ['code' => 'anexo_12', 'name' => 'Anexo 12 - Plan de intervención',                                        'required_state' => self::STATE_ANALISE_MEDIDAS],
            ['code' => 'anexo_13', 'name' => 'Anexo 13 - Documento de seguimento (titoría)',                            'required_state' => self::STATE_SEGUIMENTO],
            ['code' => 'anexo_14', 'name' => 'Anexo 14 - Documento de seguimento (dirección/orientación)',              'required_state' => self::STATE_SEGUIMENTO],
            ['code' => 'anexo_15', 'name' => 'Anexo 15 - Derivación a servizos externos',                              'required_state' => self::STATE_SEGUIMENTO],
            ['code' => 'anexo_16', 'name' => 'Anexo 16 - Informe final e peche do caso',                               'required_state' => self::STATE_PECHE_CON_ACOSO]
        ];
    }

    public function getExclusiveTools(): array {
        return [
            'actuacion_ciberacoso',
            'medidas_urxentes'
        ];
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        if ($toState === 'tancament') {
            if ($fromState === self::STATE_PECHE_CON_ACOSO || $fromState === self::STATE_PECHE_SEN_ACOSO) {
                return true;
            }
            return "Non se pode finalizar o caso desde a fase actual ('$fromState'). Debe estar en estado de peche.";
        }

        $allowed = $this->getValidTransitions($fromState);
        if (!in_array($toState, $allowed, true)) {
            return "Transición non permitida no protocolo de Galicia: de '$fromState' a '$toState'.";
        }
        return true;
    }
}
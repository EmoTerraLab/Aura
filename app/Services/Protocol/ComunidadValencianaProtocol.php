<?php
namespace App\Services\Protocol;

/**
 * ComunidadValencianaProtocol - Protocol d'actuació davant supòsits d'assetjament escolar.
 * Basat en l'Annex I de l'Ordre 62/2014 de la Conselleria d'Educació (Generalitat Valenciana).
 */
class ComunidadValencianaProtocol implements ProtocolInterface {

    // Estats del Protocol CV
    public const STATE_DETECCIO = 'deteccio';
    public const STATE_COMUNICACIO_DIRECCIO = 'comunicacio_direccio';
    public const STATE_PRIMERES_ACTUACIONS = 'primeres_actuacions';
    public const STATE_RECOLLIDA_INFORMACIO = 'recollida_informacio';
    public const STATE_VALORACIO = 'valoracio';
    public const STATE_NO_ACREDITAT = 'no_acreditat';
    public const STATE_ACREDITAT = 'acreditat';
    public const STATE_PLA_INTERVENCIO = 'pla_intervencio';
    public const STATE_SEGUIMENT = 'seguiment';
    public const STATE_TANCAMENT = 'tancament';
    public const STATE_REOBERT = 'reobert';

    public function getCode(): string {
        return 'comunidad_valenciana';
    }

    public function getName(): string {
        return 'Comunitat Valenciana';
    }

    public function isFullyImplemented(): bool {
        return true;
    }

    public function getManageUrl(int $caseId): string {
        // Seguint el patró de Catalunya, s'integra en el dashboard de staff
        return "/staff/reports/{$caseId}";
    }

    public function getLegalReference(): string {
        return "Annex I de l'Ordre 62/2014 + Decret 39/2008 + Orde 3/2017 (UEO) + Orde 20/2019";
    }

    public function getInitialState(): string {
        return self::STATE_DETECCIO;
    }

    public function getAllStates(): array {
        return [
            self::STATE_DETECCIO,
            self::STATE_COMUNICACIO_DIRECCIO,
            self::STATE_PRIMERES_ACTUACIONS,
            self::STATE_RECOLLIDA_INFORMACIO,
            self::STATE_VALORACIO,
            self::STATE_NO_ACREDITAT,
            self::STATE_ACREDITAT,
            self::STATE_PLA_INTERVENCIO,
            self::STATE_SEGUIMENT,
            self::STATE_TANCAMENT,
            self::STATE_REOBERT
        ];
    }

    public function getValidTransitions(string $currentState): array {
        return match($currentState) {
            self::STATE_DETECCIO => [self::STATE_COMUNICACIO_DIRECCIO],
            self::STATE_COMUNICACIO_DIRECCIO => [self::STATE_PRIMERES_ACTUACIONS],
            self::STATE_PRIMERES_ACTUACIONS => [self::STATE_RECOLLIDA_INFORMACIO],
            self::STATE_RECOLLIDA_INFORMACIO => [self::STATE_VALORACIO],
            self::STATE_VALORACIO => [self::STATE_ACREDITAT, self::STATE_NO_ACREDITAT],
            self::STATE_NO_ACREDITAT => [self::STATE_PLA_INTERVENCIO, self::STATE_TANCAMENT],
            self::STATE_ACREDITAT => [self::STATE_PLA_INTERVENCIO],
            self::STATE_PLA_INTERVENCIO => [self::STATE_SEGUIMENT],
            self::STATE_SEGUIMENT => [self::STATE_TANCAMENT, self::STATE_PLA_INTERVENCIO],
            self::STATE_TANCAMENT => [self::STATE_REOBERT],
            self::STATE_REOBERT => [self::STATE_PLA_INTERVENCIO],
            default => []
        };
    }

    public function getStateLabel(string $state): string {
        return match($state) {
            self::STATE_DETECCIO => 'Detecció',
            self::STATE_COMUNICACIO_DIRECCIO => 'Comunicació direcció',
            self::STATE_PRIMERES_ACTUACIONS => 'Primeres actuacions',
            self::STATE_RECOLLIDA_INFORMACIO => "Recollida d'informació",
            self::STATE_VALORACIO => 'Valoració',
            self::STATE_NO_ACREDITAT => 'No acreditat',
            self::STATE_ACREDITAT => 'Acreditat',
            self::STATE_PLA_INTERVENCIO => "Pla d'intervenció",
            self::STATE_SEGUIMENT => 'Seguiment',
            self::STATE_TANCAMENT => 'Tancament',
            self::STATE_REOBERT => 'Reobert',
            default => $state
        };
    }

    public function getTimelineSteps(): array {
        return [
            ['key' => self::STATE_DETECCIO, 'label' => 'Detecció', 'icon' => 'search', 'deadline_days' => null],
            ['key' => self::STATE_COMUNICACIO_DIRECCIO, 'label' => 'Comunicació direcció', 'icon' => 'envelope', 'deadline_days' => null],
            ['key' => self::STATE_PRIMERES_ACTUACIONS, 'label' => 'Primeres actuacions', 'icon' => 'shield-exclamation', 'deadline_days' => 1],
            ['key' => self::STATE_RECOLLIDA_INFORMACIO, 'label' => "Recollida d'informació", 'icon' => 'clipboard-list', 'deadline_days' => null],
            ['key' => self::STATE_VALORACIO, 'label' => 'Valoració', 'icon' => 'gavel', 'deadline_days' => null],
            ['key' => self::STATE_PLA_INTERVENCIO, 'label' => "Pla d'intervenció", 'icon' => 'flag', 'deadline_days' => null],
            ['key' => self::STATE_SEGUIMENT, 'label' => 'Seguiment', 'icon' => 'eye', 'deadline_days' => null],
            ['key' => self::STATE_TANCAMENT, 'label' => 'Tancament', 'icon' => 'check-circle', 'deadline_days' => null]
        ];
    }

    public function getActionsForState(string $state, array $case): array {
        $cid = $case['id'] ?? 0;
        return match($state) {
            self::STATE_DETECCIO => [
                ['key' => 'comunicar_direccio', 'label' => 'Comunicar a Direcció', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".self::STATE_COMUNICACIO_DIRECCIO."')"]
            ],
            self::STATE_COMUNICACIO_DIRECCIO => [
                ['key' => 'iniciar_primeres_actuacions', 'label' => 'Iniciar Primeres Actuacions', 'style' => 'primary', 'onclick' => "nextPhase($cid, '".self::STATE_PRIMERES_ACTUACIONS."')"],
                ['key' => 'sollicitar_assessorament_ueo', 'label' => 'Sol·licitar Assessorament UEO', 'style' => 'link', 'onclick' => "openFollowupModal($cid, 'ueo_assessorament')"]
            ],
            self::STATE_PRIMERES_ACTUACIONS => [
                ['key' => 'constituir_equip', 'label' => "Constituir Equip d'Intervenció", 'style' => 'primary', 'onclick' => "alert('Equip constituït (Dins de les 24h)')"],
                ['key' => 'organitzar_proteccio', 'label' => 'Organitzar Protecció i Vigilància', 'style' => 'warning', 'onclick' => "openSecurityMap($cid)"],
                ['key' => 'tutoria_afectiva', 'label' => 'Assignar Tutoria Afectiva', 'style' => 'secondary', 'onclick' => "alert('Tutoria afectiva assignada')"],
                ['key' => 'registre_previ', 'label' => 'Registre a PREVI-ITACA', 'style' => 'indigo', 'onclick' => "alert('Registre realitzat')"],
                ['key' => 'passar_recollida', 'label' => "Passar a Recollida d'Informació", 'style' => 'success', 'onclick' => "nextPhase($cid, '".self::STATE_RECOLLIDA_INFORMACIO."')"]
            ],
            self::STATE_RECOLLIDA_INFORMACIO => [
                ['key' => 'entrevista_alumnat', 'label' => 'Entrevistes Individuals Alumnat', 'style' => 'primary', 'onclick' => "alert('Entrevistes registrades')"],
                ['key' => 'entrevista_families', 'label' => 'Entrevistes Famílies', 'style' => 'primary', 'onclick' => "alert('Entrevistes families registrades')"],
                ['key' => 'custodia_evidencies', 'label' => "Custòdia d'Evidències", 'style' => 'warning', 'onclick' => "alert('Evidències custodiades')"],
                ['key' => 'passar_valoracio', 'label' => 'Passar a Valoració', 'style' => 'success', 'onclick' => "nextPhase($cid, '".self::STATE_VALORACIO."')"]
            ],
            self::STATE_VALORACIO => [
                ['key' => 'acreditat', 'label' => 'Assetjament Acreditat', 'style' => 'danger', 'onclick' => "protocolClassify($cid, '".self::STATE_ACREDITAT."')"],
                ['key' => 'no_acreditat', 'label' => 'No Acreditat', 'style' => 'secondary', 'onclick' => "protocolClassify($cid, '".self::STATE_NO_ACREDITAT."')"]
            ],
            self::STATE_ACREDITAT => [
                ['key' => 'comunicar_families', 'label' => 'Comunicar a Famílies', 'style' => 'primary', 'onclick' => "alert('Famílies comunicades')"],
                ['key' => 'notificar_previ', 'label' => 'Notificar a PREVI-ITACA', 'style' => 'indigo', 'onclick' => "alert('Notificació realitzada')"],
                ['key' => 'informar_comissio', 'label' => "Informar Comissió d'Igualtat", 'style' => 'secondary', 'onclick' => "alert('Comissió informada')"],
                ['key' => 'nomenar_instructor', 'label' => "Nomenar Instructor/a", 'style' => 'warning', 'onclick' => "alert('Instructor nomenat')"],
                ['key' => 'iniciar_pla', 'label' => "Iniciar Pla d'Intervenció", 'style' => 'success', 'onclick' => "nextPhase($cid, '".self::STATE_PLA_INTERVENCIO."')"]
            ],
            self::STATE_NO_ACREDITAT => [
                ['key' => 'comunicar_families_no_acreditat', 'label' => 'Comunicar No Acreditació', 'style' => 'secondary', 'onclick' => "alert('Famílies informades')"],
                ['key' => 'pla_esbrinar', 'label' => "Pla per esbrinar causes", 'style' => 'primary', 'onclick' => "nextPhase($cid, '".self::STATE_PLA_INTERVENCIO."')"],
                ['key' => 'tancar_directament', 'label' => 'Tancar Directament', 'style' => 'dark', 'onclick' => "nextPhase($cid, '".self::STATE_TANCAMENT."')"]
            ],
            self::STATE_PLA_INTERVENCIO => [
                ['key' => 'mesures_educatives', 'label' => 'Mesures Educatives Correctores', 'style' => 'primary', 'onclick' => "alert('Mesures aplicades')"],
                ['key' => 'mesures_disciplinaries', 'label' => 'Mesures Disciplinàries', 'style' => 'warning', 'onclick' => "alert('Disciplinària aplicada')"],
                ['key' => 'mesures_suport', 'label' => 'Mesures de Suport i Acompanyament', 'style' => 'secondary', 'onclick' => "alert('Suport aplicat')"],
                ['key' => 'fiscalia_menors', 'label' => 'Comunicar Fiscalia de Menors', 'style' => 'danger', 'onclick' => "alert('Fiscalia comunicada')"],
                ['key' => 'intervencio_grupal', 'label' => 'Intervenció en Grup Classe', 'style' => 'indigo', 'onclick' => "alert('Intervenció realitzada')"],
                ['key' => 'passar_seguiment', 'label' => 'Passar a Seguiment', 'style' => 'success', 'onclick' => "nextPhase($cid, '".self::STATE_SEGUIMENT."')"]
            ],
            self::STATE_SEGUIMENT => [
                ['key' => 'sessio_seguiment', 'label' => 'Nova Sessió de Seguiment', 'style' => 'primary', 'onclick' => "openFollowupModal($cid)"],
                ['key' => 'tornar_pla', 'label' => "Tornar al Pla d'Intervenció", 'style' => 'warning', 'onclick' => "nextPhase($cid, '".self::STATE_PLA_INTERVENCIO."')"],
                ['key' => 'tancar', 'label' => 'Tancar Protocol', 'style' => 'success', 'onclick' => "nextPhase($cid, '".self::STATE_TANCAMENT."')"]
            ],
            self::STATE_TANCAMENT => [
                ['key' => 'reobrir', 'label' => 'Reobrir per Recaiguda', 'style' => 'danger-outline', 'onclick' => "nextPhase($cid, '".self::STATE_REOBERT."')"]
            ],
            self::STATE_REOBERT => [
                ['key' => 'reactivar_pla', 'label' => "Reactivar Pla d'Intervenció", 'style' => 'primary', 'onclick' => "nextPhase($cid, '".self::STATE_PLA_INTERVENCIO."')"]
            ],
            default => []
        };
    }

    public function getDocuments(): array {
        return [
            ['code' => 'annex_i_ordre_62_2014', 'name' => "Annex I — Informe de l'equip d'intervenció (Ordre 62/2014)", 'annex_table' => null, 'required_state' => self::STATE_VALORACIO],
            ['code' => 'annex_ii_ordre_62_2014', 'name' => "Annex II — Comunicació a la comissió de convivència", 'annex_table' => null, 'required_state' => self::STATE_ACREDITAT],
            ['code' => 'annex_vii_ordre_62_2014', 'name' => "Annex VII — Comunicació a la Fiscalia de Menors", 'annex_table' => null, 'required_state' => self::STATE_PLA_INTERVENCIO],
            ['code' => 'previ_notificacio', 'name' => "Notificació PREVI-ITACA", 'annex_table' => null, 'required_state' => self::STATE_ACREDITAT],
            ['code' => 'informe_direccio_tancament', 'name' => "Informe de la direcció — Tancament", 'annex_table' => null, 'required_state' => self::STATE_TANCAMENT]
        ];
    }

    public function getExclusiveTools(): array {
        return [
            'annex_i_ordre_62_2014',
            'annex_ii_ordre_62_2014',
            'annex_vii_ordre_62_2014',
            'previ_itaca',
            'informe_direccio_tancament',
            'especificacio_lgtbifobia',
            'especificacio_discapacitat',
            'tutoria_afectiva',
            'comissio_convivencia',
            'ueo_assessorament',
            'inspeccio_educacio',
            'fiscalia_menors',
            'mesures_educatives_correctores',
            'mesures_disciplinaries',
            'mesures_suport',
            'metode_no_inculpacio',
            'metode_pikas'
        ];
    }

    public function getDeadlineForState(string $state): ?int {
        return ($state === self::STATE_PRIMERES_ACTUACIONS) ? 1 : null;
    }

    public function getDeadlineAlert(string $state, int $schoolDaysElapsed): ?array {
        if ($state === self::STATE_PRIMERES_ACTUACIONS) {
            if ($schoolDaysElapsed <= 0) {
                return ['level' => 'ok', 'message' => "Dins del termini de 24 hores per constituir l'equip d'intervenció"];
            } elseif ($schoolDaysElapsed === 1) {
                return ['level' => 'warning', 'message' => "Termini límit avui: l'equip d'intervenció ha d'estar constituït"];
            } else {
                return ['level' => 'overdue', 'message' => "Termini superat: l'equip d'intervenció havia d'estar constituït en 24h"];
            }
        }
        return null;
    }

    public function canTransition(string $fromState, string $toState, array $case): bool|string {
        $allowed = $this->getValidTransitions($fromState);
        
        if (!in_array($toState, $allowed)) {
            return "Transició no vàlida de '$fromState' a '$toState' en el protocol de la Comunitat Valenciana.";
        }

        return true;
    }
}

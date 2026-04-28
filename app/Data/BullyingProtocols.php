<?php

namespace App\Data;

class BullyingProtocols
{
    public static function get(string $ccaa): ?array
    {
        $protocols = self::all();
        return $protocols[$ccaa] ?? null;
    }

    public static function getCommunities(): array
    {
        return array_map(fn($p) => [
            'code'  => $p['code'],
            'name'  => $p['name'],
            'color' => $p['color'],
        ], self::all());
    }

    public static function all(): array
    {
        return [
            'cataluna'  => self::cataluna(),
            'andalucia' => self::andalucia(),
            'madrid'    => self::madrid(),
            'valencia'  => self::valencia(),
            'galicia'   => self::galicia(),
            'pais_vasco' => self::paisVasco(),
            'canarias'  => self::canarias(),
            'castilla_leon' => self::castillaLeon(),
            'castilla_la_mancha' => self::castillaLaMancha(),
            'murcia'    => self::murcia(),
            'aragon'    => self::aragon(),
            'baleares'  => self::baleares(),
            'extremadura' => self::extremadura(),
            'asturias'  => self::asturias(),
            'navarra'   => self::navarra(),
            'cantabria' => self::cantabria(),
            'la_rioja'  => self::laRioja(),
        ];
    }

    private static function cataluna(): array {
        return [
            'code' => 'cataluna',
            'name' => 'Catalunya',
            'authority' => 'Departament d\'Educació - Generalitat de Catalunya',
            'document_title' => 'Protocol d\'actuació davant de qualsevol tipus de violència en l\'àmbit educatiu',
            'document_date' => 'Juliol 2024',
            'document_url' => 'https://educacio.gencat.cat/ca/arees-actuacio/centres-serveis-educatius/convivencia-mediacio/protocols/',
            'color' => '#7C3AED',
            'key_principles' => [
                'Perspectiva de gènere i LGBTIQ+',
                'Perspectiva interseccional i antiracista',
                'Enfocament Restauratiu Global (ERG)',
                'Interès superior de l\'infant',
                'Tolerància zero davant qualsevol violència'
            ],
            'types_of_violence' => [
                'Assetjament escolar i ciberassetjament',
                'Conductes d\'odi i discriminació',
                'Violències masclistes',
                'Violències sexuals entre alumnat'
            ],
            'applicable_to' => [
                'Tots els centres públics i privats concertats de Catalunya',
                'Conductes dins i fora del centre que afectin l\'alumnat'
            ],
            'phases' => [
                ['number' => 1, 'title' => 'Detecció i identificació', 'description' => 'Comunicació a la direcció del centre davant sospita.', 'responsible' => 'Qualsevol membre de la comunitat', 'timeframe' => 'Immediat', 'actions' => ['Identificar tipus violència', 'Comunicar a direcció', 'Registrar al REVA']],
                ['number' => 2, 'title' => 'Valoració i diagnosi', 'description' => 'Recollida d\'informació i tipificació.', 'responsible' => 'Equip de valoració', 'timeframe' => '48-72 hores', 'actions' => ['Entrevistes', 'Elaborar informe', 'Tipificar situació']],
                ['number' => 3, 'title' => 'Comunicació', 'description' => 'Informar a agents implicats i inspecció.', 'responsible' => 'Direcció', 'timeframe' => 'Immediat', 'actions' => ['Informar famílies', 'Comunicar a Inspecció', 'Avís a autoritats si cal']],
                ['number' => 4, 'title' => 'Intervenció educativa', 'description' => 'Mesures de protecció i pla de treball.', 'responsible' => 'Direcció i tutors', 'timeframe' => 'Continuat', 'actions' => ['Mesures d\'urgència', 'Pla de treball individual', 'Intervenció grup-classe']],
                ['number' => 5, 'title' => 'Seguiment i tancament', 'description' => 'Avaluació de la resolució del cas.', 'responsible' => 'Direcció i tutors', 'timeframe' => 'Mínim 3 mesos', 'actions' => ['Entrevistes seguiment', 'Avaluació clima aula', 'Tancament formal']]
            ],
            'emergency_contacts' => [
                ['name' => 'USAV', 'description' => 'Suport Violència', 'contact' => 'Via REVA'],
                ['name' => 'Mossos d\'Esquadra', 'description' => 'Emergències', 'contact' => '112 / 088']
            ]
        ];
    }

    private static function andalucia(): array {
        return [
            'code' => 'andalucia',
            'name' => 'Andalucía',
            'authority' => 'Junta de Andalucía',
            'document_title' => 'Protocolo de actuación ante acoso escolar',
            'color' => '#16A34A',
            'phases' => [
                ['number' => 1, 'title' => 'Detección', 'responsible' => 'Comunidad educativa', 'timeframe' => 'Inmediato', 'actions' => ['Comunicación tutor/dirección']],
                ['number' => 2, 'title' => 'Valoración inicial', 'responsible' => 'Equipo directivo', 'timeframe' => '24h', 'actions' => ['Apertura de expediente', 'Medidas cautelares']],
                ['number' => 3, 'title' => 'Instrucción', 'responsible' => 'Instructor designado', 'timeframe' => '10 días', 'actions' => ['Entrevistas', 'Recogida evidencias']],
                ['number' => 4, 'title' => 'Medidas', 'responsible' => 'Dirección', 'timeframe' => 'Continuado', 'actions' => ['Plan de actuación', 'Intervención']],
                ['number' => 5, 'title' => 'Seguimiento', 'responsible' => 'Tutor/Orientación', 'timeframe' => 'Mínimo un trimestre', 'actions' => ['Informes periódicos']]
            ],
            'emergency_contacts' => [['name' => 'Infancia TSAI', 'contact' => '900 20 20 20']]
        ];
    }

    private static function madrid(): array {
        return [
            'code' => 'madrid',
            'name' => 'Madrid',
            'authority' => 'Comunidad de Madrid',
            'document_title' => 'Protocolo de acoso escolar CM',
            'color' => '#DC2626',
            'phases' => [
                ['number' => 1, 'title' => 'Comunicación', 'responsible' => 'Director', 'timeframe' => 'Inmediato', 'actions' => ['Informar a Inspección']],
                ['number' => 2, 'title' => 'Investigación', 'responsible' => 'Equipo directivo', 'timeframe' => '10 días', 'actions' => ['Informe de orientación']],
                ['number' => 3, 'title' => 'Actuaciones', 'responsible' => 'Centro', 'timeframe' => 'Continuado', 'actions' => ['Medidas educativas']]
            ],
            'emergency_contacts' => [['name' => 'Inspección Madrid', 'contact' => 'DAT correspondiente']]
        ];
    }

    private static function valencia(): array {
        return [
            'code' => 'valencia',
            'name' => 'C. Valenciana',
            'authority' => 'Generalitat Valenciana',
            'color' => '#F59E0B',
            'phases' => [['number' => 1, 'title' => 'Detecció', 'responsible' => 'Centre', 'actions' => ['PREVI']]],
            'emergency_contacts' => [['name' => 'REVI', 'contact' => 'Registre Violència']]
        ];
    }

    private static function galicia(): array {
        return [
            'code' => 'galicia',
            'name' => 'Galicia',
            'authority' => 'Xunta de Galicia',
            'color' => '#3B82F6',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo xeral']]],
            'emergency_contacts' => [['name' => 'Inspección Galicia', 'contact' => 'Xefatura Territorial']]
        ];
    }

    private static function paisVasco(): array {
        return [
            'code' => 'pais_vasco',
            'name' => 'Euskadi',
            'authority' => 'Gobierno Vasco',
            'color' => '#10B981',
            'phases' => [['number' => 1, 'title' => 'Detekzioa', 'responsible' => 'Ikastetxea', 'actions' => ['Bizikasi']]],
            'emergency_contacts' => [['name' => 'Berritzegune', 'contact' => 'Sare Hezkuntza']]
        ];
    }

    private static function canarias(): array {
        return [
            'code' => 'canarias',
            'name' => 'Canarias',
            'authority' => 'Gobierno de Canarias',
            'color' => '#FACC15',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo canario']]],
            'emergency_contacts' => [['name' => 'Emergencias', 'contact' => '112']]
        ];
    }

    private static function castillaLeon(): array {
        return [
            'code' => 'castilla_leon',
            'name' => 'Castilla y León',
            'authority' => 'Junta de Castilla y León',
            'color' => '#94A3B8',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Plan de Convivencia']]],
            'emergency_contacts' => [['name' => 'Inspección CyL', 'contact' => 'Dirección Provincial']]
        ];
    }

    private static function castillaLaMancha(): array {
        return [
            'code' => 'castilla_la_mancha',
            'name' => 'Castilla-La Mancha',
            'authority' => 'Junta de Comunidades de CLM',
            'color' => '#B45309',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Unidad convivencial']]],
            'emergency_contacts' => [['name' => 'Inspección CLM', 'contact' => 'Delegación']]
        ];
    }

    private static function murcia(): array {
        return [
            'code' => 'murcia',
            'name' => 'R. de Murcia',
            'authority' => 'Región de Murcia',
            'color' => '#BE123C',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Observatorio']]],
            'emergency_contacts' => [['name' => 'Inspección Murcia', 'contact' => 'Servicio Inspección']]
        ];
    }

    private static function aragon(): array {
        return [
            'code' => 'aragon',
            'name' => 'Aragón',
            'authority' => 'Gobierno de Aragón',
            'color' => '#EA580C',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo aragonés']]],
            'emergency_contacts' => [['name' => 'Inspección Aragón', 'contact' => 'Servicio Provincial']]
        ];
    }

    private static function baleares(): array {
        return [
            'code' => 'baleares',
            'name' => 'Illes Balears',
            'authority' => 'Govern Balear',
            'color' => '#F97316',
            'phases' => [['number' => 1, 'title' => 'Detecció', 'responsible' => 'Centre', 'actions' => ['CONVIVÈXIT']]],
            'emergency_contacts' => [['name' => 'Convivèxit', 'contact' => 'Govern Balear']]
        ];
    }

    private static function extremadura(): array {
        return [
            'code' => 'extremadura',
            'name' => 'Extremadura',
            'authority' => 'Junta de Extremadura',
            'color' => '#065F46',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo extremeño']]],
            'emergency_contacts' => [['name' => 'Inspección Ex', 'contact' => 'Delegación']]
        ];
    }

    private static function asturias(): array {
        return [
            'code' => 'asturias',
            'name' => 'Asturias',
            'authority' => 'Principado de Asturias',
            'color' => '#0369A1',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo asturiano']]],
            'emergency_contacts' => [['name' => 'Inspección Asturias', 'contact' => 'Consejería']]
        ];
    }

    private static function navarra(): array {
        return [
            'code' => 'navarra',
            'name' => 'Navarra',
            'authority' => 'Gobierno de Navarra',
            'color' => '#9F1239',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Laguntza']]],
            'emergency_contacts' => [['name' => 'Inspección Navarra', 'contact' => 'Navarra']]
        ];
    }

    private static function cantabria(): array {
        return [
            'code' => 'cantabria',
            'name' => 'Cantabria',
            'authority' => 'Gobierno de Cantabria',
            'color' => '#E11D48',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo cántabro']]],
            'emergency_contacts' => [['name' => 'Inspección Cantabria', 'contact' => 'Cantabria']]
        ];
    }

    private static function laRioja(): array {
        return [
            'code' => 'la_rioja',
            'name' => 'La Rioja',
            'authority' => 'Gobierno de La Rioja',
            'color' => '#DC2626',
            'phases' => [['number' => 1, 'title' => 'Detección', 'responsible' => 'Centro', 'actions' => ['Protocolo riojano']]],
            'emergency_contacts' => [['name' => 'Inspección Rioja', 'contact' => 'Logroño']]
        ];
    }
}

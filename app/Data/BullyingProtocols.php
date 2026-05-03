<?php
namespace App\Data;

class BullyingProtocols {
    public static function getAll(): array {
        return [
            'ARA' => [
                'metadata' => [
                    'code' => 'ARA',
                    'name' => 'Aragón',
                    'authority' => 'Gobierno de Aragón - Departamento de Educación',
                    'document_title' => 'Protocolo de actuación inmediata ante posibles situaciones de acoso escolar',
                    'document_date' => 'Resolución 19/10/2018',
                    'document_url' => 'https://educa.aragon.es/convivencia',
                    'color' => '#E30613',
                    'main_tool' => 'Anexos I al X',
                ],
                'content' => [
                    'key_principles' => [
                        'Protección inmediata a la víctima',
                        'Intervención eficaz, rápida y no precipitada',
                        'Discreción y confidencialidad',
                        'Transparencia y diálogo con las familias',
                    ],
                    'types_of_violence' => [
                        'Física',
                        'Psicológica',
                        'Social / Relacional',
                        'Ciberacoso',
                        'Sexual',
                    ],
                    'applicable_to' => 'Centros públicos y privados concertados de Aragón.',
                ],
                'phases' => [
                    [
                        'id' => 'deteccio',
                        'number' => 1,
                        'title' => 'Fase 1: Detección, comunicación y planificación',
                        'description' => 'Comunicación inicial y medidas urgentes de protección.',
                        'responsible' => 'Dirección',
                        'actions' => [
                            'Comunicación (Anexo I-a)',
                            'Decisión de inicio (Anexo I-b)',
                            'Medidas de protección (Anexo II)',
                        ],
                        'timeframe' => '1-2 días lectivos',
                        'tools' => 'Anexos I, II, III, IV',
                    ],
                    [
                        'id' => 'investigacion',
                        'number' => 2,
                        'title' => 'Fase 2: Proceso de recogida de información',
                        'description' => 'Investigación detallada mediante entrevistas y observación.',
                        'responsible' => 'Equipo de Valoración',
                        'actions' => [
                            'Entrevistas (Anexo V)',
                            'Registro de indicadores (Anexo VI)',
                        ],
                        'timeframe' => 'Hasta 18 días lectivos',
                        'tools' => 'Anexos V, VI',
                    ],
                    [
                        'id' => 'resolucion',
                        'number' => 3,
                        'title' => 'Fase 3: Análisis y toma de decisiones',
                        'description' => 'Valoración final del caso y notificación a Inspección.',
                        'responsible' => 'Dirección',
                        'actions' => [
                            'Acta de valoración (Anexo VII)',
                            'Informe-Resumen (Anexo VIII)',
                        ],
                        'timeframe' => 'Día 20-22 lectivo',
                        'tools' => 'Anexos VII, VIII',
                    ],
                    [
                        'id' => 'seguimiento',
                        'number' => 4,
                        'title' => 'Fase 4: Plan de supervisión y seguimiento',
                        'description' => 'Vigilancia periódica de la evolución de la situación.',
                        'responsible' => 'Responsable de seguimiento',
                        'actions' => [
                            'Registro de seguimiento (Anexo IX)',
                            'Acta de cierre (Anexo X)',
                        ],
                        'timeframe' => 'Indeterminado',
                        'tools' => 'Anexos IX, X',
                    ],
                ],
                'emergency_contacts' => [
                    ['name' => 'Guardia Civil (Rural)', 'description' => 'Emergencias en ámbito rural', 'contact' => '062'],
                    ['name' => 'Policía Nacional (Urbano)', 'description' => 'Emergencias en ámbito urbano', 'contact' => '091'],
                    ['name' => 'GRUME Zaragoza', 'description' => 'Grupo de Menores de Policía', 'contact' => '976 344 393'],
                    ['name' => 'EOE Convivencia Escolar', 'description' => 'Equipo de orientación autonómico', 'contact' => 'equipoconvivencia@aragon.es'],
                ],
            ],
            'CAT' => [
                'metadata' => [
                    'code' => 'CAT',
                    'name' => 'Catalunya',
                    'authority' => "Departament d'Educació - Generalitat de Catalunya",
                    'document_title' => "Protocol d'actuació davant de qualsevol tipus de violència en l'àmbit educatiu",
                    'document_date' => "Juliol 2024",
                    'document_url' => "https://educacio.gencat.cat/ca/escola/convivencia-benestar/protocols-actuacio/",
                    'color' => '#7C3AED',
                    'main_tool' => "REVA (Registre de Violències de l'Alumnat)"
                ],
                'content' => [
                    'key_principles' => [
                        "Protecció i benestar de l'alumnat com a prioritat absoluta.",
                        "Enfocament restauratiu i no punitiu.",
                        "Intervenció immediata i coordinada.",
                        "Confidencialitat i protecció de dades."
                    ],
                    'types_of_violence' => [
                        "Violència física, verbal, emocional o social.",
                        "Ciberassetjament.",
                        "Violència masclista.",
                        "LGTBI-fòbia.",
                        "Assetjament escolar (bullying)."
                    ],
                    'applicable_to' => "Tots els centres educatius de Catalunya."
                ],
                'phases' => [
                    [
                        'id' => 'deteccio',
                        'number' => 1,
                        'title' => "Detecció i identificació",
                        'description' => "Qualsevol membre de la comunitat educativa que tingui coneixement d'una possible situació de violència l'ha de comunicar a la direcció.",
                        'responsible' => "Direcció del centre",
                        'actions' => [
                            "Recollida de la informació inicial.",
                            "Activació immediata del protocol si hi ha indicis.",
                            "Comunicació a la família si no hi ha risc per a l'alumne."
                        ],
                        'timeframe' => "Immediat",
                        'tools' => "Formulari de comunicació inicial"
                    ],
                    [
                        'id' => 'valoracio',
                        'number' => 2,
                        'title' => "Valoració: diagnosi i tipificació",
                        'description' => "L'equip de valoració analitza la situació per determinar si es tracta de violència i de quin tipus.",
                        'responsible' => "Equip de valoració (Direcció, Tutor/a, Orientador/a)",
                        'actions' => [
                            "Entrevistes amb les persones implicades (sense confrontació).",
                            "Anàlisi del clima de grup.",
                            "Tipificació del cas al sistema REVA."
                        ],
                        'timeframe' => "48-72 hores",
                        'tools' => "REVA"
                    ],
                    [
                        'id' => 'comunicacio',
                        'number' => 3,
                        'title' => "Comunicació",
                        'description' => "Notificació formal a les autoritats i a les famílies de les decisions preses.",
                        'responsible' => "Direcció",
                        'actions' => [
                            "Notificació a la Inspecció d'Educació.",
                            "Comunicació oficial a les famílies.",
                            "Si s'escau, denúncia a Mossos d'Esquadra."
                        ],
                        'timeframe' => "Immediat després de la valoració",
                        'tools' => "Models de notificació oficial"
                    ],
                    [
                        'id' => 'intervencio',
                        'number' => 4,
                        'title' => "Intervenció educativa",
                        'description' => "Aplicació de mesures de protecció per a la víctima i mesures educatives per a l'agressor.",
                        'responsible' => "Equip docent i orientació",
                        'actions' => [
                            "Pla de seguretat per a la víctima.",
                            "Mesures educatives i restauratives.",
                            "Treball amb el grup-classe."
                        ],
                        'timeframe' => "Continuat",
                        'tools' => "Pla de treball individualitzat"
                    ],
                    [
                        'id' => 'tancament',
                        'number' => 5,
                        'title' => "Seguiment i tancament",
                        'description' => "Avaluació de l'eficàcia de les mesures i finalització del protocol quan s'ha resolt el conflicte.",
                        'responsible' => "Direcció",
                        'actions' => [
                            "Reunions de seguiment periòdiques.",
                            "Avaluació final de la situació de convivència.",
                            "Registre del tancament al sistema REVA."
                        ],
                        'timeframe' => "A mitjà/llarg termini",
                        'tools' => "Informe final de tancament"
                    ]
                ],
                'emergency_contacts' => [
                    [
                        'name' => "Infància Respon",
                        'description' => "Servei d'atenció permanent per a maltractaments a menors.",
                        'contact' => "116 111"
                    ],
                    [
                        'name' => "Mossos d'Esquadra",
                        'description' => "Emergències policials.",
                        'contact' => "112"
                    ],
                    [
                        'name' => "Unitat de Suport a l'Alumnat en Situació de Violència (USAV)",
                        'description' => "Suport tècnic i acompanyament.",
                        'contact' => "900 10 73 00"
                    ],
                    [
                        'name' => "Barnahus (Coordinació General)",
                        'description' => "Derivació obligatòria per violència sexual.",
                        'contact' => "935 126 939 / 677 389 352 (coordinaciogeneral.barnahus@gencat.cat)"
                    ]
                ]
            ],
            'VAL' => [
                'metadata' => [
                    'code' => 'VAL',
                    'name' => 'Comunitat Valenciana',
                    'authority' => 'Conselleria d’Educació, Cultura i Esport (Generalitat Valenciana)',
                    'document_title' => "Annex I de l'Ordre 62/2014, de 28 de juliol",
                    'document_date' => '2014-2019',
                    'document_url' => 'https://ceice.gva.es/va/web/convivencia-escolar/assetjament-escolar',
                    'color' => '#f97316',
                    'main_tool' => 'PREVI-ITACA',
                ],
                'content' => [
                    'key_principles' => [
                        "Prioritat absoluta a la protecció de l'alumnat.",
                        "Intervenció immediata (termini de 24h per a constituir l'equip).",
                        "Confidencialitat i cura en el tractament de la informació.",
                        "Coordinació amb la UEO (Unitat Especialitzada d’Orientació)."
                    ],
                    'types_of_violence' => [
                        "Física i verbal.",
                        "Psicològica i social.",
                        "Ciberassetjament.",
                        "LGTBIfòbia o per motius de discapacitat."
                    ],
                    'applicable_to' => "Tots els centres docents de la Comunitat Valenciana."
                ],
                'phases' => [
                    [
                        'id' => 'deteccio',
                        'number' => 1,
                        'title' => 'Detecció i Comunicació',
                        'description' => "Qualsevol indici ha de ser comunicat immediatament a la direcció del centre.",
                        'responsible' => 'Direcció / Equip Docent',
                        'actions' => [
                            'Recollida de dades inicials.',
                            'Comunicació urgent a la direcció.'
                        ],
                        'timeframe' => 'Immediat',
                        'tools' => 'Informe inicial'
                    ],
                    [
                        'id' => 'primeres_actuacions',
                        'number' => 2,
                        'title' => 'Primeres Actuacions (24h)',
                        'description' => "Constitució de l'equip d'intervenció i mesures urgents de protecció.",
                        'responsible' => "Equip Directiu",
                        'actions' => [
                            "Constitució de l'equip d'intervenció.",
                            "Organització de la protecció i vigilància.",
                            "Assignació de tutoria afectiva.",
                            "Registre a PREVI-ITACA."
                        ],
                        'timeframe' => '24 hores',
                        'tools' => 'PREVI-ITACA'
                    ],
                    [
                        'id' => 'recollida',
                        'number' => 3,
                        'title' => "Recollida d'Informació",
                        'description' => "Anàlisi detallada mitjançant entrevistes i observació directa.",
                        'responsible' => "Equip d'intervenció",
                        'actions' => [
                            "Entrevistes individuals amb alumnat.",
                            "Entrevistes amb les famílies.",
                            "Custòdia d'evidències."
                        ],
                        'timeframe' => 'Màxim 10 dies lectivos',
                        'tools' => 'Annex I (Ordre 62/2014)'
                    ],
                    [
                        'id' => 'valoracio',
                        'number' => 4,
                        'title' => 'Valoració i Classificació',
                        'description' => "Determinació de si la situació es classifica com a assetjament o no.",
                        'responsible' => "Direcció / Equip d'intervenció",
                        'actions' => [
                            "Emissió de l'informe de valoració.",
                            "Classificació (Acreditat / No acreditat).",
                            "Comunicació a les famílies."
                        ],
                        'timeframe' => 'Immediat després de la recollida',
                        'tools' => 'PREVI-ITACA'
                    ],
                    [
                        'id' => 'intervencio',
                        'number' => 5,
                        'title' => "Pla d'Intervenció",
                        'description' => "Aplicació de mesures educatives, disciplinàries i de suport.",
                        'responsible' => "Equip docent i orientació",
                        'actions' => [
                            "Mesures educatives correctores.",
                            "Mesures disciplinàries si s'escau.",
                            "Intervenció en grup classe.",
                            "Comunicació a Fiscalia de Menors (si escau)."
                        ],
                        'timeframe' => 'Continuat',
                        'tools' => 'Mesures de suport'
                    ],
                    [
                        'id' => 'seguiment',
                        'number' => 6,
                        'title' => 'Seguiment i Tancament',
                        'description' => "Avaluació periòdica de les mesures i tancament del protocol.",
                        'responsible' => "Direcció",
                        'actions' => [
                            "Sessions de seguiment.",
                            "Informe final de la direcció.",
                            "Tancament a PREVI-ITACA."
                        ],
                        'timeframe' => 'Variable',
                        'tools' => 'PREVI-ITACA'
                    ]
                ],
                'emergency_contacts' => [
                    [
                        'name' => "Emergències (Generalitat Valenciana)",
                        'description' => "Atenció d'emergències.",
                        'contact' => "112"
                    ],
                    [
                        'name' => "Assistència a la Infància",
                        'description' => "Servei d'atenció telefònica gratuïta.",
                        'contact' => "116 111"
                    ],
                    [
                        'name' => "UEO - Unitat Especialitzada d’Orientació",
                        'description' => "Suport tècnic en convivència.",
                        'contact' => "ceice.gva.es"
                    ]
                ]
            ],
            'MUR' => [
                'metadata' => [
                    'code' => 'MUR',
                    'name' => 'Región de Murcia',
                    'authority' => 'Consejería de Educación, Formación Profesional y Empleo (Región de Murcia)',
                    'document_title' => 'Protocolo de actuación ante situaciones de acoso escolar en centros docentes',
                    'document_date' => '2024',
                    'document_url' => 'https://www.carm.es/web/pagina?IDCONTENIDO=15053&IDTIPO=100&RASTRO=c77$m',
                    'color' => '#facc15',
                    'main_tool' => 'Anexos I al VI',
                ],
                'content' => [
                    'key_principles' => [
                        'Adopción inmediata de medidas de protección urgentes',
                        'Designación de equipo de intervención (Día 0)',
                        'Orden estricto de entrevistas',
                        'Colaboración con EOEP Específico de Convivencia',
                    ],
                    'types_of_violence' => [
                        'Física y verbal',
                        'Exclusión social y psicológica',
                        'Ciberacoso',
                        'Acoso por identidad de género o discapacidad',
                    ],
                    'applicable_to' => 'Todos los centros docentes de la Región de Murcia.',
                ],
                'phases' => [
                    [
                        'id' => 'mur_inicial',
                        'number' => 1,
                        'title' => 'Designación y Medidas Urgentes',
                        'description' => 'Inicio del protocolo, designación del equipo y comunicación a Inspección (Anexo I).',
                        'responsible' => 'Dirección',
                        'actions' => [
                            'Designación del equipo de intervención',
                            'Medidas urgentes de seguridad',
                            'Envío de Anexo I a Inspección',
                        ],
                        'timeframe' => 'Día 0',
                        'tools' => 'Anexo I',
                    ],
                    [
                        'id' => 'mur_intervencio',
                        'number' => 2,
                        'title' => 'Intervención y Entrevistas',
                        'description' => 'Realización de entrevistas en orden estricto y recogida de datos.',
                        'responsible' => 'Jefatura de Estudios / Equipo Intervención',
                        'actions' => [
                            'Entrevistas (Víctima -> Observadores -> Familias -> Agresores)',
                            'Asesoramiento EOEP',
                        ],
                        'timeframe' => 'Días 1-20 lectivos',
                        'tools' => 'Pautas de entrevista oficiales',
                    ],
                    [
                        'id' => 'mur_informe',
                        'number' => 3,
                        'title' => 'Emisión del Informe',
                        'description' => 'Redacción del informe final del equipo de intervención (Anexo IV).',
                        'responsible' => 'Equipo de Intervención',
                        'actions' => [
                            'Elaboración del informe de intervención',
                            'Entrega a la dirección del centro',
                        ],
                        'timeframe' => 'Máximo 20 días lectivos',
                        'tools' => 'Anexo IV',
                    ],
                    [
                        'id' => 'mur_valoracion',
                        'number' => 4,
                        'title' => 'Valoración y Decisión',
                        'description' => 'Reunión conjunta para determinar la existencia de acoso (Anexo V).',
                        'responsible' => 'Dirección',
                        'actions' => [
                            'Acta de reunión conjunta',
                            'Determinación de evidencias',
                            'Elaboración Plan Seguimiento (Anexo VI) si procede',
                        ],
                        'timeframe' => 'Inmediato tras el informe',
                        'tools' => 'Acta y Anexo V',
                    ],
                    [
                        'id' => 'mur_cierre',
                        'number' => 5,
                        'title' => 'Cierre y Actuaciones Posteriores',
                        'description' => 'Comunicación a familias, Inspección y autoridades legales según edad.',
                        'responsible' => 'Dirección',
                        'actions' => [
                            'Entrega individualizada de Anexo V',
                            'Comunicación Servicios Sociales / Fiscalía / FCSE',
                        ],
                        'timeframe' => 'Variable',
                        'tools' => 'Anexo V / Notificaciones Legales',
                    ],
                ],
                'emergency_contacts' => [
                    ['name' => 'Emergencias Murcia', 'description' => 'Atención de emergencias 112', 'contact' => '112'],
                    ['name' => 'EOEP Específico Convivencia', 'description' => 'Equipo orientación específico', 'contact' => 'convivencia.escolar@murciaeduca.es'],
                    ['name' => 'Fiscalía de Menores Murcia', 'description' => 'Casos > 14 años', 'contact' => '968 27 75 00'],
                    ['name' => 'Servicios Sociales (Ayto)', 'description' => 'Casos < 14 años', 'contact' => 'Consultar municipio'],
                ],
            ],
            'AND' => [
                'metadata' => [
                    'code' => 'AND',
                    'name' => 'Andalucía',
                    'authority' => "Junta de Andalucía - Consejería de Desarrollo Educativo y Formación Profesional",
                    'document_title' => "Protocolo de actuación en casos de acoso escolar",
                    'document_date' => "2024",
                    'document_url' => "https://www.juntadeandalucia.es/educacion/portals/web/convivencia-escolar/protocolos",
                    'color' => '#16A34A',
                    'main_tool' => "Sistema Séneca"
                ],
                'content' => [
                    'key_principles' => [
                        "Prioridad del interés superior del menor.",
                        "Prevención y detección temprana.",
                        "Actuación inmediata y protección.",
                        "Coordinación interinstitucional."
                    ],
                    'types_of_violence' => [
                        "Maltrato físico y verbal.",
                        "Exclusión social y psicológica.",
                        "Ciberacoso.",
                        "Acoso por motivos de orientación sexual, identidad de género o etnia."
                    ],
                    'applicable_to' => "Centros docentes públicos y concertados de Andalucía."
                ],
                'phases' => [
                    [
                        'id' => 'identificacion',
                        'number' => 1,
                        'title' => "Identificación de la situación",
                        'description' => "Comunicación de cualquier indicio de acoso a la dirección del centro.",
                        'responsible' => "Dirección",
                        'actions' => [
                            "Entrevista inicial con quien informa.",
                            "Recogida de evidencias.",
                            "Apertura del expediente en Séneca."
                        ],
                        'timeframe' => "Inmediato",
                        'tools' => "Séneca"
                    ],
                    [
                        'id' => 'proteccion',
                        'number' => 2,
                        'title' => "Actuaciones inmediatas y protección",
                        'description' => "Adopción de medidas para garantizar la seguridad de las partes implicadas.",
                        'responsible' => "Equipo directivo",
                        'actions' => [
                            "Medidas cautelares de protección.",
                            "Comunicación a las familias.",
                            "Asignación de responsables de seguimiento."
                        ],
                        'timeframe' => "24-48 horas",
                        'tools' => "Plan de convivencia"
                    ],
                    [
                        'id' => 'valoracion',
                        'number' => 3,
                        'title' => "Valoración técnica y tipificación",
                        'description' => "Análisis por parte del equipo de orientación para confirmar la situación de acoso.",
                        'responsible' => "Equipo de Orientación / Departamento de Orientación",
                        'actions' => [
                            "Entrevistas individuales y grupales.",
                            "Observación sistemática.",
                            "Informe de valoración."
                        ],
                        'timeframe' => "Máximo 10 días lectivos",
                        'tools' => "Cuestionarios y plantillas oficiales"
                    ],
                    [
                        'id' => 'comunicacion',
                        'number' => 4,
                        'title' => "Comunicación y derivación",
                        'description' => "Informar a la Inspección Educativa y derivar a servicios externos si es necesario.",
                        'responsible' => "Dirección",
                        'actions' => [
                            "Notificación a Inspección de Educación.",
                            "Derivación a Salud o Servicios Sociales si procede.",
                            "Comunicación a Fiscalía si hay indicios de delito."
                        ],
                        'timeframe' => "Tras la valoración",
                        'tools' => "Modelos oficiales"
                    ],
                    [
                        'id' => 'intervencion',
                        'number' => 5,
                        'title' => "Plan de actuación y seguimiento",
                        'description' => "Implementación de medidas educativas y revisión periódica de la situación.",
                        'responsible' => "Tutor/a y Equipo Docente",
                        'actions' => [
                            "Programa de intervención con el alumnado implicado.",
                            "Acciones con el grupo de iguales.",
                            "Reuniones de seguimiento con familias."
                        ],
                        'timeframe' => "Continuo",
                        'tools' => "Séneca - Módulo de convivencia"
                    ]
                ],
                'emergency_contacts' => [
                    [
                        'name' => "Teléfono de asistencia a la infancia (Andalucía)",
                        'description' => "Atención a menores en situación de riesgo.",
                        'contact' => "900 85 18 11"
                    ],
                    [
                        'name' => "Emergencias",
                        'description' => "Atención inmediata.",
                        'contact' => "112"
                    ]
                ]
            ],
            'MAD' => [
                'metadata' => [
                    'code' => 'MAD',
                    'name' => 'Madrid',
                    'authority' => "Comunidad de Madrid - Consejería de Educación",
                    'document_title' => "Protocolo de actuación ante situaciones de acoso escolar",
                    'document_date' => "2024",
                    'document_url' => "https://www.comunidad.madrid/servicios/educacion/convivencia-escolar",
                    'color' => '#E30613',
                    'main_tool' => "SICE"
                ],
                'content' => [
                    'key_principles' => ["Inmediatez", "Confidencialidad", "Protección del menor"],
                    'types_of_violence' => ["Acoso físico", "Ciberacoso", "Exclusión"],
                    'applicable_to' => "Centros de la Comunidad de Madrid"
                ],
                'phases' => [
                    ['id' => 'f1', 'number' => 1, 'title' => 'Denuncia/Detección', 'description' => 'Comunicación a dirección.', 'responsible' => 'Cualquier miembro', 'actions' => ['Parte de incidencia'], 'timeframe' => '24h', 'tools' => ['SICE']],
                    ['id' => 'f2', 'number' => 2, 'title' => 'Actuaciones Urgentes', 'description' => 'Medidas cautelares.', 'responsible' => 'Dirección', 'actions' => ['Protección'], 'timeframe' => '48h', 'tools' => ['Plan de Convivencia']]
                ],
                'emergency_contacts' => [['name' => 'Emergencias', 'description' => 'General', 'contact' => '112']]
            ],
            'GAL' => [
                'metadata' => [
                    'code' => 'GAL',
                    'name' => 'Galicia',
                    'authority' => 'Consellería de Educación, Ciencia, Universidades e FP (Xunta de Galicia)',
                    'document_title' => 'Protocolo educativo para a prevención, detección e tratamento do acoso escolar',
                    'document_date' => '2024',
                    'document_url' => 'http://www.edu.xunta.gal/portal/convivencia/protocolos',
                    'color' => '#0099cc',
                    'main_tool' => 'XADE / Aplicación de Convivencia',
                ],
                'content' => [
                    'key_principles' => [
                        'Atención preferente á vítima',
                        'Actuación inmediata e coordinada',
                        'Discreción e confidencialidade',
                        'Enfoque educativo e restaurativo',
                    ],
                    'types_of_violence' => [
                        'Maltrato físico e verbal',
                        'Exclusión social e psicológica',
                        'Ciberacoso',
                        'Acoso por identidade de xénero ou orientación sexual',
                    ],
                    'applicable_to' => 'Todos os centros docentes de Galicia.',
                ],
                'phases' => [
                    [
                        'id' => 'gal_deteccion',
                        'number' => 1,
                        'title' => 'Detección e Comunicación',
                        'description' => 'Notificación de calquera indicio de acoso á dirección do centro.',
                        'responsible' => 'Calquera membro da comunidade educativa',
                        'actions' => [
                            'Comunicación inmediata á dirección',
                            'Recollida inicial de información',
                            'Apertura do expediente no XADE',
                        ],
                        'timeframe' => 'Inmediato',
                        'tools' => 'XADE',
                    ],
                    [
                        'id' => 'gal_proteccion',
                        'number' => 2,
                        'title' => 'Medidas de Protección',
                        'description' => 'Adopción de medidas urxentes para garantir a seguridade da bítima.',
                        'responsible' => 'Equipo Directivo',
                        'actions' => [
                            'Medidas cautelares de vixilancia e protección',
                            'Comunicación ás familias',
                            'Garantía da integridade do alumnado',
                        ],
                        'timeframe' => '24-48 horas',
                        'tools' => 'Plan de Convivencia',
                    ],
                    [
                        'id' => 'gal_investigacion',
                        'number' => 3,
                        'title' => 'Investigación e Valoración',
                        'description' => 'Proceso de recollida de información para confirmar a situación de acoso.',
                        'responsible' => 'Equipo de Valoración / Orientación',
                        'actions' => [
                            'Entrevistas cos implicados e observadores',
                            'Análise de evidencias e clima de grupo',
                            'Informe técnico de valoración',
                        ],
                        'timeframe' => 'Máximo 10 días lectivos',
                        'tools' => 'Plantillas de entrevista oficiais',
                    ],
                    [
                        'id' => 'gal_resolucion',
                        'number' => 4,
                        'title' => 'Resolución e Plan de Actuación',
                        'description' => 'Determinación final e aplicación de medidas educativas e disciplinarias.',
                        'responsible' => 'Dirección',
                        'actions' => [
                            'Comunicación da resolución a Inspección e familias',
                            'Implementación do Plan de Actuación Individualizado',
                            'Medidas de reparación e apoio',
                        ],
                        'timeframe' => 'Tras a valoración',
                        'tools' => 'Informe de resolución oficial',
                    ],
                    [
                        'id' => 'gal_seguimento',
                        'number' => 5,
                        'title' => 'Seguimento e Peche',
                        'description' => 'Avaliación da eficacia das medidas e peche do expediente.',
                        'responsible' => 'Equipo Directivo e Titor/a',
                        'actions' => [
                            'Vixilancia periódica da evolución',
                            'Reunións de seguimento coas familias',
                            'Rexistro do peche no XADE',
                        ],
                        'timeframe' => 'Variable',
                        'tools' => 'Rexistro de seguimento',
                    ],
                ],
                'emergency_contacts' => [
                    ['name' => 'Emerxencias Galicia', 'description' => 'Atención de emerxencias 112', 'contact' => '112'],
                    ['name' => 'Teléfono da Infancia (Galicia)', 'description' => 'Atención a menores en situación de risco', 'contact' => '116 111'],
                    ['name' => 'Policía Autonómica', 'description' => 'Equipo de menores', 'contact' => '981 54 44 00'],
                ],
            ],
            'PV' => [
                'metadata' => [
                    'code' => 'PV',
                    'name' => 'País Vasco',
                    'authority' => "Eusko Jaurlaritza - Departamento de Educación",
                    'document_title' => "Eskola-jazarpenari aurre egiteko protokoloa",
                    'document_date' => "2024",
                    'document_url' => "https://www.euskadi.eus/protocolo-acoso-escolar/",
                    'color' => '#009543',
                    'main_tool' => "Bizikasi"
                ],
                'content' => [
                    'key_principles' => ["Biktimaren babesa", "Prebentzioa"],
                    'types_of_violence' => ["Bullying", "Ziber-bullying"],
                    'applicable_to' => "Euskadiko ikastetxeak"
                ],
                'phases' => [
                    ['id' => 'f1', 'number' => 1, 'title' => 'Jakinarazpena', 'description' => 'Zuzendaritzari abisua eman.', 'responsible' => 'Denok', 'actions' => ['Informazioa bildu'], 'timeframe' => 'Berehala', 'tools' => ['Bizikasi']]
                ],
                'emergency_contacts' => [['name' => 'Larrialdiak', 'description' => 'Orokorra', 'contact' => '112']]
            ]
        ];
    }

    public static function getByCode(string $code): ?array {
        $all = self::getAll();
        return $all[$code] ?? null;
    }
}

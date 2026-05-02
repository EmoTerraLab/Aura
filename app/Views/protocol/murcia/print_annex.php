<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documento Oficial - <?= htmlspecialchars(strtoupper($type)) ?> - Expediente #<?= $case['id'] ?></title>
    <style>
        @page { size: A4; margin: 2.5cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.5; color: #000; margin: 0; padding: 0; }
        .no-print { background: #f1f5f9; padding: 1rem; text-align: center; border-bottom: 1px solid #cbd5e1; margin-bottom: 1cm; }
        .no-print .btn { display: inline-block; padding: 0.6rem 2rem; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 10pt; margin: 0 0.5rem; text-decoration: none; }
        .btn-print { background: #000; color: #fff; }
        .btn-back { background: #e2e8f0; color: #334155; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1cm; border-bottom: 2pt solid #000; padding-bottom: 0.5cm; }
        .gov-logo { font-weight: bold; text-transform: uppercase; font-size: 13pt; line-height: 1.3; }
        .gov-logo small { font-size: 9pt; font-weight: normal; text-transform: none; display: block; margin-top: 2px; }
        .annex-type { font-weight: bold; font-size: 16pt; text-align: right; }
        .annex-type small { display: block; font-size: 9pt; font-weight: normal; color: #555; }
        .official-title { text-align: center; text-transform: uppercase; font-weight: bold; margin: 0.8cm 0; font-size: 12pt; letter-spacing: 1px; }
        .section { margin-bottom: 0.8cm; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 10pt; margin-bottom: 0.3cm; display: block; border-bottom: 1pt solid #000; padding-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 0.5cm; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: top; font-size: 10pt; }
        th { background-color: #f2f2f2; width: 30%; font-weight: bold; }
        .content-block { background: #fafafa; border: 1px solid #ccc; padding: 12px 16px; margin: 0.3cm 0; white-space: pre-wrap; font-size: 10pt; min-height: 2cm; }
        .signature-area { margin-top: 2.5cm; display: flex; justify-content: space-between; gap: 2cm; }
        .signature { border-top: 1pt solid #000; width: 7cm; text-align: center; padding-top: 5px; font-size: 9pt; }
        .footer { margin-top: 1.5cm; text-align: center; font-size: 8pt; color: #888; border-top: 1px solid #ddd; padding-top: 0.3cm; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="/protocol/murcia/case/<?= $case['id'] ?>" class="btn btn-back">← Volver al Expediente</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Imprimir Documento Oficial</button>
    </div>

    <div class="header">
        <div class="gov-logo">
            Región de Murcia<br>
            <small>Consejería de Educación, Formación Profesional y Empleo</small>
        </div>
        <div class="annex-type">
            <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $type))) ?>
            <small>Expediente MUR-<?= $case['id'] ?>/<?= date('Y') ?></small>
        </div>
    </div>

    <div class="official-title">
        <?php
        $titles = [
            'designacio' => 'Acta de Designación del Equipo de Intervención',
            'medides_urgencia' => 'Registro de Medidas de Urgencia Adoptadas',
            'anexo_i' => 'Anexo I — Comunicación de Inicio a Inspección Educativa',
            'entrevista_victima' => 'Registro de Entrevista — Presunta Víctima',
            'entrevista_observadores' => 'Registro de Entrevista — Observadores',
            'entrevista_familia_victima' => 'Registro de Entrevista — Familia de la Víctima',
            'entrevista_familia_agresor' => 'Registro de Entrevista — Familia del Agresor',
            'entrevista_agresor' => 'Registro de Entrevista — Presunto Agresor',
            'anexo_iv' => 'Anexo IV — Informe del Equipo de Intervención',
            'acta_reunio' => 'Acta de Reunión Conjunta de Valoración',
            'anexo_v' => 'Anexo V — Resolución y Medidas',
            'anexo_vi' => 'Anexo VI — Plan de Seguimiento',
            'comunicacio_legal' => 'Comunicación a Autoridades Competentes',
        ];
        echo $titles[$type] ?? 'Documento Oficial del Protocolo de Acoso';
        ?>
    </div>

    <!-- Datos del Centro -->
    <div class="section">
        <span class="section-title">1. Datos del Centro Educativo</span>
        <table>
            <tr><th>Centro</th><td><?= htmlspecialchars($school_name) ?></td></tr>
            <tr><th>Expediente</th><td>MUR-<?= $case['id'] ?>/<?= date('Y') ?></td></tr>
            <tr><th>Fecha del documento</th><td><?= date('d/m/Y', strtotime($annex['created_at'])) ?></td></tr>
            <tr><th>Registrado por</th><td><?= htmlspecialchars($submitted_by_name ?? 'Personal autorizado') ?></td></tr>
        </table>
    </div>

    <!-- Contenido específico según tipo -->
    <div class="section">
        <span class="section-title">2. Contenido del Documento</span>

        <?php if ($type === 'designacio'): ?>
            <table>
                <tr><th>Coordinador designado</th><td>ID: <?= $content['coordinator_id'] ?? '—' ?></td></tr>
                <tr><th>Miembros del equipo</th><td><?= is_array($content['team_members'] ?? null) ? implode(', ', $content['team_members']) : ($content['team_members'] ?? '—') ?></td></tr>
                <tr><th>Fecha de constitución</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif ($type === 'medides_urgencia'): ?>
            <table>
                <tr><th>Medidas adoptadas</th><td><div class="content-block"><?= htmlspecialchars($content['measures'] ?? '') ?></div></td></tr>
                <tr><th>Fecha de adopción</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif ($type === 'anexo_i'): ?>
            <table>
                <tr><th>Enviado a Inspección Educativa</th><td><?= ($content['sent_to_inspeccion'] ?? false) ? 'SÍ' : 'NO' ?></td></tr>
                <tr><th>Enviado a Ordenación Académica</th><td><?= ($content['sent_to_ordenacion'] ?? false) ? 'SÍ' : 'NO' ?></td></tr>
                <tr><th>Fecha de envío</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif (str_starts_with($type, 'entrevista_')): ?>
            <?php
            $interviewLabels = [
                'entrevista_victima' => 'Presunta Víctima',
                'entrevista_observadores' => 'Observadores',
                'entrevista_familia_victima' => 'Familia de la Víctima',
                'entrevista_familia_agresor' => 'Familia del Agresor',
                'entrevista_agresor' => 'Presunto(s) Agresor(es)',
            ];
            ?>
            <table>
                <tr><th>Tipo de entrevista</th><td><?= $interviewLabels[$type] ?? ucfirst(str_replace('entrevista_', '', $type)) ?></td></tr>
                <tr><th>Fecha de la entrevista</th><td><?= $content['date'] ?? '—' ?></td></tr>
                <tr><th>Notas / Declaraciones</th><td><div class="content-block"><?= htmlspecialchars($content['notes'] ?? '') ?></div></td></tr>
            </table>

        <?php elseif ($type === 'anexo_iv'): ?>
            <p><strong>Informe emitido por el equipo de intervención:</strong></p>
            <div class="content-block"><?= htmlspecialchars($content['report_content'] ?? '') ?></div>
            <table>
                <tr><th>Fecha de emisión</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif ($type === 'acta_reunio'): ?>
            <p><strong>Acta de la reunión conjunta convocada por la Dirección del centro:</strong></p>
            <div class="content-block"><?= htmlspecialchars($content['notes'] ?? '') ?></div>
            <table>
                <tr><th>Fecha de reunión</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif ($type === 'anexo_v'): ?>
            <table>
                <tr><th>Conclusión</th><td><strong><?= ($content['conclusion'] ?? 'no_evidencias') === 'si_evidencias' ? 'SÍ existen evidencias de acoso escolar' : 'NO se han encontrado evidencias de acoso escolar' ?></strong></td></tr>
                <tr><th>Medidas adoptadas</th><td><?= is_array($content['measures'] ?? null) ? implode(', ', $content['measures']) : htmlspecialchars($content['measures'] ?? '—') ?></td></tr>
                <tr><th>Fecha de resolución</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif ($type === 'anexo_vi'): ?>
            <p><strong>Plan de seguimiento establecido:</strong></p>
            <div class="content-block"><?= htmlspecialchars($content['followup_plan'] ?? '') ?></div>
            <table>
                <tr><th>Fecha</th><td><?= $content['date'] ?? '—' ?></td></tr>
            </table>

        <?php elseif ($type === 'comunicacio_legal'): ?>
            <table>
                <tr><th>Grupo de edad del alumno</th><td><?= htmlspecialchars($content['age_group'] ?? '—') ?></td></tr>
                <tr><th>Entidad notificada</th><td><strong><?= htmlspecialchars($content['entity'] ?? '—') ?></strong></td></tr>
                <tr><th>Fecha de comunicación</th><td><?= $content['date'] ?? '—' ?></td></tr>
                <tr><th>Observaciones</th><td><?= htmlspecialchars($content['notes'] ?? '—') ?></td></tr>
            </table>

        <?php else: ?>
            <div class="content-block"><?= htmlspecialchars(json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></div>
        <?php endif; ?>
    </div>

    <!-- Firma -->
    <div class="section">
        <span class="section-title">3. Firmas</span>
    </div>

    <div class="signature-area">
        <div class="signature">Fdo. El/La Director/a<br>(Sello del Centro)</div>
        <div class="signature">Fdo. Coordinador/a<br>del Equipo de Intervención</div>
    </div>

    <div class="footer">
        Documento generado por Aura PDP — <?= htmlspecialchars($school_name) ?> — <?= date('d/m/Y H:i') ?>
    </div>
</body>
</html>

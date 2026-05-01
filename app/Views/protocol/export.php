<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11pt; color: #1a1a1a; line-height: 1.5; padding: 2rem; }
        .header { border-bottom: 3px solid #004f56; padding-bottom: 1rem; margin-bottom: 2rem; display: flex; justify-content: space-between; }
        .title { font-size: 18pt; font-weight: bold; color: #004f56; }
        .meta { font-size: 9pt; color: #666; text-align: right; }
        h2 { font-size: 14pt; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-top: 2rem; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 10pt; }
        th { background: #f9f9f9; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; text-transform: uppercase; }
        .phase-list { list-style: none; padding: 0; }
        .phase-item { margin-bottom: 1rem; padding-left: 1.5rem; position: relative; }
        .phase-item::before { content: '✓'; position: absolute; left: 0; color: #004f56; font-weight: bold; }
        @media print { .no-print { display: none; } }
        .btn-print { background: #004f56; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="btn-print">🖨️ Imprimir / Guardar como PDF</button>
    </div>

    <div class="header">
        <div>
            <div class="title">EXPEDIENTE DE PROTOCOLO</div>
            <div style="font-weight: bold;"><?= \App\Core\Config::get('school_name') ?></div>
        </div>
        <div class="meta">
            Caso #<?= $report['id'] ?><br>
            Generado el: <?= $generated_at ?><br>
            Por: <?= htmlspecialchars($generated_by) ?>
        </div>
    </div>

    <section>
        <h2>1. Información General del Reporte</h2>
        <table>
            <tr><th style="width: 30%;">Fecha de Reporte</th><td><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></td></tr>
            <tr><th>Alumno/a</th><td><?= htmlspecialchars($report['student_name'] ?? 'Anónimo') ?></td></tr>
            <tr><th>Descripción Inicial</th><td><?= nl2br(htmlspecialchars($report['content'])) ?></td></tr>
        </table>
    </section>

    <section>
        <h2>2. Estado del Protocolo Legal (CCAA: <?= $protocol['name'] ?? 'No configurada' ?>)</h2>
        <table>
            <tr><th style="width: 30%;">Fase Actual</th><td><span class="badge" style="background: #e0f2f1; color: #004f56;"><?= strtoupper($case['current_phase']) ?></span></td></tr>
            <tr><th>Severidad</th><td><?= ucfirst($case['severity_preliminary'] ?? 'Pendiente') ?></td></tr>
            <tr><th>Clasificación</th><td><?= htmlspecialchars($case['classification'] ?? 'Pendiente') ?></td></tr>
            <tr><th>Reconocimiento de hechos</th><td><?= $case['aggressor_acknowledges_facts'] === 1 ? 'SÍ (Proceso Restaurativo)' : ($case['aggressor_acknowledges_facts'] === 0 ? 'NO (Proceso Disciplinario)' : 'Pendiente') ?></td></tr>
        </table>
    </section>

    <?php if ($case['security_map']): ?>
    <?php $map = json_decode($case['security_map'], true); ?>
    <section>
        <h2>3. Mapa de Seguridad y Protección</h2>
        <table>
            <tr><th style="width: 30%;">Espacios Seguros</th><td><?= nl2br(htmlspecialchars($map['espais_segurs'] ?? '---')) ?></td></tr>
            <tr><th>Espacios de Riesgo</th><td><?= nl2br(htmlspecialchars($map['espais_de_risc'] ?? '---')) ?></td></tr>
            <tr><th>Referentes</th><td><?= htmlspecialchars($map['persones_de_suport'] ?? '---') ?></td></tr>
        </table>
    </section>
    <?php endif; ?>

    <section>
        <h2>4. Actuaciones Restaurativas / Acuerdos</h2>
        <?php if (empty($restorative)): ?>
            <p style="color: #666; font-style: italic;">No se han registrado sesiones restaurativas en este expediente.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Participantes</th>
                        <th>Acuerdos de Reparación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($restorative as $p): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($p['session_date'])) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $p['practice_type'])) ?></td>
                        <td><?= htmlspecialchars($p['participants']) ?></td>
                        <td><?= nl2br(htmlspecialchars($p['agreements'])) ?></td>
                        <td><?= ucfirst($p['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <div style="margin-top: 4rem; border-top: 1px solid #ddd; padding-top: 1rem; font-size: 8pt; color: #999; text-align: center;">
        Documento confidencial generado por Aura PDP Control Center. El uso de esta información está sujeto a la Ley de Protección de Datos.
    </div>
</body>
</html>

<?php
// Plantilla base Anexo 16 - Galicia (Informe final e peche do caso)
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <title>Anexo 16 - Informe final e peche do caso</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 14px; color: #555; }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; font-size: 16px; margin-bottom: 10px; background-color: #e0e0e0; padding: 5px; }
        .label { font-weight: bold; }
        .value { border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; padding-left: 5px; }
        .textarea-box { border: 1px solid #000; min-height: 80px; padding: 10px; margin-top: 5px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ANEXO 16: INFORME FINAL E PECHE DO CASO</div>
        <div class="subtitle">Comunicación á Inspección Educativa</div>
    </div>

    <div class="section">
        <p><span class="label">Centro Educativo:</span> <span class="value"><?= htmlspecialchars($schoolName ?? '________________________') ?></span></p>
        <p><span class="label">Código de Centro:</span> <span class="value"><?= htmlspecialchars($schoolCode ?? '___________') ?></span></p>
        <p><span class="label">Localidade:</span> <span class="value"><?= htmlspecialchars($schoolCity ?? '________________________') ?></span></p>
        <p><span class="label">Data de inicio do protocolo:</span> <span class="value"><?= htmlspecialchars($startDate ?? '___________') ?></span></p>
    </div>

    <div class="section">
        <div class="section-title">1. DATOS DO ALUMNO/A AFECTADO/A</div>
        <p><span class="label">Nome e apelidos:</span> <span class="value"><?= htmlspecialchars($studentName ?? '________________________') ?></span></p>
        <p><span class="label">Curso e grupo:</span> <span class="value"><?= htmlspecialchars($studentCourse ?? '________________________') ?></span></p>
    </div>

    <div class="section">
        <div class="section-title">2. RESUMO DAS ACTUACIÓNS REALIZADAS (Fases 1 a 4)</div>
        <div class="textarea-box"><?= htmlspecialchars($summaryActions ?? 'Resumo das actuacións do equipo de intervención, medidas adoptadas e seguimento...') ?></div>
    </div>

    <div class="section">
        <div class="section-title">3. CONCLUSIÓN FINAL DO CASO</div>
        <p>Tras as actuacións realizadas e o seguimento correspondente, determínase o peche do caso por:</p>
        <div style="margin-top: 10px;">
            <p>
                <input type="checkbox" <?= (isset($closureReason) && $closureReason === 'cese') ? 'checked' : '' ?>> 
                Cese definitivo das condutas de acoso e recuperación do benestar do alumno/a.
            </p>
            <p>
                <input type="checkbox" <?= (isset($closureReason) && $closureReason === 'traslado') ? 'checked' : '' ?>> 
                Traslado de centro (do alumno/a afectado/a ou do presunto agresor/a).
            </p>
            <p>
                <input type="checkbox" <?= (isset($closureReason) && $closureReason === 'externo') ? 'checked' : '' ?>> 
                Derivación completa e asunción do caso por servizos externos (FCSE, Fiscalía, Servizos Sociais).
            </p>
        </div>
        <p style="margin-top: 15px;"><span class="label">Observacións adicionais:</span></p>
        <div class="textarea-box"><?= htmlspecialchars($closureObservations ?? '') ?></div>
    </div>

    <div class="section" style="margin-top: 50px;">
        <p>En <span class="value"><?= htmlspecialchars($schoolCity ?? '________________________') ?></span>, a <?= date('d') ?> de <?= date('m') ?> de <?= date('Y') ?></p>
        <br><br><br>
        <table width="100%">
            <tr>
                <td width="50%" style="text-align: center;">
                    <p>A Dirección do Centro</p>
                    <br><br>
                    <p>Asinado: ___________________________</p>
                </td>
                <td width="50%" style="text-align: center;">
                    <p>Selo do Centro</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

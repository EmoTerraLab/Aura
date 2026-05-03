<?php
// Plantilla base Anexo 11 - Galicia (Informe de valoración e análise da información)
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <title>Anexo 11 - Informe de valoración e análise</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 14px; color: #555; }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; font-size: 16px; margin-bottom: 10px; background-color: #f0f0f0; padding: 5px; }
        .label { font-weight: bold; }
        .value { border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; padding-left: 5px; }
        .textarea-box { border: 1px solid #000; min-height: 100px; padding: 10px; margin-top: 10px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ANEXO 11: INFORME DE VALORACIÓN E ANÁLISE DA INFORMACIÓN</div>
        <div class="subtitle">Fase 3: Análise e medidas</div>
    </div>

    <div class="section">
        <p><span class="label">Centro Educativo:</span> <span class="value"><?= htmlspecialchars($schoolName ?? '________________________') ?></span></p>
        <p><span class="label">Data de emisión:</span> <span class="value"><?= htmlspecialchars($reportDate ?? date('d/m/Y')) ?></span></p>
    </div>

    <div class="section">
        <div class="section-title">1. DATOS DO ALUMNO/A AFECTADO/A</div>
        <p><span class="label">Nome e apelidos:</span> <span class="value"><?= htmlspecialchars($studentName ?? '________________________') ?></span></p>
        <p><span class="label">Curso e grupo:</span> <span class="value"><?= htmlspecialchars($studentCourse ?? '________________________') ?></span></p>
    </div>

    <div class="section">
        <div class="section-title">2. RESUMO DAS ACTUACIÓNS DE RECOLLIDA DE INFORMACIÓN</div>
        <p><i>Persoas entrevistadas, documentos revisados, observacións directas.</i></p>
        <div class="textarea-box"><?= htmlspecialchars($informationSummary ?? '') ?></div>
    </div>

    <div class="section">
        <div class="section-title">3. ANÁLISE DOS FEITOS E CONCLUSIÓN</div>
        <p>Tendo en conta a información recollida, o equipo responsable determina que:</p>
        <div style="margin-top: 10px; padding-left: 20px;">
            <p>
                <input type="checkbox" <?= (isset($isBullying) && $isBullying === true) ? 'checked' : '' ?>> 
                <span class="label">CONFÍRMASE</span> a existencia de indicadores de acoso escolar.
            </p>
            <p>
                <input type="checkbox" <?= (isset($isBullying) && $isBullying === false) ? 'checked' : '' ?>> 
                <span class="label">DESCÁRTASE</span> a existencia de acoso escolar. (Procedeuse doutra maneira: conflito puntual, etc.)
            </p>
        </div>
        <br>
        <p><i>Fundamentación da conclusión:</i></p>
        <div class="textarea-box"><?= htmlspecialchars($valuationReasoning ?? '') ?></div>
    </div>

    <div class="section">
        <div class="section-title">4. PROPOSTA INICIAL DE MEDIDAS</div>
        <p><i>Medidas a incluír no Plan de Intervención (Anexo 12) relativas ás vítimas, agresores, observadores e familias.</i></p>
        <div class="textarea-box"><?= htmlspecialchars($proposedMeasures ?? '') ?></div>
    </div>

    <div class="section" style="margin-top: 50px;">
        <table width="100%">
            <tr>
                <td width="33%" style="text-align: center;">
                    <p>A Dirección</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
                <td width="33%" style="text-align: center;">
                    <p>Xefatura de Estudos</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
                <td width="33%" style="text-align: center;">
                    <p>Orientación</p>
                    <br><br><br>
                    <p>___________________</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

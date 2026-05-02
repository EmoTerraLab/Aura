<?php
// Plantilla base Anexo 4 - Galicia (Entrevista co alumno/a presuntamente acosado/a)
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <title>Anexo 4 - Entrevista Vítima</title>
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
        .checkbox-group { margin-top: 10px; }
        .checkbox-item { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ANEXO 4: ENTREVISTA CO ALUMNO/A PRESUNTAMENTE ACOSADO/A</div>
        <div class="subtitle">Fase 2: Recollida de Información</div>
    </div>

    <div class="section">
        <p><span class="label">Centro Educativo:</span> <span class="value"><?= htmlspecialchars($schoolName ?? '________________________') ?></span></p>
        <p><span class="label">Data da entrevista:</span> <span class="value"><?= htmlspecialchars($interviewDate ?? date('d/m/Y')) ?></span></p>
        <p><span class="label">Persoa(s) que realiza(n) a entrevista:</span> <span class="value"><?= htmlspecialchars($interviewerName ?? '________________________') ?></span></p>
    </div>

    <div class="section">
        <div class="section-title">1. DATOS DO ALUMNO/A</div>
        <p><span class="label">Nome e apelidos:</span> <span class="value"><?= htmlspecialchars($studentName ?? '________________________') ?></span></p>
        <p><span class="label">Curso e grupo:</span> <span class="value"><?= htmlspecialchars($studentCourse ?? '________________________') ?></span></p>
        <p><span class="label">Idade:</span> <span class="value"><?= htmlspecialchars($studentAge ?? '______') ?> anos</span></p>
    </div>

    <div class="section">
        <div class="section-title">2. DESCRICIÓN DA SITUACIÓN (relato do alumno/a)</div>
        <p><i>Que está a pasar? Desde cando? Onde e cando adoitan ocorrer os feitos?</i></p>
        <div class="textarea-box"><?= htmlspecialchars($situationDescription ?? '') ?></div>
    </div>

    <div class="section">
        <div class="section-title">3. PERSOAS IMPLICADAS</div>
        <p><i>Quen participa nestas situacións? (Presuntos agresores/as e observadores/as)</i></p>
        <div class="textarea-box"><?= htmlspecialchars($involvedPersons ?? '') ?></div>
    </div>

    <div class="section">
        <div class="section-title">4. IMPACTO E EMOCIÓNS</div>
        <p><i>Como te sentes ante esta situación? (Medo, tristura, illamento, síntomas físicos...)</i></p>
        <div class="textarea-box"><?= htmlspecialchars($emotionalImpact ?? '') ?></div>
    </div>

    <div class="section">
        <div class="section-title">5. ACCIÓNS PREVIAS</div>
        <p><i>Cótachesllo a alguén antes? Pediches axuda?</i></p>
        <div class="textarea-box"><?= htmlspecialchars($previousActions ?? '') ?></div>
    </div>

    <div class="section" style="margin-top: 50px;">
        <table width="100%">
            <tr>
                <td width="50%" style="text-align: center;">
                    <p>Asinado (O/A alumno/a, de consideralo oportuno)</p>
                    <br><br><br>
                    <p>___________________________</p>
                </td>
                <td width="50%" style="text-align: center;">
                    <p>Asinado (Persoa entrevistadora)</p>
                    <br><br><br>
                    <p>___________________________</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

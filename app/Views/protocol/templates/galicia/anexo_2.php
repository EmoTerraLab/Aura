<?php
// Plantilla base Anexo 2 - Galicia (Nomeamento da persoa responsable da atención e apoio)
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <title>Anexo 2 - Nomeamento de responsable</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 14px; color: #555; }
        .section { margin-bottom: 30px; }
        .label { font-weight: bold; }
        .value { border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; padding-left: 5px; }
        .text-body { text-align: justify; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ANEXO 2: NOMEAMENTO DA PERSOA RESPONSABLE DA ATENCIÓN E APOIO</div>
        <div class="subtitle">Fase 1: Detección e comunicación</div>
    </div>

    <div class="section">
        <p><span class="label">Centro Educativo:</span> <span class="value"><?= htmlspecialchars($schoolName ?? '________________________') ?></span></p>
        <p><span class="label">Código de Centro:</span> <span class="value"><?= htmlspecialchars($schoolCode ?? '___________') ?></span></p>
        <p><span class="label">Localidade:</span> <span class="value"><?= htmlspecialchars($schoolCity ?? '________________________') ?></span></p>
    </div>

    <div class="section text-body">
        <p>A Dirección do centro educativo, de acordo co establecido no Protocolo educativo para a prevención e actuación ante o acoso escolar na Comunidade Autónoma de Galicia, procede a nomear a:</p>
        <br>
        <p><span class="label">D./Dna.:</span> <span class="value"><?= htmlspecialchars($designatedTeacher ?? '________________________') ?></span></p>
        <p><span class="label">Cargo/Posto:</span> <span class="value"><?= htmlspecialchars($teacherRole ?? 'Titor/a - Orientador/a') ?></span></p>
        <br>
        <p>Como persoa responsable da atención, apoio e acompañamento do/a alumno/a:</p>
        <br>
        <p><span class="label">Nome e apelidos:</span> <span class="value"><?= htmlspecialchars($studentName ?? '________________________') ?></span></p>
        <p><span class="label">Curso e grupo:</span> <span class="value"><?= htmlspecialchars($studentCourse ?? '________________________') ?></span></p>
        <br>
        <p>As súas funcións serán garantir un espazo seguro para o/a alumno/a, escoitalo/a, ofrecerlle apoio emocional básico e estar pendente de calquera nova incidencia mentres se desenvolve a recollida de información e a análise do caso.</p>
    </div>

    <div class="section" style="margin-top: 50px;">
        <table width="100%">
            <tr>
                <td width="50%" style="text-align: center;">
                    <p>A Dirección do Centro</p>
                    <br><br><br>
                    <p>Asinado: ___________________________</p>
                </td>
                <td width="50%" style="text-align: center;">
                    <p>Recibín (Persoa designada)</p>
                    <br><br><br>
                    <p>Asinado: ___________________________</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

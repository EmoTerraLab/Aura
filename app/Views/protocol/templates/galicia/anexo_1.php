<?php
// Plantilla base Anexo 1 - Galicia
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <title>Anexo 1 - Comunicación á Inspección Educativa</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 14px; color: #555; }
        .section { margin-bottom: 20px; }
        .label { font-weight: bold; }
        .value { border-bottom: 1px dotted #000; display: inline-block; min-width: 200px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ANEXO 1: COMUNICACIÓN DUNHA POSIBLE SITUACIÓN DE ACOSO ESCOLAR</div>
        <div class="subtitle">Á Inspección Educativa</div>
    </div>

    <div class="section">
        <p>Centro Educativo: <span class="value"><?= htmlspecialchars($schoolName ?? '________________________') ?></span></p>
        <p>Código de Centro: <span class="value"><?= htmlspecialchars($schoolCode ?? '___________') ?></span></p>
        <p>Localidade: <span class="value"><?= htmlspecialchars($schoolCity ?? '________________________') ?></span></p>
    </div>

    <div class="section">
        <p>Datos do alumno/a afectado/a:</p>
        <p><span class="label">Nome e apelidos:</span> <span class="value"><?= htmlspecialchars($studentName ?? '________________________') ?></span></p>
        <p><span class="label">Curso e grupo:</span> <span class="value"><?= htmlspecialchars($studentCourse ?? '________________________') ?></span></p>
    </div>

    <div class="section">
        <p>Descrición breve dos feitos detectados e medidas urxentes adoptadas:</p>
        <div style="border: 1px solid #000; height: 150px; padding: 10px;">
            <?= nl2br(htmlspecialchars($factsDescription ?? '')) ?>
        </div>
    </div>

    <div class="section" style="margin-top: 50px;">
        <p>En <span class="value"><?= htmlspecialchars($schoolCity ?? '________________________') ?></span>, a <?= date('d') ?> de <?= date('m') ?> de <?= date('Y') ?></p>
        <p>A Dirección do Centro,</p>
        <br><br><br>
        <p>Asinado: ___________________________</p>
    </div>
</body>
</html>

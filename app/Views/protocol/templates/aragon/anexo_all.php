<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión Oficial - Anexo <?= htmlspecialchars($type) ?></title>
    <style>
        @page { size: A4; margin: 2.5cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.4; color: #000; }
        .no-print { background: #f1f5f9; padding: 1rem; text-align: center; border-bottom: 1px solid #cbd5e1; margin-bottom: 2cm; }
        .btn-print { background: #000; color: #fff; padding: 0.5rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5cm; border-bottom: 2px solid #000; padding-bottom: 0.5cm; }
        .gov-logo { font-weight: bold; text-transform: uppercase; font-size: 14pt; }
        .annex-number { font-weight: bold; font-size: 18pt; }
        .official-title { text-align: center; text-transform: uppercase; font-weight: bold; margin-bottom: 1cm; font-size: 12pt; text-decoration: underline; }
        .section { margin-bottom: 1cm; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 10pt; margin-bottom: 0.3cm; display: block; border-bottom: 1px solid #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 0.5cm; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; width: 30%; }
        .signature-box { margin-top: 3cm; display: flex; justify-content: space-between; }
        .signature { border-top: 1px solid #000; width: 6cm; text-align: center; padding-top: 5px; font-size: 9pt; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print"><button onclick="window.print()" class="btn-print">🖨️ IMPRIMIR ANEXO OFICIAL</button></div>
    <div class="header">
        <div class="gov-logo">GOBIERNO DE ARAGÓN<br><span style="font-size: 9pt;">Departamento de Educación, Cultura y Deporte</span></div>
        <div class="annex-number">ANEXO <?= htmlspecialchars($type) ?></div>
    </div>
    <div class="official-title">Documento Oficial de Protocolo</div>
    <div class="section">
        <span class="section-title">Datos del Centro y Expediente</span>
        <table>
            <tr><th>Centro Educativo</th><td><?= htmlspecialchars($school_name) ?></td></tr>
            <tr><th>Código de Expediente</th><td>#<?= $case['id'] ?> / AR-<?= date('Y') ?></td></tr>
            <tr><th>Fecha de este documento</th><td><?= date('d/m/Y', strtotime($annex['created_at'])) ?></td></tr>
        </table>
    </div>
    <div class="signature-box"><div class="signature">Fdo. El/La Director/a<br>(Sello del Centro)</div><div class="signature">Fdo. Orientación</div></div>
</body>
</html>

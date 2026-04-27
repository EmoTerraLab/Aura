<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>En mantenimiento / Under Maintenance</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon { font-size: 4rem; margin-bottom: 1.5rem; }
        h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; color: #f1f5f9; }
        .subtitle { font-size: 0.95rem; color: #94a3b8; margin-bottom: 1.5rem; }
        .message {
            background: #0f172a;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .time { font-size: 0.8rem; color: #64748b; }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%;
               background: #f59e0b; animation: pulse 1.5s infinite; margin-right: 6px; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
    </style>
</head>
<body>
<div class='card'>
    <div class='icon'>🔧</div>
    <h1>En mantenimiento</h1>
    <p class='subtitle'>Under Maintenance</p>
    <div class='message'>
        <span class='dot'></span>
        <?= htmlspecialchars($maintenanceData['message'] ?? 'El sistema está en mantenimiento.') ?>
    </div>
    <?php if (!empty($maintenanceData['estimated_end'])): ?>
    <p class='time'>⏱ Tiempo estimado: <?= htmlspecialchars($maintenanceData['estimated_end']) ?></p>
    <?php endif; ?>
    <?php if (!empty($maintenanceData['enabled_at'])): ?>
    <p class='time' style='margin-top:0.5rem'>Inicio: <?= htmlspecialchars($maintenanceData['enabled_at']) ?></p>
    <?php endif; ?>
</div>
</body>
</html>

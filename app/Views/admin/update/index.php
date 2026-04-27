<div class="space-y-6">
    <!-- Encabezado de Estado -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Actualizaciones del Sistema</h1>
                <p class="text-slate-500">Versión actual: <span class="font-mono font-bold text-indigo-600">v<?= $currentVersion ?></span></p>
            </div>
            <div class="flex space-x-3">
                <button onclick="checkIntegrity()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">
                    🔍 Verificar integridad
                </button>
                <?php if ($maintenanceActive): ?>
                    <form action="/admin/update/maintenance/disable" method="POST">
                        <?= \App\Core\Csrf::tokenField() ?>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700">
                            🔓 Desactivar mantenimiento
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($maintenanceActive): ?>
            <div class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-md flex items-start">
                <span class="text-orange-400 mr-3">⚠️</span>
                <div>
                    <p class="text-sm font-medium text-orange-800">El modo mantenimiento está ACTIVO</p>
                    <p class="text-xs text-orange-700 mt-1">Mensaje: <?= htmlspecialchars($maintenanceData['message'] ?? '') ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Migraciones Pendientes -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-semibold text-slate-800">Migraciones Pendientes</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= empty($pending) ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800' ?>">
                <?= count($pending) ?> pendientes
            </span>
        </div>
        
        <?php if (empty($pending)): ?>
            <div class="p-8 text-center">
                <p class="text-slate-400">✨ El sistema está actualizado a la última versión.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 font-medium">Versión</th>
                            <th class="px-6 py-3 font-medium">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($pending as $m): ?>
                            <tr>
                                <td class="px-6 py-4 font-mono font-bold text-indigo-600"><?= $m['version'] ?></td>
                                <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($m['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button id="run-update-btn" onclick="runUpdate()" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 shadow-sm transition-all">
                    🚀 Ejecutar actualización
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Log de ejecución (oculto por defecto) -->
    <div id="update-log-container" class="hidden bg-slate-900 rounded-lg shadow-lg overflow-hidden">
        <div class="p-3 bg-slate-800 flex justify-between items-center">
            <span class="text-xs font-mono text-slate-400 uppercase tracking-wider">Update Console</span>
            <div class="flex space-x-1">
                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
            </div>
        </div>
        <pre id="update-log" class="p-4 text-green-400 font-mono text-xs leading-relaxed max-h-64 overflow-y-auto"></pre>
    </div>

    <!-- Historial y Backups -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Historial -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Historial de Migraciones</h2>
            </div>
            <div class="max-h-80 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase">
                        <tr>
                            <th class="px-4 py-2 font-medium">Fecha</th>
                            <th class="px-4 py-2 font-medium">Versión</th>
                            <th class="px-4 py-2 font-medium">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach (array_reverse($executed) as $m): ?>
                            <tr>
                                <td class="px-4 py-3 text-slate-400"><?= $m['executed_at'] ?></td>
                                <td class="px-4 py-3 font-mono text-slate-700"><?= $m['version'] ?></td>
                                <td class="px-4 py-3 text-slate-400"><?= $m['execution_time_ms'] ?>ms</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Backups -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Backups Recientes</h2>
            </div>
            <div class="max-h-80 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase">
                        <tr>
                            <th class="px-4 py-2 font-medium">Fecha</th>
                            <th class="px-4 py-2 font-medium">Tamaño</th>
                            <th class="px-4 py-2 font-medium text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($backups as $b): ?>
                            <tr>
                                <td class="px-4 py-3 text-slate-700"><?= $b['date'] ?></td>
                                <td class="px-4 py-3 text-slate-400"><?= $b['size_mb'] ?> MB</td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="restoreBackup('<?= $b['filename'] ?>')" class="text-indigo-600 hover:text-indigo-900 font-bold">Restaurar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gestión de Mantenimiento Manual -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-slate-200">
        <h2 class="font-semibold text-slate-800 mb-4">Control Manual de Mantenimiento</h2>
        <form action="/admin/update/maintenance/enable" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?= \App\Core\Csrf::tokenField() ?>
            <div class="md:col-span-2">
                <input type="text" name="message" placeholder="Mensaje para los usuarios" class="w-full px-3 py-2 border rounded-md text-sm">
            </div>
            <div>
                <input type="text" name="estimated_end" placeholder="Tiempo estimado (ej: 15 min)" class="w-full px-3 py-2 border rounded-md text-sm">
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-md hover:bg-slate-900">Activar Modo Mantenimiento</button>
            </div>
        </form>
    </div>
</div>

<script>
async function runUpdate() {
    if (!confirm('¿Confirmas que quieres ejecutar las migraciones pendientes?\n\nSe creará un backup automático antes de comenzar.')) return;

    const btn = document.getElementById('run-update-btn');
    const logContainer = document.getElementById('update-log-container');
    const log = document.getElementById('update-log');
    
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    btn.textContent = '⏳ Ejecutando...';
    
    logContainer.classList.remove('hidden');
    log.textContent = 'Iniciando proceso de actualización...\n';

    try {
        const res = await fetch('/admin/update/run', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= \App\Core\Session::get("csrf_token") ?>'
            },
            body: 'csrf_token=<?= \App\Core\Session::get("csrf_token") ?>'
        });

        const result = await res.json();

        if (result.success) {
            log.textContent += '\n✅ ' + result.message + '\n\n';
            if (result.migrations) {
                result.migrations.forEach(m => {
                    log.textContent += `  ✓ ${m.version} — ${m.description} (${m.time_ms}ms)\n`;
                });
            }
            log.textContent += '\n🎉 Actualización completada correctamente. Recargando...';
            setTimeout(() => location.reload(), 2000);
        } else {
            log.textContent += '\n❌ Error: ' + result.error + '\n';
            if (result.backup_used) {
                log.textContent += '\n🔄 Se ha restaurado el backup automáticamente.';
            }
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.textContent = '🔄 Reintentar';
        }
    } catch (e) {
        log.textContent += '\n❌ Error de red: ' + e.message;
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.textContent = '🔄 Reintentar';
    }
}

async function checkIntegrity() {
    try {
        const res = await fetch('/admin/update/integrity');
        const result = await res.json();
        let msg = "Resultado de integridad:\n\n";
        for(let key in result.checks) {
            msg += (result.checks[key].ok ? "✅" : "❌") + " " + key + ": " + result.checks[key].detail + "\n";
        }
        alert(msg);
    } catch(e) {
        alert("Error al verificar integridad");
    }
}

async function restoreBackup(filename) {
    if(!confirm("⚠️ ¿Estás SEGURO de restaurar este backup?\n\nSe sobrescribirá la base de datos actual. El sistema entrará en modo mantenimiento.")) return;
    
    try {
        const res = await fetch('/admin/update/backup/restore', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= \App\Core\Session::get("csrf_token") ?>'
            },
            body: JSON.stringify({
                filename: filename,
                csrf_token: '<?= \App\Core\Session::get("csrf_token") ?>'
            })
        });
        const result = await res.json();
        if(result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert("Error: " + result.error);
        }
    } catch(e) {
        alert("Error de red");
    }
}
</script>

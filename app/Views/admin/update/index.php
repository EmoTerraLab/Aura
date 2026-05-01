<div class="space-y-8 animate-[fadeIn_0.3s_ease-out]">
    <!-- Header -->
    <div class="bg-surface-container-lowest rounded-2xl p-8 border border-surface-variant/50 ambient-shadow">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-primary-container flex items-center justify-center text-on-primary-container">
                    <span class="material-symbols-outlined text-4xl">system_update</span>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-primary tracking-tight">Gestión de Actualizaciones</h1>
                    <p class="text-on-surface-variant font-medium mt-1">
                        Versión instalada: <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-bold ml-2">v<?= $currentVersion ?></span>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="createBackup()" class="bg-slate-800 text-white px-6 py-3 rounded-full font-bold text-sm shadow-lg shadow-slate-800/20 hover:bg-slate-900 transition-all flex items-center gap-2 group">
                    <span class="material-symbols-outlined text-sm group-hover:rotate-180 transition-transform">backup</span>
                    Backup Manual
                </button>
                <button onclick="checkIntegrity()" class="bg-white text-on-surface border border-outline-variant px-6 py-3 rounded-full font-bold text-sm hover:bg-surface-container transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    Verificar Integridad
                </button>
            </div>
        </div>

        <?php if ($maintenanceActive): ?>
            <div class="mt-8 p-6 bg-error-container text-on-error-container rounded-2xl border border-error/20 flex items-center gap-6">
                <div class="w-12 h-12 rounded-full bg-error/10 flex items-center justify-center text-error">
                    <span class="material-symbols-outlined text-3xl">lock_open</span>
                </div>
                <div class="flex-1">
                    <p class="font-black text-lg leading-none">Modo Mantenimiento ACTIVO</p>
                    <p class="text-sm opacity-80 mt-2">Los usuarios no pueden acceder a la plataforma: <span class="font-bold underline italic"><?= htmlspecialchars($maintenanceData['message'] ?? '') ?></span></p>
                </div>
                <form action="/admin/update/maintenance/disable" method="POST">
                    <?= \App\Core\Csrf::tokenField() ?>
                    <button type="submit" class="bg-error text-on-error px-6 py-3 rounded-full font-bold text-sm shadow-lg shadow-error/20 hover:scale-105 transition-transform">
                        Desactivar ahora
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left: Pending Migrations -->
        <div class="xl:col-span-2 space-y-8">
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/50 ambient-shadow overflow-hidden">
                <div class="px-8 py-6 border-b border-surface-variant/30 flex justify-between items-center bg-surface-container-low/30">
                    <h2 class="text-xl font-black text-primary flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">pending_actions</span>
                        Migraciones Pendientes
                    </h2>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest <?= empty($pending) ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' ?>">
                        <?= count($pending) ?> detectadas
                    </span>
                </div>
                
                <?php if (empty($pending)): ?>
                    <div class="p-16 text-center flex flex-col items-center">
                        <div class="w-20 h-20 rounded-full bg-green-50 flex items-center justify-center text-green-500 mb-6">
                            <span class="material-symbols-outlined text-5xl">task_alt</span>
                        </div>
                        <h3 class="text-2xl font-black text-on-surface mb-2">¡Todo al día!</h3>
                        <p class="text-on-surface-variant">El sistema está sincronizado con la última versión de la base de datos.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="w-full text-left">
                            <thead class="bg-surface-container-low/50 text-on-surface-variant uppercase text-[11px] font-black tracking-widest">
                                <tr>
                                    <th class="px-8 py-4">Versión / ID</th>
                                    <th class="px-8 py-4">Descripción del cambio</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-variant/20">
                                <?php foreach ($pending as $m): ?>
                                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                                        <td class="px-8 py-6">
                                            <span class="font-mono font-bold text-primary text-sm bg-primary/5 px-3 py-1.5 rounded-lg border border-primary/10">
                                                <?= $m['version'] ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-on-surface font-medium"><?= htmlspecialchars($m['description']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-8 bg-surface-container-low/10 border-t border-surface-variant/30 flex justify-end">
                        <button id="run-update-btn" onclick="runUpdate()" class="bg-primary text-on-primary px-10 py-4 rounded-full font-black text-lg shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-3">
                            <span class="material-symbols-outlined">rocket_launch</span>
                            EJECUTAR ACTUALIZACIÓN
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Console Log -->
            <div id="update-log-container" class="hidden bg-slate-950 rounded-2xl shadow-2xl overflow-hidden border-4 border-slate-900 animate-[fadeIn_0.3s_ease-out]">
                <div class="px-6 py-3 bg-slate-900 flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Update Engine Console
                    </span>
                    <div class="flex gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-slate-800"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-slate-800"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-slate-800"></div>
                    </div>
                </div>
                <pre id="update-log" class="p-8 text-green-400 font-mono text-sm leading-relaxed max-h-80 overflow-y-auto no-scrollbar selection:bg-green-500 selection:text-slate-950"></pre>
            </div>

            <!-- Migration History -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/50 ambient-shadow overflow-hidden">
                <div class="px-8 py-5 border-b border-surface-variant/30 bg-surface-container-low/10">
                    <h2 class="text-lg font-black text-on-surface-variant flex items-center gap-3 uppercase tracking-tighter">
                        <span class="material-symbols-outlined text-outline">history</span>
                        Historial de Ejecución
                    </h2>
                </div>
                <div class="max-h-96 overflow-y-auto no-scrollbar">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-surface-container-lowest/90 backdrop-blur-md text-[10px] font-black text-outline uppercase tracking-widest border-b border-surface-variant/20">
                            <tr>
                                <th class="px-8 py-3">Fecha de Ejecución</th>
                                <th class="px-8 py-3">Versión</th>
                                <th class="px-8 py-3 text-right">Duración</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-variant/10 text-xs">
                            <?php foreach (array_reverse($executed) as $m): ?>
                                <tr class="hover:bg-surface-container-low/20 transition-colors">
                                    <td class="px-8 py-4 text-outline font-medium tracking-tight"><?= $m['executed_at'] ?></td>
                                    <td class="px-8 py-4 font-mono font-bold text-on-surface"><?= $m['version'] ?></td>
                                    <td class="px-8 py-4 text-right">
                                        <span class="bg-surface-container px-2 py-1 rounded-md text-on-surface-variant font-bold"><?= $m['execution_time_ms'] ?>ms</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Backups Section -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/50 ambient-shadow overflow-hidden">
                <div class="px-6 py-5 border-b border-surface-variant/30 bg-surface-container-low/10 flex justify-between items-center">
                    <h2 class="text-sm font-black text-on-surface-variant uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">folder_zip</span>
                        Backups SQL
                    </h2>
                    <span class="bg-primary/10 text-primary text-[10px] font-black px-2 py-0.5 rounded"><?= count($backups) ?></span>
                </div>
                <div class="max-h-[400px] overflow-y-auto no-scrollbar">
                    <div class="divide-y divide-surface-variant/10">
                        <?php foreach ($backups as $b): ?>
                            <div class="p-6 hover:bg-surface-container-low/20 transition-all group">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-black text-on-surface tracking-tight leading-none"><?= $b['date'] ?></p>
                                    <span class="text-[10px] font-black bg-surface-container px-2 py-1 rounded text-on-surface-variant uppercase tracking-widest"><?= $b['size_mb'] ?> MB</span>
                                </div>
                                <div class="flex items-center gap-4 mt-4">
                                    <button onclick="restoreBackup('<?= $b['filename'] ?>')" class="flex-1 text-[11px] font-black uppercase tracking-widest bg-white border border-outline-variant text-on-surface-variant py-2.5 rounded-full hover:bg-error hover:text-white hover:border-error transition-all flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-sm">settings_backup_restore</span>
                                        Restaurar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Maintenance Control -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/50 ambient-shadow p-6">
                <h2 class="text-sm font-black text-on-surface-variant uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-sm">construction</span>
                    Control Manual
                </h2>
                <form action="/admin/update/maintenance/enable" method="POST" class="space-y-4">
                    <?= \App\Core\Csrf::tokenField() ?>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-outline uppercase ml-4">Mensaje de aviso</label>
                        <input type="text" name="message" placeholder="Ej: Actualización programada" class="w-full bg-surface-container-low text-on-surface text-sm rounded-full px-5 py-3 border-none focus:ring-2 focus:ring-primary/30 outline-none placeholder:text-outline-variant">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-outline uppercase ml-4">Tiempo estimado</label>
                        <input type="text" name="estimated_end" placeholder="Ej: 15 minutos" class="w-full bg-surface-container-low text-on-surface text-sm rounded-full px-5 py-3 border-none focus:ring-2 focus:ring-primary/30 outline-none placeholder:text-outline-variant">
                    </div>
                    <button type="submit" class="w-full bg-slate-800 text-white py-4 rounded-full font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-slate-800/10 hover:bg-slate-900 transition-all mt-2">
                        Activar Mantenimiento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
async function runUpdate() {
    if (!confirm('¿Confirmas que quieres ejecutar las migraciones pendientes?\n\nSe creará un backup automático antes de comenzar.')) return;

    const btn = document.getElementById('run-update-btn');
    const logContainer = document.getElementById('update-log-container');
    const log = document.getElementById('update-log');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> EJECUTANDO...';
    
    logContainer.classList.remove('hidden');
    log.textContent = '> Aura Update Engine v2.21.1\n> Iniciando proceso de sincronización...\n';

    try {
        const res = await fetch('/admin/update/run', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= \App\Core\Session::get("csrf_token") ?>'
            },
            body: 'csrf_token=<?= \App\Core\Session::get("csrf_token") ?>'
        });

        const result = await res.json();

        if (result.success) {
            log.textContent += '\n[OK] ' + result.message + '\n';
            if (result.migrations) {
                result.migrations.forEach(m => {
                    log.textContent += `  >> SYNCED: ${m.version} (${m.time_ms}ms)\n`;
                });
            }
            log.textContent += '\n[SUCCESS] El sistema se ha actualizado correctamente.\n[!] Recargando interfaz...';
            setTimeout(() => location.reload(), 2000);
        } else {
            log.textContent += '\n[ERROR] ' + result.error + '\n';
            if (result.backup_used) {
                log.textContent += '\n[ROLLBACK] Se ha restaurado el backup automáticamente por seguridad.';
            }
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined">refresh</span> REINTENTAR';
        }
    } catch (e) {
        log.textContent += '\n[NETWORK ERROR] ' + e.message;
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">refresh</span> REINTENTAR';
    }
}

async function checkIntegrity() {
    try {
        const res = await fetch('/admin/update/integrity');
        const result = await res.json();
        let msg = "INSPECCIÓN DE INTEGRIDAD:\n" + "─".repeat(30) + "\n\n";
        for(let key in result.checks) {
            msg += (result.checks[key].ok ? "✅" : "❌") + " " + key.toUpperCase() + ": " + result.checks[key].detail + "\n";
        }
        alert(msg);
    } catch(e) {
        alert("Error crítico durante la inspección");
    }
}

async function createBackup() {
    if(!confirm("¿Quieres crear un backup de la base de datos ahora?")) return;
    try {
        const res = await fetch('/admin/update/backup/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?= \App\Core\Session::get("csrf_token") ?>'
            }
        });
        const result = await res.json();
        if(result.success) {
            alert("Backup generado: " + result.message);
            location.reload();
        } else {
            alert("Error: " + result.error);
        }
    } catch(e) {
        alert("Fallo en la conexión");
    }
}

async function restoreBackup(filename) {
    const confirmation = prompt(`⚠️ ALERTA DE SEGURIDAD: Vas a restaurar un backup.\n\nEsto sobrescribirá permanentemente la base de datos actual.\n\nPara confirmar, escribe "RESTAURAR" en el cuadro de abajo:`);
    
    if(confirmation !== "RESTAURAR") {
        if(confirmation !== null) alert("Confirmación denegada.");
        return;
    }
    
    try {
        const res = await fetch('/admin/update/backup/restore', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= \App\Core\Session::get("csrf_token") ?>'
            },
            body: JSON.stringify({
                filename: filename,
                confirmation: confirmation,
                csrf_token: '<?= \App\Core\Session::get("csrf_token") ?>'
            })
        });
        const result = await res.json();
        if(result.success) {
            alert("Sistema restaurado con éxito.");
            location.reload();
        } else {
            alert("Error en la restauración: " + result.error);
        }
    } catch(e) {
        alert("Fallo en la red");
    }
}
</script>

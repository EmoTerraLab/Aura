<div class="space-y-6 animate-fadeIn font-display">
    <!-- Header -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 border border-surface-variant/40 shadow-card">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                    <span class="material-symbols-outlined text-3xl">system_update</span>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight">Gestión de Actualizaciones</h1>
                    <p class="text-xs md:text-sm text-on-surface-variant font-medium mt-0.5">
                        Versión instalada: <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-xs font-bold ml-1">v<?= $currentVersion ?></span>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2.5">
                <button onclick="createBackup()" class="h-10 bg-primary text-on-primary px-5 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-base">backup</span>
                    Backup Manual
                </button>
                <button onclick="checkIntegrity()" class="h-10 bg-surface-container-low text-on-surface border border-surface-variant/40 px-5 rounded-xl font-bold text-xs hover:bg-surface-container-high transition-all flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-base">verified_user</span>
                    Verificar Integridad
                </button>
            </div>
        </div>

        <?php if ($maintenanceActive): ?>
            <div class="mt-6 p-4 bg-error/10 text-error rounded-xl border border-error/20 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-error/20 flex items-center justify-center text-error shrink-0">
                    <span class="material-symbols-outlined text-2xl">lock_open</span>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-sm">Modo Mantenimiento ACTIVO</p>
                    <p class="text-xs opacity-90 mt-0.5">Los usuarios no pueden acceder a la plataforma: <span class="font-bold underline italic"><?= htmlspecialchars($maintenanceData['message'] ?? '') ?></span></p>
                </div>
                <form action="/admin/update/maintenance/disable" method="POST">
                    <?= \App\Core\Csrf::tokenField() ?>
                    <button type="submit" class="h-9 bg-error text-white px-4 rounded-xl font-bold text-xs shadow-xs hover:bg-error/90 transition-all cursor-pointer">
                        Desactivar ahora
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left: Pending Migrations -->
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-variant/40 flex justify-between items-center bg-surface-container-low">
                    <h2 class="text-sm md:text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">pending_actions</span>
                        Migraciones Pendientes
                    </h2>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= empty($pending) ? 'bg-prisma-mint/25 text-teal-900' : 'bg-amber-100 text-amber-900' ?>">
                        <?= count($pending) ?> detectadas
                    </span>
                </div>
                
                <?php if (empty($pending)): ?>
                    <div class="p-12 text-center flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-prisma-mint/20 flex items-center justify-center text-teal-800 mb-4">
                            <span class="material-symbols-outlined text-3xl">task_alt</span>
                        </div>
                        <h3 class="text-lg font-bold text-on-surface mb-1">¡Todo al día!</h3>
                        <p class="text-xs text-on-surface-variant">El sistema está sincronizado con la última versión de la base de datos.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Versión / ID</th>
                                    <th class="px-6 py-3">Descripción del cambio</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-variant/20 font-body-md">
                                <?php foreach ($pending as $m): ?>
                                    <tr class="hover:bg-surface-container-low transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-mono font-bold text-primary text-xs bg-primary/5 px-2.5 py-1 rounded-lg border border-primary/15">
                                                <?= $m['version'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-on-surface font-medium"><?= htmlspecialchars($m['description']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 bg-surface-container-low border-t border-surface-variant/40 flex justify-end">
                        <button id="run-update-btn" onclick="runUpdate()" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-base">rocket_launch</span>
                            <span>EJECUTAR ACTUALIZACIÓN</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Console Log -->
            <div id="update-log-container" class="hidden bg-slate-950 rounded-2xl shadow-card overflow-hidden border border-slate-800 animate-fadeIn">
                <div class="px-5 py-2.5 bg-slate-900 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-prisma-mint animate-pulse"></span>
                        Prisma Update Engine Console
                    </span>
                    <div class="flex gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                        <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                        <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                    </div>
                </div>
                <pre id="update-log" class="p-6 text-prisma-mint font-mono text-xs leading-relaxed max-h-72 overflow-y-auto no-scrollbar"></pre>
            </div>

            <!-- Migration History -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-variant/40 bg-surface-container-low">
                    <h2 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">history</span>
                        Historial de Ejecución
                    </h2>
                </div>
                <div class="max-h-80 overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="sticky top-0 bg-surface-container-low text-[10px] font-bold text-on-surface-variant uppercase tracking-wider border-b border-surface-variant/30">
                            <tr>
                                <th class="px-6 py-2.5">Fecha de Ejecución</th>
                                <th class="px-6 py-2.5">Versión</th>
                                <th class="px-6 py-2.5 text-right">Duración</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-variant/20 font-body-md text-xs">
                            <?php foreach (array_reverse($executed) as $m): ?>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="px-6 py-3 text-on-surface-variant font-medium"><?= $m['executed_at'] ?></td>
                                    <td class="px-6 py-3 font-mono font-bold text-on-surface"><?= $m['version'] ?></td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="bg-surface-container-high px-2 py-0.5 rounded text-on-surface-variant font-semibold text-[10px]"><?= $m['execution_time_ms'] ?>ms</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Backups Section -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card overflow-hidden">
                <div class="px-5 py-4 border-b border-surface-variant/40 bg-surface-container-low flex justify-between items-center">
                    <h2 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">folder_zip</span>
                        Backups SQL
                    </h2>
                    <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-md"><?= count($backups) ?></span>
                </div>
                <div class="max-h-[360px] overflow-y-auto">
                    <div class="divide-y divide-surface-variant/20">
                        <?php foreach ($backups as $b): ?>
                            <div class="p-4 hover:bg-surface-container-low transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-bold text-on-surface leading-tight"><?= $b['date'] ?></p>
                                    <span class="text-[10px] font-mono font-bold bg-surface-container-high px-1.5 py-0.5 rounded text-on-surface-variant"><?= $b['size_mb'] ?> MB</span>
                                </div>
                                <button onclick="restoreBackup('<?= $b['filename'] ?>')" class="w-full text-[11px] font-bold uppercase tracking-wider bg-surface-container-low border border-surface-variant/40 text-on-surface-variant py-2 rounded-xl hover:bg-error hover:text-white hover:border-error transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">settings_backup_restore</span>
                                    Restaurar
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Maintenance Control -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card p-5">
                <h2 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider flex items-center gap-1.5 mb-4">
                    <span class="material-symbols-outlined text-base">construction</span>
                    Control Manual de Mantenimiento
                </h2>
                <form action="/admin/update/maintenance/enable" method="POST" class="space-y-3 font-body-md">
                    <?= \App\Core\Csrf::tokenField() ?>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Mensaje de aviso</label>
                        <input type="text" name="message" placeholder="Ej: Actualización programada" class="w-full h-10 bg-surface-container-low text-on-surface text-xs rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Tiempo estimado</label>
                        <input type="text" name="estimated_end" placeholder="Ej: 15 minutos" class="w-full h-10 bg-surface-container-low text-on-surface text-xs rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <button type="submit" class="w-full h-11 bg-primary text-on-primary rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all mt-1 cursor-pointer font-display">
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
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> EJECUTANDO...';
    
    logContainer.classList.remove('hidden');
    log.textContent = '> Prisma Update Engine v2.22.0\n> Iniciando proceso de sincronización...\n';

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
            setTimeout(() => location.reload(), 1500);
        } else {
            log.textContent += '\n[ERROR] ' + result.error + '\n';
            if (result.backup_used) {
                log.textContent += '\n[ROLLBACK] Se ha restaurado el backup automáticamente por seguridad.';
            }
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">refresh</span> REINTENTAR';
        }
    } catch (e) {
        log.textContent += '\n[NETWORK ERROR] ' + e.message;
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm">refresh</span> REINTENTAR';
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

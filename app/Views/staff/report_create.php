<div class="flex flex-col lg:flex-row gap-8 animate-[fadeIn_0.4s_ease-out]">
    <!-- Form Content -->
    <div class="flex-1 space-y-6">
        <header>
            <h1 class="text-2xl font-black text-primary">Registrar Nueva Incidencia</h1>
            <p class="text-slate-500">Documenta una observación o sospecha de acoso entre alumnos.</p>
        </header>

        <div class="bg-white rounded-3xl border border-slate-100 p-8 ambient-shadow">
            <form id="staff-report-form" action="/staff/reports" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>
                
                <div class="space-y-2">
                    <label class="font-bold text-sm ml-2">Título de la incidencia</label>
                    <input type="text" name="title" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20" placeholder="Ej: Conducta reiterada en el recreo">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-bold text-sm ml-2">Categoría</label>
                        <select name="category" id="category" onchange="checkProtocolTrigger()" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20">
                            <option value="comportamiento">Comportamiento General</option>
                            <option value="acoso">Acoso / Bullying</option>
                            <option value="ciberacoso">Ciberacoso</option>
                            <option value="rendimiento">Rendimiento Académico</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="font-bold text-sm ml-2">Urgencia</label>
                        <select name="urgency" id="urgency" onchange="checkProtocolTrigger()" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20">
                            <option value="baja">Baja</option>
                            <option value="normal" selected>Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="font-bold text-sm ml-2">Descripción detallada</label>
                    <textarea name="description" rows="5" required class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20" placeholder="Describe los hechos observados..."></textarea>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-primary text-white px-10 py-4 rounded-full font-bold shadow-lg hover:scale-105 transition-all">
                        Registrar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Protocol Sidebar Hint -->
    <aside id="protocol-hint" class="lg:w-80 space-y-6 hidden animate-[fadeIn_0.3s_ease-out]">
        <div class="bg-teal-50 border border-teal-100 rounded-3xl p-6">
            <div class="flex items-center gap-2 text-teal-700 mb-4">
                <span class="material-symbols-outlined fill-1">verified_user</span>
                <h3 class="font-bold text-sm uppercase tracking-wider"><?= \App\Core\Lang::t('protocol.hint_title') ?></h3>
            </div>
            <p class="text-xs text-teal-800/70 leading-relaxed mb-6">
                <?= \App\Core\Lang::t('protocol.hint_desc') ?>
            </p>
            
            <div id="protocol-steps" class="space-y-4">
                <!-- Se llena vía API -->
            </div>

            <div class="mt-8 pt-6 border-t border-teal-200/50">
                <a href="/protocolo-acoso" target="_blank" class="flex items-center justify-between text-teal-700 font-bold text-xs hover:underline">
                    Ver protocolo completo
                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                </a>
            </div>
        </div>
    </aside>
</div>

<script>
    async function checkProtocolTrigger() {
        const category = document.getElementById('category').value;
        const urgency = document.getElementById('urgency').value;
        const hint = document.getElementById('protocol-hint');
        const steps = document.getElementById('protocol-steps');

        const isTriggered = ['acoso', 'ciberacoso'].includes(category) || ['alta', 'urgente'].includes(urgency);

        if (isTriggered) {
            if (hint.classList.contains('hidden')) {
                hint.classList.remove('hidden');
                // Cargar datos
                try {
                    const res = await fetch('/api/protocol');
                    const data = await res.json();
                    
                    if (data && data.phases) {
                        steps.innerHTML = data.phases.slice(0, 3).map(p => `
                            <div class="flex gap-3">
                                <div class="w-6 h-6 rounded-full bg-teal-200 text-teal-700 flex items-center justify-center text-[10px] font-bold shrink-0">${p.number}</div>
                                <div>
                                    <p class="text-xs font-bold text-teal-900">${p.title}</p>
                                    <p class="text-[10px] text-teal-700/70 line-clamp-2">${p.description}</p>
                                </div>
                            </div>
                        `).join('');
                    }
                } catch (e) { console.error(e); }
            }
        } else {
            hint.classList.add('hidden');
        }
    }
</script>

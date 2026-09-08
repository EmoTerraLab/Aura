<div class="flex flex-col lg:flex-row gap-6 animate-fadeIn font-display">
    <!-- Form Content -->
    <div class="flex-1 space-y-4">
        <header>
            <h1 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight">Registrar Nueva Incidencia</h1>
            <p class="text-xs md:text-sm text-on-surface-variant">Documenta una observación o sospecha de acoso entre alumnos.</p>
        </header>

        <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 p-6 md:p-8 shadow-card">
            <form id="staff-report-form" action="/staff/reports" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>
                
                <div class="space-y-1.5">
                    <label class="font-bold text-xs uppercase tracking-wider text-on-surface-variant ml-1">Título de la incidencia</label>
                    <input type="text" name="title" required class="w-full h-11 bg-surface-container-low border border-surface-variant/40 rounded-xl px-4 text-xs md:text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 placeholder:text-outline-variant font-body-md" placeholder="Ej: Conducta reiterada en el recreo">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-bold text-xs uppercase tracking-wider text-on-surface-variant ml-1">Categoría</label>
                        <select name="category" id="category" onchange="checkProtocolTrigger()" class="w-full h-11 bg-surface-container-low border border-surface-variant/40 rounded-xl px-4 text-xs md:text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md">
                            <option value="comportamiento">Comportamiento General</option>
                            <option value="acoso">Acoso / Bullying</option>
                            <option value="ciberacoso">Ciberacoso</option>
                            <option value="rendimiento">Rendimiento Académico</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-bold text-xs uppercase tracking-wider text-on-surface-variant ml-1">Urgencia</label>
                        <select name="urgency" id="urgency" onchange="checkProtocolTrigger()" class="w-full h-11 bg-surface-container-low border border-surface-variant/40 rounded-xl px-4 text-xs md:text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md">
                            <option value="baja">Baja</option>
                            <option value="normal" selected>Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-xs uppercase tracking-wider text-on-surface-variant ml-1">Descripción detallada</label>
                    <textarea name="description" rows="5" required class="w-full bg-surface-container-low border border-surface-variant/40 rounded-xl p-4 text-xs md:text-sm text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 placeholder:text-outline-variant resize-none font-body-md" placeholder="Describe los hechos observados de forma objetiva..."></textarea>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="h-11 bg-primary text-on-primary px-8 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center gap-2 cursor-pointer">
                        <span>Registrar Reporte</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Protocol Sidebar Hint -->
    <aside id="protocol-hint" class="lg:w-80 space-y-4 hidden animate-fadeIn font-display">
        <div class="bg-prisma-sky/20 border border-prisma-sky/50 rounded-2xl p-5 shadow-card">
            <div class="flex items-center gap-2 text-sky-950 mb-3">
                <span class="material-symbols-outlined text-xl">gavel</span>
                <h3 class="font-bold text-xs uppercase tracking-wider"><?= \App\Core\Lang::t('protocol.hint_title') ?></h3>
            </div>
            <p class="text-xs text-sky-950/80 leading-relaxed mb-4">
                <?= \App\Core\Lang::t('protocol.hint_desc') ?>
            </p>
            
            <div id="protocol-steps" class="space-y-2.5">
                <!-- Injected via JS -->
            </div>

            <div class="mt-6 pt-4 border-t border-prisma-sky/40">
                <a href="/protocolo-acoso" target="_blank" class="flex items-center justify-between text-sky-950 font-bold text-xs hover:underline">
                    <span>Ver protocolo oficial</span>
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
                try {
                    const res = await fetch('/api/protocol');
                    const data = await res.json();
                    
                    if (data && data.phases) {
                        steps.innerHTML = data.phases.slice(0, 3).map(p => `
                            <div class="flex gap-2.5 items-start">
                                <div class="w-5 h-5 rounded-md bg-prisma-sky/40 text-sky-950 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">${p.number}</div>
                                <div>
                                    <p class="text-xs font-bold text-sky-950">${p.title}</p>
                                    <p class="text-[11px] text-sky-900/75 line-clamp-2">${p.description}</p>
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

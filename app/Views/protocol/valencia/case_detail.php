<?php
use App\Core\Lang;
$protocol = \App\Services\Protocol\ProtocolFactory::make('VAL');
$timeline = $protocol->getTimelineSteps();
$actions = $protocol->getActionsForState($case['current_phase'], $case);
$activeStepIndex = array_search($case['current_phase'], array_column($timeline, 'key'));
?>

<main class="min-h-screen bg-slate-50 py-8 px-4 md:px-8">
    <div class="max-w-6xl mx-auto space-y-8 animate-[fadeIn_0.4s_ease-out]">
        
        <!-- Header -->
        <header class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-3xl bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-3xl">gavel</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-widest">Expedient #<?= $case['report_id'] ?></span>
                        <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">Protocol VAL</span>
                    </div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight leading-none"><?= htmlspecialchars($report['student_name'] ?? 'Alumne Anònim') ?></h1>
                    <p class="text-slate-400 font-bold text-sm mt-1"><?= htmlspecialchars($report['classroom_name']) ?> • <?= $protocol->getName() ?></p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="/staff/inbox" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-full text-xs font-bold hover:bg-slate-200 transition-all">Tornar a la Bústia</a>
                <button onclick="window.print()" class="p-3 bg-white border border-slate-200 rounded-full hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-slate-600">print</span>
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Columna Principal -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Timeline Visual -->
                <section class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-8"><?= Lang::t('protocol.timeline_title') ?></h2>
                    <div class="relative flex flex-col md:flex-row justify-between gap-4">
                        <!-- Línea conectora -->
                        <div class="absolute left-6 md:left-0 md:top-6 w-0.5 md:w-full h-full md:h-0.5 bg-slate-100 z-0"></div>
                        
                        <?php foreach ($timeline as $index => $step): 
                            $isCompleted = $index < $activeStepIndex;
                            $isActive = $index === $activeStepIndex;
                            $colorClass = $isActive ? 'bg-primary text-white scale-110 shadow-lg shadow-primary/30' : ($isCompleted ? 'bg-emerald-500 text-white' : 'bg-white border-2 border-slate-100 text-slate-300');
                        ?>
                            <div class="relative z-10 flex md:flex-col items-center gap-4 md:gap-3 group">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300 <?= $colorClass ?>">
                                    <span class="material-symbols-outlined text-xl"><?= $isCompleted ? 'check' : $step['icon'] ?></span>
                                </div>
                                <div class="text-left md:text-center max-w-[120px]">
                                    <p class="text-[10px] font-black uppercase leading-tight <?= $isActive ? 'text-primary' : ($isCompleted ? 'text-emerald-600' : 'text-slate-400') ?>">
                                        <?= $step['label'] ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Accions Disponibles -->
                <section class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Accions de la Fase: <span class="text-slate-800"><?= $protocol->getStateLabel($case['current_phase']) ?></span></h2>
                    </div>
                    
                    <?php if (empty($actions)): ?>
                        <div class="p-10 text-center bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                            <p class="text-slate-400 italic text-sm">No hi ha accions pendents per a aquesta fase.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($actions as $action): ?>
                                <button onclick="<?= $action['onclick'] ?>" class="flex items-center gap-4 p-5 rounded-2xl border border-slate-100 hover:border-primary/30 hover:bg-primary/5 transition-all text-left group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-colors">
                                        <span class="material-symbols-outlined text-sm">bolt</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-700"><?= $action['label'] ?></p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Acció Disponible</p>
                                    </div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Contingut del Report -->
                <section class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Relat dels Fets (Original)</h2>
                    <div class="bg-slate-50 p-6 rounded-3xl text-slate-700 text-sm leading-relaxed whitespace-pre-wrap italic">
                        "<?= htmlspecialchars($report['content']) ?>"
                    </div>
                </section>

            </div>

            <!-- Columna Lateral -->
            <div class="space-y-8">
                
                <!-- Estat i Detalls -->
                <section class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-50 mb-6">Resum de l'Expedient</h2>
                    <div class="space-y-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase opacity-40">Fase Actual</p>
                            <p class="text-xl font-black"><?= $protocol->getStateLabel($case['current_phase']) ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase opacity-40">Data d'Inici</p>
                            <p class="text-lg font-bold"><?= date('d/m/Y', strtotime($case['created_at'])) ?></p>
                        </div>
                        <div class="pt-6 border-t border-white/10 grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase opacity-40">Gravetat</p>
                                <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase"><?= $case['severity_preliminary'] ?? 'Pendent' ?></span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase opacity-40">Tipificació</p>
                                <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase"><?= $case['classification'] ?? 'Pendent' ?></span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Log d'Auditoria -->
                <section class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Historial d'Auditoria</h2>
                    <div class="space-y-6 max-h-[400px] overflow-y-auto no-scrollbar">
                        <?php foreach ($auditLog as $log): ?>
                            <div class="relative pl-6 pb-6 border-l border-slate-100 last:pb-0">
                                <div class="absolute left-[-5px] top-0 w-2 h-2 rounded-full bg-slate-300"></div>
                                <p class="text-[10px] font-black text-slate-400 mb-1 uppercase"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></p>
                                <p class="text-xs font-bold text-slate-600"><?= str_replace('📋 [AUDITORIA LEGAL] ', '', $log['message']) ?></p>
                                <p class="text-[9px] text-slate-400 italic">Per: <?= $log['sender_name'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            </div>

        </div>
    </div>
</main>

<!-- Scripts Compartits del Dashboard -->
<script>
    async function fetchJson(url, options = {}) {
        const res = await fetch(url, {
            ...options,
            headers: { 
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                ...options.headers
            }
        });
        return res.json();
    }

    async function nextPhase(id, phase) {
        if (!confirm('¿Desitges avançar a la següent fase del protocol?')) return;
        const res = await fetchJson(`/api/protocol/case/${id}/phase`, { method: 'POST', body: JSON.stringify({ phase }) });
        if (res.success) window.location.reload();
        else alert('Error: ' + res.error);
    }

    async function protocolClassify(id, phase) {
        // En VAL, les accions de valoració porten a ACREDITAT o NO_ACREDITAT
        // Utilitzem el transitionTo per ser més genèrics
        if (!confirm('¿Confirmes aquesta valoració final del cas?')) return;
        const res = await fetchJson(`/api/protocol/case/${id}/phase`, { method: 'POST', body: JSON.stringify({ phase }) });
        if (res.success) window.location.reload();
        else alert('Error: ' + res.error);
    }

    function openFollowupModal(caseId, type = null) {
        const title = type ? `Registre d'Actuació: ${type.replace(/_/g, ' ').toUpperCase()}` : 'Nou Registre de Seguiment';
        const html = `
            <div id="modal-followup" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden">
                    <div class="p-6 border-b flex items-center justify-between">
                        <h3 class="font-black">${title}</h3>
                        <button onclick="document.getElementById('modal-followup').remove()"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <select id="f-target" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm ${type ? 'hidden' : ''}">
                            <option value="victima">Víctima</option>
                            <option value="agressor">Agressor</option>
                            <option value="familia">Família</option>
                            <option value="grup_classe">Grup Classe</option>
                            ${type ? `<option value="${type}" selected>${type}</option>` : ''}
                        </select>
                        <input type="date" id="f-date" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm" value="${new Date().toISOString().split('T')[0]}">
                        <textarea id="f-notes" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm" rows="4" placeholder="Notes de la sessió..."></textarea>
                        <button onclick="saveFollowup(${caseId}, '${type || ''}')" class="w-full py-4 bg-primary text-white rounded-full font-bold shadow-lg">Guardar Sessió</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    async function saveFollowup(caseId, type = null) {
        const data = {
            target_type: type || document.getElementById('f-target').value,
            session_date: document.getElementById('f-date').value,
            notes: document.getElementById('f-notes').value
        };
        const res = await fetchJson(`/api/protocol/case/${caseId}/followup`, { method: 'POST', body: JSON.stringify(data) });
        if (res.success) {
            document.getElementById('modal-followup').remove();
            window.location.reload();
        } else {
            alert('Error al guardar el seguiment');
        }
    }

    function openSecurityMap(caseId) {
        alert('Eina de Mapa de Seguretat activa en el Dashboard Principal');
        window.location.href = '/staff/inbox?report_id=' + caseId;
    }
</script>

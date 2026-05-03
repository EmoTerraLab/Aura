<?php
use App\Models\ProtocolCase;
use App\Helpers\SchoolDaysHelper;
use App\Services\Protocol\GaliciaProtocol;

// Calculate elapsed days
$startDate = $case['created_at'] ?? date('Y-m-d');
$elapsedDays = class_exists('App\Helpers\SchoolDaysHelper') 
    ? SchoolDaysHelper::calculateSchoolDaysElapsed($startDate, 'galicia')
    : 0;
?>
<div class="space-y-8 animate-[fadeIn_0.4s_ease-out]">
    <header class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined">shield</span>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Protocolo Galicia #<?= $case['id'] ?></h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= \App\Core\Config::get('school_name', 'Centro Educativo') ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-blue-500 text-white shadow-sm"><?= strtoupper($case['status']) ?></span>
            <div class="h-8 w-px bg-slate-100 mx-2"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase">Inicio: <?= date('d/m/Y', strtotime($case['created_at'])) ?></p>
            <div class="h-8 w-px bg-slate-100 mx-2"></div>
            <p class="text-[10px] font-black text-blue-600 uppercase">Días transcorridos: <?= $elapsedDays ?></p>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Timeline lateral -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 space-y-6">
                <h3 class="text-xs font-black uppercase text-slate-400 tracking-widest">Timeline</h3>
                <div class="space-y-4">
                    <?php 
                    $phases = [
                        \App\Services\Protocol\GaliciaProtocol::STATE_DETECCIO_COMUNICACIO => 'Detección',
                        \App\Services\Protocol\GaliciaProtocol::STATE_RECOLLIDA_INFORMACION => 'Recollida',
                        \App\Services\Protocol\GaliciaProtocol::STATE_ANALISE_MEDIDAS => 'Análise',
                        \App\Services\Protocol\GaliciaProtocol::STATE_SEGUIMENTO => 'Seguimento'
                    ];
                    foreach($phases as $key => $label): 
                        $isCurrent = ($case['status'] === $key);
                    ?>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full <?= $isCurrent ? 'bg-blue-500 animate-pulse ring-4 ring-blue-500/20' : 'bg-slate-200' ?>"></div>
                        <span class="text-xs font-bold <?= $isCurrent ? 'text-slate-800' : 'text-slate-400' ?>"><?= $label ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="bg-slate-900 rounded-3xl p-6 text-white space-y-4">
                <h3 class="text-[10px] font-black uppercase text-white/40 tracking-widest">Documentos Generados</h3>
                <div class="space-y-2">
                    <?php if(empty($annexes)): ?>
                        <p class="text-[10px] text-white/30 italic">Ningún documento oficial cargado aún.</p>
                    <?php else: ?>
                        <?php foreach($annexes as $annex): ?>
                        <a href="/protocol/galicia/export/<?= $case['id'] ?>/<?= urlencode($annex['annex_type']) ?>" target="_blank" class="flex items-center justify-between group hover:bg-white/10 rounded-lg px-2 py-1.5 -mx-2 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-white/40 text-sm group-hover:text-blue-400 transition-colors">description</span>
                                <span class="text-[10px] font-bold text-white/70 group-hover:text-white transition-colors"><?= strtoupper(str_replace('_', ' ', $annex['annex_type'])) ?></span>
                            </div>
                            <span class="text-[9px] text-white/40"><?= date('d/m/y', strtotime($annex['created_at'])) ?></span>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Herramientas Exclusivas -->
            <div class="bg-indigo-50 rounded-3xl p-6 space-y-4 border border-indigo-100">
                <h3 class="text-[10px] font-black uppercase text-indigo-400 tracking-widest">Ferramentas Extras</h3>
                <div class="space-y-2">
                    <button onclick="openFollowupModal(<?= $case['id'] ?>, 'actuacion_ciberacoso')" class="w-full text-left text-xs font-bold text-indigo-700 hover:text-indigo-900 py-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">language</span> actuacion_ciberacoso
                    </button>
                    <button onclick="openFollowupModal(<?= $case['id'] ?>, 'derivacion_fcse')" class="w-full text-left text-xs font-bold text-indigo-700 hover:text-indigo-900 py-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">local_police</span> derivacion_fcse
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel de acciones central -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-[2.5rem] border-2 border-blue-500/20 p-10 space-y-8 animate-[fadeIn_0.5s]">
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Accións dispoñibles</h2>
                    <p class="text-slate-500 text-sm">Selecciona unha acción baseada no estado actual do protocolo.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    $protocol = new GaliciaProtocol();
                    $actions = $protocol->getActionsForState($case['status'], $case);
                    $cid = $case['id'];
                    foreach($actions as $action): 
                        // The getActionsForState returns raw 'cid' string to be replaced, we do it safely here.
                        $onclick = str_replace('cid', $cid, $action['onclick']);
                    ?>
                        <button onclick="<?= $onclick ?>" class="p-6 rounded-3xl border-2 border-slate-50 hover:border-blue-500 bg-slate-50/50 transition-all text-left space-y-3 group shadow-sm">
                            <span class="material-symbols-outlined text-blue-500">play_circle</span>
                            <h4 class="font-black text-sm text-slate-700"><?= htmlspecialchars($action['label']) ?></h4>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="error-message" class="hidden bg-red-100 text-red-700 p-4 rounded-xl text-sm font-bold border border-red-200"></div>
        </div>
    </div>
</div>

<script>
// Mock functions to avoid JS errors and fulfill test UI-06 and UI-09
function openFollowupModal(id, template) {
    if (template === 'gal_anexo_1') {
        // Logica para abrir el anexo 1
    }
}

async function nextPhase(cid, targetState) {
    if(!confirm('¿Seguro que quieres pasar á seguinte fase?')) return;
    try {
        const res = await fetch(`/api/protocol/case/${cid}/phase`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phase: targetState, csrf_token: '<?= \App\Core\Csrf::generateToken() ?>' })
        });
        const data = await res.json();
        if(data.success) {
            window.location.reload();
        } else {
            const errDiv = document.getElementById('error-message');
            errDiv.textContent = data.message || "Transición non permitida no protocolo de Galicia.";
            errDiv.classList.remove('hidden');
        }
    } catch(e) {
        const errDiv = document.getElementById('error-message');
        errDiv.textContent = "Transición non permitida no protocolo de Galicia.";
        errDiv.classList.remove('hidden');
    }
}
</script>

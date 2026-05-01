<?php
use App\Models\ProtocolCase;
?>
<div class="space-y-8 animate-[fadeIn_0.4s_ease-out]">
    <header class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined">gavel</span>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Expediente Murcia #<?= $case['id'] ?></h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= \App\Core\Config::get('school_name', 'Centro Educativo') ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-amber-500 text-white shadow-sm"><?= strtoupper($case['status']) ?></span>
            <div class="h-8 w-px bg-slate-100 mx-2"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase">Inicio: <?= date('d/m/Y', strtotime($case['created_at'])) ?></p>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Timeline lateral -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 space-y-6">
                <h3 class="text-xs font-black uppercase text-slate-400 tracking-widest">Estado del Flujo</h3>
                <div class="space-y-4">
                    <?php 
                    $phases = [
                        ProtocolCase::PHASE_MUR_INICIAL => 'Inicio y Medidas',
                        ProtocolCase::PHASE_MUR_INTERVENCION => 'Entrevistas',
                        ProtocolCase::PHASE_MUR_INFORME => 'Informe (Anexo IV)',
                        ProtocolCase::PHASE_MUR_VALORACION => 'Valoración',
                        ProtocolCase::PHASE_MUR_CIERRE => 'Cierre'
                    ];
                    $reached = false;
                    foreach($phases as $key => $label): 
                        $isCurrent = ($case['status'] === $key);
                    ?>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full <?= $isCurrent ? 'bg-amber-500 ring-4 ring-amber-500/20' : 'bg-slate-200' ?>"></div>
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
                        <div class="flex items-center justify-between group">
                            <span class="text-[10px] font-bold text-white/70"><?= strtoupper($annex['annex_type']) ?></span>
                            <span class="text-[9px] text-white/40"><?= date('d/m/y', strtotime($annex['created_at'])) ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel de acciones central -->
        <div class="lg:col-span-3 space-y-6">
            
            <?php if ($case['status'] === ProtocolCase::PHASE_MUR_INICIAL): ?>
            <div class="bg-white rounded-[2.5rem] border-2 border-amber-500/20 p-10 space-y-8 animate-[fadeIn_0.5s]">
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Fase 1: Designación y Comunicación</h2>
                    <p class="text-slate-500 text-sm">Designa al equipo coordinador y registra las medidas de urgencia (Día 0).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <button onclick="openMurciaModal('designacion')" class="p-8 rounded-3xl border-2 border-slate-50 hover:border-amber-500 bg-slate-50/50 transition-all text-left space-y-3 group">
                        <span class="material-symbols-outlined text-amber-500">person_add</span>
                        <h4 class="font-black text-sm text-slate-700">Designar Equipo</h4>
                        <p class="text-[11px] text-slate-500">Coordinado por Jefatura de Estudios.</p>
                    </button>

                    <button onclick="openMurciaModal('medidas')" class="p-8 rounded-3xl border-2 border-slate-50 hover:border-amber-500 bg-slate-50/50 transition-all text-left space-y-3 group">
                        <span class="material-symbols-outlined text-amber-500">shield_with_heart</span>
                        <h4 class="font-black text-sm text-slate-700">Medidas de Urgencia</h4>
                        <p class="text-[11px] text-slate-500">Vigilancia discreta y protección víctima.</p>
                    </button>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                    <button onclick="submitAnexoI()" class="flex items-center gap-2 text-indigo-600 font-black text-xs uppercase tracking-widest hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined">send</span> Enviar Anexo I a Inspección
                    </button>
                    <button onclick="nextStep('<?= ProtocolCase::PHASE_MUR_INTERVENCION ?>')" class="bg-slate-900 text-white px-8 py-4 rounded-full font-black shadow-xl hover:bg-amber-600 transition-all">Pasar a Intervención</button>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($case['status'] === ProtocolCase::PHASE_MUR_INTERVENCION): ?>
            <div class="bg-white rounded-[2.5rem] border-2 border-amber-500/20 p-10 space-y-8 animate-[fadeIn_0.5s]">
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Fase 2: Intervención y Entrevistas</h2>
                    <p class="text-slate-500 text-sm">Realiza las entrevistas en el orden estricto establecido (Máx 20 días).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php 
                    $interviews = [
                        'victima' => '1º Presunta Víctima',
                        'observadores' => '2º Observadores',
                        'familia_victima' => '3º Padres Víctima',
                        'familia_agresor' => '4º Padres Agresores',
                        'agresor' => '5º Presunto(s) Agresor(es)'
                    ];
                    foreach($interviews as $type => $label):
                    ?>
                    <button onclick="openMurciaModal('entrevista', '<?= $type ?>')" class="p-6 rounded-2xl bg-slate-50 hover:bg-amber-50 border border-slate-100 transition-all text-left space-y-2">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest"><?= $label ?></span>
                        <p class="text-xs font-bold text-slate-700">Registrar sesión</p>
                    </button>
                    <?php endforeach; ?>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button onclick="nextStep('<?= ProtocolCase::PHASE_MUR_INFORME ?>')" class="bg-slate-900 text-white px-8 py-4 rounded-full font-black shadow-xl hover:bg-amber-600 transition-all">Finalizar Entrevistas y Redactar Informe</button>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($case['status'] === ProtocolCase::PHASE_MUR_INFORME): ?>
            <div class="bg-white rounded-[2.5rem] border-2 border-amber-500/20 p-10 space-y-8 animate-[fadeIn_0.5s]">
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Fase 3: Emisión del Informe (Anexo IV)</h2>
                    <p class="text-slate-500 text-sm">Resume las actuaciones y conclusiones del equipo de intervención.</p>
                </div>

                <div class="space-y-4">
                    <label class="text-xs font-black uppercase text-slate-400">Contenido del Informe</label>
                    <textarea id="informe-content" class="w-full bg-slate-50 border-0 rounded-3xl p-8 text-sm focus:ring-4 ring-amber-500/10 min-h-[300px]" placeholder="Redacta aquí las conclusiones del equipo..."></textarea>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button onclick="submitAnexoIV()" class="bg-amber-500 text-white px-10 py-5 rounded-full font-black shadow-xl hover:scale-105 active:scale-95 transition-all">Firmar y Enviar a Dirección</button>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($case['status'] === ProtocolCase::PHASE_MUR_VALORACION): ?>
            <div class="bg-white rounded-[2.5rem] border-2 border-amber-500/20 p-10 space-y-8 animate-[fadeIn_0.5s]">
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Fase 4: Valoración y Decisión (Anexo V)</h2>
                    <p class="text-slate-500 text-sm">Dirección convoca reunión conjunta para determinar evidencias.</p>
                </div>

                <form id="form-valuation" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4 p-8 rounded-[2rem] bg-slate-50">
                            <h4 class="font-black text-slate-700">¿Existen evidencias de acoso?</h4>
                            <div class="flex flex-col gap-3">
                                <label class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 cursor-pointer">
                                    <input type="radio" name="conclusion" value="si_evidencias" class="text-amber-500">
                                    <span class="text-sm font-bold text-slate-700">SÍ HAY EVIDENCIAS</span>
                                </label>
                                <label class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 cursor-pointer">
                                    <input type="radio" name="conclusion" value="no_evidencias" checked class="text-amber-500">
                                    <span class="text-sm font-bold text-slate-700">NO HAY EVIDENCIAS</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-black text-slate-700">Acta de Reunión</h4>
                            <textarea name="acta_notes" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm" rows="5" placeholder="Resumen de lo manifestado por los asistentes..."></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end">
                        <button type="button" onclick="submitValuation()" class="bg-slate-900 text-white px-10 py-5 rounded-full font-black shadow-xl">Guardar Valoración y Medidas</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Murcia (Contenedor Dinámico) -->
<div id="modal-murcia" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden animate-[scaleIn_0.3s_ease-out]">
        <div id="modal-murcia-content"></div>
    </div>
</div>

<script>
async function nextStep(phase) {
    if(!confirm('¿Seguro que quieres pasar a la siguiente fase?')) return;
    const res = await fetch(`/api/protocol/case/<?= $case['id'] ?>/phase`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phase, csrf_token: '<?= \App\Core\Csrf::generateToken() ?>' })
    });
    const data = await res.json();
    if(data.success) window.location.reload();
}

function openMurciaModal(type, extra = '') {
    const modal = document.getElementById('modal-murcia');
    const content = document.getElementById('modal-murcia-content');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    let html = '';
    if(type === 'designacion') {
        html = `
            <div class="p-10 space-y-6">
                <h3 class="text-xl font-black">Designación de Equipo</h3>
                <div class="space-y-4">
                    <label class="text-xs font-bold text-slate-400 uppercase">Coordinador (Jefe de Estudios)</label>
                    <select id="coordinator_id" class="w-full bg-slate-50 border-0 rounded-2xl p-4">
                        <?php foreach($staff as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button onclick="saveDesignation()" class="w-full py-5 bg-amber-500 text-white rounded-full font-black">Guardar Designación</button>
            </div>
        `;
    } else if(type === 'entrevista') {
        html = `
            <div class="p-10 space-y-6">
                <h3 class="text-xl font-black uppercase tracking-tight">Registro Entrevista: ${extra}</h3>
                <textarea id="int_notes" class="w-full bg-slate-50 border-0 rounded-3xl p-6 text-sm" rows="6" placeholder="Notas detalladas de la sesión..."></textarea>
                <button onclick="saveInterview('${extra}')" class="w-full py-5 bg-amber-500 text-white rounded-full font-black shadow-lg">Guardar Registro</button>
            </div>
        `;
    }
    
    content.innerHTML = html + `<button onclick="closeMurciaModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-800"><span class="material-symbols-outlined">close</span></button>`;
}

function closeMurciaModal() {
    document.getElementById('modal-murcia').classList.add('hidden');
    document.getElementById('modal-murcia').classList.remove('flex');
}

async function saveDesignation() {
    const coordId = document.getElementById('coordinator_id').value;
    const res = await fetch(`/api/protocol/murcia/designation/<?= $case['id'] ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `coordinator_id=${coordId}&csrf_token=<?= \App\Core\Csrf::generateToken() ?>`
    });
    const data = await res.json();
    if(data.success) window.location.reload();
}

async function saveInterview(type) {
    const notes = document.getElementById('int_notes').value;
    const res = await fetch(`/api/protocol/murcia/interview/<?= $case['id'] ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `type=${type}&notes=${encodeURIComponent(notes)}&csrf_token=<?= \App\Core\Csrf::generateToken() ?>`
    });
    const data = await res.json();
    if(data.success) {
        alert('Entrevista registrada correctamente.');
        closeMurciaModal();
        window.location.reload();
    }
}

async function submitAnexoI() {
    if(!confirm('¿Confirmas el envío del Anexo I a Inspección?')) return;
    const res = await fetch(`/api/protocol/murcia/anexo-i/<?= $case['id'] ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `csrf_token=<?= \App\Core\Csrf::generateToken() ?>`
    });
    const data = await res.json();
    if(data.success) alert('Anexo I enviado y registrado.');
}

async function submitAnexoIV() {
    const content = document.getElementById('informe-content').value;
    if(!content) return alert('Debes redactar el contenido del informe.');
    const res = await fetch(`/api/protocol/murcia/anexo-iv/<?= $case['id'] ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `content=${encodeURIComponent(content)}&csrf_token=<?= \App\Core\Csrf::generateToken() ?>`
    });
    const data = await res.json();
    if(data.success) window.location.reload();
}

async function submitValuation() {
    const formData = new FormData(document.getElementById('form-valuation'));
    formData.append('csrf_token', '<?= \App\Core\Csrf::generateToken() ?>');
    const res = await fetch(`/api/protocol/murcia/valuation/<?= $case['id'] ?>`, {
        method: 'POST',
        body: formData
    });
    const data = await res.json();
    if(data.success) {
        alert('Valoración registrada. Pasando a actuaciones posteriores.');
        nextStep('<?= ProtocolCase::PHASE_MUR_CIERRE ?>');
    }
}
</script>

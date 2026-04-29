<?php
use App\Models\AragonProtocolCase;
$startDate = $case['created_at'];
?>
<div class="space-y-8 animate-[fadeIn_0.4s_ease-out]">
    <header class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-100 ambient-shadow">
        <div class="flex items-center gap-4">
            <div><h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Expediente #<?= $case['id'] ?></h1></div>
        </div>
        <div class="flex flex-col items-end">
            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-primary text-white shadow-sm"><?= strtoupper($case['status']) ?></span>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <?php if ($case['status'] === AragonProtocolCase::STATE_COMUNICACION_RECIBIDA): ?>
            <section id="panel-decision" class="bg-white rounded-3xl border-2 border-primary/20 p-8 space-y-6">
                <form id="form-decision" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" onclick="setDecision('iniciar')" id="btn-iniciar" class="p-6 rounded-2xl border-2 border-slate-100 hover:border-primary transition-all text-center group"><p class="font-black text-sm text-slate-600">INICIAR PROTOCOLO</p></button>
                        <button type="button" onclick="setDecision('no_iniciar')" id="btn-no-iniciar" class="p-6 rounded-2xl border-2 border-slate-100 hover:border-red-400 transition-all text-center group"><p class="font-black text-sm text-slate-600">NO INICIAR</p></button>
                    </div>
                    <div id="section-iniciar" class="hidden space-y-6 animate-[fadeIn_0.3s]">
                        <div class="bg-slate-50 p-6 rounded-2xl space-y-4">
                            <p class="text-xs font-black uppercase text-slate-400 tracking-widest">Medidas de Protección Inmediatas (Anexo II)</p>
                            <label><input type="checkbox" name="measures[]" value="vigilancia_recreos" class="rounded text-primary"> Vigilancia en recreos</label>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end"><button type="button" onclick="submitDecision()" class="bg-primary text-white px-10 py-4 rounded-full font-black shadow-lg">Guardar y Continuar</button></div>
                </form>
            </section>
            <?php endif; ?>
            
            <?php if ($case['status'] === AragonProtocolCase::STATE_PROTOCOLO_INICIADO): ?>
            <section id="panel-team" class="bg-white rounded-3xl border-2 border-primary/20 p-8 space-y-6">
                <form id="form-team" class="space-y-4">
                    <label class="font-bold text-sm">Selecciona los miembros del equipo:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <?php foreach($staff as $member): ?>
                        <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                            <input type="checkbox" name="team_ids[]" value="<?= $member['id'] ?>" class="rounded text-primary">
                            <span class="text-xs font-medium"><?= htmlspecialchars($member['name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" onclick="submitTeam()" class="w-full mt-4 bg-primary text-white py-4 rounded-full font-black uppercase tracking-widest">Activar Equipo</button>
                </form>
            </section>
            <?php endif; ?>

            <?php if ($case['status'] === AragonProtocolCase::STATE_EN_VALORACION): ?>
            <div class="mt-12 p-8 bg-slate-900 rounded-[3rem] text-white shadow-2xl space-y-8 animate-[fadeIn_0.5s]">
                <div class="flex items-center justify-between border-b border-white/10 pb-6"><h3 class="text-2xl font-black tracking-tight">Resolución del Expediente</h3></div>
                <form id="form-resolution" class="space-y-10">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php foreach(['desigualdad', 'recurrencia', 'intencionalidad'] as $char): ?>
                            <div class="bg-white/5 p-6 rounded-3xl border border-white/10 space-y-4">
                                <p class="text-sm font-bold capitalize"><?= $char ?></p>
                                <label><input type="radio" name="char_<?= $char ?>" value="si_acreditada" onchange="checkLogic()" class="text-primary"> Sí</label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <select name="conclusion" id="conclusion-select" onchange="checkLogic()" class="w-full bg-white/5 border-none rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-primary/40"><option value="no_acreditado">No acreditado como acoso escolar</option><option value="acoso_confirmado">ACOSO ESCOLAR CONFIRMADO</option></select>
                        <div id="logic-warning" class="hidden p-4 bg-amber-500/10 border border-amber-500/50 rounded-2xl flex items-center gap-3"><p class="text-xs text-amber-200">Sugerencia: Para confirmar acoso, las 3 características deberían estar acreditadas.</p></div>
                    </div>
                    <div class="pt-8 border-t border-white/10 flex justify-end gap-6 items-center"><button type="button" onclick="submitResolution()" class="bg-primary text-white px-10 py-5 rounded-full font-black shadow-xl hover:bg-primary/80 hover:scale-105 active:scale-95 transition-all uppercase tracking-widest text-xs flex items-center gap-2">Firmar y Generar Informe Oficial</button></div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
let currentDecision = null;
function setDecision(val) {
    currentDecision = val;
    document.getElementById('section-iniciar').classList.toggle('hidden', val !== 'iniciar');
    document.getElementById('btn-iniciar').classList.toggle('border-primary', val === 'iniciar');
    document.getElementById('btn-no-iniciar').classList.toggle('border-red-400', val === 'no_iniciar');
}
async function submitDecision() {
    if(!currentDecision) return alert('Debes seleccionar una opción.');
    const formData = new FormData(document.getElementById('form-decision'));
    formData.append('decision', currentDecision);
    formData.append('csrf_token', '<?= \App\Core\Csrf::generateToken() ?>');
    const res = await fetch('/api/protocol/aragon/decision/<?= $case['id'] ?>', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.success) window.location.reload();
}
async function submitTeam() {
    const formData = new FormData(document.getElementById('form-team'));
    formData.append('csrf_token', '<?= \App\Core\Csrf::generateToken() ?>');
    const res = await fetch('/api/protocol/aragon/constitute-team/<?= $case['id'] ?>', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.success) window.location.reload();
}
function checkLogic() {
    const chars = ['char_desigualdad', 'char_recurrencia', 'char_intencionalidad'];
    const allAcredited = chars.every(name => {
        const selected = document.querySelector(`input[name="${name}"]:checked`);
        return selected && selected.value === 'si_acreditada';
    });
    const warning = document.getElementById('logic-warning');
    const select = document.getElementById('conclusion-select');
    if (!allAcredited && select.value === 'acoso_confirmado') { warning.classList.remove('hidden'); } else { warning.classList.add('hidden'); }
}
async function submitResolution() {
    if (!confirm('¿Estás seguro de emitir esta resolución? Esta acción es irreversible.')) return;
    const formData = new FormData(document.getElementById('form-resolution'));
    formData.append('csrf_token', '<?= \App\Core\Csrf::generateToken() ?>');
    const res = await fetch('/api/protocol/aragon/resolution/<?= $case['id'] ?>', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) { window.location.reload(); } else { alert('Error: ' + data.error); }
}
</script>

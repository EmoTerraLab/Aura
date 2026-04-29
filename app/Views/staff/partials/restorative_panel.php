<div id="restorative-module" class="hidden mt-8 space-y-6 animate-[fadeIn_0.3s_ease-out]">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">groups</span>
            <?= \App\Core\Lang::t('protocol.restorative_title') ?>
        </h3>
        <span class="text-[10px] font-bold text-slate-400 max-w-[200px] text-right leading-tight italic">
            <?= \App\Core\Lang::t('protocol.mediation_warning') ?>
        </span>
    </div>

    <!-- Acknowledgement Section -->
    <div id="ack-section" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
        <p class="text-sm font-bold text-slate-700"><?= \App\Core\Lang::t('protocol.acknowledge_question') ?></p>
        <div class="flex gap-4">
            <button onclick="saveAck(1)" id="btn-ack-yes" class="flex-1 py-3 px-4 rounded-xl border-2 transition-all font-bold text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                <?= \App\Core\Lang::t('protocol.acknowledge_yes') ?>
            </button>
            <button onclick="saveAck(0)" id="btn-ack-no" class="flex-1 py-3 px-4 rounded-xl border-2 transition-all font-bold text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">cancel</span>
                <?= \App\Core\Lang::t('protocol.acknowledge_no') ?>
            </button>
        </div>
        <p id="ack-warning" class="hidden text-[11px] text-error font-medium p-3 bg-error/5 rounded-lg border border-error/10">
            <?= \App\Core\Lang::t('protocol.acknowledge_warning') ?>
        </p>
    </div>

    <!-- Restorative Practices List -->
    <div id="practices-container" class="hidden space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-black uppercase text-slate-400 tracking-widest">Sessions i Acords</h4>
            <button onclick="showAddPracticeModal()" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Nova Sessió
            </button>
        </div>

        <div id="practices-list" class="space-y-3">
            <!-- Se llena vía AJAX -->
        </div>
    </div>
</div>

<!-- Modal Simple para añadir práctica -->
<div id="modal-add-practice" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl space-y-6">
        <h3 class="text-xl font-bold text-slate-800">Programar Pràctica Restaurativa</h3>
        
        <div class="space-y-4">
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Tipus</label>
                <select id="new-practice-type" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20">
                    <option value="conversa_restaurativa"><?= \App\Core\Lang::t('protocol.practice_conversa') ?></option>
                    <option value="reunio_restaurativa"><?= \App\Core\Lang::t('protocol.practice_reunio') ?></option>
                    <option value="cercle_de_grup"><?= \App\Core\Lang::t('protocol.practice_cercle') ?></option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Data</label>
                <input type="date" id="new-practice-date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Participants</label>
                <input type="text" id="new-practice-participants" placeholder="Ej: Tutor, Alumne A, Alumne B" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase">Acords de Reparació Inicials</label>
                <textarea id="new-practice-agreements" rows="3" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20"></textarea>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button onclick="closeAddPracticeModal()" class="flex-1 py-3 text-slate-500 font-bold text-sm">Cancel·lar</button>
            <button onclick="submitPractice()" class="flex-1 py-3 bg-primary text-white rounded-full font-bold text-sm shadow-lg">Guardar Sessió</button>
        </div>
    </div>
</div>

<script>
    async function loadRestorativeModule(caseId) {
        if (!caseId) return;
        try {
            const res = await fetchJson(`/api/protocol/case/${caseId}/restorative`);
            if (!res.success) return;

            updateAckUI(res.acknowledged);
            renderPractices(res.practices);
        } catch (e) { console.error(e); }
    }

    function updateAckUI(ack) {
        const btnYes = document.getElementById('btn-ack-yes');
        const btnNo = document.getElementById('btn-ack-no');
        const warning = document.getElementById('ack-warning');
        const practices = document.getElementById('practices-container');

        // Reset
        btnYes.className = btnYes.className.replace(/border-primary|bg-primary\/10|text-primary|border-slate-100/g, '').trim() + ' border-slate-100 text-slate-400';
        btnNo.className = btnNo.className.replace(/border-error|bg-error\/5|text-error|border-slate-100/g, '').trim() + ' border-slate-100 text-slate-400';
        
        if (ack === 1) {
            btnYes.classList.replace('border-slate-100', 'border-primary');
            btnYes.classList.replace('text-slate-400', 'text-primary');
            btnYes.classList.add('bg-primary/10');
            warning.classList.add('hidden');
            practices.classList.remove('hidden');
        } else if (ack === 0) {
            btnNo.classList.replace('border-slate-100', 'border-error');
            btnNo.classList.replace('text-slate-400', 'text-error');
            btnNo.classList.add('bg-error/5');
            warning.classList.remove('hidden');
            practices.classList.add('hidden');
        } else {
            practices.classList.add('hidden');
            warning.classList.add('hidden');
        }
    }

    async function saveAck(value) {
        const res = await fetchJson(`/api/protocol/case/${currentCaseId}/acknowledgment`, {
            method: 'POST',
            body: { acknowledged: value }
        });
        if (res.success) updateAckUI(value);
    }

    function renderPractices(list) {
        const listDiv = document.getElementById('practices-list');
        if (!list || list.length === 0) {
            listDiv.innerHTML = '<p class="text-center text-slate-400 py-6 text-xs italic">No s’han registrat pràctiques restauratives encara.</p>';
            return;
        }

        listDiv.innerHTML = list.map(p => `
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[9px] font-black uppercase text-primary tracking-tighter">${p.practice_type.replace(/_/g, ' ')}</span>
                        <p class="text-xs font-bold text-slate-700">${new Date(p.session_date).toLocaleDateString()}</p>
                    </div>
                    <select onchange="updatePracticeStatus(${p.id}, this.value)" class="text-[10px] font-bold border-none bg-white rounded-full py-1 px-3 shadow-sm">
                        <option value="pending" ${p.status==='pending'?'selected':''}>Pendent</option>
                        <option value="completed" ${p.status==='completed'?'selected':''}>Completat</option>
                        <option value="failed" ${p.status==='failed'?'selected':''}>Incomplert</option>
                    </select>
                </div>
                <p class="text-[11px] text-slate-600"><strong>Participants:</strong> ${p.participants}</p>
                <div class="p-3 bg-white rounded-xl border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 mb-1 uppercase tracking-widest">Acords</p>
                    <p class="text-xs text-slate-800">${p.agreements}</p>
                </div>
                <p class="text-[9px] text-slate-400">Facilitat per: ${p.facilitator_name}</p>
            </div>
        `).join('');
    }

    async function updatePracticeStatus(id, status) {
        const res = await fetchJson(`/api/restorative/${id}/status`, {
            method: 'PATCH',
            body: { status }
        });
        if (res.success) {
            // No recargar todo, solo feedback visual o recarga silenciosa
            console.log('Status updated');
        }
    }

    function showAddPracticeModal() { document.getElementById('modal-add-practice').classList.remove('hidden'); }
    function closeAddPracticeModal() { document.getElementById('modal-add-practice').classList.add('hidden'); }

    async function submitPractice() {
        const data = {
            practice_type: document.getElementById('new-practice-type').value,
            session_date: document.getElementById('new-practice-date').value,
            participants: document.getElementById('new-practice-participants').value,
            agreements: document.getElementById('new-practice-agreements').value
        };

        const res = await fetchJson(`/api/protocol/case/${currentCaseId}/restorative/add`, {
            method: 'POST',
            body: data
        });

        if (res.success) {
            closeAddPracticeModal();
            loadRestorativeModule(currentCaseId);
        } else {
            alert(res.error || 'Error al guardar');
        }
    }
</script>

<div id="restorative-module" class="hidden mt-6 space-y-4 animate-fadeIn font-display">
    <div class="flex items-center justify-between">
        <h3 class="text-sm md:text-base font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-lg">groups</span>
            <?= \App\Core\Lang::t('protocol.restorative_title') ?>
        </h3>
        <span class="text-[10px] font-semibold text-on-surface-variant max-w-[220px] text-right leading-tight italic">
            <?= \App\Core\Lang::t('protocol.mediation_warning') ?>
        </span>
    </div>

    <!-- Acknowledgement Section -->
    <div id="ack-section" class="bg-surface-container-lowest p-4 md:p-5 rounded-2xl border border-surface-variant/40 shadow-card space-y-3">
        <p class="text-xs font-bold text-on-surface"><?= \App\Core\Lang::t('protocol.acknowledge_question') ?></p>
        <div class="flex gap-3">
            <button onclick="saveAck(1)" id="btn-ack-yes" class="flex-1 h-11 rounded-xl border border-surface-variant/40 transition-all font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-base">check_circle</span>
                <?= \App\Core\Lang::t('protocol.acknowledge_yes') ?>
            </button>
            <button onclick="saveAck(0)" id="btn-ack-no" class="flex-1 h-11 rounded-xl border border-surface-variant/40 transition-all font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-base">cancel</span>
                <?= \App\Core\Lang::t('protocol.acknowledge_no') ?>
            </button>
        </div>
        <p id="ack-warning" class="hidden text-[11px] text-error font-medium p-2.5 bg-error/10 rounded-xl border border-error/20">
            <?= \App\Core\Lang::t('protocol.acknowledge_warning') ?>
        </p>
    </div>

    <!-- Restorative Practices List -->
    <div id="practices-container" class="hidden space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Sesiones y Acuerdos</h4>
            <button onclick="showAddPracticeModal()" class="text-xs font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Nueva Sesión
            </button>
        </div>

        <div id="practices-list" class="space-y-2.5 font-body-md">
            <!-- Se llena vía AJAX -->
        </div>
    </div>
</div>

<!-- Modal Simple para añadir práctica -->
<div id="modal-add-practice" class="hidden fixed inset-0 z-[100] bg-primary/40 backdrop-blur-xs flex items-center justify-center p-4 font-display">
    <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 max-w-md w-full shadow-card border border-surface-variant/40 space-y-4">
        <h3 class="text-base md:text-lg font-bold text-on-surface">Programar Práctica Restaurativa</h3>
        
        <div class="space-y-3 font-body-md">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Tipo</label>
                <select id="new-practice-type" class="w-full h-10 bg-surface-container-low border border-surface-variant/40 rounded-xl px-3 text-xs text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 font-display">
                    <option value="conversa_restaurativa"><?= \App\Core\Lang::t('protocol.practice_conversa') ?></option>
                    <option value="reunio_restaurativa"><?= \App\Core\Lang::t('protocol.practice_reunio') ?></option>
                    <option value="cercle_de_grup"><?= \App\Core\Lang::t('protocol.practice_cercle') ?></option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Fecha</label>
                <input type="date" id="new-practice-date" value="<?= date('Y-m-d') ?>" class="w-full h-10 bg-surface-container-low border border-surface-variant/40 rounded-xl px-3 text-xs text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Participantes</label>
                <input type="text" id="new-practice-participants" placeholder="Ej: Tutor, Alumno A, Alumno B" class="w-full h-10 bg-surface-container-low border border-surface-variant/40 rounded-xl px-3 text-xs text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Acuerdos de Reparación Iniciales</label>
                <textarea id="new-practice-agreements" rows="3" class="w-full bg-surface-container-low border border-surface-variant/40 rounded-xl p-3 text-xs text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 resize-none"></textarea>
            </div>
        </div>

        <div class="flex gap-2.5 pt-2">
            <button onclick="closeAddPracticeModal()" class="flex-1 h-10 text-on-surface-variant hover:text-on-surface font-bold text-xs rounded-xl cursor-pointer">Cancelar</button>
            <button onclick="submitPractice()" class="flex-1 h-10 bg-primary text-on-primary rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer">Guardar Sesión</button>
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

        btnYes.classList.remove('border-primary', 'bg-primary/10', 'text-primary');
        btnYes.classList.add('border-surface-variant/40', 'text-on-surface-variant');
        btnNo.classList.remove('border-error', 'bg-error/10', 'text-error');
        btnNo.classList.add('border-surface-variant/40', 'text-on-surface-variant');
        
        if (ack === 1) {
            btnYes.classList.remove('border-surface-variant/40', 'text-on-surface-variant');
            btnYes.classList.add('border-teal-600', 'text-teal-900', 'bg-prisma-mint/20');
            warning.classList.add('hidden');
            practices.classList.remove('hidden');
        } else if (ack === 0) {
            btnNo.classList.remove('border-surface-variant/40', 'text-on-surface-variant');
            btnNo.classList.add('border-error', 'text-error', 'bg-error/10');
            warning.classList.remove('hidden');
            practices.classList.add('hidden');
        } else {
            practices.classList.add('hidden');
            warning.classList.add('hidden');
        }
    }

    async function saveAck(value) {
        try {
            const res = await fetchJson(`/api/protocol/case/${currentCaseId}/acknowledgment`, {
                method: 'POST',
                body: { acknowledged: value }
            });
            if (res.success) {
                updateAckUI(value);
            } else {
                alert(res.error || 'Error al guardar');
            }
        } catch (e) {
            console.error(e);
            alert('Error de conexión');
        }
    }

    function renderPractices(list) {
        const listDiv = document.getElementById('practices-list');
        if (!list || list.length === 0) {
            listDiv.innerHTML = '<p class="text-center text-on-surface-variant py-4 text-xs italic">No se han registrado sesiones restaurativas todavía.</p>';
            return;
        }

        listDiv.innerHTML = list.map(p => `
            <div class="bg-surface-container-low border border-surface-variant/30 p-3.5 rounded-xl space-y-2 font-display">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[9px] font-bold uppercase text-primary tracking-wider">${p.practice_type.replace(/_/g, ' ')}</span>
                        <p class="text-xs font-semibold text-on-surface">${new Date(p.session_date).toLocaleDateString()}</p>
                    </div>
                    <select onchange="updatePracticeStatus(${p.id}, this.value)" class="text-[10px] font-bold bg-surface-container-lowest rounded-lg py-1 px-2 border border-surface-variant/40 outline-none">
                        <option value="pending" ${p.status==='pending'?'selected':''}>Pendiente</option>
                        <option value="completed" ${p.status==='completed'?'selected':''}>Completado</option>
                        <option value="failed" ${p.status==='failed'?'selected':''}>Incumplido</option>
                    </select>
                </div>
                <p class="text-xs text-on-surface-variant font-body-md"><strong class="font-display">Participantes:</strong> ${p.participants}</p>
                <div class="p-2.5 bg-surface-container-lowest rounded-lg border border-surface-variant/30 font-body-md">
                    <p class="text-[10px] font-bold text-on-surface-variant mb-0.5 uppercase tracking-wider font-display">Acuerdos</p>
                    <p class="text-xs text-on-surface leading-relaxed">${p.agreements}</p>
                </div>
                <p class="text-[9px] text-on-surface-variant/70">Facilitado por: ${p.facilitator_name}</p>
            </div>
        `).join('');
    }

    async function updatePracticeStatus(id, status) {
        await fetchJson(`/api/restorative/${id}/status`, {
            method: 'PATCH',
            body: { status }
        });
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

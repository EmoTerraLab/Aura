<?php
// Vista para la herramienta exclusiva: Medidas Urxentes (Galicia)
?>
<div class="p-8 space-y-6 animate-[fadeIn_0.3s_ease-out]">
    <div class="flex items-center gap-4 border-b border-rose-100 pb-4">
        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-600">
            <span class="material-symbols-outlined text-2xl">shield_with_heart</span>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">Medidas Urxentes de Protección</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Protocolo Galicia - Ferramenta Exclusiva</p>
        </div>
    </div>

    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
        As medidas urxentes adoptaranse de forma inmediata para garantir a seguridade da posible vítima e deter o maltrato, antes mesmo de rematar a avaliación do caso.
    </div>

    <form id="form-medidas-urxentes" class="space-y-6">
        
        <!-- Tipos de medidas -->
        <div class="space-y-4">
            <label class="block text-xs font-black uppercase text-slate-500 tracking-widest">Accións Preventivas Aplicadas</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-start gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-rose-300 bg-slate-50 transition-all has-[:checked]:bg-rose-50 has-[:checked]:border-rose-500">
                    <input type="checkbox" name="measures[]" value="vixilancia_recreos" class="mt-1 w-5 h-5 text-rose-600 border-rose-300 rounded focus:ring-rose-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-700">Vixilancia específica (Recreos)</span>
                        <span class="block text-xs text-slate-500 mt-1">Aumento da atención nas zonas non estruturadas.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-rose-300 bg-slate-50 transition-all has-[:checked]:bg-rose-50 has-[:checked]:border-rose-500">
                    <input type="checkbox" name="measures[]" value="vixilancia_comedor" class="mt-1 w-5 h-5 text-rose-600 border-rose-300 rounded focus:ring-rose-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-700">Vixilancia (Comedor/Transporte)</span>
                        <span class="block text-xs text-slate-500 mt-1">Supervisión en servizos complementarios.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-rose-300 bg-slate-50 transition-all has-[:checked]:bg-rose-50 has-[:checked]:border-rose-500">
                    <input type="checkbox" name="measures[]" value="separacion_espazos" class="mt-1 w-5 h-5 text-rose-600 border-rose-300 rounded focus:ring-rose-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-700">Separación preventiva</span>
                        <span class="block text-xs text-slate-500 mt-1">Distanciar nos espazos aula/recreo.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-rose-300 bg-slate-50 transition-all has-[:checked]:bg-rose-50 has-[:checked]:border-rose-500">
                    <input type="checkbox" name="measures[]" value="cambio_grupo" class="mt-1 w-5 h-5 text-rose-600 border-rose-300 rounded focus:ring-rose-500">
                    <div>
                        <span class="block text-sm font-bold text-slate-700">Cambio de grupo cautelar</span>
                        <span class="block text-xs text-slate-500 mt-1">Medida excepcional por seguridade inminente.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Descrición detallada -->
        <div class="space-y-3">
            <label class="block text-xs font-black uppercase text-slate-500 tracking-widest">Detalle da aplicación e profesorado encargado</label>
            <textarea name="measures_details" rows="4" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-medium text-slate-700 focus:ring-4 focus:ring-rose-500/10 placeholder:text-slate-400" placeholder="Ex: O profesorado de garda fará un seguimento na zona oeste do patio..."></textarea>
        </div>

        <!-- Comunicación Familias -->
        <div class="space-y-3">
            <label class="flex items-center gap-3 p-4 bg-slate-50 border-2 border-slate-100 rounded-xl cursor-pointer">
                <input type="checkbox" name="familias_informadas" value="1" class="w-5 h-5 text-indigo-600 border-indigo-300 rounded focus:ring-indigo-500">
                <span class="text-sm font-bold text-slate-700">As familias foron informadas destas medidas cautelares</span>
            </label>
        </div>

        <!-- Botones -->
        <div class="pt-4 flex justify-end gap-3">
            <button type="button" onclick="closeFollowupModal()" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="button" onclick="submitMedidasUrxentes(<?= htmlspecialchars($caseId ?? 0) ?>)" class="px-8 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black shadow-lg shadow-rose-200 transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">security</span> Activar Medidas
            </button>
        </div>
    </form>
</div>

<script>
async function submitMedidasUrxentes(caseId) {
    const form = document.getElementById('form-medidas-urxentes');
    const formData = new FormData(form);
    
    try {
        const res = await fetch(`/api/protocol/galicia/tools/medidas-urxentes/${caseId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '<?= \App\Core\Csrf::generateToken() ?>'
            }
        });
        
        const data = await res.json();
        if (data.success) {
            closeFollowupModal();
            window.location.reload();
        } else {
            const errDiv = document.createElement('div');
            errDiv.className = 'mt-4 bg-red-100 text-red-700 p-4 rounded-xl text-sm font-bold border border-red-200';
            errDiv.textContent = "Erro ao gardar: " + (data.message || 'Descoñecido');
            form.appendChild(errDiv);
        }
    } catch(e) {
        const errDiv = document.createElement('div');
        errDiv.className = 'mt-4 bg-red-100 text-red-700 p-4 rounded-xl text-sm font-bold border border-red-200';
        errDiv.textContent = "Erro de rede ao enviar as medidas.";
        form.appendChild(errDiv);
    }
}
</script>

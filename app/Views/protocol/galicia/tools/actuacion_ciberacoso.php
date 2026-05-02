<?php
// Vista para la herramienta exclusiva: Actuación Ciberacoso (Galicia)
?>
<div class="p-8 space-y-6 animate-[fadeIn_0.3s_ease-out]">
    <div class="flex items-center gap-4 border-b border-indigo-100 pb-4">
        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-2xl">language</span>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">Rexistro de Ciberacoso</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Protocolo Galicia - Ferramenta Exclusiva</p>
        </div>
    </div>

    <form id="form-ciberacoso" class="space-y-6">
        
        <!-- Plataforma/Medio -->
        <div class="space-y-3">
            <label class="block text-xs font-black uppercase text-slate-500 tracking-widest">Plataforma / Medio Utilizado</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php 
                $platforms = ['WhatsApp / Telegram', 'Instagram', 'TikTok', 'X / Twitter', 'Videoxogos', 'Foros / Outros'];
                foreach($platforms as $idx => $platform): 
                ?>
                <label class="flex items-center justify-center p-3 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-indigo-300 transition-colors bg-slate-50 has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500 has-[:checked]:text-indigo-700">
                    <input type="checkbox" name="platforms[]" value="<?= htmlspecialchars($platform) ?>" class="hidden">
                    <span class="text-xs font-bold text-slate-600"><?= $platform ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tipo de Agresión Digital -->
        <div class="space-y-3">
            <label class="block text-xs font-black uppercase text-slate-500 tracking-widest">Tipo de Agresión Dixital</label>
            <select name="agresion_type" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10">
                <option value="">Selecciona o tipo principal...</option>
                <option value="insultos_amenazas">Insultos ou ameazas directas</option>
                <option value="difusion_imaxes">Difusión de imaxes/vídeos sen consentimento</option>
                <option value="suplantacion">Suplantación de identidade (Perfís falsos)</option>
                <option value="exclusion">Exclusión de grupos ou foros</option>
                <option value="sexting_non_consentido">Sexting non consentido / Sextorsión</option>
            </select>
        </div>

        <!-- Evidencias / Preservación -->
        <div class="space-y-3">
            <label class="block text-xs font-black uppercase text-slate-500 tracking-widest">Preservación de Probas Técnicas</label>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-800 space-y-3">
                <p class="font-bold">Indica as evidencias tecnolóxicas gardadas (Non alterar os orixinais):</p>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="evidences[]" value="capturas" class="w-4 h-4 text-amber-600 border-amber-300 rounded focus:ring-amber-500">
                        <span class="font-medium">Capturas de pantalla (Screenshots)</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="evidences[]" value="urls" class="w-4 h-4 text-amber-600 border-amber-300 rounded focus:ring-amber-500">
                        <span class="font-medium">URLs ou ligazóns orixinais aos perfís/publicacións</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="evidences[]" value="audios_videos" class="w-4 h-4 text-amber-600 border-amber-300 rounded focus:ring-amber-500">
                        <span class="font-medium">Audios ou vídeos gardados no dispositivo</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Derivación FCSE -->
        <div class="space-y-3">
            <label class="flex items-start gap-3 p-4 border-2 border-red-100 bg-red-50 rounded-2xl cursor-pointer">
                <input type="checkbox" name="derivacion_fcse" value="1" class="mt-1 w-5 h-5 text-red-600 border-red-300 rounded focus:ring-red-500">
                <div>
                    <span class="block text-sm font-black text-red-700">Requirir derivación a FCSE / Fiscalía</span>
                    <span class="block text-xs text-red-600/80 mt-1">Marcar se a gravidade do ciberacoso (ex. sextorsión, difusión masiva) require intervención policial inmediata.</span>
                </div>
            </label>
        </div>

        <div class="space-y-3">
            <label class="block text-xs font-black uppercase text-slate-500 tracking-widest">Notas Adicionais (IPs, Identificadores...)</label>
            <textarea name="technical_notes" rows="4" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm focus:ring-4 focus:ring-indigo-500/10 placeholder:text-slate-400" placeholder="Anota calquera dato técnico relevante para a investigación..."></textarea>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <button type="button" onclick="closeFollowupModal()" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="button" onclick="submitCiberacoso(<?= htmlspecialchars($caseId ?? 0) ?>)" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span> Gardar Rexistro
            </button>
        </div>
    </form>
</div>

<script>
async function submitCiberacoso(caseId) {
    const form = document.getElementById('form-ciberacoso');
    const formData = new FormData(form);
    
    // Aquí implementas a túa chamada fetch para gardar o formulario no backend Aura
    try {
        const res = await fetch(`/api/protocol/galicia/tools/ciberacoso/${caseId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '<?= \App\Core\Csrf::generateToken() ?>' // Asumindo o método usado no ecosistema Aura
            }
        });
        
        const data = await res.json();
        if (data.success) {
            closeFollowupModal();
            window.location.reload(); // Recargar para ver as ferramentas aplicadas
        } else {
            const errDiv = document.createElement('div');
            errDiv.className = 'mt-4 bg-red-100 text-red-700 p-4 rounded-xl text-sm font-bold border border-red-200';
            errDiv.textContent = "Erro ao gardar: " + (data.message || 'Descoñecido');
            form.appendChild(errDiv);
        }
    } catch(e) {
        const errDiv = document.createElement('div');
        errDiv.className = 'mt-4 bg-red-100 text-red-700 p-4 rounded-xl text-sm font-bold border border-red-200';
        errDiv.textContent = "Erro de rede ao enviar o formulario.";
        form.appendChild(errDiv);
    }
}
</script>

<div class="max-w-4xl mx-auto space-y-6 animate-[fadeIn_0.4s_ease-out]">
    <div id="emergency-alert" class="hidden bg-red-600 text-white p-8 rounded-3xl shadow-2xl space-y-4 border-4 border-red-400 animate-pulse">
        <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-5xl">emergency_home</span>
            <div><h2 class="text-2xl font-black uppercase tracking-tight">Alerta de Emergencia: Violencia Sexual</h2></div>
        </div>
    </div>
    <div class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm relative overflow-hidden">
        <form action="/protocol/aragon/anexo-1a" method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2"><label class="font-bold text-sm ml-2 text-slate-700">Rol comunicante</label><select name="reporter_role" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20 transition-all"><option value="alumno">Alumnado</option></select></div>
            </div>
            <div class="p-6 bg-red-50 rounded-3xl border-2 border-red-100 border-dashed transition-all hover:bg-red-100/50">
                <label class="flex items-center gap-4 cursor-pointer select-none">
                    <input type="checkbox" name="is_sexual_violence" id="check-sexual-violence" class="w-8 h-8 rounded-lg text-red-600 border-red-200 focus:ring-red-500/20">
                    <div><span class="font-black text-red-800 uppercase tracking-tight italic">Posibles indicadores de Violencia Sexual</span></div>
                </label>
            </div>
            <div class="pt-6 border-t border-slate-100 flex justify-end gap-4"><button type="submit" class="bg-primary text-white px-10 py-4 rounded-full font-black shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 uppercase tracking-wide">Registrar y Activar Protocolo</button></div>
        </form>
    </div>
</div>
<script>document.getElementById('check-sexual-violence').addEventListener('change', function(e) { document.getElementById('emergency-alert').classList.toggle('hidden', !this.checked); });</script>

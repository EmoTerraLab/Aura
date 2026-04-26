<h2 class="text-xl font-bold text-on-surface mb-6">Apariencia</h2>
<form method="POST" action="/admin/settings/appearance" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Color Primario</label>
            <div class="flex items-center gap-3">
                <input type="color" id="primaryColorPicker" name="app_primary_color" value="<?= htmlspecialchars($settings['app_primary_color'] ?? '#004f56') ?>" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                <input type="text" id="primaryColorText" value="<?= htmlspecialchars($settings['app_primary_color'] ?? '#004f56') ?>" class="flex-1 bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none uppercase font-mono">
            </div>
            <p class="text-[10px] text-slate-400">Color principal de la aplicación (botones, menús, destacables).</p>
        </div>
        
        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Color de Acento (Secundario)</label>
            <div class="flex items-center gap-3">
                <input type="color" id="accentColorPicker" name="app_accent_color" value="<?= htmlspecialchars($settings['app_accent_color'] ?? '#066972') ?>" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                <input type="text" id="accentColorText" value="<?= htmlspecialchars($settings['app_accent_color'] ?? '#066972') ?>" class="flex-1 bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-secondary/20 outline-none uppercase font-mono">
            </div>
            <p class="text-[10px] text-slate-400">Color secundario (iconos, etiquetas secundarias).</p>
        </div>

        <div class="space-y-2 md:col-span-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Texto del Footer</label>
            <input type="text" name="footer_text" value="<?= htmlspecialchars($settings['footer_text'] ?? '') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none" placeholder="Aura powered by EmoTerraLab">
        </div>
    </div>

    <!-- Preview Box -->
    <div class="mt-8 p-6 rounded-2xl border border-surface-variant/50 bg-slate-50">
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Vista Previa</h3>
        <div class="flex gap-4 items-center">
            <button type="button" id="previewPrimaryBtn" class="px-6 py-2 rounded-full font-bold text-white shadow-lg transition-transform" style="background-color: <?= htmlspecialchars($settings['app_primary_color'] ?? '#004f56') ?>">Botón Primario</button>
            <div id="previewAccentIcon" class="w-10 h-10 rounded-full flex items-center justify-center text-white shadow-md" style="background-color: <?= htmlspecialchars($settings['app_accent_color'] ?? '#066972') ?>">
                <span class="material-symbols-outlined">spa</span>
            </div>
        </div>
    </div>

    <div class="pt-4 border-t border-surface-variant flex justify-end">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Guardar Cambios</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const primPick = document.getElementById('primaryColorPicker');
        const primText = document.getElementById('primaryColorText');
        const accPick = document.getElementById('accentColorPicker');
        const accText = document.getElementById('accentColorText');
        const prevPrim = document.getElementById('previewPrimaryBtn');
        const prevAcc = document.getElementById('previewAccentIcon');

        function updatePrim(val) {
            primPick.value = val; primText.value = val; prevPrim.style.backgroundColor = val;
        }
        function updateAcc(val) {
            accPick.value = val; accText.value = val; prevAcc.style.backgroundColor = val;
        }

        primPick.addEventListener('input', e => updatePrim(e.target.value));
        primText.addEventListener('input', e => updatePrim(e.target.value));
        accPick.addEventListener('input', e => updateAcc(e.target.value));
        accText.addEventListener('input', e => updateAcc(e.target.value));
    });
</script>

<h2 class="text-base md:text-lg font-bold text-on-surface mb-5 font-display">Apariencia</h2>
<form method="POST" action="/admin/settings/appearance" class="space-y-5 font-display">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-body-md">
        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Color Primario (Charcoal / Base)</label>
            <div class="flex items-center gap-2.5">
                <input type="color" id="primaryColorPicker" name="app_primary_color" value="<?= htmlspecialchars($settings['app_primary_color'] ?? '#1E2433') ?>" class="w-11 h-11 rounded-xl cursor-pointer border border-surface-variant/40 p-1 bg-surface-container-low">
                <input type="text" id="primaryColorText" value="<?= htmlspecialchars($settings['app_primary_color'] ?? '#1E2433') ?>" class="flex-1 h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none uppercase font-mono text-xs">
            </div>
            <p class="text-[10px] text-on-surface-variant">Color principal de la aplicación (#1E2433 para Prisma).</p>
        </div>
        
        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Color de Acento (Mint / Canal)</label>
            <div class="flex items-center gap-2.5">
                <input type="color" id="accentColorPicker" name="app_accent_color" value="<?= htmlspecialchars($settings['app_accent_color'] ?? '#79E0CC') ?>" class="w-11 h-11 rounded-xl cursor-pointer border border-surface-variant/40 p-1 bg-surface-container-low">
                <input type="text" id="accentColorText" value="<?= htmlspecialchars($settings['app_accent_color'] ?? '#79E0CC') ?>" class="flex-1 h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-secondary/20 outline-none uppercase font-mono text-xs">
            </div>
            <p class="text-[10px] text-on-surface-variant">Color secundario (#79E0CC Mint para Prisma).</p>
        </div>

        <div class="space-y-1.5 md:col-span-2">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Texto del Footer</label>
            <input type="text" name="footer_text" value="<?= htmlspecialchars($settings['footer_text'] ?? 'Prisma — Tecnología para cuidar la convivencia escolar') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm" placeholder="Prisma — Tecnología para cuidar la convivencia escolar">
        </div>
    </div>

    <!-- Preview Box -->
    <div class="p-5 rounded-2xl border border-surface-variant/40 bg-surface-container-low">
        <h3 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">Vista Previa</h3>
        <div class="flex gap-3 items-center">
            <button type="button" id="previewPrimaryBtn" class="h-10 px-6 rounded-xl font-bold text-xs text-white shadow-xs transition-transform" style="background-color: <?= htmlspecialchars($settings['app_primary_color'] ?? '#1E2433') ?>">Botón Principal</button>
            <div id="previewAccentIcon" class="w-10 h-10 rounded-xl flex items-center justify-center text-primary font-bold shadow-xs" style="background-color: <?= htmlspecialchars($settings['app_accent_color'] ?? '#79E0CC') ?>">
                <span class="material-symbols-outlined text-xl">spa</span>
            </div>
        </div>
    </div>

    <div class="pt-3 border-t border-surface-variant/30 flex justify-end">
        <button type="submit" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer">Guardar Cambios</button>
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

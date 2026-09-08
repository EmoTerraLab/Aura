<div class="space-y-6 font-display">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">policy</span>
        </div>
        <div>
            <h2 class="text-base md:text-lg font-bold text-on-surface">Protocolo de Actuación Autonómico</h2>
            <p class="text-xs text-on-surface-variant">Configura la normativa de referencia según tu ubicación geográfica.</p>
        </div>
    </div>

    <form action="/admin/settings/ccaa" method="POST" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="font-bold text-xs uppercase tracking-wider text-on-surface-variant ml-1">Comunidad Autónoma</label>
                <select name="ccaa_code" onchange="updatePreview(this.value)" class="w-full h-11 bg-surface-container-low border border-surface-variant/40 rounded-xl px-4 text-xs md:text-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value="">Seleccionar una CCAA...</option>
                    <option value="AND" <?= ($settings['ccaa_code'] ?? '') === 'AND' ? 'selected' : '' ?>>Andalucía</option>
                    <option value="ARA" <?= ($settings['ccaa_code'] ?? '') === 'ARA' ? 'selected' : '' ?>>Aragón</option>
                    <option value="AST" <?= ($settings['ccaa_code'] ?? '') === 'AST' ? 'selected' : '' ?>>Asturias</option>
                    <option value="BAL" <?= ($settings['ccaa_code'] ?? '') === 'BAL' ? 'selected' : '' ?>>Baleares</option>
                    <option value="CAN" <?= ($settings['ccaa_code'] ?? '') === 'CAN' ? 'selected' : '' ?>>Canarias</option>
                    <option value="CNT" <?= ($settings['ccaa_code'] ?? '') === 'CNT' ? 'selected' : '' ?>>Cantabria</option>
                    <option value="CYL" <?= ($settings['ccaa_code'] ?? '') === 'CYL' ? 'selected' : '' ?>>Castilla y León</option>
                    <option value="CLM" <?= ($settings['ccaa_code'] ?? '') === 'CLM' ? 'selected' : '' ?>>Castilla-La Mancha</option>
                    <option value="CAT" <?= ($settings['ccaa_code'] ?? '') === 'CAT' ? 'selected' : '' ?>>Catalunya</option>
                    <option value="VAL" <?= ($settings['ccaa_code'] ?? '') === 'VAL' ? 'selected' : '' ?>>Comunitat Valenciana</option>
                    <option value="EXT" <?= ($settings['ccaa_code'] ?? '') === 'EXT' ? 'selected' : '' ?>>Extremadura</option>
                    <option value="GAL" <?= ($settings['ccaa_code'] ?? '') === 'GAL' ? 'selected' : '' ?>>Galicia</option>
                    <option value="MAD" <?= ($settings['ccaa_code'] ?? '') === 'MAD' ? 'selected' : '' ?>>Madrid</option>
                    <option value="MUR" <?= ($settings['ccaa_code'] ?? '') === 'MUR' ? 'selected' : '' ?>>Murcia</option>
                    <option value="NAV" <?= ($settings['ccaa_code'] ?? '') === 'NAV' ? 'selected' : '' ?>>Navarra</option>
                    <option value="PV" <?= ($settings['ccaa_code'] ?? '') === 'PV' ? 'selected' : '' ?>>País Vasco / Euskadi</option>
                    <option value="RIO" <?= ($settings['ccaa_code'] ?? '') === 'RIO' ? 'selected' : '' ?>>La Rioja</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="font-bold text-xs uppercase tracking-wider text-on-surface-variant ml-1 block">Opciones</label>
                <div class="flex items-center gap-4 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ccaa_protocol_active" value="1" <?= ($settings['ccaa_protocol_active'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 accent-primary rounded">
                        <span class="text-xs font-semibold text-on-surface">Protocolo Activo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ccaa_show_to_students" value="1" <?= ($settings['ccaa_show_to_students'] ?? '1') === '1' ? 'checked' : '' ?> class="w-4 h-4 accent-primary rounded">
                        <span class="text-xs font-semibold text-on-surface">Visible para Alumnos</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Preview Area -->
        <div id="protocol-preview" class="p-5 bg-surface-container-low rounded-2xl border border-surface-variant/40 hidden animate-fadeIn">
            <h4 class="text-[10px] font-bold uppercase text-on-surface-variant mb-3 tracking-wider">Previsualización del Protocolo</h4>
            <div id="preview-content" class="space-y-3 text-xs">
                <!-- Injected via JS -->
            </div>
        </div>

        <div class="pt-4 border-t border-surface-variant/30 flex justify-end">
            <button type="submit" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
    async function updatePreview(code) {
        const previewDiv = document.getElementById('protocol-preview');
        const contentDiv = document.getElementById('preview-content');
        
        if (!code) {
            previewDiv.classList.add('hidden');
            return;
        }

        try {
            previewDiv.classList.remove('hidden');
            contentDiv.innerHTML = `<div class="flex items-center gap-2 text-primary"><span class="material-symbols-outlined animate-spin text-sm">refresh</span> Cargando datos oficiales de la comunidad...</div>`;
            
            setTimeout(() => {
                contentDiv.innerHTML = `
                    <p class="font-bold text-on-surface">Protocolo detectado para ${code}</p>
                    <ul class="list-disc ml-5 text-on-surface-variant space-y-1">
                        <li>Incluye las fases de detección y valoración técnica oficiales.</li>
                        <li>Configura los contactos de Inspección Educativa regional.</li>
                        <li>Adapta el lenguaje y formularios a la normativa autonómica.</li>
                    </ul>
                `;
            }, 300);
        } catch (e) {
            console.error(e);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const initial = document.querySelector('select[name="ccaa_code"]').value;
        if(initial) updatePreview(initial);
    });
</script>

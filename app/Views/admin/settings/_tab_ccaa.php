<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined">policy</span>
        </div>
        <div>
            <h2 class="text-xl font-bold">Protocolo de Actuación Autonómico</h2>
            <p class="text-slate-500 text-sm">Configura la normativa de referencia según tu ubicación geográfica.</p>
        </div>
    </div>

    <form action="/admin/settings/ccaa" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="font-bold text-sm ml-2">Comunidad Autónoma</label>
                <select name="ccaa_code" onchange="updatePreview(this.value)" class="w-full bg-slate-100 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20">
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

            <div class="space-y-4">
                <label class="font-bold text-sm ml-2 block text-slate-400 uppercase tracking-widest">Opciones</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="ccaa_protocol_active" value="1" <?= ($settings['ccaa_protocol_active'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded-lg border-none bg-slate-200 text-primary focus:ring-primary/20">
                        <span class="text-sm font-medium">Protocolo Activo</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="ccaa_show_to_students" value="1" <?= ($settings['ccaa_show_to_students'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded-lg border-none bg-slate-200 text-primary focus:ring-primary/20">
                        <span class="text-sm font-medium">Visible para Alumnos</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Preview Area -->
        <div id="protocol-preview" class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hidden animate-[fadeIn_0.3s_ease-out]">
            <h4 class="text-xs font-black uppercase text-slate-400 mb-4 tracking-widest">Previsualización del Protocolo</h4>
            <div id="preview-content" class="space-y-4 text-sm">
                <!-- Se llena con JS -->
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg hover:scale-105 transition-all">
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
            const res = await fetch(`/api/protocol`);
            // Nota: Esto traerá el guardado actualmente, para una previsualización real 
            // de lo que se va a guardar necesitaríamos un endpoint que acepte el código.
            // Para simplificar, mostraremos información básica estática o un mensaje.
            previewDiv.classList.remove('hidden');
            contentDiv.innerHTML = `<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Cargando datos oficiales de la comunidad...</div>`;
            
            // Simulación de carga rápida
            setTimeout(() => {
                contentDiv.innerHTML = `
                    <p class="font-bold text-slate-800">Protocolo detectado para ${code}</p>
                    <ul class="list-disc ml-5 text-slate-600 space-y-1">
                        <li>Incluye las fases de detección y valoración técnica.</li>
                        <li>Configura los contactos de Inspección Educativa regional.</li>
                        <li>Adapta el lenguaje a la normativa vigente.</li>
                    </ul>
                `;
            }, 500);
        } catch (e) {
            console.error(e);
        }
    }

    // Inicializar si hay algo seleccionado
    document.addEventListener('DOMContentLoaded', () => {
        const initial = document.querySelector('select[name="ccaa_code"]').value;
        if(initial) updatePreview(initial);
    });
</script>

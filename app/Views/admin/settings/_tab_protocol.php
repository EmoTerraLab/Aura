<?php
use App\Core\Lang;
use App\Core\Csrf;
use App\Data\BullyingProtocols;

$ccaaList = [
    '' => '-- Selecciona una Comunidad Autónoma --',
    'AND' => 'Andalucía',
    'ARA' => 'Aragón',
    'AST' => 'Asturias',
    'BAL' => 'Baleares',
    'CAN' => 'Canarias',
    'CNT' => 'Cantabria',
    'CYL' => 'Castilla y León',
    'CLM' => 'Castilla-La Mancha',
    'CAT' => 'Catalunya',
    'VAL' => 'Comunitat Valenciana',
    'EXT' => 'Extremadura',
    'GAL' => 'Galicia',
    'MAD' => 'Madrid',
    'MUR' => 'Murcia',
    'NAV' => 'Navarra',
    'PV'  => 'País Vasco / Euskadi',
    'RIO' => 'La Rioja',
    'CEU' => 'Ceuta',
    'MEL' => 'Melilla'
];

$selectedCcaa = $settings['ccaa_code'] ?? '';
$protocolActive = $settings['ccaa_protocol_active'] ?? '1';
$showToStudents = $settings['ccaa_show_to_students'] ?? '1';

$protocolData = $selectedCcaa ? BullyingProtocols::getByCode($selectedCcaa) : null;
?>

<form action="/admin/settings/protocol" method="POST" class="space-y-6 font-display">
    <input type="hidden" name="csrf_token" value="<?= Csrf::generateToken() ?>">

    <div class="space-y-1">
        <h2 class="text-base md:text-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">gavel</span>
            Protocolo Oficial de Acoso Escolar
        </h2>
        <p class="text-xs text-on-surface-variant">Selecciona el protocolo autonómico que servirá de guía contextual para el personal y el alumnado.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="font-bold text-xs text-on-surface-variant uppercase tracking-wider">Comunidad Autónoma</label>
                <select name="ccaa_code" onchange="updateDynamicPreview(this.value)" class="w-full h-11 bg-surface-container-low border border-surface-variant/40 rounded-xl px-4 text-xs md:text-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    <?php foreach ($ccaaList as $code => $name): ?>
                        <option value="<?= $code ?>" <?= $selectedCcaa === $code ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-on-surface-variant/70 italic">Cada CCAA tiene sus propios tiempos, fases y herramientas oficiales (Séneca, REVA, etc.).</p>
            </div>

            <div class="space-y-3 pt-3 border-t border-surface-variant/30">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="hidden" name="ccaa_protocol_active" value="0">
                    <input type="checkbox" name="ccaa_protocol_active" value="1" <?= $protocolActive === '1' ? 'checked' : '' ?> class="w-4 h-4 accent-primary rounded">
                    <div>
                        <span class="block font-bold text-xs text-on-surface group-hover:text-primary transition-colors">Activar Guía Contextual</span>
                        <span class="block text-[11px] text-on-surface-variant">Muestra información del protocolo en los reportes de riesgo.</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="hidden" name="ccaa_show_to_students" value="0">
                    <input type="checkbox" name="ccaa_show_to_students" value="1" <?= $showToStudents === '1' ? 'checked' : '' ?> class="w-4 h-4 accent-primary rounded">
                    <div>
                        <span class="block font-bold text-xs text-on-surface group-hover:text-primary transition-colors">Visible para Alumnos</span>
                        <span class="block text-[11px] text-on-surface-variant">Permite que los alumnos consulten el protocolo desde su panel.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="bg-surface-container-low rounded-2xl p-5 border border-surface-variant/40 relative overflow-hidden">
            <div class="relative z-10" id="preview-container">
                <h3 class="font-bold text-on-surface-variant uppercase tracking-wider text-[10px] mb-3">Previsualización Dinámica</h3>
                
                <div id="preview-content">
                    <?php if ($protocolData): ?>
                        <div class="space-y-3 animate-fadeIn">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-xs" style="background-color: <?= $protocolData['metadata']['color'] ?>">
                                    <span class="material-symbols-outlined text-xl">assured_workload</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-xs md:text-sm text-on-surface leading-tight truncate"><?= $protocolData['metadata']['name'] ?></h4>
                                    <p class="text-[10px] text-on-surface-variant uppercase font-semibold tracking-tight truncate"><?= $protocolData['metadata']['authority'] ?></p>
                                </div>
                            </div>
                            <div class="p-3.5 bg-surface-container-lowest rounded-xl border border-surface-variant/40 shadow-xs space-y-1.5">
                                <p class="text-xs font-bold text-on-surface"><?= $protocolData['metadata']['document_title'] ?></p>
                                <div class="flex gap-2">
                                    <span class="px-2 py-0.5 bg-surface-container-high rounded text-[10px] font-bold text-on-surface-variant"><?= count($protocolData['phases']) ?> FASES</span>
                                    <span class="px-2 py-0.5 bg-prisma-mint/25 rounded text-[10px] font-bold text-teal-900"><?= $protocolData['metadata']['main_tool'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 text-center animate-fadeIn">
                            <span class="material-symbols-outlined text-outline-variant text-4xl mb-1">map</span>
                            <p class="text-xs text-on-surface-variant italic">Selecciona una CCAA para ver los detalles del protocolo.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-4 border-t border-surface-variant/30 flex justify-end">
        <button type="submit" class="h-11 bg-primary text-on-primary font-bold text-xs py-2 px-6 rounded-xl transition-all shadow-xs hover:bg-primary/90 cursor-pointer">
            Guardar Configuración de Protocolos
        </button>
    </div>
</form>

<script>
async function updateDynamicPreview(code) {
    const content = document.getElementById('preview-content');
    if (!code) {
        content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-8 text-center animate-fadeIn">
                <span class="material-symbols-outlined text-outline-variant text-4xl mb-1">map</span>
                <p class="text-xs text-on-surface-variant italic">Selecciona una CCAA para ver los detalles del protocolo.</p>
            </div>
        `;
        return;
    }

    content.innerHTML = '<div class="flex flex-col items-center justify-center py-8"><span class="material-symbols-outlined animate-spin text-primary text-3xl">refresh</span></div>';

    try {
        const res = await fetch(`/api/protocol-info?code=${code}`);
        const data = await res.json();

        if (data.success && data.protocol) {
            const p = data.protocol;
            content.innerHTML = `
                <div class="space-y-3 animate-fadeIn">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-xs" style="background-color: ${p.metadata.color}">
                            <span class="material-symbols-outlined text-xl">assured_workload</span>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs md:text-sm text-on-surface leading-tight truncate">${p.metadata.name}</h4>
                            <p class="text-[10px] text-on-surface-variant uppercase font-semibold tracking-tight truncate">${p.metadata.authority}</p>
                        </div>
                    </div>
                    <div class="p-3.5 bg-surface-container-lowest rounded-xl border border-surface-variant/40 shadow-xs space-y-1.5">
                        <p class="text-xs font-bold text-on-surface">${p.metadata.document_title}</p>
                        <div class="flex gap-2">
                            <span class="px-2 py-0.5 bg-surface-container-high rounded text-[10px] font-bold text-on-surface-variant">${p.phases.length} FASES</span>
                            <span class="px-2 py-0.5 bg-prisma-mint/25 rounded text-[10px] font-bold text-teal-900">${p.metadata.main_tool}</span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 text-center animate-fadeIn">
                    <span class="material-symbols-outlined text-amber-500 text-4xl mb-1">warning</span>
                    <p class="text-xs text-on-surface-variant">No hay información detallada disponible para esta región todavía.</p>
                </div>
            `;
        }
    } catch (e) {
        content.innerHTML = '<p class="text-error text-xs text-center p-6">Error cargando previsualización.</p>';
    }
}
</script>

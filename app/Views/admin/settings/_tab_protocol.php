<?php
use App\Core\Lang;
use App\Core\Csrf;
use App\Data\BullyingProtocols;

$ccaaList = [
    '' => '-- Selecciona una Comunidad Autónoma --',
    'andalucia' => 'Andalucía',
    'aragon' => 'Aragón',
    'asturias' => 'Asturias',
    'baleares' => 'Baleares',
    'canarias' => 'Canarias',
    'cantabria' => 'Cantabria',
    'castilla_leon' => 'Castilla y León',
    'castilla_la_mancha' => 'Castilla-La Mancha',
    'cataluna' => 'Catalunya',
    'comunidad_valenciana' => 'Comunidad Valenciana',
    'extremadura' => 'Extremadura',
    'galicia' => 'Galicia',
    'madrid' => 'Madrid',
    'murcia' => 'Murcia',
    'navarra' => 'Navarra',
    'pais_vasco' => 'País Vasco',
    'rioja' => 'La Rioja',
    'ceuta' => 'Ceuta',
    'melilla' => 'Melilla'
];

$selectedCcaa = $settings['ccaa_code'] ?? '';
$protocolActive = $settings['ccaa_protocol_active'] ?? '1';
$showToStudents = $settings['ccaa_show_to_students'] ?? '1';

$protocolData = $selectedCcaa ? BullyingProtocols::getByCode($selectedCcaa) : null;
?>

<form action="/admin/settings/protocol" method="POST" class="space-y-8">
    <input type="hidden" name="csrf_token" value="<?= Csrf::generateToken() ?>">

    <div class="space-y-1">
        <h2 class="text-xl font-black text-primary flex items-center gap-2">
            <span class="material-symbols-outlined">gavel</span>
            Protocolo Oficial de Acoso Escolar
        </h2>
        <p class="text-sm text-slate-500">Selecciona el protocolo autonómico que servirá de guía contextual para el personal y el alumnado.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="font-bold text-sm text-slate-700">Comunidad Autónoma</label>
                <select name="ccaa_code" class="w-full bg-white border border-surface-variant rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                    <?php foreach ($ccaaList as $code => $name): ?>
                        <option value="<?= $code ?>" <?= $selectedCcaa === $code ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[11px] text-slate-400 italic">Cada CCAA tiene sus propios tiempos, fases y herramientas oficiales (Séneca, REVA, etc.).</p>
            </div>

            <div class="space-y-4 pt-4 border-t border-slate-100">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="hidden" name="ccaa_protocol_active" value="0">
                    <input type="checkbox" name="ccaa_protocol_active" value="1" <?= $protocolActive === '1' ? 'checked' : '' ?> class="w-5 h-5 accent-primary rounded-lg">
                    <div>
                        <span class="block font-bold text-sm text-slate-700 group-hover:text-primary transition-colors">Activar Guía Contextual</span>
                        <span class="block text-xs text-slate-500 italic">Muestra información del protocolo en los reportes de riesgo.</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="hidden" name="ccaa_show_to_students" value="0">
                    <input type="checkbox" name="ccaa_show_to_students" value="1" <?= $showToStudents === '1' ? 'checked' : '' ?> class="w-5 h-5 accent-primary rounded-lg">
                    <div>
                        <span class="block font-bold text-sm text-slate-700 group-hover:text-primary transition-colors">Visible para Alumnos</span>
                        <span class="block text-xs text-slate-500 italic">Permite que los alumnos consulten el protocolo desde su panel.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200/60 relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-black text-slate-400 uppercase tracking-widest text-[10px] mb-4">Previsualización Dinámica</h3>
                
                <?php if ($protocolData): ?>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white" style="background-color: <?= $protocolData['metadata']['color'] ?>">
                                <span class="material-symbols-outlined">assured_workload</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 leading-tight"><?= $protocolData['metadata']['name'] ?></h4>
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-tight"><?= $protocolData['metadata']['authority'] ?></p>
                            </div>
                        </div>
                        <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm space-y-2">
                            <p class="text-[13px] font-bold text-slate-700"><?= $protocolData['metadata']['document_title'] ?></p>
                            <div class="flex gap-2">
                                <span class="px-2 py-0.5 bg-slate-100 rounded text-[10px] font-bold text-slate-500"><?= count($protocolData['phases']) ?> FASES</span>
                                <span class="px-2 py-0.5 bg-teal-50 rounded text-[10px] font-bold text-teal-600"><?= $protocolData['metadata']['main_tool'] ?></span>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 text-center italic mt-4 italic">El color de acento de la interfaz cambiará automáticamente a <span style="color: <?= $protocolData['metadata']['color'] ?>; font-weight: bold;"><?= $protocolData['metadata']['color'] ?></span> para este protocolo.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="material-symbols-outlined text-slate-300 text-5xl mb-2">map</span>
                        <p class="text-sm text-slate-400 font-medium italic">Selecciona una CCAA para ver los detalles del protocolo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="pt-6 border-t border-slate-100 flex justify-end">
        <button type="submit" class="bg-primary hover:bg-primary-variant text-white font-bold py-3 px-8 rounded-full transition-all ambient-shadow active:scale-95">
            Guardar Configuración de Protocolos
        </button>
    </div>
</form>

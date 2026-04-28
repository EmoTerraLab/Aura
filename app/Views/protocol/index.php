<?php
use App\Core\Lang;
use App\Core\Config;

$ccaaColor = $protocol['metadata']['color'] ?? '#056972';
$metadata = $protocol['metadata'];
?>

<main class="min-h-screen bg-slate-50 py-8 px-4 md:px-8">
    <div class="max-w-4xl mx-auto space-y-8 animate-[fadeIn_0.4s_ease-out]">
        
        <!-- Header del Protocolo -->
        <header class="bg-white rounded-[2rem] p-8 md:p-12 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full -mr-32 -mt-32 z-0"></div>
            
            <div class="relative z-10 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-3xl flex items-center justify-center text-white shadow-lg" style="background-color: <?= $ccaaColor ?>">
                            <span class="material-symbols-outlined text-3xl">assured_workload</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none mb-1"><?= $metadata['authority'] ?></p>
                            <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight leading-none"><?= $metadata['name'] ?></h1>
                        </div>
                    </div>
                    <a href="<?= $metadata['document_url'] ?>" target="_blank" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 px-5 py-2.5 rounded-full text-xs font-bold transition-all self-start md:self-center">
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                        Documento Oficial
                    </a>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-wrap gap-3">
                    <span class="px-4 py-1.5 bg-slate-100 rounded-full text-[11px] font-black text-slate-500 uppercase"><?= $metadata['document_date'] ?></span>
                    <span class="px-4 py-1.5 rounded-full text-[11px] font-black text-white uppercase" style="background-color: <?= $ccaaColor ?>">Herramienta: <?= $metadata['main_tool'] ?></span>
                    <span class="px-4 py-1.5 bg-slate-100 rounded-full text-[11px] font-black text-slate-500 uppercase"><?= $metadata['code'] ?></span>
                </div>

                <p class="text-lg text-slate-600 leading-relaxed font-medium">
                    <?= $metadata['document_title'] ?>
                </p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Columna Izquierda: Principios y Tipos -->
            <div class="md:col-span-1 space-y-6">
                <section class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                    <h2 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-4">Principios Clave</h2>
                    <ul class="space-y-3">
                        <?php foreach ($protocol['content']['key_principles'] as $principle): ?>
                            <li class="flex gap-3 text-sm text-slate-600 leading-snug">
                                <span class="material-symbols-outlined text-sm mt-0.5" style="color: <?= $ccaaColor ?>">check_circle</span>
                                <?= $principle ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                    <h2 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-4">Tipos de Violencia</h2>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($protocol['content']['types_of_violence'] as $type): ?>
                            <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[11px] font-bold border border-slate-100"><?= $type ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="rounded-[2rem] p-6 text-white shadow-lg" style="background-color: <?= $ccaaColor ?>">
                    <h2 class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-4">Contactos de Emergencia</h2>
                    <div class="space-y-4">
                        <?php foreach ($protocol['emergency_contacts'] as $contact): ?>
                            <div class="space-y-1">
                                <p class="text-xs font-black uppercase opacity-80 leading-tight"><?= $contact['name'] ?></p>
                                <a href="tel:<?= str_replace(' ', '', $contact['contact']) ?>" class="block text-xl font-black hover:underline decoration-2 underline-offset-4">
                                    <?= $contact['contact'] ?>
                                </a>
                                <p class="text-[10px] opacity-70 italic"><?= $contact['description'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <!-- Columna Derecha: Fases -->
            <div class="md:col-span-2 space-y-6">
                <h2 class="text-xl font-black text-slate-800 px-2">Fases del Protocolo</h2>
                
                <div class="space-y-4 relative">
                    <!-- Línea vertical decorativa -->
                    <div class="absolute left-8 top-4 bottom-4 w-0.5 bg-slate-200 z-0"></div>

                    <?php foreach ($protocol['phases'] as $phase): ?>
                        <div class="relative z-10 flex gap-6 group">
                            <div class="flex-none">
                                <div class="w-16 h-16 rounded-2xl bg-white border-4 border-slate-50 shadow-sm flex items-center justify-center text-xl font-black text-slate-400 group-hover:scale-110 group-hover:border-white transition-all duration-300" style="color: <?= $ccaaColor ?>; border-color: <?= $ccaaColor ?>20">
                                    <?= $phase['number'] ?>
                                </div>
                            </div>
                            <div class="flex-1 bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 group-hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-black text-slate-800"><?= $phase['title'] ?></h3>
                                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black text-slate-400 uppercase"><?= $phase['timeframe'] ?></span>
                                </div>
                                <p class="text-sm text-slate-500 leading-relaxed mb-4">
                                    <?= $phase['description'] ?>
                                </p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-50">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Responsable</p>
                                        <p class="text-xs font-bold text-slate-700"><?= $phase['responsible'] ?></p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Herramientas</p>
                                        <p class="text-xs font-bold text-slate-700"><?= $phase['tools'] ?></p>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-2">
                                    <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Acciones Principales</p>
                                    <div class="flex flex-col gap-1.5">
                                        <?php foreach ($phase['actions'] as $action): ?>
                                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                                <div class="w-1 h-1 rounded-full flex-none" style="background-color: <?= $ccaaColor ?>"></div>
                                                <?= $action ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <footer class="text-center py-8">
            <a href="/" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-600 text-xs font-bold transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Volver al Panel de Gestión
            </a>
        </footer>
    </div>
</main>

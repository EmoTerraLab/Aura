<?php
use App\Core\Lang;
use App\Core\Config;

$ccaaColor = $protocol['metadata']['color'] ?? '#1E2433';
$metadata = $protocol['metadata'];
$bodyClass = "min-h-screen bg-surface font-body-md text-on-surface";
?>

<main class="min-h-screen bg-surface py-8 px-4 md:px-8 font-display">
    <div class="max-w-4xl mx-auto space-y-6 animate-fadeIn">
        
        <!-- Header del Protocolo -->
        <header class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 relative overflow-hidden">
            <div class="relative z-10 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-xs shrink-0" style="background-color: <?= $ccaaColor ?>">
                            <span class="material-symbols-outlined text-2xl">assured_workload</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant leading-none mb-1"><?= $metadata['authority'] ?></p>
                            <h1 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight leading-none"><?= $metadata['name'] ?></h1>
                        </div>
                    </div>
                    <a href="<?= $metadata['document_url'] ?>" target="_blank" class="inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container-high text-on-surface px-4 py-2 rounded-xl text-xs font-bold transition-all self-start md:self-center border border-surface-variant/40">
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                        Documento Oficial
                    </a>
                </div>

                <div class="pt-4 border-t border-surface-variant/30 flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-surface-container-low rounded-lg text-[10px] font-bold text-on-surface-variant uppercase"><?= $metadata['document_date'] ?></span>
                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold text-white uppercase" style="background-color: <?= $ccaaColor ?>">Herramienta: <?= $metadata['main_tool'] ?></span>
                    <span class="px-3 py-1 bg-surface-container-low rounded-lg text-[10px] font-bold text-on-surface-variant uppercase"><?= $metadata['code'] ?></span>
                </div>

                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed font-normal font-body-md">
                    <?= $metadata['document_title'] ?>
                </p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Columna Izquierda: Principios y Tipos -->
            <div class="md:col-span-1 space-y-4">
                <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-card border border-surface-variant/40">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Principios Clave</h2>
                    <ul class="space-y-2 font-body-md">
                        <?php foreach ($protocol['content']['key_principles'] as $principle): ?>
                            <li class="flex gap-2 text-xs text-on-surface leading-snug">
                                <span class="material-symbols-outlined text-sm mt-0.5" style="color: <?= $ccaaColor ?>">check_circle</span>
                                <span><?= $principle ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-card border border-surface-variant/40">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Tipos de Acoso / Violencia</h2>
                    <div class="flex flex-wrap gap-1.5 font-body-md">
                        <?php foreach ($protocol['content']['types_of_violence'] as $type): ?>
                            <span class="px-2.5 py-1 bg-surface-container-low text-on-surface rounded-lg text-[11px] font-medium border border-surface-variant/30"><?= $type ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="rounded-2xl p-5 text-white shadow-card" style="background-color: <?= $ccaaColor ?>">
                    <h2 class="text-[10px] font-bold uppercase tracking-wider opacity-80 mb-3">Contactos de Emergencia</h2>
                    <div class="space-y-3">
                        <?php foreach ($protocol['emergency_contacts'] as $contact): ?>
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-bold uppercase opacity-80 leading-tight"><?= $contact['name'] ?></p>
                                <a href="tel:<?= str_replace(' ', '', $contact['contact']) ?>" class="block text-base font-bold hover:underline">
                                    <?= $contact['contact'] ?>
                                </a>
                                <p class="text-[10px] opacity-75 font-body-md"><?= $contact['description'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <!-- Columna Derecha: Fases -->
            <div class="md:col-span-2 space-y-4">
                <h2 class="text-sm md:text-base font-bold text-on-surface px-1">Fases del Protocolo Oficial</h2>
                
                <div class="space-y-3">
                    <?php foreach ($protocol['phases'] as $phase): ?>
                        <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-card border border-surface-variant/40 hover:border-primary/30 transition-all flex gap-4">
                            <div class="flex-none">
                                <div class="w-10 h-10 rounded-xl bg-surface-container-low border border-surface-variant/40 flex items-center justify-center text-sm font-bold shrink-0" style="color: <?= $ccaaColor ?>">
                                    <?= $phase['number'] ?>
                                </div>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xs md:text-sm font-bold text-on-surface"><?= $phase['title'] ?></h3>
                                    <span class="px-2.5 py-0.5 bg-surface-container-low rounded-md text-[10px] font-bold text-on-surface-variant uppercase"><?= $phase['timeframe'] ?></span>
                                </div>
                                <p class="text-xs text-on-surface-variant leading-relaxed font-body-md font-normal">
                                    <?= $phase['description'] ?>
                                </p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-surface-variant/30 text-xs">
                                    <div>
                                        <p class="text-[9px] font-bold uppercase text-on-surface-variant/70 tracking-wider">Responsable</p>
                                        <p class="text-xs font-semibold text-on-surface"><?= $phase['responsible'] ?></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-bold uppercase text-on-surface-variant/70 tracking-wider">Herramientas</p>
                                        <p class="text-xs font-semibold text-on-surface"><?= $phase['tools'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <footer class="text-center py-6">
            <a href="/staff/inbox" class="inline-flex items-center gap-1.5 text-on-surface-variant hover:text-primary text-xs font-bold transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Volver al Panel de Gestión
            </a>
        </footer>
    </div>
</main>

<?php
use App\Core\Lang;
$bodyClass = "min-h-screen bg-surface font-body-md text-on-surface";
?>
<main class="min-h-screen bg-surface py-8 px-4 md:px-8 font-display">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fadeIn">
            <div>
                <a href="/staff/inbox" class="inline-flex items-center gap-1.5 text-primary text-xs font-bold hover:underline mb-2">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Volver a Bandeja
                </a>
                <h1 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight"><?= Lang::t('protocol.dashboard_title') ?></h1>
                <p class="text-xs text-on-surface-variant font-medium">Gestión activa de expedientes y protocolos autonómicos</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="h-10 px-4 bg-surface-container-lowest border border-surface-variant/40 rounded-xl hover:bg-surface-container-low transition-colors shadow-xs flex items-center gap-2 text-xs font-bold text-on-surface cursor-pointer">
                    <span class="material-symbols-outlined text-base">refresh</span>
                    <span>Actualizar</span>
                </button>
            </div>
        </header>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fadeIn">
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card space-y-1">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider"><?= Lang::t('protocol.active_expedients') ?></p>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-primary"><?= $totalActive ?></span>
                </div>
            </div>
            
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-error/30 shadow-card space-y-1 bg-error/5">
                <p class="text-[10px] font-bold uppercase text-error tracking-wider"><?= Lang::t('protocol.barnahus_alerts') ?></p>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-error"><?= $totalBarnahus ?></span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card space-y-1">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider"><?= Lang::t('protocol.weekly_followups') ?></p>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-teal-800"><?= $totalFollowups ?></span>
                </div>
            </div>
        </div>

        <!-- Alertas de Plazos -->
        <?php if (!empty($alerts)): ?>
        <div class="bg-amber-500/10 border border-amber-500/30 p-4 md:p-5 rounded-2xl space-y-2">
            <h3 class="text-amber-900 font-bold text-xs uppercase flex items-center gap-1.5 tracking-wider">
                <span class="material-symbols-outlined text-base text-amber-700">warning</span> <?= Lang::t('protocol.deadline_alerts') ?>
            </h3>
            <ul class="space-y-1">
                <?php foreach ($alerts as $alert): ?>
                    <li class="text-xs font-semibold text-amber-900/90">• <?= $alert ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Active Cases Table -->
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card overflow-hidden">
            <div class="p-4 md:p-5 border-b border-surface-variant/40 bg-surface-container-low flex justify-between items-center">
                <h2 class="font-bold text-on-surface uppercase text-xs tracking-wider"><?= Lang::t('protocol.active_cases_table') ?></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-container-low text-[10px] font-bold text-on-surface-variant uppercase tracking-wider border-b border-surface-variant/40">
                            <th class="px-6 py-3">ID</th>
                            <th class="px-4 py-3"><?= Lang::t('protocol.student') ?></th>
                            <th class="px-4 py-3"><?= Lang::t('protocol.phase') ?></th>
                            <th class="px-4 py-3"><?= Lang::t('protocol.days_active') ?></th>
                            <th class="px-6 py-3 text-right"><?= Lang::t('sociogram.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-variant/20 font-body-md">
                        <?php foreach ($cases as $case): ?>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-3.5 font-bold text-on-surface-variant/70">#<?= $case['id'] ?></td>
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-on-surface text-xs"><?= htmlspecialchars($case['student_name']) ?></p>
                                <p class="text-[10px] font-semibold text-on-surface-variant uppercase tracking-wider"><?= htmlspecialchars($case['classroom_name']) ?></p>
                            </td>
                            <td class="px-4 py-3.5">
                                <?php 
                                    $phaseColors = [
                                        'deteccion' => 'bg-surface-container-high text-on-surface-variant',
                                        'valoracion' => 'bg-primary/10 text-primary',
                                        'comunicacio' => 'bg-amber-100 text-amber-900',
                                        'intervencio' => 'bg-prisma-mint/25 text-teal-900',
                                        'violencia_sexual_actiu' => 'bg-error text-white'
                                    ];
                                    $color = $phaseColors[$case['current_phase']] ?? 'bg-surface-container-high text-on-surface-variant';
                                ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase font-display <?= $color ?>">
                                    <?= str_replace('_', ' ', $case['current_phase']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-on-surface text-xs"><?= floor($case['days_active']) ?> <?= Lang::t('protocol.days') ?></td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="/staff/inbox?report_id=<?= $case['id'] ?>" class="w-8 h-8 inline-flex items-center justify-center bg-surface-container-low border border-surface-variant/40 text-on-surface-variant hover:text-primary rounded-lg transition-all" title="Ver Expediente">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($cases)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-on-surface-variant italic text-xs"><?= Lang::t('protocol.no_active_cases') ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

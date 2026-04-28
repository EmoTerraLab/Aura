<?php
use App\Core\Lang;
?>
<main class="min-h-screen bg-slate-50 py-10 px-4 md:px-10">
    <div class="max-w-7xl mx-auto space-y-10">
        
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tight">Panell Global de Convivència</h1>
                <p class="text-slate-500 font-bold">Gestió activa de protocolos CCAA</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="p-3 bg-white border border-slate-200 rounded-full hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-slate-600">refresh</span>
                </button>
            </div>
        </header>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-2">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Expedients Actius</p>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-black text-primary"><?= $totalActive ?></span>
                    <span class="text-xs font-bold text-slate-400 mb-2">Casos oberts</span>
                </div>
            </div>
            
            <div class="bg-red-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-red-600/20 space-y-2">
                <p class="text-[10px] font-black uppercase opacity-60 tracking-widest">Alertes Barnahus</p>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-black"><?= $totalBarnahus ?></span>
                    <span class="text-xs font-bold opacity-80 mb-2">Violència Sexual</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-2">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Seguiments Setmanals</p>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-black text-emerald-500"><?= $totalFollowups ?></span>
                    <span class="text-xs font-bold text-slate-400 mb-2">Sessions registrades</span>
                </div>
            </div>
        </div>

        <!-- Alertas de Plazos -->
        <?php if (!empty($alerts)): ?>
        <div class="bg-amber-50 border border-amber-200 p-6 rounded-3xl space-y-3">
            <h3 class="text-amber-800 font-black text-xs uppercase flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">warning</span> Alertes de Compliment (Deadlines)
            </h3>
            <ul class="space-y-1">
                <?php foreach ($alerts as $alert): ?>
                    <li class="text-xs font-bold text-amber-700">• <?= $alert ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Active Cases Table -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h2 class="font-black text-slate-800 uppercase text-sm tracking-widest">Expedients en Curs</h2>
            </div>
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-8 py-4">ID</th>
                            <th>Alumnat</th>
                            <th>Fase Actual</th>
                            <th>Dies Actiu</th>
                            <th class="px-8">Accions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach ($cases as $case): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition-colors group">
                            <td class="px-8 py-6 font-bold text-slate-400">#<?= $case['id'] ?></td>
                            <td>
                                <p class="font-black text-slate-800"><?= $case['student_name'] ?></p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase"><?= $case['classroom_name'] ?></p>
                            </td>
                            <td>
                                <?php 
                                    $phaseColors = [
                                        'deteccion' => 'bg-slate-100 text-slate-500',
                                        'valoracion' => 'bg-primary/10 text-primary',
                                        'comunicacio' => 'bg-amber-100 text-amber-700',
                                        'intervencio' => 'bg-emerald-100 text-emerald-700',
                                        'violencia_sexual_actiu' => 'bg-red-600 text-white animate-pulse'
                                    ];
                                    $color = $phaseColors[$case['current_phase']] ?? 'bg-slate-100';
                                ?>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase <?= $color ?>">
                                    <?= str_replace('_', ' ', $case['current_phase']) ?>
                                </span>
                            </td>
                            <td class="font-bold text-slate-600"><?= floor($case['days_active']) ?> dies</td>
                            <td class="px-8">
                                <a href="/staff/inbox" class="p-2 bg-slate-100 text-slate-400 rounded-full hover:bg-primary hover:text-white transition-all inline-flex items-center justify-center">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($cases)): ?>
                        <tr>
                            <td colspan="5" class="py-20 text-center text-slate-400 italic">No hi ha expedients actius actualment.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

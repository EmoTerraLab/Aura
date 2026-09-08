<?php
use App\Core\Lang;
$bodyClass = "min-h-screen bg-surface font-body-md text-on-surface";
?>
<main class="min-h-screen bg-surface py-8 px-4 md:px-8 font-display">
    <div class="max-w-6xl mx-auto space-y-6">
        
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fadeIn">
            <div>
                <a href="/staff/inbox" class="inline-flex items-center gap-1.5 text-primary text-xs font-bold hover:underline mb-2">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <?= Lang::t('nav.back') ?>
                </a>
                <h1 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight"><?= Lang::t('sociogram.analysis_header') ?? 'Resultados del Sociograma' ?></h1>
                <p class="text-xs md:text-sm text-on-surface-variant font-medium"><?= htmlspecialchars($survey['title']) ?> · <?= htmlspecialchars($survey['classroom_name']) ?></p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="h-10 px-4 bg-surface-container-lowest border border-surface-variant/40 rounded-xl hover:bg-surface-container-low transition-colors shadow-xs flex items-center gap-2 text-xs font-bold text-on-surface cursor-pointer">
                    <span class="material-symbols-outlined text-base">print</span>
                    <span>Imprimir Informe</span>
                </button>
            </div>
        </header>

        <!-- Métricas de Impacto (Resumen Bento) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fadeIn">
            <!-- Líderes Positivos -->
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card space-y-2">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-prisma-mint/30 text-teal-900 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">star</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase text-teal-900 tracking-wider">Líderes Positivos</p>
                </div>
                <?php 
                $leaders = array_slice($metrics, 0, 3);
                foreach ($leaders as $l): ?>
                    <div class="flex justify-between items-center text-xs font-semibold text-on-surface">
                        <span class="truncate"><?= htmlspecialchars($l['name']) ?></span>
                        <span class="text-teal-900 bg-prisma-mint/25 px-2 py-0.5 rounded-md text-[10px] font-bold"><?= $l['pos_count'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Alumnos Aislados -->
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card space-y-2">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/15 text-amber-900 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">person_off</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase text-amber-900 tracking-wider">Alumnos Aislados</p>
                </div>
                <?php 
                $isolated = array_filter($metrics, fn($m) => $m['pos_count'] == 0);
                foreach (array_slice($isolated, 0, 3) as $i): ?>
                    <p class="text-xs font-semibold text-on-surface truncate"><?= htmlspecialchars($i['name']) ?></p>
                <?php endforeach; 
                if (empty($isolated)) echo '<p class="text-xs text-on-surface-variant/70 italic">Ningún alumno aislado.</p>';
                ?>
            </div>

            <!-- Rechazo Grupal -->
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card space-y-2">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-red-500/15 text-red-900 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">block</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase text-red-900 tracking-wider">Rechazo Grupal</p>
                </div>
                <?php 
                usort($metrics, fn($a, $b) => $b['neg_count'] <=> $a['neg_count']);
                $rejected = array_slice(array_filter($metrics, fn($m) => $m['neg_count'] > 0), 0, 3);
                foreach ($rejected as $r): ?>
                    <div class="flex justify-between items-center text-xs font-semibold text-on-surface">
                        <span class="truncate"><?= htmlspecialchars($r['name']) ?></span>
                        <span class="text-red-700 bg-red-100 px-2 py-0.5 rounded-md text-[10px] font-bold"><?= $r['neg_count'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Víctimas Potenciales -->
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-error/30 shadow-card space-y-2 bg-error/5">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-error text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">priority_high</span>
                    </div>
                    <p class="text-[10px] font-bold uppercase text-error tracking-wider">Víctimas Potenciales</p>
                </div>
                <?php 
                usort($metrics, fn($a, $b) => $b['victim_count'] <=> $a['victim_count']);
                $victims = array_slice(array_filter($metrics, fn($m) => $m['victim_count'] > 0), 0, 3);
                foreach ($victims as $v): ?>
                    <div class="flex justify-between items-center bg-surface-container-lowest rounded-lg px-2.5 py-1.5 border border-error/20">
                        <p class="text-xs font-bold text-on-surface truncate"><?= htmlspecialchars($v['name']) ?> (<?= $v['victim_count'] ?>)</p>
                        <button onclick="openProtocol(<?= $v['id'] ?>)" class="text-[10px] bg-error text-white font-bold px-2 py-0.5 rounded-md hover:bg-error/90 transition-colors cursor-pointer">Actuar</button>
                    </div>
                <?php endforeach; 
                if (empty($victims)) echo '<p class="text-xs text-on-surface-variant/70 italic">Sin señales de alerta.</p>';
                ?>
            </div>
        </div>

        <!-- Matriz Detallada -->
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card overflow-hidden">
            <div class="p-4 md:p-5 border-b border-surface-variant/40 bg-surface-container-low">
                <h2 class="font-bold text-on-surface text-xs uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-outline-variant text-base">table_chart</span>
                    Matriz Completa del Aula
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-container-low text-[10px] font-bold text-on-surface-variant uppercase tracking-wider border-b border-surface-variant/40">
                            <th class="px-6 py-3">Estudiante</th>
                            <th class="px-4 py-3">Afinidad Positiva (+)</th>
                            <th class="px-4 py-3">Rechazo (-)</th>
                            <th class="px-4 py-3">Señales de Alerta (?)</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-variant/20 font-body-md">
                        <?php 
                        usort($metrics, fn($a, $b) => strcmp($a['name'], $b['name']));
                        foreach ($metrics as $m): ?>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-3 font-semibold text-on-surface flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface-variant font-bold text-xs">
                                    <?= strtoupper(substr($m['name'], 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($m['name']) ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 bg-surface-container-high rounded-full w-20 overflow-hidden">
                                        <div class="h-full bg-teal-600 rounded-full" style="width: <?= min($m['pos_count']*20, 100) ?>%"></div>
                                    </div>
                                    <span class="font-bold text-teal-800 text-xs"><?= $m['pos_count'] ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 bg-surface-container-high rounded-full w-20 overflow-hidden">
                                        <div class="h-full bg-red-500 rounded-full" style="width: <?= min($m['neg_count']*20, 100) ?>%"></div>
                                    </div>
                                    <span class="font-bold text-red-600 text-xs"><?= $m['neg_count'] ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($m['victim_count'] > 0): ?>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-amber-100 text-amber-800 border border-amber-300 inline-flex items-center gap-1 font-display">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        <?= $m['victim_count'] ?> señales
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-surface-container-high text-on-surface-variant/70 font-display font-medium">
                                        0 señales
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <button onclick="openProtocol(<?= $m['id'] ?>)" class="w-8 h-8 inline-flex items-center justify-center bg-surface-container-low border border-surface-variant/40 text-on-surface-variant hover:text-error hover:border-error/40 rounded-lg transition-all cursor-pointer" title="Abrir Protocolo">
                                    <span class="material-symbols-outlined text-base">security</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
function openProtocol(studentId) {
    if (confirm('¿Deseas abrir un protocolo preventivo oficial para este alumno/a?')) {
        window.location.href = `/staff/inbox?new_report=1&student_id=${studentId}`;
    }
}
</script>

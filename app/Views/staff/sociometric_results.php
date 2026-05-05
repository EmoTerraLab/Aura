<?php
use App\Core\Lang;
?>
<main class="min-h-screen bg-slate-50 py-10 px-4 md:px-10">
    <div class="max-w-6xl mx-auto space-y-10">
        
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 animate-[fadeIn_0.4s_ease-out]">
            <div>
                <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tight"><?= Lang::t('sociogram.analysis_header') ?? 'Resultados del Sociograma' ?></h1>
                <p class="text-slate-500 font-bold"><?= htmlspecialchars($survey['title']) ?> · <?= htmlspecialchars($survey['classroom_name']) ?></p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="p-3 bg-white border border-slate-200 rounded-full hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-600">print</span>
                </button>
            </div>
        </header>

        <!-- Métricas de Impacto (Resumen) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-[slideUp_0.5s_ease-out]">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-2 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-primary text-xl">star</span>
                    <p class="text-[10px] font-black uppercase text-primary tracking-widest">Líderes Positivos</p>
                </div>
                <?php 
                $leaders = array_slice($metrics, 0, 3);
                foreach ($leaders as $l): ?>
                    <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($l['name']) ?> <span class="text-primary bg-primary/10 px-2 py-0.5 rounded-full text-xs ml-1"><?= $l['pos_count'] ?></span></p>
                <?php endforeach; ?>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-2 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-amber-500 text-xl">person_off</span>
                    <p class="text-[10px] font-black uppercase text-amber-600 tracking-widest">Alumnos Aislados</p>
                </div>
                <?php 
                $isolated = array_filter($metrics, fn($m) => $m['pos_count'] == 0);
                foreach (array_slice($isolated, 0, 3) as $i): ?>
                    <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($i['name']) ?></p>
                <?php endforeach; 
                if (empty($isolated)) echo '<p class="text-[11px] text-slate-400 italic font-medium">Ningún alumno aislado.</p>';
                ?>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-2 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-red-500 text-xl">block</span>
                    <p class="text-[10px] font-black uppercase text-red-500 tracking-widest">Rechazo Grupal</p>
                </div>
                <?php 
                usort($metrics, fn($a, $b) => $b['neg_count'] <=> $a['neg_count']);
                $rejected = array_slice(array_filter($metrics, fn($m) => $m['neg_count'] > 0), 0, 3);
                foreach ($rejected as $r): ?>
                    <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($r['name']) ?> <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded-full text-xs ml-1"><?= $r['neg_count'] ?></span></p>
                <?php endforeach; ?>
            </div>

            <div class="bg-gradient-to-br from-red-600 to-red-700 p-6 rounded-[2rem] text-white shadow-lg shadow-red-600/30 space-y-2 relative overflow-hidden group hover:shadow-red-600/40 transition-shadow">
                <div class="absolute -right-4 -top-4 opacity-10 transform group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl">warning</span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-white text-xl">priority_high</span>
                        <p class="text-[10px] font-black uppercase opacity-90 tracking-widest">Víctimas Potenciales</p>
                    </div>
                    <?php 
                    usort($metrics, fn($a, $b) => $b['victim_count'] <=> $a['victim_count']);
                    $victims = array_slice(array_filter($metrics, fn($m) => $m['victim_count'] > 0), 0, 3);
                    foreach ($victims as $v): ?>
                        <div class="flex justify-between items-center bg-black/10 rounded-xl px-3 py-2 mb-2 backdrop-blur-sm">
                            <p class="text-sm font-bold"><?= htmlspecialchars($v['name']) ?> (<?= $v['victim_count'] ?>)</p>
                            <button onclick="openProtocol(<?= $v['id'] ?>)" class="text-[10px] bg-white text-red-700 hover:bg-slate-100 font-bold px-3 py-1 rounded-full transition-colors shadow-sm">ACTUAR</button>
                        </div>
                    <?php endforeach; 
                    if (empty($victims)) echo '<p class="text-[11px] text-white/70 italic font-medium">No se detectaron señales de alerta.</p>';
                    ?>
                </div>
            </div>
        </div>

        <!-- Matriz Detallada -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-[slideUp_0.6s_ease-out]">
            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <h2 class="font-black text-slate-800 uppercase text-sm tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400">table_chart</span>
                    Matriz Completa del Aula
                </h2>
            </div>
            <div class="overflow-x-auto no-scrollbar">
                <div class="min-w-[800px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-8 py-4">Estudiante</th>
                                <th>Afinidad Positiva (+)</th>
                                <th>Rechazo (-)</th>
                                <th>Señales de Alerta (?)</th>
                                <th class="px-8 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Re-ordenar por nombre para la tabla
                            usort($metrics, fn($a, $b) => strcmp($a['name'], $b['name']));
                            foreach ($metrics as $m): ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5 font-black text-slate-700 text-sm flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                                        <?= strtoupper(substr($m['name'], 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($m['name']) ?>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 bg-slate-100 rounded-full w-24 overflow-hidden">
                                            <div class="h-full bg-primary rounded-full transition-all duration-1000" style="width: <?= min($m['pos_count']*20, 100) ?>%"></div>
                                        </div>
                                        <span class="font-bold text-primary text-xs w-4"><?= $m['pos_count'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 bg-slate-100 rounded-full w-24 overflow-hidden">
                                            <div class="h-full bg-red-400 rounded-full transition-all duration-1000" style="width: <?= min($m['neg_count']*20, 100) ?>%"></div>
                                        </div>
                                        <span class="font-bold text-red-500 text-xs w-4"><?= $m['neg_count'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($m['victim_count'] > 0): ?>
                                        <span class="px-3 py-1 rounded-full font-black text-[10px] bg-amber-100 text-amber-700 border border-amber-200 inline-flex items-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[12px]">warning</span>
                                            <?= $m['victim_count'] ?> señales
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full font-bold text-[10px] bg-slate-100 text-slate-400">
                                            0 señales
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 text-right">
                                    <button onclick="openProtocol(<?= $m['id'] ?>)" class="p-2 bg-white border border-slate-200 text-slate-400 rounded-full hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm group-hover:shadow" title="Abrir Protocolo">
                                        <span class="material-symbols-outlined text-[18px]">security</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

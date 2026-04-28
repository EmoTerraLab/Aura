<?php
use App\Core\Lang;
?>
<main class="min-h-screen bg-slate-50 py-10 px-4 md:px-10">
    <div class="max-w-6xl mx-auto space-y-10">
        
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tight">Anàlisi del Clima d'Aula</h1>
                <p class="text-slate-500 font-bold"><?= $survey['title'] ?> · <?= $survey['classroom_name'] ?></p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="p-3 bg-white border border-slate-200 rounded-full hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-slate-600">print</span>
                </button>
            </div>
        </header>

        <!-- Métricas de Impacto (Resumen) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-1">
                <p class="text-[9px] font-black uppercase text-primary tracking-widest">Més elegits (Líders)</p>
                <?php 
                $leaders = array_slice($metrics, 0, 3);
                foreach ($leaders as $l): ?>
                    <p class="text-xs font-bold text-slate-700"><?= $l['name'] ?> (<?= $l['pos_count'] ?>)</p>
                <?php endforeach; ?>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-1">
                <p class="text-[9px] font-black uppercase text-amber-600 tracking-widest">Aïllament (0 vots positius)</p>
                <?php 
                $isolated = array_filter($metrics, fn($m) => $m['pos_count'] == 0);
                foreach (array_slice($isolated, 0, 3) as $i): ?>
                    <p class="text-xs font-bold text-slate-700"><?= $i['name'] ?></p>
                <?php endforeach; 
                if (empty($isolated)) echo '<p class="text-[10px] text-slate-400 italic">Cap alumne aïllat.</p>';
                ?>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-1">
                <p class="text-[9px] font-black uppercase text-red-500 tracking-widest">Rebutjats (Vots negatius)</p>
                <?php 
                usort($metrics, fn($a, $b) => $b['neg_count'] <=> $a['neg_count']);
                $rejected = array_slice(array_filter($metrics, fn($m) => $m['neg_count'] > 0), 0, 3);
                foreach ($rejected as $r): ?>
                    <p class="text-xs font-bold text-slate-700"><?= $r['name'] ?> (<?= $r['neg_count'] ?>)</p>
                <?php endforeach; ?>
            </div>

            <div class="bg-red-600 p-6 rounded-[2rem] text-white shadow-lg shadow-red-600/20 space-y-1">
                <p class="text-[9px] font-black uppercase opacity-60 tracking-widest">Possibles Víctimes</p>
                <?php 
                usort($metrics, fn($a, $b) => $b['victim_count'] <=> $a['victim_count']);
                $victims = array_slice(array_filter($metrics, fn($m) => $m['victim_count'] > 0), 0, 3);
                foreach ($victims as $v): ?>
                    <div class="flex justify-between items-center">
                        <p class="text-xs font-bold"><?= $v['name'] ?> (<?= $v['victim_count'] ?>)</p>
                        <button onclick="openProtocol(<?= $v['id'] ?>)" class="text-[10px] bg-white/20 hover:bg-white/40 px-2 py-0.5 rounded-full">Activar</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Matriz Detallada -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50">
                <h2 class="font-black text-slate-800 uppercase text-sm tracking-widest">Matriu de Nominacions</h2>
            </div>
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-8 py-4">Alumne/a</th>
                            <th>Positius (+)</th>
                            <th>Negatius (-)</th>
                            <th>Víctima (?)</th>
                            <th class="px-8">Accions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach ($metrics as $m): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition-colors">
                            <td class="px-8 py-6 font-black text-slate-700"><?= $m['name'] ?></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 bg-primary rounded-full" style="width: <?= min($m['pos_count']*10, 100) ?>px"></div>
                                    <span class="font-bold text-primary"><?= $m['pos_count'] ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 bg-red-400 rounded-full" style="width: <?= min($m['neg_count']*10, 100) ?>px"></div>
                                    <span class="font-bold text-red-500"><?= $m['neg_count'] ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="px-3 py-1 rounded-full font-black text-[10px] <?= $m['victim_count'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400' ?>">
                                    <?= $m['victim_count'] ?> senyals
                                </span>
                            </td>
                            <td class="px-8">
                                <button onclick="openProtocol(<?= $m['id'] ?>)" class="p-2 bg-slate-100 text-slate-400 rounded-full hover:bg-red-600 hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-sm">emergency_home</span>
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
    if (confirm('Vols obrir un protocol preventiu per a aquest alumne/a?')) {
        // Redirigir al creador de reportis de staff amb dades pre-carregades
        window.location.href = `/staff/inbox?new_report=1&student_id=${studentId}`;
    }
}
</script>

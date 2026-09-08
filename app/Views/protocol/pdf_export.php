<?php
use App\Core\Lang;
?>
<div class="bg-white min-h-screen p-8 md:p-16 text-slate-900 font-display" id="print-area">
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header Informe -->
        <div class="flex justify-between items-start border-b-2 border-primary pb-6">
            <div class="flex items-center gap-3">
                <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" alt="Prisma" class="w-10 h-10 rounded-lg object-contain">
                <div>
                    <h1 class="text-2xl font-bold uppercase tracking-tight text-primary">Informe de Protocol d'Actuació</h1>
                    <p class="text-slate-500 font-semibold text-xs">Prisma — Tecnologia per a la convivència escolar</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-bold text-sm">Expedient #<?= $case['id'] ?></p>
                <p class="text-xs text-slate-500"><?= date('d/m/Y') ?></p>
            </div>
        </div>

        <!-- Seccion 1: Dades Generals -->
        <section class="space-y-3 font-body-md text-xs">
            <h2 class="text-xs font-bold bg-slate-100 p-2.5 rounded-lg uppercase tracking-wider text-slate-800 font-display">1. Dades del Cas</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="font-bold text-slate-500 uppercase text-[10px] font-display">Alumnat Implicat</p>
                    <p class="font-semibold"><?= htmlspecialchars($report['student_name'] ?? 'Anònim') ?> (<?= htmlspecialchars($report['classroom_name'] ?? 'Aula') ?>)</p>
                </div>
                <div>
                    <p class="font-bold text-slate-500 uppercase text-[10px] font-display">Tipificació</p>
                    <p class="font-semibold"><?= strtoupper($case['classification'] ?? 'Pendent') ?> (<?= $case['severity_preliminary'] ?>)</p>
                </div>
            </div>
            <div>
                <p class="font-bold text-slate-500 uppercase text-[10px] font-display">Relat Inicial</p>
                <p class="italic bg-slate-50 p-3 rounded-lg border border-slate-100">"<?= htmlspecialchars($report['content'] ?? '') ?>"</p>
            </div>
        </section>

        <!-- Seccion 2: Comunicacions -->
        <section class="space-y-3 font-body-md text-xs">
            <h2 class="text-xs font-bold bg-slate-100 p-2.5 rounded-lg uppercase tracking-wider text-slate-800 font-display">2. Comunicacions Obligatòries</h2>
            <ul class="space-y-2">
                <li class="flex items-center gap-2">
                    <span class="font-bold"><?= ($case['communications']['inspeccio'] ?? false) ? '✅' : '❌' ?></span>
                    Comunicat a Inspecció (REVA)
                </li>
                <li class="flex items-center gap-2">
                    <span class="font-bold"><?= ($case['communications']['familia_victima'] ?? false) ? '✅' : '❌' ?></span>
                    Comunicat a família víctima
                </li>
                <li class="flex items-center gap-2">
                    <span class="font-bold"><?= ($case['communications']['familia_agressor'] ?? false) ? '✅' : '❌' ?></span>
                    Comunicat a família agressor
                </li>
            </ul>
        </section>

        <!-- Seccion 3: Mesures (Mapa de Seguretat) -->
        <?php if ($map): ?>
        <section class="space-y-3 font-body-md text-xs">
            <h2 class="text-xs font-bold bg-slate-100 p-2.5 rounded-lg uppercase tracking-wider text-slate-800 font-display">3. Mapa de Seguretat i Mesures</h2>
            <div class="grid grid-cols-1 gap-3">
                <div class="border border-slate-200 p-3 rounded-lg">
                    <p class="font-bold text-slate-500 uppercase text-[10px] font-display">Espais Segurs</p>
                    <p><?= nl2br(htmlspecialchars($map['espais_segurs'])) ?></p>
                </div>
                <div class="border border-slate-200 p-3 rounded-lg">
                    <p class="font-bold text-slate-500 uppercase text-[10px] font-display">Espais de Risc</p>
                    <p><?= nl2br(htmlspecialchars($map['espais_de_risc'])) ?></p>
                </div>
                <div class="border border-slate-200 p-3 rounded-lg">
                    <p class="font-bold text-slate-500 uppercase text-[10px] font-display">Persones de Suport</p>
                    <p><?= htmlspecialchars($map['persones_de_suport']) ?></p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Seccion 4: Seguiment -->
        <section class="space-y-3 font-body-md text-xs">
            <h2 class="text-xs font-bold bg-slate-100 p-2.5 rounded-lg uppercase tracking-wider text-slate-800 font-display">4. Historial de Seguiment</h2>
            <?php if (empty($followups)): ?>
                <p class="italic text-slate-400">No s'han registrat sessions de seguiment.</p>
            <?php else: ?>
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b text-left text-slate-500 font-display font-bold">
                            <th class="py-2">Data</th>
                            <th>Destinatari</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($followups as $f): ?>
                        <tr>
                            <td class="py-2 font-semibold"><?= date('d/m/Y', strtotime($f['session_date'])) ?></td>
                            <td class="font-bold uppercase text-[10px]"><?= $f['target_type'] ?></td>
                            <td><?= htmlspecialchars($f['notes']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Seccion 5: Cierre -->
        <section class="space-y-3 font-body-md text-xs">
            <h2 class="text-xs font-bold bg-slate-100 p-2.5 rounded-lg uppercase tracking-wider text-slate-800 font-display">5. Validació de Tancament</h2>
            <div class="border-2 border-primary p-6 rounded-xl space-y-4 font-display">
                <p>Estat actual: <span class="font-bold uppercase text-primary"><?= $case['current_phase'] ?></span></p>
                <div class="grid grid-cols-2 gap-6 pt-4">
                    <div class="border-t border-slate-300 pt-8 text-center">
                        <p class="text-[10px] font-bold uppercase text-slate-500">Firma Direcció / Coordinació</p>
                    </div>
                    <div class="border-t border-slate-300 pt-8 text-center">
                        <p class="text-[10px] font-bold uppercase text-slate-500">Segell del Centre</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="no-print pt-6 flex justify-center gap-3 font-display">
            <button onclick="window.print()" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs flex items-center gap-2 shadow-xs cursor-pointer">
                <span class="material-symbols-outlined text-base">print</span> Imprimir / Guardar PDF
            </button>
            <button onclick="window.history.back()" class="h-11 bg-surface-container-low text-on-surface-variant px-6 rounded-xl font-bold text-xs cursor-pointer">
                Tornar enrere
            </button>
        </div>
    </div>
</div>

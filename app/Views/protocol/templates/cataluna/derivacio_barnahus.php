<?php
/**
 * Plantilla Oficial: Fitxa de derivació a Barnahus (Catalunya)
 */
?>
<div class="bg-white min-h-screen p-12 text-slate-900 font-serif" id="print-area">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center border-b-2 border-black pb-4 mb-8">
            <div class="text-xs font-bold uppercase">Generalitat de Catalunya<br>Derivació de Violència Sexual</div>
            <div class="text-right text-lg font-black"><?= htmlspecialchars($schoolName) ?></div>
        </div>

        <h1 class="text-2xl font-bold text-center mb-10 underline uppercase">Fitxa de notificació i derivació a Barnahus</h1>

        <div class="space-y-8 text-sm">
            <section class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                <h2 class="font-black uppercase text-[10px] mb-2">1. Dades del centre educatiu</h2>
                <p><strong>Nom del centre:</strong> <?= htmlspecialchars($schoolName) ?></p>
                <p><strong>Codi de centre:</strong> [CONSULTAR CONFIGURACIÓ]</p>
            </section>

            <section class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                <h2 class="font-black uppercase text-[10px] mb-2">2. Dades de l'alumne/a (Víctima)</h2>
                <p><strong>Nom i cognoms:</strong> <?= htmlspecialchars($report['student_name']) ?></p>
                <p><strong>Grup:</strong> <?= htmlspecialchars($report['classroom_name']) ?></p>
            </section>

            <section class="space-y-4">
                <h2 class="font-black uppercase text-[10px]">3. Descripció dels indicadors detectats</h2>
                <div class="p-6 border-2 border-slate-900 min-h-[200px]">
                    <p class="italic">"S'han detectat indicadors de presumpte cas de violència sexual que requereixen intervenció especialitzada segons el protocol Barnahus. No s'ha realitzat cap interrogatori diagnòstic per evitar la victimització secundària."</p>
                </div>
            </section>

            <section class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <h2 class="font-black uppercase text-[10px] text-red-900 mb-2 text-center">Mesures de protecció immediates aplicades</h2>
                <div class="border-t border-red-200 pt-4 mt-2">
                    <ul class="list-disc pl-8 space-y-2">
                        <li>Activació immediata del circuit de protecció.</li>
                        <li>Notificació a la Direcció del centre.</li>
                        <li>Designació de persona referent per a l'alumne/a.</li>
                    </ul>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-2 gap-20 pt-32">
            <div class="border-t border-black text-center pt-2">
                <p class="text-[10px] font-bold">Signatura de la Direcció del centre</p>
            </div>
            <div class="border-t border-black text-center pt-2">
                <p class="text-[10px] font-bold">Segell del centre</p>
            </div>
        </div>

        <div class="no-print mt-20 flex justify-center">
            <button onclick="window.print()" class="bg-red-600 text-white px-10 py-4 rounded-full font-bold shadow-lg uppercase">Imprimir Fitxa Barnahus</button>
        </div>
    </div>
</div>

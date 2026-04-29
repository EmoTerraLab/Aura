<?php
/**
 * Plantilla Oficial: Addenda de compromís educatiu (Catalunya)
 */
?>
<div class="bg-white min-h-screen p-12 text-slate-900 font-serif" id="print-area">
    <style>
        @media print { .no-print { display: none !important; } }
        .official-header { border-bottom: 2px solid #000; margin-bottom: 2rem; padding-bottom: 1rem; }
    </style>

    <div class="max-w-4xl mx-auto">
        <div class="official-header flex justify-between items-center">
            <div class="text-xs font-bold uppercase">
                Generalitat de Catalunya<br>Departament d'Educació
            </div>
            <div class="text-right">
                <p class="text-lg font-black"><?= htmlspecialchars($schoolName) ?></p>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-center mb-10 underline uppercase">
            Addenda de continguts específics de la carta de compromís educatiu
        </h1>

        <div class="space-y-6 text-sm leading-relaxed">
            <p><strong>Dades de l'alumne/a:</strong> <?= htmlspecialchars($report['student_name']) ?></p>
            <p><strong>Grup:</strong> <?= htmlspecialchars($report['classroom_name']) ?></p>
            <p><strong>Data d'inici de les mesures:</strong> <?= date('d/m/Y', strtotime($case['created_at'])) ?></p>

            <div class="space-y-4 pt-4">
                <p>En el marc del <strong>Protocol d'actuació davant de qualsevol tipus de violència</strong>, el centre i la família acorden els següents compromisos:</p>
                
                <ul class="list-disc pl-8 space-y-2">
                    <li>El centre aplicarà les mesures de protecció i suport previstes en el pla d'intervenció.</li>
                    <li>La família es compromet a col·laborar en el seguiment de les actuacions i a comunicar qualsevol incidència rellevant.</li>
                    <li>Ambdues parts vetllaran per la confidencialitat del procés i pel benestar de l'alumnat implicat.</li>
                </ul>
            </div>

            <div class="pt-10 space-y-4">
                <p>Aquesta addenda s'incorpora a la carta de compromís educatiu del centre i serà revisada segons el calendari de seguiment establert (Fase 5).</p>
            </div>

            <div class="grid grid-cols-2 gap-20 pt-20">
                <div class="border-t border-black text-center pt-2">
                    <p class="text-[10px] font-bold">Signatura de la Direcció del centre</p>
                </div>
                <div class="border-t border-black text-center pt-2">
                    <p class="text-[10px] font-bold">Signatura dels representants legals (pare/mare/tutors)</p>
                </div>
            </div>
        </div>

        <div class="no-print mt-20 flex justify-center">
            <button onclick="window.print()" class="bg-primary text-white px-10 py-4 rounded-full font-bold shadow-lg">IMPRIMIR ADDENDA</button>
        </div>
    </div>
</div>

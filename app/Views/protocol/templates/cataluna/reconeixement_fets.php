<?php
/**
 * Plantilla Oficial: Reconeixement dels fets i acceptació de la sanció (Catalunya)
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
            Document de reconeixement dels fets i acceptació de la sanció
        </h1>

        <div class="space-y-6 text-sm leading-relaxed">
            <p>En el marc de l'expedient disciplinari o procés de gestió de conflicte derivat del Protocol de Violència (Cas #<?= $case['id'] ?>), l'alumne/a <strong>[NOM DE L'AGRESSOR/A]</strong>, assistit pels seus representants legals, exposa:</p>
            
            <ol class="list-decimal pl-8 space-y-4">
                <li>Que reconeix la veracitat dels fets tipificats com a <strong><?= strtoupper(htmlspecialchars($case['classification'] ?? 'Falta de convivència')) ?></strong>.</li>
                <li>Que accepta la mesura correctora o sanció proposada per la direcció del centre.</li>
                <li>Que manifesta la seva voluntat de participar en **pràctiques restauratives** per reparar el dany causat i millorar la convivència al centre.</li>
            </ol>

            <p class="pt-6">Aquest reconeixement permet agilitzar la resolució de l'expedient i prioritzar l'enfocament educatiu i restauratiu del protocol.</p>

            <div class="grid grid-cols-2 gap-20 pt-20">
                <div class="border-t border-black text-center pt-2">
                    <p class="text-[10px] font-bold">Signatura de l'alumne/a</p>
                </div>
                <div class="border-t border-black text-center pt-2">
                    <p class="text-[10px] font-bold">Signatura dels representants legals</p>
                </div>
            </div>

            <div class="pt-20">
                <p class="text-right text-xs">Data: <?= date('d/m/Y') ?></p>
                <div class="w-48 border-t border-black float-right mt-10 text-center">
                    <p class="text-[10px] font-bold">Segell del centre</p>
                </div>
            </div>
        </div>

        <div class="no-print mt-40 flex justify-center">
            <button onclick="window.print()" class="bg-slate-900 text-white px-10 py-4 rounded-full font-bold shadow-lg uppercase tracking-widest">Imprimir Reconeixement</button>
        </div>
    </div>
</div>

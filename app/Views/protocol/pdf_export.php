<?php
use App\Core\Lang;
?>
<div class="bg-white min-h-screen p-8 md:p-16 text-slate-900" id="print-area">
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-10">
        <!-- Header Informe -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6">
            <div>
                <h1 class="text-3xl font-black uppercase">Informe de Protocolo de Actuación</h1>
                <p class="text-slate-500 font-bold">Aura - Gestión de Convivencia</p>
            </div>
            <div class="text-right">
                <p class="font-bold">Caso #<?= $case['id'] ?></p>
                <p class="text-sm"><?= date('d/m/Y') ?></p>
            </div>
        </div>

        <!-- Seccion 1: Datos Generales -->
        <section class="space-y-4">
            <h2 class="text-lg font-black bg-slate-100 p-2 uppercase">1. Datos del Caso</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-bold text-slate-500 uppercase text-[10px]">Alumnado Implicado</p>
                    <p><?= $report['student_name'] ?> (<?= $report['classroom_name'] ?>)</p>
                </div>
                <div>
                    <p class="font-bold text-slate-500 uppercase text-[10px]">Tipificación</p>
                    <p><?= strtoupper($case['classification'] ?? 'Pendiente') ?> (<?= $case['severity_preliminary'] ?>)</p>
                </div>
            </div>
            <div class="text-sm">
                <p class="font-bold text-slate-500 uppercase text-[10px]">Relato Inicial</p>
                <p class="italic">"<?= $report['content'] ?>"</p>
            </div>
        </section>

        <!-- Seccion 2: Comunicaciones -->
        <section class="space-y-4">
            <h2 class="text-lg font-black bg-slate-100 p-2 uppercase">2. Comunicaciones Obligatorias</h2>
            <ul class="text-sm space-y-2">
                <li class="flex items-center gap-2">
                    <span class="font-bold"><?= ($case['communications']['inspeccio'] ?? false) ? '✅' : '❌' ?></span>
                    Comunicado a Inspección (REVA)
                </li>
                <li class="flex items-center gap-2">
                    <span class="font-bold"><?= ($case['communications']['familia_victima'] ?? false) ? '✅' : '❌' ?></span>
                    Comunicado a familia víctima
                </li>
                <li class="flex items-center gap-2">
                    <span class="font-bold"><?= ($case['communications']['familia_agressor'] ?? false) ? '✅' : '❌' ?></span>
                    Comunicado a familia agresor
                </li>
            </ul>
        </section>

        <!-- Seccion 3: Medidas (Mapa de Seguridad) -->
        <?php if ($map): ?>
        <section class="space-y-4">
            <h2 class="text-lg font-black bg-slate-100 p-2 uppercase">3. Mapa de Seguridad y Medidas</h2>
            <div class="grid grid-cols-1 gap-4 text-sm">
                <div class="border p-3">
                    <p class="font-bold text-slate-500 uppercase text-[10px]">Espacios Seguros</p>
                    <p><?= nl2br(htmlspecialchars($map['espais_segurs'])) ?></p>
                </div>
                <div class="border p-3">
                    <p class="font-bold text-slate-500 uppercase text-[10px]">Espacios de Riesgo</p>
                    <p><?= nl2br(htmlspecialchars($map['espais_de_risc'])) ?></p>
                </div>
                <div class="border p-3">
                    <p class="font-bold text-slate-500 uppercase text-[10px]">Personas de Apoyo</p>
                    <p><?= htmlspecialchars($map['persones_de_suport']) ?></p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Seccion 4: Seguimiento -->
        <section class="space-y-4">
            <h2 class="text-lg font-black bg-slate-100 p-2 uppercase">4. Historial de Seguimiento</h2>
            <?php if (empty($followups)): ?>
                <p class="text-sm italic text-slate-400">No se han registrado sesiones de seguimiento.</p>
            <?php else: ?>
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2">Fecha</th>
                            <th>Destinatario</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($followups as $f): ?>
                        <tr class="border-b">
                            <td class="py-2"><?= date('d/m/Y', strtotime($f['session_date'])) ?></td>
                            <td class="font-bold uppercase text-[10px]"><?= $f['target_type'] ?></td>
                            <td><?= htmlspecialchars($f['notes']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Seccion 5: Cierre -->
        <section class="space-y-4">
            <h2 class="text-lg font-black bg-slate-100 p-2 uppercase">5. Validación de Cierre</h2>
            <div class="text-sm border-2 border-slate-900 p-6 space-y-4">
                <p>Estado actual: <span class="font-black uppercase"><?= $case['current_phase'] ?></span></p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="border-t pt-10 text-center">
                        <p class="text-[10px] font-bold">Firma Dirección / Coordinación</p>
                    </div>
                    <div class="border-t pt-10 text-center">
                        <p class="text-[10px] font-bold">Sello del Centro</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="no-print pt-10 flex justify-center gap-4">
            <button onclick="window.print()" class="bg-slate-900 text-white px-8 py-3 rounded-full font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">print</span> Imprimir / Guardar PDF
            </button>
            <button onclick="window.history.back()" class="bg-slate-100 text-slate-600 px-8 py-3 rounded-full font-bold">
                Volver atrás
            </button>
        </div>
    </div>
</div>

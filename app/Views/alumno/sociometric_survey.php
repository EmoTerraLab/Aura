<?php
use App\Core\Lang;
?>
<main class="min-h-screen bg-slate-50 py-10 px-4">
    <div class="max-w-2xl mx-auto space-y-8 animate-[fadeIn_0.4s_ease-out]">
        
        <header class="text-center space-y-2">
            <div class="w-16 h-16 bg-primary/10 text-primary rounded-3xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">hub</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800"><?= $survey['title'] ?></h1>
            <p class="text-sm text-slate-500 font-medium">Les teves respostes són totalment confidencials i només les veurà el teu tutor/a.</p>
        </header>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-12 space-y-10">
            
            <!-- Pregunta 1: Afinitat Positiva -->
            <section class="space-y-4">
                <h2 class="text-lg font-black text-slate-800 leading-tight">1. Amb quins 3 companys/es t'agradaria fer un treball en grup?</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="positive-nominations">
                    <?php foreach ($classmates as $c): ?>
                        <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-primary/5 border-2 border-transparent transition-all group has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <input type="checkbox" name="positive[]" value="<?= $c['id'] ?>" class="hidden peer">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-primary peer-checked:bg-primary flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[12px] text-white hidden peer-checked:block">check</span>
                            </div>
                            <span class="text-sm font-bold text-slate-600 group-hover:text-primary transition-colors"><?= $c['name'] ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <hr class="border-slate-50">

            <!-- Pregunta 2: Afinitat Negativa -->
            <section class="space-y-4">
                <h2 class="text-lg font-black text-slate-800 leading-tight">2. Amb quins 3 companys/es NO t'agradaria asseure't al costat?</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="negative-nominations">
                    <?php foreach ($classmates as $c): ?>
                        <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-red-50 border-2 border-transparent transition-all group has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="checkbox" name="negative[]" value="<?= $c['id'] ?>" class="hidden peer">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-red-500 peer-checked:bg-red-500 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[12px] text-white hidden peer-checked:block">close</span>
                            </div>
                            <span class="text-sm font-bold text-slate-600 group-hover:text-red-600 transition-colors"><?= $c['name'] ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <hr class="border-slate-50">

            <!-- Pregunta 3: Detecció de Víctimes -->
            <section class="space-y-4">
                <h2 class="text-lg font-black text-slate-800 leading-tight">3. Creus que algun company/a ho està passant malament perquè es fiquen amb ell/a?</h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Pots marcar-ne diversos si cal</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="victim-nominations">
                    <?php foreach ($classmates as $c): ?>
                        <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-amber-50 border-2 border-transparent transition-all group has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="checkbox" name="victims[]" value="<?= $c['id'] ?>" class="hidden peer">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-amber-500 peer-checked:bg-amber-500 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[12px] text-white hidden peer-checked:block">priority_high</span>
                            </div>
                            <span class="text-sm font-bold text-slate-600 group-hover:text-amber-700 transition-colors"><?= $c['name'] ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="pt-6">
                <button onclick="submitSurvey(<?= $survey['id'] ?>)" id="btn-submit" class="w-full py-5 bg-primary text-white rounded-full font-black shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Enviar respostes
                </button>
            </div>

        </div>
    </div>
</main>

<script>
async function submitSurvey(surveyId) {
    const btn = document.getElementById('btn-submit');
    const positive = Array.from(document.querySelectorAll('input[name="positive[]"]:checked')).map(i => i.value);
    const negative = Array.from(document.querySelectorAll('input[name="negative[]"]:checked')).map(i => i.value);
    const victims = Array.from(document.querySelectorAll('input[name="victims[]"]:checked')).map(i => i.value);

    if (positive.length > 3 || negative.length > 3) {
        alert('Si us plau, selecciona un màxim de 3 companys per a les preguntes 1 i 2.');
        return;
    }

    btn.disabled = true;
    btn.innerText = 'Enviant...';

    try {
        const res = await fetch('/api/sociometric/respond', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ survey_id: surveyId, positive, negative, victims })
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = '/alumno/dashboard?survey_success=1';
        } else {
            alert('Error: ' + data.error);
            btn.disabled = false;
            btn.innerText = 'Enviar respostes';
        }
    } catch (e) {
        alert('Error de connexió.');
        btn.disabled = false;
    }
}
</script>

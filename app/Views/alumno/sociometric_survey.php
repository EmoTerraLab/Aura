<?php
use App\Core\Lang;
$bodyClass = "min-h-screen bg-surface font-body-md text-on-surface";
?>
<main class="min-h-screen bg-surface py-8 md:py-12 px-4">
    <div class="max-w-2xl mx-auto space-y-6 animate-fadeIn">
        
        <header class="text-center space-y-2">
            <a href="/alumno/dashboard" class="inline-block mb-2 transition-transform hover:scale-105">
                <img src="<?= BASE_URL ?>assets/prisma-logo.png" alt="Prisma" class="h-8 md:h-10 w-auto mx-auto object-contain">
            </a>
            <div class="w-14 h-14 bg-prisma-lavender/30 text-purple-900 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-sm border border-prisma-lavender/40">
                <span class="material-symbols-outlined text-3xl">hub</span>
            </div>
            <h1 class="text-xl md:text-2xl font-display font-bold text-on-surface tracking-tight"><?= htmlspecialchars($survey['title']) ?></h1>
            <p class="text-xs md:text-sm text-on-surface-variant font-medium">Tus respuestas son totalmente confidenciales y solo las verá el equipo de orientación.</p>
        </header>

        <div class="bg-surface-container-lowest rounded-2xl shadow-card border border-surface-variant/40 p-6 md:p-8 space-y-8">
            
            <!-- Pregunta 1: Afinidad Positiva -->
            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-prisma-mint/30 text-teal-900 font-display font-bold text-xs flex items-center justify-center">1</span>
                    <h2 class="text-sm md:text-base font-display font-bold text-on-surface leading-tight">¿Con qué 3 compañeros/as te gustaría hacer un trabajo en grupo?</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="positive-nominations">
                    <?php foreach ($classmates as $c): ?>
                        <label class="flex items-center gap-3 p-3.5 bg-surface-container-low rounded-xl cursor-pointer hover:bg-prisma-mint/15 border border-surface-variant/30 transition-all group has-[:checked]:border-teal-600 has-[:checked]:bg-prisma-mint/20">
                            <input type="checkbox" name="positive[]" value="<?= $c['id'] ?>" class="hidden peer">
                            <div class="w-5 h-5 rounded-lg border-2 border-outline-variant peer-checked:border-teal-700 peer-checked:bg-teal-700 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[14px] text-white hidden peer-checked:block">check</span>
                            </div>
                            <span class="text-xs md:text-sm font-semibold text-on-surface group-hover:text-teal-900 transition-colors"><?= htmlspecialchars($c['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <hr class="border-surface-variant/30">

            <!-- Pregunta 2: Afinidad Negativa -->
            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-red-100 text-red-900 font-display font-bold text-xs flex items-center justify-center">2</span>
                    <h2 class="text-sm md:text-base font-display font-bold text-on-surface leading-tight">¿Con qué 3 compañeros/as NO te gustaría sentarte al lado?</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="negative-nominations">
                    <?php foreach ($classmates as $c): ?>
                        <label class="flex items-center gap-3 p-3.5 bg-surface-container-low rounded-xl cursor-pointer hover:bg-red-50 border border-surface-variant/30 transition-all group has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="checkbox" name="negative[]" value="<?= $c['id'] ?>" class="hidden peer">
                            <div class="w-5 h-5 rounded-lg border-2 border-outline-variant peer-checked:border-red-600 peer-checked:bg-red-600 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[14px] text-white hidden peer-checked:block">close</span>
                            </div>
                            <span class="text-xs md:text-sm font-semibold text-on-surface group-hover:text-red-700 transition-colors"><?= htmlspecialchars($c['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <hr class="border-surface-variant/30">

            <!-- Pregunta 3: Detección de Víctimas -->
            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-900 font-display font-bold text-xs flex items-center justify-center">3</span>
                    <h2 class="text-sm md:text-base font-display font-bold text-on-surface leading-tight">¿Crees que algún compañero/a lo está pasando mal porque se meten con él/ella?</h2>
                </div>
                <p class="text-[11px] text-on-surface-variant font-medium">Puedes marcar a varios compañeros si lo crees necesario</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="victim-nominations">
                    <?php foreach ($classmates as $c): ?>
                        <label class="flex items-center gap-3 p-3.5 bg-surface-container-low rounded-xl cursor-pointer hover:bg-amber-50 border border-surface-variant/30 transition-all group has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="checkbox" name="victims[]" value="<?= $c['id'] ?>" class="hidden peer">
                            <div class="w-5 h-5 rounded-lg border-2 border-outline-variant peer-checked:border-amber-600 peer-checked:bg-amber-600 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-[14px] text-white hidden peer-checked:block">priority_high</span>
                            </div>
                            <span class="text-xs md:text-sm font-semibold text-on-surface group-hover:text-amber-800 transition-colors"><?= htmlspecialchars($c['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="pt-4">
                <button onclick="submitSurvey(<?= $survey['id'] ?>)" id="btn-submit" class="w-full h-12 bg-primary text-on-primary rounded-xl font-display font-bold text-sm shadow-sm hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>Enviar respuestas</span>
                    <span class="material-symbols-outlined text-[18px]">send</span>
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
        alert('Por favor, selecciona un máximo de 3 compañeros para las preguntas 1 y 2.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> Enviando...';

    try {
        const res = await fetchJson('/api/sociometric/respond', {
            method: 'POST',
            body: { survey_id: surveyId, positive, negative, victims }
        });
        
        if (res.success) {
            window.location.href = '/alumno/dashboard?survey_success=1';
        } else {
            alert('Error: ' + (res.error || 'No se pudo guardar la respuesta'));
            btn.disabled = false;
            btn.innerHTML = '<span>Enviar respuestas</span><span class="material-symbols-outlined text-[18px]">send</span>';
        }
    } catch (e) {
        alert('Error de conexión con el servidor.');
        btn.disabled = false;
        btn.innerHTML = '<span>Enviar respuestas</span><span class="material-symbols-outlined text-[18px]">send</span>';
    }
}
</script>

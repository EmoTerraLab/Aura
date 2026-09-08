<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-prisma-mint/20 via-surface to-background"></div>

<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-lg mx-auto">
    <!-- Card -->
    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 relative overflow-hidden">
        <div class="flex flex-col items-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-prisma-mint/20 text-teal-800 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <h1 class="text-xl md:text-2xl font-display font-bold text-on-surface text-center">¡Verificación activada!</h1>
            <p class="text-xs md:text-sm text-on-surface-variant text-center mt-1">Tu cuenta ahora está protegida con verificación en dos pasos.</p>
        </div>

        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 mb-6">
            <p class="text-xs font-bold text-amber-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-lg">warning</span>
                <span>¡Guarda estos códigos ahora! No volverás a verlos.</span>
            </p>
            <p class="text-[11px] text-amber-800/90 mt-1 pl-6 leading-relaxed">Si pierdes el acceso a tu app autenticadora, podrás usar cada código una sola vez para iniciar sesión de forma segura.</p>
        </div>
        
        <div class="bg-surface-container-low border border-surface-variant/40 rounded-xl p-4 md:p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <?php foreach($recoveryCodes as $i => $code): ?>
                    <div class="flex items-center gap-2 bg-surface-container-lowest p-2.5 rounded-lg border border-surface-variant/30">
                        <span class="text-on-surface-variant/60 font-mono text-[11px] w-4"><?= $i + 1 ?>.</span>
                        <code class="font-mono text-primary font-bold text-xs tracking-widest flex-1 text-center select-all"><?= $code ?></code>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4 flex justify-center">
                <button type="button" onclick="window.print()" class="text-xs font-display font-semibold text-on-surface-variant hover:text-on-surface flex items-center gap-1.5 border border-outline-variant/40 rounded-lg px-3.5 py-2 hover:bg-surface-container-high transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-sm">print</span> Imprimir códigos
                </button>
            </div>
        </div>

        <a href="/staff/inbox" class="block w-full h-11 bg-primary text-on-primary font-display font-bold text-sm rounded-xl shadow-sm hover:bg-primary/90 active:scale-[0.99] transition-all text-center flex items-center justify-center">
            He guardado los códigos, continuar
        </a>
    </div>
</main>

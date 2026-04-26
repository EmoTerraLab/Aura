<main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-lg mx-auto mt-10 mb-10">
    <div class="w-full bg-surface-container-lowest rounded-3xl p-8 shadow-2xl relative overflow-hidden border border-surface-variant/50">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-primary via-secondary to-tertiary-container"></div>
        
        <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 rounded-full bg-green-100 text-green-700 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <h2 class="text-2xl font-black text-on-surface text-center">¡Verificación activada!</h2>
        </div>

        <p class="text-sm text-slate-600 mb-6 text-center">Tu cuenta ahora está protegida con verificación en dos pasos. A continuación, encontrarás 8 códigos de recuperación de emergencia.</p>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
            <p class="text-sm font-bold text-amber-900 flex items-start gap-2">
                <span class="material-symbols-outlined text-amber-600 shrink-0">warning</span>
                <span>¡Guarda estos códigos ahora! No volverás a verlos.</span>
            </p>
            <p class="text-xs text-amber-800 mt-2 pl-8">Si pierdes el acceso a tu aplicación de autenticación, podrás usar cada uno de estos códigos una sola vez para iniciar sesión de forma segura.</p>
        </div>
        
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach($recoveryCodes as $i => $code): ?>
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                        <span class="text-slate-400 font-bold text-xs w-4"><?= $i + 1 ?>.</span>
                        <code class="font-mono text-primary font-bold tracking-widest flex-1 text-center"><?= $code ?></code>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-6 flex justify-center">
                <button type="button" onclick="window.print()" class="text-sm font-bold text-slate-500 hover:text-slate-700 flex items-center gap-2 border border-slate-300 rounded-full px-4 py-2 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">print</span> Imprimir códigos
                </button>
            </div>
        </div>

        <a href="/staff/inbox" class="block w-full bg-primary text-white font-bold py-4 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform text-center">
            He guardado los códigos, continuar
        </a>
    </div>
</main>

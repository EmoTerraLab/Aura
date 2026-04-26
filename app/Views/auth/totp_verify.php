<main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-md mx-auto mt-20">
    <div class="w-full bg-surface-container-lowest rounded-xl p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden">
        <h2 class="text-2xl font-bold text-primary mb-4 text-center">Verificación en Dos Pasos</h2>
        <p class="text-sm text-slate-500 mb-6 text-center">Introduce el código de 6 dígitos generado por tu aplicación autenticadora (Google Authenticator, FreeOTP, etc).</p>
        
        <?php if(isset($_GET['error'])): ?>
            <p class="text-error text-center text-sm font-bold bg-error/10 p-2 rounded-lg mb-4">Código inválido o expirado.</p>
        <?php endif; ?>

        <form method="POST" action="/auth/2fa/totp/verify" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            
            <div id="totp-code-section">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-4 text-center">Código de verificación</label>
                <input type="text" name="totp_code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" autofocus class="w-full bg-surface-variant text-on-surface font-mono text-center text-3xl tracking-[0.5em] rounded-full py-4 px-6 border-0 focus:ring-2 focus:ring-primary/30 outline-none transition-shadow" placeholder="000000">
            </div>

            <div class="pt-4 border-t border-surface-variant/50">
                <details class="group">
                    <summary class="cursor-pointer text-sm text-slate-500 hover:text-primary font-medium text-center list-none flex items-center justify-center gap-2">
                        ¿No tienes acceso a tu app?
                        <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-4 space-y-2 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Usa un código de recuperación</label>
                        <input type="text" name="recovery_code" placeholder="XXXX-XXXX" class="w-full bg-white font-mono text-center tracking-widest rounded-lg py-2 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/30 outline-none uppercase">
                        <p class="text-[10px] text-slate-400 leading-tight">Introduce uno de los 8 códigos de emergencia que guardaste al activar la verificación en dos pasos.</p>
                    </div>
                </details>
            </div>

            <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Verificar Acceso</button>
            <a href="/login" class="block text-center text-sm text-slate-500 hover:text-slate-700 mt-2">Volver al Login</a>
        </form>
    </div>
</main>
<script>
    // UX enhancement: Select the appropriate input field based on which one is being typed in
    document.querySelector('input[name="totp_code"]').addEventListener('input', function(e) {
        document.querySelector('input[name="recovery_code"]').value = '';
    });
    document.querySelector('input[name="recovery_code"]').addEventListener('input', function(e) {
        document.querySelector('input[name="totp_code"]').value = '';
    });
</script>
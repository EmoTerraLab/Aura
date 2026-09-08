<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-prisma-sky/20 via-surface to-background"></div>

<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 md:mb-8 gap-3">
        <a href="/login" class="inline-block transition-transform hover:scale-[1.02] active:scale-[0.98]">
            <img src="<?= BASE_URL ?>assets/prisma-logo.png" alt="Prisma" class="h-10 md:h-12 w-auto object-contain">
        </a>
        <h1 class="font-display font-bold text-xl md:text-2xl text-on-surface tracking-tight text-center">Verificación en Dos Pasos</h1>
        <p class="text-xs md:text-sm text-on-surface-variant text-center max-w-xs">Introduce el código de 6 dígitos generado por tu aplicación autenticadora.</p>
    </div>

    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 relative overflow-hidden">
        <?php if(isset($_GET['error'])): ?>
            <div class="mb-5 p-3.5 bg-error/10 border-l-4 border-error rounded-r-xl flex items-center gap-2 text-error text-xs font-semibold">
                <span class="material-symbols-outlined text-sm">error</span>
                <span>Código inválido o expirado. Inténtalo de nuevo.</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/2fa/totp/verify" class="flex flex-col gap-5">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            
            <div id="totp-code-section" class="flex flex-col gap-2">
                <label class="font-display text-xs font-bold uppercase tracking-wider text-on-surface-variant text-center">Código de verificación</label>
                <input type="text" name="totp_code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" autofocus class="w-full h-14 bg-surface-container-low text-on-surface font-mono text-center text-2xl md:text-3xl tracking-[0.4em] rounded-xl border border-outline-variant/30 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-outline-variant" placeholder="000000">
            </div>

            <div class="pt-3 border-t border-surface-variant/30">
                <details class="group">
                    <summary class="cursor-pointer text-xs text-on-surface-variant hover:text-on-surface font-medium text-center list-none flex items-center justify-center gap-1.5 transition-colors">
                        <span>¿No tienes acceso a tu app?</span>
                        <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 flex flex-col gap-2 p-3.5 bg-surface-container-low rounded-xl border border-surface-variant/40">
                        <label class="font-display text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Código de recuperación</label>
                        <input type="text" name="recovery_code" placeholder="XXXX-XXXX" class="w-full h-10 bg-surface-container-lowest font-mono text-center tracking-widest rounded-lg px-3 border border-outline-variant/30 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none uppercase text-xs">
                        <p class="text-[11px] text-on-surface-variant/70 leading-tight">Introduce uno de los códigos de emergencia que guardaste al activar 2FA.</p>
                        
                        <?php if($hasWebAuthn ?? false): ?>
                            <div class="pt-3 mt-1 border-t border-surface-variant/30">
                                <a href="/auth/2fa/webauthn/switch" class="flex items-center justify-center gap-2 text-primary hover:text-primary/80 font-display font-bold text-xs py-1.5">
                                    <span class="material-symbols-outlined text-base">fingerprint</span>
                                    Usar biometría (Face ID / Huella)
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </details>
            </div>

            <button type="submit" class="w-full h-11 bg-primary text-on-primary font-display font-bold text-sm rounded-xl shadow-sm hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Verificar Acceso</span>
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
            </button>
            <a href="/login" class="font-display text-xs font-semibold text-on-surface-variant hover:text-on-surface text-center transition-colors">Volver al inicio de sesión</a>
        </form>
    </div>
</main>
<script>
    document.querySelector('input[name="totp_code"]')?.addEventListener('input', function(e) {
        const rec = document.querySelector('input[name="recovery_code"]');
        if (rec) rec.value = '';
    });
    document.querySelector('input[name="recovery_code"]')?.addEventListener('input', function(e) {
        const totp = document.querySelector('input[name="totp_code"]');
        if (totp) totp.value = '';
    });
</script>
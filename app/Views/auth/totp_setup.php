<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-prisma-lavender/20 via-surface to-background"></div>

<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 md:mb-8 gap-3">
        <a href="/staff/inbox" class="inline-block transition-transform hover:scale-[1.02] active:scale-[0.98]">
            <img src="<?= BASE_URL ?>assets/prisma-logo.png" alt="Prisma" class="h-10 md:h-12 w-auto object-contain">
        </a>
        <h1 class="font-display font-bold text-xl md:text-2xl text-on-surface tracking-tight text-center">Configurar 2FA (TOTP)</h1>
        <p class="text-xs md:text-sm text-on-surface-variant text-center max-w-xs">Escanea este código QR con tu aplicación autenticadora.</p>
    </div>

    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 relative overflow-hidden">
        <div class="flex justify-center mb-5">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-surface-variant/40">
                <?= $qrCodeSvg ?>
            </div>
        </div>
        
        <p class="text-xs text-center text-on-surface-variant mb-5">
            Si no puedes escanearlo, introduce esta clave:<br>
            <code class="inline-block mt-1 font-mono text-primary font-bold bg-surface-container-low px-2.5 py-1 rounded-lg text-xs border border-surface-variant/30 select-all"><?= htmlspecialchars($secret) ?></code>
        </p>

        <form method="POST" action="/profile/2fa/totp/activate" class="flex flex-col gap-4">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <div class="flex flex-col gap-1.5">
                <label class="font-display text-xs font-bold uppercase tracking-wider text-on-surface-variant text-center">Código de verificación</label>
                <input type="text" name="code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" autofocus class="w-full h-12 bg-surface-container-low text-on-surface font-mono text-center text-2xl tracking-[0.4em] rounded-xl border border-outline-variant/30 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-outline-variant" placeholder="000000" required>
            </div>
            <button type="submit" class="w-full h-11 bg-primary text-on-primary font-display font-bold text-sm rounded-xl shadow-sm hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Verificar y Activar</span>
                <span class="material-symbols-outlined text-[18px]">verified</span>
            </button>
            <a href="/staff/inbox" class="font-display text-xs font-semibold text-on-surface-variant hover:text-on-surface text-center transition-colors">Cancelar</a>
        </form>
    </div>
</main>

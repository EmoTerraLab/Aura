<?php 
$bodyClass = "bg-prisma-cloud text-text-primary font-sans min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Gradient Accents -->
<div class="absolute inset-0 z-0 pointer-events-none bg-gradient-to-b from-[#F4F7FD] via-[#F8FAFC] to-[#EDF8FF]"></div>
<div class="absolute -top-[15%] -left-[10%] w-[50%] h-[50%] rounded-full bg-prisma-sky/25 blur-[120px] z-0 pointer-events-none"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto my-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 gap-3">
        <a href="/login" class="h-12 flex items-center justify-center">
            <img src="<?= BASE_URL ?>assets/prisma-logo.png" 
                 onerror="this.onerror=null; this.src='https://prisma.emoterralab.com/assets/prisma-logo.png';" 
                 alt="Prisma" 
                 class="h-full w-auto max-h-12 object-contain">
        </a>
        <h1 class="font-display text-xl font-bold text-gray-900 tracking-tight text-center">
            <?= \App\Core\Lang::t('auth.forgot_password_title') ?>
        </h1>
    </div>

    <!-- Card -->
    <div class="w-full bg-white rounded-2xl p-6 md:p-8 border border-gray-200 shadow-md relative overflow-hidden">
        
        <?php if (isset($_GET['sent'])): ?>
            <div class="flex flex-col items-center text-center gap-5 animate-[fadeIn_0.25s_ease-out]">
                <div class="w-14 h-14 bg-success-bg text-success rounded-full flex items-center justify-center border border-success/20">
                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <h2 class="font-display text-lg text-gray-900 font-bold">Enlace enviado</h2>
                    <p class="font-sans text-xs text-gray-600 leading-relaxed max-w-xs"><?= \App\Core\Lang::t('auth.reset_link_sent') ?></p>
                </div>
                <a href="/login" class="w-full bg-prisma-charcoal text-white font-display text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition-all text-center min-h-[44px] flex items-center justify-center">
                    Volver al inicio de sesión
                </a>
            </div>
        <?php else: ?>
            <p class="font-sans text-xs text-gray-500 text-center mb-6 leading-relaxed">
                <?= \App\Core\Lang::t('auth.forgot_password_desc') ?>
            </p>

            <form method="POST" action="/password/forgot" class="flex flex-col gap-5">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
                
                <div class="flex flex-col gap-1.5">
                    <label class="font-display text-xs font-bold text-gray-700 ml-1" for="email">
                        <?= \App\Core\Lang::t('auth.staff_email') ?>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl pointer-events-none">mail</span>
                        <input class="w-full bg-white text-gray-900 font-sans text-sm rounded-xl py-3 pl-11 pr-4 border border-gray-300 focus:border-info focus:ring-4 focus:ring-prisma-sky/30 transition-all outline-none placeholder:text-gray-400 h-11" 
                               id="email" 
                               name="email" 
                               placeholder="<?= \App\Core\Lang::t('auth.email_placeholder') ?>" 
                               type="email" 
                               required 
                               autofocus/>
                    </div>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="text-danger bg-danger-bg border border-danger/20 text-xs text-center py-2.5 px-3 rounded-xl">
                        <?= htmlspecialchars($_GET['error'] === 'invalid_email' ? 'El formato del email no es válido.' : ($_GET['error'] === 'rate_limit' ? 'Demasiados intentos. Espera unos minutos.' : 'Ha ocurrido un error inesperado.'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <button class="w-full bg-prisma-charcoal text-white font-display text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition-all shadow-sm flex items-center justify-center gap-2 group min-h-[44px]" type="submit">
                    <span><?= \App\Core\Lang::t('auth.reset_submit') ?></span>
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">send</span>
                </button>

                <div class="flex justify-center mt-1">
                    <a href="/login" class="font-sans text-xs text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Volver al inicio de sesión
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

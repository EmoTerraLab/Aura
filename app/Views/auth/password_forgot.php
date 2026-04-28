<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-secondary-container/30 via-surface to-background"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 md:mb-10 gap-4">
        <div class="w-20 h-20 flex items-center justify-center drop-shadow-sm">
            <img src="<?= BASE_URL ?>icono-sinfondo.png" alt="Aura Logo" class="w-full h-full object-contain">
        </div>
        <h1 class="font-h1 text-h2 text-primary-container tracking-tight"><?= \App\Core\Lang::t('auth.forgot_password_title') ?></h1>
    </div>

    <!-- Card -->
    <div class="w-full bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden">
        
        <?php if (isset($_GET['sent'])): ?>
            <div class="flex flex-col items-center text-center gap-6 animate-[fadeIn_0.3s_ease-out]">
                <div class="w-16 h-16 bg-success/10 text-success rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                </div>
                <div class="flex flex-col gap-2">
                    <h2 class="font-h2 text-xl text-on-surface font-bold"><?= \App\Core\Lang::t('auth.reset_link_sent_title') ?></h2>
                    <p class="font-body-md text-on-surface-variant"><?= \App\Core\Lang::t('auth.reset_link_sent') ?></p>
                </div>
                <a href="/login" class="w-full bg-primary text-on-primary font-body-lg font-semibold py-4 rounded-full shadow-sm hover:bg-primary/90 transition-all text-center">
                    <?= \App\Core\Lang::t('auth.back_to_login') ?>
                </a>
            </div>
        <?php else: ?>
            <p class="font-body-md text-on-surface-variant text-center mb-8"><?= \App\Core\Lang::t('auth.forgot_password_desc') ?></p>

            <form method="POST" action="/password/forgot" class="flex flex-col gap-6">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
                
                <div class="flex flex-col gap-2">
                    <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="email"><?= \App\Core\Lang::t('auth.staff_email') ?></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">mail</span>
                        <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="email" name="email" placeholder="<?= \App\Core\Lang::t('auth.email_placeholder') ?>" type="email" required autofocus/>
                    </div>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="text-error text-sm px-4 text-center italic">
                        <?= $_GET['error'] === 'invalid_email' ? 'El formato del email no es válido.' : 'Ha ocurrido un error inesperado.' ?>
                    </p>
                <?php endif; ?>

                <button class="mt-2 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit">
                    <?= \App\Core\Lang::t('auth.reset_submit') ?>
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">send</span>
                </button>

                <div class="flex justify-center mt-2">
                    <a href="/login" class="font-body-md text-[14px] text-surface-tint hover:underline decoration-surface-tint/50 underline-offset-4 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <?= \App\Core\Lang::t('auth.back_to_login') ?>
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

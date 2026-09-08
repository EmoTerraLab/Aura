<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-prisma-sky/20 via-surface to-background"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 md:mb-8 gap-3">
        <a href="/login" class="inline-block transition-transform hover:scale-[1.02] active:scale-[0.98]">
            <img src="<?= BASE_URL ?>assets/prisma-logo.png" alt="Prisma" class="h-10 md:h-12 w-auto object-contain">
        </a>
    </div>

    <!-- Card -->
    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 relative overflow-hidden text-center flex flex-col gap-6">
        <div class="w-16 h-16 bg-error/10 text-error rounded-2xl flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">link_off</span>
        </div>
        
        <div class="flex flex-col gap-2">
            <h1 class="font-display font-bold text-xl text-on-surface tracking-tight"><?= \App\Core\Lang::t('auth.reset_link_invalid') ?></h1>
            <p class="text-sm text-on-surface-variant leading-relaxed"><?= \App\Core\Lang::t('auth.reset_link_expired_desc') ?></p>
        </div>
        
        <a href="/password/forgot" class="w-full h-11 bg-primary text-on-primary font-display font-bold text-sm rounded-xl shadow-sm hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
            <?= \App\Core\Lang::t('auth.forgot_password_title') ?>
        </a>
        
        <a href="/login" class="font-display text-xs font-semibold text-primary hover:underline underline-offset-4 flex items-center justify-center gap-1.5 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Volver al inicio de sesión
        </a>
    </div>
</main>

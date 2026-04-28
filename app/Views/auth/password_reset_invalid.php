<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-secondary-container/30 via-surface to-background"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Card -->
    <div class="w-full bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden text-center flex flex-col gap-6">
        <div class="w-20 h-20 bg-error/10 text-error rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-4xl">link_off</span>
        </div>
        
        <div class="flex flex-col gap-2">
            <h1 class="font-h1 text-h2 text-error tracking-tight"><?= \App\Core\Lang::t('auth.reset_link_invalid') ?></h1>
            <p class="font-body-md text-on-surface-variant"><?= \App\Core\Lang::t('auth.reset_link_expired_desc') ?></p>
        </div>
        
        <a href="/password/forgot" class="w-full bg-primary text-on-primary font-body-lg font-semibold py-4 rounded-full shadow-sm hover:bg-primary/90 transition-all">
            <?= \App\Core\Lang::t('auth.forgot_password_title') ?>
        </a>
        
        <a href="/login" class="font-body-md text-[14px] text-surface-tint hover:underline decoration-surface-tint/50 underline-offset-4 flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Volver al inicio
        </a>
    </div>
</main>

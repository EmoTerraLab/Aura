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
        <h1 class="font-h1 text-h2 text-primary-container tracking-tight"><?= \App\Core\Lang::t('auth.reset_new_password') ?></h1>
    </div>

    <!-- Card -->
    <div class="w-full bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden">
        
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-error/10 border-l-4 border-error rounded-r-lg">
                <ul class="text-error text-sm flex flex-col gap-1">
                    <?php foreach ($errors as $error): ?>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-xs">error</span>
                            <?= $error ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/password/reset" class="flex flex-col gap-6">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
            
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="password"><?= \App\Core\Lang::t('auth.reset_new_password') ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock_reset</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="password" name="password" type="password" required autofocus minlength="8" placeholder="••••••••"/>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="password_confirm"><?= \App\Core\Lang::t('auth.reset_confirm_password') ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock_check</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="password_confirm" name="password_confirm" type="password" required minlength="8" placeholder="••••••••"/>
                </div>
            </div>
            
            <button class="mt-2 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit">
                <?= \App\Core\Lang::t('auth.reset_submit') ?>
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">update</span>
            </button>
        </form>
    </div>
</main>

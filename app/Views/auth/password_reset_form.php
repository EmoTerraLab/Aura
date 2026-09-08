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
        <h1 class="font-display font-bold text-xl md:text-2xl text-on-surface tracking-tight text-center"><?= \App\Core\Lang::t('auth.reset_new_password') ?></h1>
    </div>

    <!-- Card -->
    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 relative overflow-hidden">
        
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-error/10 border-l-4 border-error rounded-r-xl">
                <ul class="text-error text-sm flex flex-col gap-1.5 font-medium">
                    <?php foreach ($errors as $error): ?>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">error</span>
                            <?= $error ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/password/reset" class="flex flex-col gap-5">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
            
            <div class="flex flex-col gap-1.5">
                <label class="font-display text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="password"><?= \App\Core\Lang::t('auth.reset_new_password') ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant text-[20px]">lock_reset</span>
                    <input class="w-full h-11 bg-surface-container-low text-on-surface text-sm font-medium rounded-xl py-2.5 pl-11 pr-4 border border-outline-variant/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none placeholder:text-outline-variant" id="password" name="password" type="password" required autofocus minlength="8" placeholder="••••••••"/>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-display text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="password_confirm"><?= \App\Core\Lang::t('auth.reset_confirm_password') ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant text-[20px]">lock_check</span>
                    <input class="w-full h-11 bg-surface-container-low text-on-surface text-sm font-medium rounded-xl py-2.5 pl-11 pr-4 border border-outline-variant/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none placeholder:text-outline-variant" id="password_confirm" name="password_confirm" type="password" required minlength="8" placeholder="••••••••"/>
                </div>
            </div>
            
            <button class="mt-2 w-full h-11 bg-primary text-on-primary font-display font-bold text-sm rounded-xl shadow-sm hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 group cursor-pointer" type="submit">
                <span><?= \App\Core\Lang::t('auth.reset_submit') ?></span>
                <span class="material-symbols-outlined text-[18px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
            </button>
        </form>
    </div>
</main>

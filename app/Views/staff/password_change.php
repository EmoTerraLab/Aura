<main class="max-w-xl mx-auto p-6 md:p-12">
    <div class="mb-8">
        <a href="/staff/inbox" class="inline-flex items-center gap-2 text-surface-tint hover:underline mb-6">
            <span class="material-symbols-outlined">arrow_back</span>
            <?= \App\Core\Lang::t('nav.back') ?>
        </a>
        <h1 class="font-h1 text-h1 text-primary-container mb-2"><?= \App\Core\Lang::t('auth.change_password') ?></h1>
        <p class="text-on-surface-variant"><?= \App\Core\Lang::t('auth.change_password_desc') ?? 'Actualiza tu contraseña para mantener tu cuenta segura.' ?></p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-error-container text-on-error-container rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <p>
                <?php 
                switch($_GET['error']) {
                    case 'current_invalid': echo \App\Core\Lang::t('auth.error_current_password') ?? 'La contraseña actual no es correcta.'; break;
                    case 'mismatch': echo \App\Core\Lang::t('auth.error_password_mismatch') ?? 'Las contraseñas no coinciden.'; break;
                    case 'too_short': echo \App\Core\Lang::t('auth.error_password_too_short') ?? 'La contraseña es demasiado corta (mínimo 8 caracteres).'; break;
                    default: echo \App\Core\Lang::t('auth.error_generic') ?? 'Ha ocurrido un error.';
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
        <form action="/profile/password" method="POST" class="flex flex-col gap-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="current_password"><?= \App\Core\Lang::t('auth.current_password') ?? 'Contraseña Actual' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock_open</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none" id="current_password" name="current_password" type="password" required/>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="new_password"><?= \App\Core\Lang::t('auth.new_password') ?? 'Nueva Contraseña' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none" id="new_password" name="new_password" type="password" minlength="8" required/>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="confirm_password"><?= \App\Core\Lang::t('auth.confirm_password') ?? 'Confirmar Nueva Contraseña' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock_reset</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none" id="confirm_password" name="confirm_password" type="password" minlength="8" required/>
                </div>
            </div>

            <button class="mt-4 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-sm hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit">
                <?= \App\Core\Lang::t('auth.update_password_btn') ?? 'Actualizar Contraseña' ?>
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">save</span>
            </button>
        </form>
    </div>
</main>

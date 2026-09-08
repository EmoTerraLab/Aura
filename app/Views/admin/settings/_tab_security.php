<h2 class="text-base md:text-lg font-bold text-on-surface mb-5 font-display">Seguridad y Autenticación</h2>
<form method="POST" action="/admin/settings/security" class="space-y-5 font-display">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-body-md">
        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Idioma por Defecto</label>
            <select name="default_lang" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm font-display">
                <?php foreach(\App\Core\Lang::supported() as $code): ?>
                    <option value="<?= $code ?>" <?= ($settings['default_lang'] ?? '') === $code ? 'selected' : '' ?>><?= \App\Core\Lang::t('lang.'.$code) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Duración de Sesión (Minutos)</label>
            <input type="number" name="session_lifetime_minutes" value="<?= htmlspecialchars($settings['session_lifetime_minutes'] ?? '120') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">2FA Alumnos</label>
            <select name="2fa_students_method" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm font-display">
                <option value="otp_email" <?= ($settings['2fa_students_method'] ?? '') === 'otp_email' ? 'selected' : '' ?>>Código OTP (Email)</option>
                <option value="webauthn" <?= ($settings['2fa_students_method'] ?? '') === 'webauthn' ? 'selected' : '' ?>>WebAuthn (FaceID/Huella)</option>
            </select>
            <p class="text-[10px] text-on-surface-variant/70">WebAuthn requiere conexión HTTPS segura.</p>
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">2FA Staff & Admin</label>
            <select name="2fa_staff_method" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm font-display">
                <option value="none" <?= ($settings['2fa_staff_method'] ?? '') === 'none' ? 'selected' : '' ?>>Desactivado</option>
                <option value="totp" <?= ($settings['2fa_staff_method'] ?? '') === 'totp' ? 'selected' : '' ?>>App TOTP (Google Auth/FreeOTP)</option>
            </select>
            <p class="text-[10px] text-on-surface-variant/70">Los usuarios de Staff deben activarlo en su perfil.</p>
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Intentos Máx. de Login</label>
            <input type="number" name="max_login_attempts" value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>
    </div>

    <div class="pt-3 border-t border-surface-variant/30 flex justify-end">
        <button type="submit" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer">Guardar Cambios</button>
    </div>
</form>
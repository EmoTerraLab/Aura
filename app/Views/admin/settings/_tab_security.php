<h2 class="text-xl font-bold text-on-surface mb-6">Seguridad y Autenticación</h2>
<form method="POST" action="/admin/settings/security" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Idioma por Defecto</label>
            <select name="default_lang" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
                <?php foreach(\App\Core\Lang::supported() as $code): ?>
                    <option value="<?= $code ?>" <?= ($settings['default_lang'] ?? '') === $code ? 'selected' : '' ?>><?= \App\Core\Lang::t('lang.'.$code) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Duración de Sesión (Minutos)</label>
            <input type="number" name="session_lifetime_minutes" value="<?= htmlspecialchars($settings['session_lifetime_minutes'] ?? '120') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">2FA Alumnos</label>
            <select name="2fa_students_method" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="otp_email" <?= ($settings['2fa_students_method'] ?? '') === 'otp_email' ? 'selected' : '' ?>>Código OTP (Email)</option>
                <option value="webauthn" <?= ($settings['2fa_students_method'] ?? '') === 'webauthn' ? 'selected' : '' ?>>WebAuthn (FaceID/Huella)</option>
            </select>
            <p class="text-[10px] text-slate-400">WebAuthn requiere que tu servidor use HTTPS.</p>
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">2FA Staff & Admin</label>
            <select name="2fa_staff_method" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
                <option value="none" <?= ($settings['2fa_staff_method'] ?? '') === 'none' ? 'selected' : '' ?>>Desactivado</option>
                <option value="totp" <?= ($settings['2fa_staff_method'] ?? '') === 'totp' ? 'selected' : '' ?>>App TOTP (Google Auth/FreeOTP)</option>
            </select>
            <p class="text-[10px] text-slate-400">Los usuarios de Staff deben configurarlo manualmente en su perfil.</p>
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Intentos Máx. de Login</label>
            <input type="number" name="max_login_attempts" value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>
    </div>

    <div class="pt-4 border-t border-surface-variant flex justify-end">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Guardar Cambios</button>
    </div>
</form>
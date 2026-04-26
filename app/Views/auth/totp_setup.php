<main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-md mx-auto mt-20">
    <div class="w-full bg-surface-container-lowest rounded-xl p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden">
        <h2 class="text-2xl font-bold text-primary mb-4 text-center">Configurar 2FA (TOTP)</h2>
        <p class="text-sm text-slate-500 mb-6 text-center">Escanea este código QR con tu aplicación autenticadora (Google Authenticator, FreeOTP, Authy, etc).</p>
        
        <div class="flex justify-center mb-6">
            <div class="bg-white p-4 rounded-xl shadow-inner border border-slate-100">
                <?= $qrCodeSvg ?>
            </div>
        </div>
        
        <p class="text-xs text-center text-slate-400 mb-6">Si no puedes escanearlo, introduce esta clave manualmente:<br><strong class="font-mono text-slate-600 bg-slate-50 px-2 py-1 rounded"><?= htmlspecialchars($secret) ?></strong></p>

        <form method="POST" action="/profile/2fa/totp/activate" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-4">Código de verificación</label>
                <input type="text" name="code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" autofocus class="w-full bg-surface-variant text-on-surface font-mono text-center text-2xl tracking-[0.5em] rounded-full py-3 px-6 border-0 focus:ring-2 focus:ring-primary/30 outline-none" placeholder="000000" required>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Verificar y Activar</button>
            <a href="/staff/inbox" class="block text-center text-sm text-slate-500 hover:text-slate-700 mt-2">Cancelar</a>
        </form>
    </div>
</main>

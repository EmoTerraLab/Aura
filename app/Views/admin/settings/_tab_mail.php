<h2 class="text-xl font-bold text-on-surface mb-6">Configuración de Correo (SMTP)</h2>
<form method="POST" action="/admin/settings/mail" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Host SMTP</label>
            <input type="text" name="mail_host" value="<?= htmlspecialchars($settings['mail_host'] ?? '') ?>" placeholder="smtp.gmail.com" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>
        
        <div class="space-y-2 flex gap-4">
            <div class="flex-1">
                <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Puerto</label>
                <input type="number" name="mail_port" value="<?= htmlspecialchars($settings['mail_port'] ?? '587') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
            </div>
            <div class="flex-1">
                <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Cifrado</label>
                <select name="mail_encryption" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value="tls" <?= ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    <option value="none" <?= ($settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Ninguno</option>
                </select>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Usuario SMTP (Email)</label>
            <input type="text" name="mail_username" value="<?= htmlspecialchars($settings['mail_username'] ?? '') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Contraseña SMTP / App Password</label>
            <?php $hasPass = !empty($settings['mail_password']); ?>
            <input type="password" name="mail_password" placeholder="<?= $hasPass ? '•••••••• (Guardada)' : 'Introduce contraseña' ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
            <?php if ($hasPass): ?>
                <p class="text-[10px] text-slate-400">Deja el campo en blanco si no deseas cambiar la contraseña actual.</p>
            <?php endif; ?>
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Dirección de Remitente (From)</label>
            <input type="email" name="mail_from_address" value="<?= htmlspecialchars($settings['mail_from_address'] ?? '') ?>" placeholder="no-reply@escuela.edu" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Nombre de Remitente</label>
            <input type="text" name="mail_from_name" value="<?= htmlspecialchars($settings['mail_from_name'] ?? 'Aura') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>
    </div>

    <div class="pt-4 border-t border-surface-variant flex justify-between items-center">
        <button type="button" onclick="document.getElementById('testMailModal').classList.replace('hidden', 'flex')" class="text-primary font-bold px-4 py-2 hover:bg-primary/5 rounded-full transition-colors">Enviar correo de prueba</button>
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Guardar Cambios</button>
    </div>
</form>

<!-- Test Mail Modal -->
<div id="testMailModal" class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-[fadeIn_0.2s_ease-out]">
        <div class="p-6 border-b border-surface-variant/30 flex justify-between items-center">
            <h3 class="text-lg font-black text-primary">Probar Conexión SMTP</h3>
            <button type="button" onclick="document.getElementById('testMailModal').classList.replace('flex', 'hidden')" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" action="/admin/settings/mail/test" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <p class="text-sm text-slate-500 mb-4">Guarda la configuración antes de probar. Se enviará un correo de prueba usando los ajustes actuales.</p>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 ml-2 uppercase">Enviar a la dirección:</label>
                <input type="email" name="test_email" class="w-full bg-slate-50 rounded-full py-3 px-5 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none" required placeholder="tu@email.com">
            </div>
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('testMailModal').classList.replace('flex', 'hidden')" class="flex-1 bg-slate-100 text-slate-600 font-bold py-3 rounded-full hover:bg-slate-200 transition-colors">Cancelar</button>
                <button type="submit" class="flex-1 bg-primary text-white font-bold py-3 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Enviar Test</button>
            </div>
        </form>
    </div>
</div>

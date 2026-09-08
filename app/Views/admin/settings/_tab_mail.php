<h2 class="text-base md:text-lg font-bold text-on-surface mb-5 font-display">Configuración de Correo (SMTP)</h2>
<form method="POST" action="/admin/settings/mail" class="space-y-5 font-display">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-body-md">
        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Host SMTP</label>
            <input type="text" name="mail_host" value="<?= htmlspecialchars($settings['mail_host'] ?? '') ?>" placeholder="smtp.gmail.com" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>
        
        <div class="space-y-1.5 flex gap-3">
            <div class="flex-1">
                <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Puerto</label>
                <input type="number" name="mail_port" value="<?= htmlspecialchars($settings['mail_port'] ?? '587') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
            </div>
            <div class="flex-1">
                <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Cifrado</label>
                <select name="mail_encryption" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm font-display">
                    <option value="tls" <?= ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    <option value="none" <?= ($settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Ninguno</option>
                </select>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Usuario SMTP (Email)</label>
            <input type="text" name="mail_username" value="<?= htmlspecialchars($settings['mail_username'] ?? '') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Contraseña SMTP / App Password</label>
            <?php $hasPass = !empty($settings['mail_password']); ?>
            <input type="password" name="mail_password" placeholder="<?= $hasPass ? '•••••••• (Guardada)' : 'Introduce contraseña' ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
            <?php if ($hasPass): ?>
                <p class="text-[10px] text-on-surface-variant/70">Deja en blanco para conservar la contraseña actual.</p>
            <?php endif; ?>
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Dirección de Remitente (From)</label>
            <input type="email" name="mail_from_address" value="<?= htmlspecialchars($settings['mail_from_address'] ?? '') ?>" placeholder="no-reply@colegio.edu" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Nombre de Remitente</label>
            <input type="text" name="mail_from_name" value="<?= htmlspecialchars($settings['mail_from_name'] ?? 'Prisma') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>
    </div>

    <div class="pt-3 border-t border-surface-variant/30 flex justify-between items-center font-display">
        <button type="button" onclick="document.getElementById('testMailModal').classList.replace('hidden', 'flex')" class="text-primary text-xs font-bold px-4 py-2 hover:bg-primary/5 rounded-xl transition-colors cursor-pointer">Enviar correo de prueba</button>
        <button type="submit" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer">Guardar Cambios</button>
    </div>
</form>

<!-- Test Mail Modal -->
<div id="testMailModal" class="fixed inset-0 z-[100] bg-primary/40 backdrop-blur-xs hidden items-center justify-center p-4 font-display">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md overflow-hidden shadow-card border border-surface-variant/40 animate-fadeIn">
        <div class="p-5 border-b border-surface-variant/40 flex justify-between items-center bg-surface-container-low">
            <h3 class="text-sm md:text-base font-bold text-on-surface">Probar Conexión SMTP</h3>
            <button type="button" onclick="document.getElementById('testMailModal').classList.replace('flex', 'hidden')" class="text-on-surface-variant hover:text-on-surface cursor-pointer"><span class="material-symbols-outlined text-lg">close</span></button>
        </div>
        <form method="POST" action="/admin/settings/mail/test" class="p-5 md:p-6 space-y-3.5 font-body-md">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <p class="text-xs text-on-surface-variant mb-2">Guarda la configuración antes de probar. Se enviará un correo de prueba usando los ajustes actuales.</p>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display">Enviar a la dirección:</label>
                <input type="email" name="test_email" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs" required placeholder="tu@email.com">
            </div>
            <div class="pt-2 flex gap-2.5 font-display">
                <button type="button" onclick="document.getElementById('testMailModal').classList.replace('flex', 'hidden')" class="flex-1 h-10 bg-surface-container-low text-on-surface-variant font-bold text-xs rounded-xl hover:bg-surface-container-high transition-colors cursor-pointer">Cancelar</button>
                <button type="submit" class="flex-1 h-10 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:bg-primary/90 transition-transform cursor-pointer">Enviar Test</button>
            </div>
        </form>
    </div>
</div>

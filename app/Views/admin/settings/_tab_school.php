<h2 class="text-xl font-bold text-on-surface mb-6">Escuela e Identidad</h2>
<form method="POST" action="/admin/settings/school" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Nombre de la Escuela</label>
            <input type="text" name="school_name" value="<?= htmlspecialchars($settings['school_name'] ?? '') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>
        
        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Email de Contacto</label>
            <input type="email" name="school_contact_email" value="<?= htmlspecialchars($settings['school_contact_email'] ?? '') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Sitio Web</label>
            <input type="url" name="school_website" value="<?= htmlspecialchars($settings['school_website'] ?? '') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none">
        </div>

        <div class="space-y-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">URL del Logo (Opcional)</label>
            <input type="url" name="school_logo_url" value="<?= htmlspecialchars($settings['school_logo_url'] ?? '') ?>" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none" placeholder="https://...">
        </div>
        
        <div class="space-y-2 md:col-span-2">
            <label class="block font-bold text-sm text-slate-500 uppercase tracking-wide">Dirección Física</label>
            <textarea name="school_address" rows="3" class="w-full bg-slate-50 rounded-xl py-3 px-4 border border-surface-variant focus:ring-2 focus:ring-primary/20 outline-none"><?= htmlspecialchars($settings['school_address'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="pt-4 border-t border-surface-variant flex justify-end">
        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">Guardar Cambios</button>
    </div>
</form>
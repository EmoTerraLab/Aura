<h2 class="text-base md:text-lg font-bold text-on-surface mb-5 font-display">Escuela e Identidad</h2>
<form method="POST" action="/admin/settings/school" class="space-y-5 font-display">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-body-md">
        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Nombre de la Escuela / Organización</label>
            <input type="text" name="school_name" value="<?= htmlspecialchars($settings['school_name'] ?? 'Prisma') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>
        
        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Email de Contacto</label>
            <input type="email" name="school_contact_email" value="<?= htmlspecialchars($settings['school_contact_email'] ?? '') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Sitio Web</label>
            <input type="url" name="school_website" value="<?= htmlspecialchars($settings['school_website'] ?? '') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm">
        </div>

        <div class="space-y-1.5">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">URL del Logo Oficial</label>
            <input type="url" name="school_logo_url" value="<?= htmlspecialchars($settings['school_logo_url'] ?? 'https://prisma.emoterralab.com/assets/prisma-logo.png') ?>" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm" placeholder="https://...">
        </div>
        
        <div class="space-y-1.5 md:col-span-2">
            <label class="block font-bold text-xs text-on-surface-variant uppercase tracking-wider font-display">Dirección Física</label>
            <textarea name="school_address" rows="3" class="w-full bg-surface-container-low rounded-xl p-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs md:text-sm resize-none"><?= htmlspecialchars($settings['school_address'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="pt-3 border-t border-surface-variant/30 flex justify-end">
        <button type="submit" class="h-11 bg-primary text-on-primary px-6 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer">Guardar Cambios</button>
    </div>
</form>
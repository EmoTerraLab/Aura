<div class="h-[60vh] flex flex-col items-center justify-center text-center space-y-6">
    <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
        <span class="material-symbols-outlined text-5xl">settings_applications</span>
    </div>
    <div>
        <h1 class="text-2xl font-black text-slate-800"><?= \App\Core\Lang::t('protocol.not_configured') ?></h1>
        <p class="text-slate-500 max-w-md mx-auto mt-2"><?= \App\Core\Lang::t('protocol.not_configured_desc') ?></p>
    </div>
    <a href="/admin/settings?tab=ccaa" class="btn bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg hover:scale-105 transition-transform">
        <?= \App\Core\Lang::t('protocol.go_to_settings') ?>
    </a>
</div>

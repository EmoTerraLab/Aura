<?php $bodyClass = "bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col lg:flex-row overflow-hidden"; ?>

<!-- SideNavBar -->
<nav id="app-sidebar" class="bg-slate-50 dark:bg-slate-950 shadow-[4px_0_24px_rgba(6,105,114,0.04)] h-screen w-64 fixed left-0 top-0 z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col py-6">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">admin_panel_settings</span></div>
        <div><h1 class="font-h2 text-h2 text-teal-700 font-black tracking-tight leading-none">Aura</h1><p class="font-label-caps text-label-caps text-surface-tint opacity-70 mt-1">Control Panel</p></div>
    </div>
    <div class="flex-1 overflow-y-auto no-scrollbar space-y-1">
        <a href="/admin" class="w-[calc(100%-16px)] text-left flex items-center gap-3 text-slate-500 hover:bg-teal-50/50 rounded-full mx-2 px-4 py-3 transition-colors duration-150">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
        <a href="/admin/settings" class="w-[calc(100%-16px)] text-left flex items-center gap-3 bg-teal-50 text-teal-700 rounded-full mx-2 px-4 py-3 transition-colors duration-150">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-medium">Configuración</span>
        </a>
        <div class="mt-8 px-4">
            <a href="/staff/inbox" class="block bg-secondary-container rounded-DEFAULT p-4 ambient-shadow relative overflow-hidden transition-transform hover:scale-[1.02]">
                <span class="material-symbols-outlined text-secondary mb-2">forum</span>
                <h3 class="font-body-md text-[14px] font-semibold text-on-secondary-container leading-tight">Ir a Bandeja Staff</h3>
            </a>
        </div>
    </div>
    <div class="mt-auto pt-4 border-t border-surface-variant/50 mx-4 flex flex-col gap-1">
        <div class="px-4 py-2"><?= \App\Core\Lang::renderSelector() ?></div>
        <div class="px-4 py-2 flex items-center justify-between text-xs text-slate-500">
            <span><?= htmlspecialchars(\App\Core\Auth::user()['name']) ?></span>
            <span class="font-bold uppercase">ADMIN</span>
        </div>
        <form action="/logout" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>
            <button type="submit" class="w-full text-left flex items-center gap-3 text-slate-500 px-4 py-3 hover:bg-red-50 hover:text-red-600 rounded-full transition-colors">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-sm font-medium"><?= \App\Core\Lang::t('admin.logout') ?></span>
            </button>
        </form>
    </div>
</nav>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[55] hidden lg:hidden"></div>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-50 flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b border-surface-variant">
    <h1 class="text-xl font-bold text-teal-700">Aura Admin</h1>
    <div class="flex items-center gap-2">
        <a href="/admin" class="p-2 text-slate-500"><span class="material-symbols-outlined">dashboard</span></a>
        <button onclick="toggleSidebar()" class="p-2 text-slate-500">
            <span class="material-symbols-outlined" id="menu-icon">menu</span>
        </button>
    </div>
</nav>

<main class="flex-1 lg:ml-64 flex flex-col h-screen pt-16 lg:pt-0 bg-surface overflow-y-auto no-scrollbar">
    <div class="p-4 md:p-10 max-w-5xl mx-auto w-full space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl md:text-3xl font-black text-primary">Ajustes del Sistema</h1>
        </header>

        <?php if (isset($_GET['saved'])): ?>
            <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2 animate-[fadeIn_0.3s_ease-out]">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="text-sm font-bold">Cambios guardados correctamente.</span>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2">
            <a href="/admin/settings?tab=school" class="px-5 py-2.5 rounded-full text-xs md:text-sm font-bold whitespace-nowrap transition-colors <?= $tab === 'school' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-lowest text-slate-500 hover:bg-surface-container-low' ?>">
                Escuela e Identidad
            </a>
            <a href="/admin/settings?tab=appearance" class="px-5 py-2.5 rounded-full text-xs md:text-sm font-bold whitespace-nowrap transition-colors <?= $tab === 'appearance' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-lowest text-slate-500 hover:bg-surface-container-low' ?>">
                Apariencia
            </a>
            <a href="/admin/settings?tab=mail" class="px-5 py-2.5 rounded-full text-xs md:text-sm font-bold whitespace-nowrap transition-colors <?= $tab === 'mail' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-lowest text-slate-500 hover:bg-surface-container-low' ?>">
                Correo (SMTP)
            </a>
            <a href="/admin/settings?tab=security" class="px-5 py-2.5 rounded-full text-xs md:text-sm font-bold whitespace-nowrap transition-colors <?= $tab === 'security' ? 'bg-primary text-white shadow-md' : 'bg-surface-container-lowest text-slate-500 hover:bg-surface-container-low' ?>">
                Seguridad y Autenticación
            </a>
        </div>

        <div class="bg-surface-container-lowest rounded-3xl border border-surface-variant/50 ambient-shadow p-5 md:p-8">
            <?php
            $tabFile = __DIR__ . '/_tab_' . $tab . '.php';
            if (file_exists($tabFile)) {
                require $tabFile;
            } else {
                echo "<p class='text-slate-500 italic'>Sección no encontrada.</p>";
            }
            ?>
        </div>
    </div>
</main>

<?php ob_start(); ?>
<script>
    function toggleSidebar() {
        const s = document.getElementById('app-sidebar');
        const o = document.getElementById('sidebar-overlay');
        const i = document.getElementById('menu-icon');
        const isOpen = !s.classList.contains('-translate-x-full');
        if (isOpen) {
            s.classList.add('-translate-x-full');
            o.classList.add('hidden');
            i.innerText = 'menu';
        } else {
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
            i.innerText = 'close';
        }
    }
</script>
<?php $scripts = ob_get_clean(); ?>

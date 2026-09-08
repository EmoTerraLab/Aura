<?php $bodyClass = "bg-surface text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col lg:flex-row overflow-hidden"; ?>

<!-- SideNavBar -->
<aside id="app-sidebar" class="bg-surface-container-lowest border-r border-surface-variant/40 h-screen w-64 fixed left-0 top-0 z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col py-6 shadow-xs font-display">
    <div class="px-6 mb-6 flex items-center gap-3">
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" alt="Prisma" class="w-8 h-8 rounded-lg object-contain shadow-xs">
        <div>
            <h1 class="text-base font-bold text-primary tracking-tight leading-none">Prisma</h1>
            <p class="text-[11px] text-on-surface-variant/70 mt-1">Panel de Control</p>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto space-y-1 px-3">
        <a href="/admin" class="w-full text-left flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-xl px-3.5 py-2.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="text-xs">Dashboard</span>
        </a>
        <?php if (\App\Core\Config::get("ccaa_protocol_active", "1") === "1"): ?>
        <a href="/protocolo-acoso" class="w-full text-left flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-xl px-3.5 py-2.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-[20px]">policy</span>
            <span class="text-xs"><?= \App\Core\Lang::t("protocol.nav_title") ?></span>
        </a>
        <?php endif; ?>

        <a href="/admin/settings" class="w-full text-left flex items-center gap-3 bg-prisma-mint/25 text-teal-900 font-bold rounded-xl px-3.5 py-2.5 transition-colors">
            <span class="material-symbols-outlined text-[20px] text-teal-800">settings</span>
            <span class="text-xs">Configuración</span>
        </a>
        <div class="mt-6 px-1">
            <a href="/staff/inbox" class="block bg-prisma-lavender/25 rounded-xl p-3.5 border border-prisma-lavender/40 hover:scale-[1.02] transition-transform">
                <span class="material-symbols-outlined text-purple-900 text-lg mb-1">forum</span>
                <h3 class="text-xs font-bold text-purple-950 leading-tight">Ir a Bandeja Staff</h3>
            </a>
        </div>
    </div>
    <div class="mt-auto pt-3 border-t border-surface-variant/40 px-3 flex flex-col gap-2">
        <div><?= \App\Core\Lang::renderSelector() ?></div>
        <div class="px-2 py-1 flex items-center justify-between text-[11px] text-on-surface-variant/70">
            <span class="truncate font-semibold"><?= htmlspecialchars(\App\Core\Auth::user()['name']) ?></span>
            <span class="font-bold uppercase tracking-wider text-[9px] bg-primary/10 text-primary px-1.5 py-0.5 rounded">ADMIN</span>
        </div>
        <form action="/logout" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>
            <button type="submit" class="w-full text-left flex items-center gap-2.5 text-on-surface-variant hover:text-error hover:bg-error/10 px-3 py-2 rounded-xl transition-colors cursor-pointer text-xs font-semibold">
                <span class="material-symbols-outlined text-base">logout</span>
                <span><?= \App\Core\Lang::t('admin.logout') ?></span>
            </button>
        </form>
    </div>
</aside>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[55] hidden lg:hidden"></div>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-50 flex justify-between items-center px-4 h-14 bg-surface/90 backdrop-blur-md border-b border-surface-variant/40 font-display">
    <div class="flex items-center gap-2.5">
        <button onclick="toggleSidebar()" class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-primary cursor-pointer">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" alt="Prisma" class="h-7 w-7 rounded-lg object-contain shadow-xs">
        <h1 class="text-base font-bold text-primary">Prisma Admin</h1>
    </div>
    <div class="flex items-center gap-1">
        <a href="/admin" class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined text-xl">dashboard</span></a>
    </div>
</nav>

<main class="flex-1 lg:ml-64 flex flex-col h-screen pt-14 lg:pt-0 bg-surface overflow-y-auto">
    <div class="p-4 md:p-8 max-w-5xl mx-auto w-full space-y-5 font-display">
        <header class="flex items-center justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight">Ajustes del Sistema</h1>
        </header>

        <?php if (isset($_GET['saved'])): ?>
            <div class="bg-prisma-mint/20 border border-teal-600/30 text-teal-950 px-4 py-3 rounded-xl flex items-center gap-2 animate-fadeIn text-xs font-semibold">
                <span class="material-symbols-outlined text-base text-teal-800">check_circle</span>
                <span>Cambios guardados correctamente.</span>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="flex gap-2 overflow-x-auto pb-1">
            <a href="/admin/settings?tab=school" class="h-10 px-4 rounded-xl text-xs font-bold whitespace-nowrap flex items-center transition-all <?= $tab === 'school' ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-low border border-surface-variant/40' ?>">
                Escuela e Identidad
            </a>
            <a href="/admin/settings?tab=appearance" class="h-10 px-4 rounded-xl text-xs font-bold whitespace-nowrap flex items-center transition-all <?= $tab === 'appearance' ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-low border border-surface-variant/40' ?>">
                Apariencia
            </a>
            <a href="/admin/settings?tab=mail" class="h-10 px-4 rounded-xl text-xs font-bold whitespace-nowrap flex items-center transition-all <?= $tab === 'mail' ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-low border border-surface-variant/40' ?>">
                Correo (SMTP)
            </a>
            <a href="/admin/settings?tab=security" class="h-10 px-4 rounded-xl text-xs font-bold whitespace-nowrap flex items-center transition-all <?= $tab === 'security' ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-low border border-surface-variant/40' ?>">
                Seguridad y Autenticación
            </a>
            <a href="/admin/settings?tab=protocol" class="h-10 px-4 rounded-xl text-xs font-bold whitespace-nowrap flex items-center transition-all <?= $tab === 'protocol' ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-low border border-surface-variant/40' ?>">
                Protocolo Acoso
            </a>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card p-5 md:p-8">
            <?php
            $tabFile = __DIR__ . '/_tab_' . $tab . '.php';
            if (file_exists($tabFile)) {
                require $tabFile;
            } else {
                echo "<p class='text-on-surface-variant italic text-xs'>Sección no encontrada.</p>";
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
            if (i) i.innerText = 'menu';
        } else {
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
            if (i) i.innerText = 'close';
        }
    }
</script>
<?php $scripts = ob_get_clean(); ?>

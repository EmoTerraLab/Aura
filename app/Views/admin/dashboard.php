<?php $bodyClass = "bg-surface text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col lg:flex-row overflow-hidden"; ?>

<!-- SideNavBar -->
<aside id="app-sidebar" class="fixed lg:static inset-y-0 left-0 w-64 bg-surface-container-lowest border-r border-surface-variant/40 z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col shadow-xs py-6 h-screen font-display">
    <div class="px-6 mb-6 flex items-center gap-3">
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" alt="Prisma" class="w-8 h-8 rounded-lg object-contain shadow-xs">
        <div>
            <h1 class="text-base font-bold text-primary tracking-tight leading-none">Prisma</h1>
            <p class="text-[11px] text-on-surface-variant/70 mt-1">Panel de Control</p>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto space-y-1 px-3">
        <button onclick="switchTab('users')" id="tab-btn-users" class="w-full text-left flex items-center gap-3 bg-prisma-mint/25 text-teal-900 rounded-xl px-3.5 py-2.5 transition-all cursor-pointer font-bold">
            <span class="material-symbols-outlined text-[20px] text-teal-800">group</span>
            <span class="text-xs"><?= \App\Core\Lang::t('admin.users') ?></span>
        </button>
        <button onclick="switchTab('classrooms')" id="tab-btn-classrooms" class="w-full text-left flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-xl px-3.5 py-2.5 transition-all cursor-pointer font-medium">
            <span class="material-symbols-outlined text-[20px]">meeting_room</span>
            <span class="text-xs"><?= \App\Core\Lang::t('admin.classrooms') ?></span>
        </button>
        <a href="/admin/settings" id="tab-btn-settings" class="w-full text-left flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-xl px-3.5 py-2.5 transition-all cursor-pointer font-medium">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span class="text-xs">Configuración</span>
        </a>
        <?php
        try {
            $totalFiles = count(glob(__DIR__ . '/../../../database/migrations/[0-9]*.php'));
            $db_inst = \App\Core\Database::getInstance();
            $stmt_mig = $db_inst->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='migrations'");
            if ($stmt_mig->fetchColumn() > 0) {
                $stmt = $db_inst->query('SELECT COUNT(*) FROM migrations');
                $executed = (int)$stmt->fetchColumn();
            } else {
                $executed = 0;
            }
            $pendingCount = max(0, $totalFiles - $executed);
        } catch (\Exception $e) { $pendingCount = 0; }
        ?>
        <a href="/admin/update" class="w-full text-left flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded-xl px-3.5 py-2.5 transition-all cursor-pointer font-medium <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/update') ? 'bg-prisma-mint/25 text-teal-900 font-bold' : '' ?>">
            <span class="material-symbols-outlined text-[20px]">system_update</span>
            <span class="text-xs">Actualizaciones</span>
            <?php if ($pendingCount > 0): ?>
                <span class="ml-auto bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $pendingCount ?></span>
            <?php endif; ?>
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

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-primary/40 backdrop-blur-xs z-[55] lg:hidden"></div>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-50 flex justify-between items-center px-4 h-14 bg-surface/90 backdrop-blur-md border-b border-surface-variant/40 font-display">
    <div class="flex items-center gap-2.5">
        <button onclick="toggleSidebar()" class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-primary cursor-pointer">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" alt="Prisma" class="h-7 w-7 rounded-lg object-contain shadow-xs">
        <h1 class="text-base font-bold text-primary">Prisma Admin</h1>
    </div>
    <div class="flex items-center gap-2">
        <?= \App\Core\Lang::renderSelector() ?>
    </div>
</nav>

<main class="flex-1 flex flex-col h-screen pt-14 lg:pt-0 bg-surface overflow-y-auto">

    <div class="p-4 md:p-8 max-w-7xl mx-auto w-full space-y-6 font-display">
        <?php if ($pendingCount > 0): ?>
        <!-- Update Alert Banner -->
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5 flex items-center gap-4 animate-pulse">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-800 shrink-0">
                <span class="material-symbols-outlined text-2xl">update</span>
            </div>
            <div class="flex-1">
                <h3 class="text-amber-950 font-bold text-sm">Actualización de sistema pendiente</h3>
                <p class="text-amber-900/80 text-xs">Hay <?= $pendingCount ?> migraciones de base de datos esperando ser aplicadas.</p>
            </div>
            <a href="/admin/update" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-colors shrink-0">
                Actualizar ahora
            </a>
        </div>
        <?php endif; ?>

        <!-- Stats Header -->
        <header class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1"><?= \App\Core\Lang::t('admin.total_users') ?></p>
                <h2 class="text-3xl font-bold text-primary"><?= $totalUsers ?? 0 ?></h2>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1"><?= \App\Core\Lang::t('admin.total_classrooms') ?></p>
                <h2 class="text-3xl font-bold text-teal-800"><?= $totalClassrooms ?? 0 ?></h2>
            </div>
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-variant/40 shadow-card">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1"><?= \App\Core\Lang::t('admin.reports_registered') ?></p>
                <h2 class="text-3xl font-bold text-purple-900"><?= $totalReports ?? 0 ?></h2>
            </div>
        </header>

        <!-- Main Content Area -->
        <section class="bg-surface-container-lowest rounded-2xl border border-surface-variant/40 shadow-card overflow-hidden">
            <div class="p-4 md:p-5 border-b border-surface-variant/40 flex justify-between items-center bg-surface-container-low">
                <h2 id="current-tab-title" class="text-sm md:text-base font-bold text-on-surface"><?= \App\Core\Lang::t('admin.users') ?></h2>
                <div id="tab-actions">
                    <button onclick="openUserModal()" class="h-10 bg-primary text-on-primary px-5 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-transform flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-base">add</span> <?= \App\Core\Lang::t('admin.new_user') ?>
                    </button>
                </div>
            </div>

            <!-- Tab Content: Users -->
            <div id="pane-users" class="tab-pane p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-surface-container-low text-[10px] font-bold uppercase tracking-wider text-on-surface-variant border-b border-surface-variant/40">
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.user_id') ?></th>
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.user_name') ?></th>
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.user_email') ?></th>
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.user_role') ?></th>
                                <th class="px-5 py-3 text-right"><?= \App\Core\Lang::t('admin.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody" class="font-body-md divide-y divide-surface-variant/20">
                            <tr><td colspan="5" class="px-5 py-8 text-center text-on-surface-variant italic"><?= \App\Core\Lang::t('admin.loading') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Classrooms -->
            <div id="pane-classrooms" class="tab-pane p-0 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-surface-container-low text-[10px] font-bold uppercase tracking-wider text-on-surface-variant border-b border-surface-variant/40">
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.classroom_id') ?></th>
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.classroom_name') ?></th>
                                <th class="px-5 py-3"><?= \App\Core\Lang::t('admin.classroom_tutor') ?></th>
                                <th class="px-5 py-3 text-right"><?= \App\Core\Lang::t('admin.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody id="classrooms-tbody" class="font-body-md divide-y divide-surface-variant/20">
                            <tr><td colspan="4" class="px-5 py-8 text-center text-on-surface-variant italic"><?= \App\Core\Lang::t('admin.loading') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Settings -->
            <div id="pane-settings" class="tab-pane p-6 md:p-8 hidden font-display">
                <div class="max-w-md space-y-4">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-xs text-on-surface uppercase tracking-wider"><?= \App\Core\Lang::t('admin.default_lang') ?></label>
                        <select id="default-lang" class="w-full h-11 bg-surface-container-low rounded-xl px-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs font-semibold">
                            <?php foreach(\App\Core\Lang::supported() as $code): ?>
                                <option value="<?= $code ?>"><?= \App\Core\Lang::t('lang.'.$code) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[11px] text-on-surface-variant">Este idioma se usará para usuarios nuevos o no logueados.</p>
                    </div>
                    <button onclick="saveSettings()" class="h-11 bg-primary text-on-primary px-8 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-all cursor-pointer"><?= \App\Core\Lang::t('admin.save') ?></button>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Modals System -->
<div id="modal-overlay" class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[100] hidden items-center justify-center p-4 font-display">
    <!-- User Modal -->
    <div id="modal-user" class="bg-surface-container-lowest rounded-2xl w-full max-w-lg overflow-hidden shadow-card border border-surface-variant/40 animate-fadeIn hidden">
        <div class="p-5 border-b border-surface-variant/40 flex justify-between items-center bg-surface-container-low">
            <h3 id="modalUserTitle" class="text-sm md:text-base font-bold text-on-surface">Nuevo Usuario</h3>
            <button onclick="closeModals()" class="text-on-surface-variant hover:text-on-surface cursor-pointer"><span class="material-symbols-outlined text-lg">close</span></button>
        </div>
        <form onsubmit="saveUser(event)" class="p-5 md:p-6 space-y-3.5 font-body-md">
            <input type="hidden" id="user-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.user_name') ?></label>
                    <input type="text" id="user-name" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs" required>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.user_email') ?></label>
                    <input type="email" id="user-email" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.user_role') ?></label>
                    <select id="user-role" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs font-display">
                        <option value="alumno">Alumno</option>
                        <option value="profesor">Profesor</option>
                        <option value="orientador">Orientador</option>
                        <option value="direccion">Dirección</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.user_password') ?></label>
                    <input type="password" id="user-password" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs">
                    <p class="text-[10px] text-on-surface-variant/70 mt-0.5"><?= \App\Core\Lang::t('admin.user_password_help') ?></p>
                </div>
            </div>
            <div class="space-y-1 hidden" id="user-classroom-container">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.user_classroom') ?></label>
                <select id="user-classroom" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs font-display">
                    <option value=""><?= \App\Core\Lang::t('admin.no_classroom') ?></option>
                </select>
            </div>
            <div class="pt-2 flex gap-2.5 font-display">
                <button type="button" onclick="closeModals()" class="flex-1 h-10 bg-surface-container-low text-on-surface-variant font-bold text-xs rounded-xl hover:bg-surface-container-high transition-colors cursor-pointer"><?= \App\Core\Lang::t('admin.cancel') ?></button>
                <button type="submit" class="flex-1 h-10 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:bg-primary/90 transition-transform cursor-pointer"><?= \App\Core\Lang::t('admin.save') ?></button>
            </div>
        </form>
    </div>

    <!-- Classroom Modal -->
    <div id="modal-classroom" class="bg-surface-container-lowest rounded-2xl w-full max-w-md overflow-hidden shadow-card border border-surface-variant/40 animate-fadeIn hidden">
        <div class="p-5 border-b border-surface-variant/40 flex justify-between items-center bg-surface-container-low">
            <h3 id="modalClassroomTitle" class="text-sm md:text-base font-bold text-on-surface">Nueva Aula</h3>
            <button onclick="closeModals()" class="text-on-surface-variant hover:text-on-surface cursor-pointer"><span class="material-symbols-outlined text-lg">close</span></button>
        </div>
        <form onsubmit="saveClassroom(event)" class="p-5 md:p-6 space-y-3.5 font-body-md">
            <input type="hidden" id="classroom-id">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.classroom_name') ?></label>
                <input type="text" id="classroom-name" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs" required>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider font-display"><?= \App\Core\Lang::t('admin.classroom_tutor_optional') ?></label>
                <select id="classroom-tutor" class="w-full h-10 bg-surface-container-low rounded-xl px-3.5 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-xs font-display">
                    <option value=""><?= \App\Core\Lang::t('admin.no_tutor') ?></option>
                </select>
            </div>
            <div class="pt-2 flex gap-2.5 font-display">
                <button type="button" onclick="closeModals()" class="flex-1 h-10 bg-surface-container-low text-on-surface-variant font-bold text-xs rounded-xl hover:bg-surface-container-high transition-colors cursor-pointer"><?= \App\Core\Lang::t('admin.cancel') ?></button>
                <button type="submit" class="flex-1 h-10 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:bg-primary/90 transition-transform cursor-pointer"><?= \App\Core\Lang::t('admin.save') ?></button>
            </div>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<script>
    let allUsers = [];
    let allClassrooms = [];
    let currentTab = 'users';

    document.addEventListener("DOMContentLoaded", () => {
        loadUsers();
        loadClassroomsInBackground();
        loadSettings();

        document.getElementById('user-role').addEventListener('change', function(e) {
            const classroomContainer = document.getElementById('user-classroom-container');
            if (e.target.value === 'alumno') classroomContainer.classList.remove('hidden');
            else classroomContainer.classList.add('hidden');
        });
    });

    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
        document.getElementById(`pane-${tab}`).classList.remove('hidden');
        
        document.querySelectorAll('[id^="tab-btn-"]').forEach(b => {
            b.classList.remove('bg-prisma-mint/25', 'text-teal-900', 'font-bold');
            b.classList.add('text-on-surface-variant', 'font-medium');
        });
        const activeBtn = document.getElementById(`tab-btn-${tab}`);
        activeBtn.classList.add('bg-prisma-mint/25', 'text-teal-900', 'font-bold');
        activeBtn.classList.remove('text-on-surface-variant', 'font-medium');

        const titles = { users: '<?= \App\Core\Lang::t('admin.users') ?>', classrooms: '<?= \App\Core\Lang::t('admin.classrooms') ?>', settings: 'Configuración' };
        document.getElementById('current-tab-title').innerText = titles[tab];
        
        const actions = document.getElementById('tab-actions');
        if (tab === 'users') {
            actions.innerHTML = `<button onclick="openUserModal()" class="h-10 bg-primary text-on-primary px-5 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-transform flex items-center gap-1.5 cursor-pointer"><span class="material-symbols-outlined text-base">add</span> <?= \App\Core\Lang::t('admin.new_user') ?></button>`;
            loadUsers();
        } else if (tab === 'classrooms') {
            actions.innerHTML = `<button onclick="openClassroomModal()" class="h-10 bg-primary text-on-primary px-5 rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 transition-transform flex items-center gap-1.5 cursor-pointer"><span class="material-symbols-outlined text-base">add</span> <?= \App\Core\Lang::t('admin.new_classroom') ?></button>`;
            loadClassrooms();
        } else {
            actions.innerHTML = '';
            loadSettings();
        }
    }

    async function loadSettings() {
        try {
            const res = await fetchJson('/admin/api/settings');
            if (res.default_lang) document.getElementById('default-lang').value = res.default_lang;
        } catch (e) {}
    }

    async function saveSettings() {
        const lang = document.getElementById('default-lang').value;
        try {
            const res = await fetchJson('/admin/api/settings/lang', { method: 'POST', body: { default_lang: lang } });
            if (res.success) alert('Configuración guardada correctamente');
            else alert(res.error || '<?= \App\Core\Lang::t('admin.error_saving') ?>');
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_connection') ?>'); }
    }

    async function loadClassroomsInBackground() {
        try {
            const res = await fetchJson('/admin/api/classrooms');
            allClassrooms = res.data || [];
            updateStudentClassroomSelect();
        } catch (e) {}
    }

    function updateStudentClassroomSelect() {
        const select = document.getElementById('user-classroom');
        select.innerHTML = '<option value=""><?= \App\Core\Lang::t('admin.no_classroom') ?></option>';
        allClassrooms.forEach(c => { select.innerHTML += `<option value="${c.id}">${c.name}</option>`; });
    }

    function updateTutorSelect() {
        const select = document.getElementById('classroom-tutor');
        select.innerHTML = '<option value=""><?= \App\Core\Lang::t('admin.no_tutor') ?></option>';
        allUsers.forEach(u => { if (u.role === 'profesor') select.innerHTML += `<option value="${u.id}">${u.name}</option>`; });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function loadUsers() {
        try {
            const res = await fetchJson('/admin/api/users');
            allUsers = res.data || [];
            const tbody = document.getElementById('users-tbody');
            tbody.innerHTML = allUsers.map(u => `
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-5 py-3.5 font-bold text-on-surface-variant/70">#${u.id}</td>
                    <td class="px-5 py-3.5 font-semibold text-on-surface">${escapeHtml(u.name)}</td>
                    <td class="px-5 py-3.5 text-on-surface-variant">${escapeHtml(u.email)}</td>
                    <td class="px-5 py-3.5"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase ${u.role==='admin'?'bg-red-100 text-red-800':(u.role==='alumno'?'bg-prisma-mint/25 text-teal-900':'bg-prisma-sky/30 text-sky-950')}">${u.role}</span></td>
                    <td class="px-5 py-3.5 text-right space-x-1">
                        <button onclick='editUser(${JSON.stringify(u).replace(/'/g, "&apos;")})' class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors cursor-pointer"><span class="material-symbols-outlined text-base">edit</span></button>
                        <button onclick="deleteUser(${u.id})" class="text-error hover:bg-error/10 p-1.5 rounded-lg transition-colors cursor-pointer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </td>
                </tr>
            `).join('') || '<tr><td colspan="5" class="px-5 py-8 text-center text-on-surface-variant italic">No hay usuarios registrados</td></tr>';
            updateTutorSelect();
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_loading_users') ?>'); }
    }

    async function saveUser(e) {
        e.preventDefault();
        const id = document.getElementById('user-id').value;
        const data = { name: document.getElementById('user-name').value, email: document.getElementById('user-email').value, role: document.getElementById('user-role').value, password: document.getElementById('user-password').value, classroom_id: document.getElementById('user-classroom').value };
        const url = id ? `/admin/api/users/${id}` : '/admin/api/users';
        const method = id ? 'PATCH' : 'POST';
        try {
            const res = await fetchJson(url, { method, body: data });
            if (res.success) { closeModals(); loadUsers(); }
            else alert(res.error || '<?= \App\Core\Lang::t('admin.error_saving') ?>');
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_connection') ?>'); }
    }

    async function deleteUser(id) {
        if (!confirm('<?= \App\Core\Lang::t('admin.confirm_delete_user') ?>')) return;
        try {
            const res = await fetchJson(`/admin/api/users/${id}`, { method: 'DELETE' });
            if (res.success) loadUsers(); else alert(res.error || '<?= \App\Core\Lang::t('admin.error_deleting') ?>');
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_connection') ?>'); }
    }

    async function loadClassrooms() {
        try {
            const res = await fetchJson('/admin/api/classrooms');
            allClassrooms = res.data || [];
            const tbody = document.getElementById('classrooms-tbody');
            tbody.innerHTML = allClassrooms.map(c => `
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-5 py-3.5 font-bold text-on-surface-variant/70">#${c.id}</td>
                    <td class="px-5 py-3.5 font-semibold text-on-surface">${escapeHtml(c.name)}</td>
                    <td class="px-5 py-3.5 text-on-surface-variant">${c.tutor_name ? escapeHtml(c.tutor_name) : '<span class="italic opacity-60"><?= \App\Core\Lang::t('admin.no_tutor') ?></span>'}</td>
                    <td class="px-5 py-3.5 text-right space-x-1">
                        <button onclick='editClassroom(${JSON.stringify(c).replace(/'/g, "&apos;")})' class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors cursor-pointer"><span class="material-symbols-outlined text-base">edit</span></button>
                        <button onclick="deleteClassroom(${c.id})" class="text-error hover:bg-error/10 p-1.5 rounded-lg transition-colors cursor-pointer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </td>
                </tr>
            `).join('') || '<tr><td colspan="4" class="px-5 py-8 text-center text-on-surface-variant italic">No hay aulas registradas</td></tr>';
            updateStudentClassroomSelect();
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_loading_classrooms') ?>'); }
    }

    async function saveClassroom(e) {
        e.preventDefault();
        const id = document.getElementById('classroom-id').value;
        const data = { name: document.getElementById('classroom-name').value, tutor_id: document.getElementById('classroom-tutor').value };
        const url = id ? `/admin/api/classrooms/${id}` : '/admin/api/classrooms';
        const method = id ? 'PATCH' : 'POST';
        try {
            const res = await fetchJson(url, { method, body: data });
            if (res.success) { closeModals(); loadClassrooms(); }
            else alert(res.error || '<?= \App\Core\Lang::t('admin.error_saving') ?>');
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_connection') ?>'); }
    }

    async function deleteClassroom(id) {
        if (!confirm('<?= \App\Core\Lang::t('admin.confirm_delete_classroom') ?>')) return;
        try {
            const res = await fetchJson(`/admin/api/classrooms/${id}`, { method: 'DELETE' });
            if (res.success) loadClassrooms(); else alert(res.error || '<?= \App\Core\Lang::t('admin.error_deleting') ?>');
        } catch (e) { alert('<?= \App\Core\Lang::t('admin.error_connection') ?>'); }
    }

    function openUserModal() {
        document.getElementById('user-id').value = '';
        document.getElementById('user-name').value = '';
        document.getElementById('user-email').value = '';
        document.getElementById('user-role').value = 'alumno';
        document.getElementById('user-password').value = '';
        document.getElementById('user-classroom').value = '';
        document.getElementById('user-classroom-container').classList.remove('hidden');
        document.getElementById('modalUserTitle').innerText = '<?= \App\Core\Lang::t('admin.new_user_title') ?>';
        showModal('user');
    }

    function editUser(user) {
        document.getElementById('user-id').value = user.id;
        document.getElementById('user-name').value = user.name;
        document.getElementById('user-email').value = user.email;
        document.getElementById('user-role').value = user.role;
        document.getElementById('user-password').value = '';
        if (user.role === 'alumno') {
            document.getElementById('user-classroom-container').classList.remove('hidden');
            document.getElementById('user-classroom').value = user.classroom_id || '';
        } else {
            document.getElementById('user-classroom-container').classList.add('hidden');
        }
        document.getElementById('modalUserTitle').innerText = '<?= \App\Core\Lang::t('admin.edit_user_title') ?>';
        showModal('user');
    }

    function openClassroomModal() {
        document.getElementById('classroom-id').value = '';
        document.getElementById('classroom-name').value = '';
        document.getElementById('classroom-tutor').value = '';
        document.getElementById('modalClassroomTitle').innerText = '<?= \App\Core\Lang::t('admin.new_classroom_title') ?>';
        showModal('classroom');
    }

    function editClassroom(classroom) {
        document.getElementById('classroom-id').value = classroom.id;
        document.getElementById('classroom-name').value = classroom.name;
        document.getElementById('classroom-tutor').value = classroom.tutor_id || '';
        document.getElementById('modalClassroomTitle').innerText = '<?= \App\Core\Lang::t('admin.edit_classroom_title') ?>';
        showModal('classroom');
    }

    function showModal(type) {
        document.getElementById('modal-overlay').classList.replace('hidden', 'flex');
        document.getElementById(`modal-${type}`).classList.remove('hidden');
    }

    function closeModals() {
        document.getElementById('modal-overlay').classList.replace('flex', 'hidden');
        document.getElementById('modal-user').classList.add('hidden');
        document.getElementById('modal-classroom').classList.add('hidden');
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
</script>
<?php $scripts = ob_get_clean(); ?>

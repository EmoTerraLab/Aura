<?php $bodyClass = "bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col lg:flex-row overflow-hidden"; ?>

<!-- SideNavBar -->
<aside id="app-sidebar" class="fixed lg:static inset-y-0 left-0 w-64 bg-slate-50 dark:bg-slate-950 border-r z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col shadow-2xl lg:shadow-none py-6 h-screen">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 flex items-center justify-center">
            <img src="<?= BASE_URL ?>icono-sinfondo.png" alt="Aura Logo" class="w-full h-full object-contain">
        </div>
        <div><h1 class="font-h2 text-h2 text-teal-700 font-black tracking-tight leading-none">Aura</h1><p class="font-label-caps text-label-caps text-surface-tint opacity-70 mt-1">Control Panel</p></div>
    </div>
    <div class="flex-1 overflow-y-auto no-scrollbar space-y-1">
        <button onclick="switchTab('users')" id="tab-btn-users" class="w-[calc(100%-16px)] text-left flex items-center gap-3 bg-teal-50 text-teal-700 rounded-full mx-2 px-4 py-3 transition-colors duration-150">
            <span class="material-symbols-outlined">group</span>
            <span class="font-medium"><?= \App\Core\Lang::t('admin.users') ?></span>
        </button>
        <button onclick="switchTab('classrooms')" id="tab-btn-classrooms" class="w-[calc(100%-16px)] text-left flex items-center gap-3 text-slate-500 hover:bg-teal-50/50 rounded-full mx-2 px-4 py-3 transition-colors duration-150">
            <span class="material-symbols-outlined">meeting_room</span>
            <span class="font-medium"><?= \App\Core\Lang::t('admin.classrooms') ?></span>
        </button>
        <a href="/admin/settings" id="tab-btn-settings" class="w-[calc(100%-16px)] text-left flex items-center gap-3 text-slate-500 hover:bg-teal-50/50 rounded-full mx-2 px-4 py-3 transition-colors duration-150">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-medium">Configuración</span>
        </a>
        <?php
        // Calcular migraciones pendientes para el badge
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
        <a href="/admin/update" class="w-[calc(100%-16px)] text-left flex items-center gap-3 text-slate-500 hover:bg-teal-50/50 rounded-full mx-2 px-4 py-3 transition-colors duration-150 <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/update') ? 'bg-teal-50 text-teal-700' : '' ?>">
            <span class="material-symbols-outlined">system_update</span>
            <span class="font-medium">Actualizaciones</span>
            <?php if ($pendingCount > 0): ?>
                <span class="ml-auto bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $pendingCount ?></span>
            <?php endif; ?>
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
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-black/20 backdrop-blur-sm z-[55] lg:hidden"></div>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-50 flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b border-surface-variant">
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-600">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <img src="<?= BASE_URL ?>icono-sinfondo.png" alt="Aura Logo" class="h-8 w-8 object-contain">
        <h1 class="text-xl font-bold text-teal-700">Aura Admin</h1>
    </div>
    <div class="flex items-center gap-2">
        <?= \App\Core\Lang::renderSelector() ?>
    </div>
</nav>

<main class="flex-1 flex flex-col h-screen pt-16 lg:pt-0 bg-surface overflow-y-auto no-scrollbar">

    <div class="p-6 lg:p-10 max-w-7xl mx-auto w-full space-y-10">
        <?php if ($pendingCount > 0): ?>
        <!-- Update Alert Banner -->
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6 flex items-center gap-6 animate-pulse">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                <span class="material-symbols-outlined text-3xl">update</span>
            </div>
            <div class="flex-1">
                <h3 class="text-orange-900 font-black text-lg">Actualización de sistema pendiente</h3>
                <p class="text-orange-700 text-sm font-medium">Hay <?= $pendingCount ?> cambios de base de datos esperando ser aplicados.</p>
            </div>
            <a href="/admin/update" class="bg-orange-600 text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-orange-600/20 hover:bg-orange-700 transition-colors">
                Actualizar ahora
            </a>
        </div>
        <?php endif; ?>

        <!-- Stats Header -->
        <header class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-surface-variant/50 ambient-shadow">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1"><?= \App\Core\Lang::t('admin.total_users') ?></p>
                <h2 class="text-4xl font-black text-primary"><?= $totalUsers ?? 0 ?></h2>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-surface-variant/50 ambient-shadow">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1"><?= \App\Core\Lang::t('admin.total_classrooms') ?></p>
                <h2 class="text-4xl font-black text-secondary"><?= $totalClassrooms ?? 0 ?></h2>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-surface-variant/50 ambient-shadow">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1"><?= \App\Core\Lang::t('admin.reports_registered') ?></p>
                <h2 class="text-4xl font-black text-tertiary-container"><?= $totalReports ?? 0 ?></h2>
            </div>
        </header>

        <!-- Main Content Area -->
        <section class="bg-surface-container-lowest rounded-3xl border border-surface-variant/50 ambient-shadow overflow-hidden">
            <div class="p-6 border-b border-surface-variant/50 flex justify-between items-center bg-surface-container-low/30">
                <h2 id="current-tab-title" class="text-xl font-bold text-on-surface"><?= \App\Core\Lang::t('admin.users') ?></h2>
                <div id="tab-actions">
                    <button onclick="openUserModal()" class="bg-primary text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-primary/20 hover:scale-105 transition-transform flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">add</span> <?= \App\Core\Lang::t('admin.new_user') ?>
                    </button>
                </div>
            </div>

            <!-- Tab Content: Users -->
            <div id="pane-users" class="tab-pane p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.user_id') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.user_name') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.user_email') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.user_role') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30 text-right"><?= \App\Core\Lang::t('admin.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody" class="text-sm">
                            <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 italic"><?= \App\Core\Lang::t('admin.loading') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Classrooms -->
            <div id="pane-classrooms" class="tab-pane p-0 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.classroom_id') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.classroom_name') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30"><?= \App\Core\Lang::t('admin.classroom_tutor') ?></th>
                                <th class="px-6 py-4 border-b border-surface-variant/30 text-right"><?= \App\Core\Lang::t('admin.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody id="classrooms-tbody" class="text-sm">
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 italic"><?= \App\Core\Lang::t('admin.loading') ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Settings -->
            <div id="pane-settings" class="tab-pane p-10 hidden">
                <div class="max-w-md space-y-6">
                    <div class="space-y-2">
                        <label class="block font-bold text-sm text-on-surface"><?= \App\Core\Lang::t('admin.default_lang') ?></label>
                        <select id="default-lang" class="w-full bg-surface-container-highest rounded-xl py-3 px-4 border-0 focus:ring-2 focus:ring-primary/20 outline-none">
                            <?php foreach(\App\Core\Lang::supported() as $code): ?>
                                <option value="<?= $code ?>"><?= \App\Core\Lang::t('lang.'.$code) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-slate-400">Este idioma se usará para usuarios nuevos o no logueados.</p>
                    </div>
                    <button onclick="saveSettings()" class="bg-primary text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-transform"><?= \App\Core\Lang::t('admin.save') ?></button>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Modals System (Tailwind) -->
<div id="modal-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <!-- User Modal -->
    <div id="modal-user" class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-[fadeIn_0.2s_ease-out] hidden">
        <div class="p-6 border-b border-surface-variant/30 flex justify-between items-center">
            <h3 id="modalUserTitle" class="text-lg font-black text-primary">Nuevo Usuario</h3>
            <button onclick="closeModals()" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form onsubmit="saveUser(event)" class="p-6 space-y-4">
            <input type="hidden" id="user-id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.user_name') ?></label>
                    <input type="text" id="user-name" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-primary/20 outline-none" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.user_email') ?></label>
                    <input type="email" id="user-email" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-primary/20 outline-none" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.user_role') ?></label>
                    <select id="user-role" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="alumno">Alumno</option>
                        <option value="profesor">Profesor</option>
                        <option value="orientador">Orientador</option>
                        <option value="direccion">Dirección</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.user_password') ?></label>
                    <input type="password" id="user-password" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-primary/20 outline-none">
                    <p class="text-[9px] text-slate-400 mt-1 ml-2"><?= \App\Core\Lang::t('admin.user_password_help') ?></p>
                </div>
            </div>
            <div class="space-y-1 hidden" id="user-classroom-container">
                <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.user_classroom') ?></label>
                <select id="user-classroom" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value=""><?= \App\Core\Lang::t('admin.no_classroom') ?></option>
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModals()" class="flex-1 bg-slate-100 text-slate-600 font-bold py-3 rounded-full hover:bg-slate-200 transition-colors"><?= \App\Core\Lang::t('admin.cancel') ?></button>
                <button type="submit" class="flex-1 bg-primary text-white font-bold py-3 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform"><?= \App\Core\Lang::t('admin.save') ?></button>
            </div>
        </form>
    </div>

    <!-- Classroom Modal -->
    <div id="modal-classroom" class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-[fadeIn_0.2s_ease-out] hidden">
        <div class="p-6 border-b border-surface-variant/30 flex justify-between items-center">
            <h3 id="modalClassroomTitle" class="text-lg font-black text-secondary">Nueva Aula</h3>
            <button onclick="closeModals()" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form onsubmit="saveClassroom(event)" class="p-6 space-y-4">
            <input type="hidden" id="classroom-id">
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.classroom_name') ?></label>
                <input type="text" id="classroom-name" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-secondary/20 outline-none" required>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 ml-2 uppercase"><?= \App\Core\Lang::t('admin.classroom_tutor_optional') ?></label>
                <select id="classroom-tutor" class="w-full bg-slate-50 rounded-full py-3 px-5 border-0 focus:ring-2 focus:ring-secondary/20 outline-none">
                    <option value=""><?= \App\Core\Lang::t('admin.no_tutor') ?></option>
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeModals()" class="flex-1 bg-slate-100 text-slate-600 font-bold py-3 rounded-full hover:bg-slate-200 transition-colors"><?= \App\Core\Lang::t('admin.cancel') ?></button>
                <button type="submit" class="flex-1 bg-secondary text-white font-bold py-3 rounded-full shadow-lg shadow-secondary/20 hover:scale-[1.02] transition-transform"><?= \App\Core\Lang::t('admin.save') ?></button>
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
        
        // Update Buttons Styling
        document.querySelectorAll('[id^="tab-btn-"]').forEach(b => {
            b.classList.remove('bg-teal-50', 'text-teal-700');
            b.classList.add('text-slate-500', 'hover:bg-teal-50/50');
        });
        const activeBtn = document.getElementById(`tab-btn-${tab}`);
        activeBtn.classList.add('bg-teal-50', 'text-teal-700');
        activeBtn.classList.remove('text-slate-500', 'hover:bg-teal-50/50');

        // Update Title & Actions
        const titles = { users: '<?= \App\Core\Lang::t('admin.users') ?>', classrooms: '<?= \App\Core\Lang::t('admin.classrooms') ?>', settings: 'Configuración' };
        document.getElementById('current-tab-title').innerText = titles[tab];
        
        const actions = document.getElementById('tab-actions');
        if (tab === 'users') {
            actions.innerHTML = `<button onclick="openUserModal()" class="bg-primary text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-primary/20 hover:scale-105 transition-transform flex items-center gap-2"><span class="material-symbols-outlined text-lg">add</span> <?= \App\Core\Lang::t('admin.new_user') ?></button>`;
            loadUsers();
        } else if (tab === 'classrooms') {
            actions.innerHTML = `<button onclick="openClassroomModal()" class="bg-secondary text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-secondary/20 hover:scale-105 transition-transform flex items-center gap-2"><span class="material-symbols-outlined text-lg">add</span> <?= \App\Core\Lang::t('admin.new_classroom') ?></button>`;
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

    // --- CRUD Usuarios ---
    async function loadUsers() {
        try {
            const res = await fetchJson('/admin/api/users');
            allUsers = res.data || [];
            const tbody = document.getElementById('users-tbody');
            tbody.innerHTML = allUsers.map(u => `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-400">#${u.id}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">${escapeHtml(u.name)}</td>
                    <td class="px-6 py-4 text-slate-500">${escapeHtml(u.email)}</td>
                    <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-[10px] font-black uppercase ${u.role==='admin'?'bg-red-100 text-red-700':(u.role==='alumno'?'bg-green-100 text-green-700':'bg-blue-100 text-blue-700')}">${u.role}</span></td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button onclick='editUser(${JSON.stringify(u).replace(/'/g, "&apos;")})' class="text-primary hover:bg-primary/10 p-2 rounded-full transition-colors"><span class="material-symbols-outlined">edit</span></button>
                        <button onclick="deleteUser(${u.id})" class="text-error hover:bg-error/10 p-2 rounded-full transition-colors"><span class="material-symbols-outlined">delete</span></button>
                    </td>
                </tr>
            `).join('') || '<tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">No hay usuarios registrados</td></tr>';
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

    // --- CRUD Aulas ---
    async function loadClassrooms() {
        try {
            const res = await fetchJson('/admin/api/classrooms');
            allClassrooms = res.data || [];
            const tbody = document.getElementById('classrooms-tbody');
            tbody.innerHTML = allClassrooms.map(c => `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-400">#${c.id}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">${escapeHtml(c.name)}</td>
                    <td class="px-6 py-4 text-slate-500">${c.tutor_name ? escapeHtml(c.tutor_name) : '<span class="italic opacity-50"><?= \App\Core\Lang::t('admin.no_tutor') ?></span>'}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button onclick='editClassroom(${JSON.stringify(c).replace(/'/g, "&apos;")})' class="text-secondary hover:bg-secondary/10 p-2 rounded-full transition-colors"><span class="material-symbols-outlined">edit</span></button>
                        <button onclick="deleteClassroom(${c.id})" class="text-error hover:bg-error/10 p-2 rounded-full transition-colors"><span class="material-symbols-outlined">delete</span></button>
                    </td>
                </tr>
            `).join('') || '<tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">No hay aulas registradas</td></tr>';
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

    // --- Modals Logic ---
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


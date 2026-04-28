<?php $bodyClass = "bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col overflow-hidden"; ?>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-[50] flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b border-surface-variant font-manrope">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">spa</span>
        </div>
        <h1 class="text-lg font-bold text-teal-700">Aura Staff</h1>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="toggleMentions()" class="relative p-2 text-slate-500">
            <span class="material-symbols-outlined">notifications</span>
            <span id="mentions-badge-mobile" class="hidden absolute top-2 right-2 h-2 w-2 rounded-full bg-error"></span>
        </button>
        <button onclick="toggleSidebar()" class="p-2 text-slate-500">
            <span class="material-symbols-outlined" id="menu-icon">menu</span>
        </button>
    </div>
</nav>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[55] hidden lg:hidden"></div>

<!-- SideNavBar -->
<nav id="app-sidebar" class="bg-slate-50 dark:bg-slate-950 shadow-[4px_0_24px_rgba(6,105,114,0.04)] h-screen w-64 fixed left-0 top-0 z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col py-6">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">spa</span></div>
        <div><h1 class="font-h2 text-h2 text-teal-700 font-black tracking-tight leading-none">Aura</h1><p class="font-label-caps text-label-caps text-surface-tint opacity-70 mt-1">School Sanctuary</p></div>
    </div>
    <div class="flex-1 overflow-y-auto no-scrollbar space-y-1">
        <a class="flex items-center gap-3 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-full mx-2 px-4 py-3 transition-colors scale-95 duration-150" href="/staff/inbox"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inbox</span><span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.inbox') ?></span></a>
        <?php if (\App\Core\Auth::role() === 'admin'): ?><a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="/admin"><span class="material-symbols-outlined">admin_panel_settings</span><span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.admin_panel') ?></span></a><?php endif; ?>
        <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="#"><span class="material-symbols-outlined">folder_open</span><span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.active_cases') ?></span></a>
        <?php if (\App\Core\Config::get('ccaa_protocol_active', '1') === '1'): ?>
            <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="/protocolo-acoso">
                <span class="material-symbols-outlined">gavel</span>
                <span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('protocol.title') ?></span>
            </a>
        <?php endif; ?>
        <div class="mt-8 px-4"><div class="bg-secondary-container rounded-DEFAULT p-4 ambient-shadow relative overflow-hidden"><div class="absolute -right-4 -top-4 w-16 h-16 bg-white/20 rounded-full blur-xl"></div><span class="material-symbols-outlined text-secondary mb-2">hub</span><h3 class="font-body-md text-body-md font-semibold text-on-secondary-container leading-tight"><?= \App\Core\Lang::t('nav.sociograms') ?></h3><p class="font-label-caps text-label-caps text-secondary mt-1 normal-case"><?= \App\Core\Lang::t('nav.hidden_dynamics') ?></p></div></div>
        
        <div class="mt-4 px-2">
            <?php if (!\App\Core\Auth::user()['totp_enabled']): ?>
                <a href="/profile/2fa/totp/setup" class="flex items-center justify-between gap-3 text-amber-600 bg-amber-50 px-4 py-3 mx-2 hover:bg-amber-100 rounded-xl transition-colors text-sm font-bold border border-amber-200">
                    <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">lock_open</span> Proteger Cuenta</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            <?php else: ?>
                <form method="POST" action="/profile/2fa/totp/disable" onsubmit="return confirm('¿Seguro que deseas desactivar la verificación en dos pasos? Tu cuenta será menos segura.')">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
                    <button type="submit" class="w-[calc(100%-16px)] flex items-center justify-between gap-3 text-emerald-700 bg-emerald-50 px-4 py-3 mx-2 hover:bg-emerald-100 rounded-xl transition-colors text-sm font-bold border border-emerald-200">
                        <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">lock</span> 2FA Activado</span>
                        <span class="material-symbols-outlined text-[18px]">settings</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-auto pt-4 border-t border-surface-variant/50 mx-4 flex flex-col gap-1">
        <div class="px-4 py-2">
            <?= \App\Core\Lang::renderSelector() ?>
        </div>
        <div class="px-4 py-2 flex items-center justify-between text-xs text-slate-500"><span><?= htmlspecialchars(\App\Core\Auth::user()['name']) ?></span><span class="font-bold uppercase"><?= htmlspecialchars(\App\Core\Auth::role()) ?></span></div>
        <form action="/logout" method="POST"><input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/><button type="submit" class="w-full text-left flex items-center gap-3 text-slate-500 px-4 py-3 hover:bg-red-50 hover:text-red-600 rounded-full transition-colors"><span class="material-symbols-outlined">logout</span><span class="text-sm font-medium"><?= \App\Core\Lang::t('nav.logout_btn') ?></span></button></form>
    </div>
</nav>

<!-- Dropdown Menciones -->
<div id="mentions-dropdown" class="hidden fixed lg:absolute right-4 top-16 lg:top-20 mt-2 w-[calc(100%-32px)] sm:w-80 rounded-xl shadow-lg bg-surface-container-lowest ring-1 ring-black ring-opacity-5 z-[70] ambient-shadow overflow-hidden">
    <div class="p-4 bg-surface-container-low border-b border-surface-variant flex justify-between items-center"><h3 class="text-sm font-bold text-on-surface"><?= \App\Core\Lang::t('staff.mentions_title') ?></h3><button onclick="toggleMentions()" class="lg:hidden text-slate-400">✕</button></div>
    <div id="mentions-list" class="max-h-64 overflow-y-auto no-scrollbar"></div>
</div>

<main class="flex-1 lg:ml-64 flex flex-col md:flex-row h-screen pt-16 lg:pt-0 bg-surface">
    <!-- Left Pane (Inbox) -->
    <section id="inbox-pane" class="w-full md:w-[40%] lg:w-[30%] h-full flex flex-col bg-surface-container-lowest border-r border-surface-variant/30 ambient-shadow z-10 overflow-hidden">
        <div class="p-6 pb-2">
            <h2 class="font-h2 text-h2 text-on-surface mb-4"><?= \App\Core\Lang::t('staff.inbox_title') ?></h2>
            <div class="flex gap-2 mb-4 overflow-x-auto no-scrollbar pb-2">
                <button class="whitespace-nowrap px-4 py-1.5 rounded-full bg-primary-container text-on-primary-container font-label-caps text-label-caps tracking-wide"><?= \App\Core\Lang::t('staff.filter_all') ?></button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto no-scrollbar px-4 pb-6 space-y-2">
            <?php foreach($reports as $report): ?>
                <?php $badge = ($report['status'] === 'new') ? 'bg-[#f8d7da] text-[#721c24]' : (($report['status'] === 'in_progress') ? 'bg-[#fff3cd] text-[#856404]' : 'bg-[#d4edda] text-[#155724]'); ?>
                <div class="bg-surface-container-lowest hover:bg-surface border-l-4 <?= $report['status']==='new'?'border-primary':'border-transparent' ?> p-4 rounded-DEFAULT cursor-pointer transition-colors shadow-sm" onclick="loadReport(<?= $report['id'] ?>)">
                    <div class="flex justify-between items-start mb-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full <?= $badge ?> font-label-caps text-[10px] tracking-wider uppercase font-bold"><?= \App\Core\Lang::t('staff.aula') ?> <?= htmlspecialchars($report['classroom_name']) ?></span>
                        <span class="font-label-caps text-[10px] text-outline"><?= date('d/m/y', strtotime($report['created_at'])) ?></span>
                    </div>
                    <h4 class="font-body-md text-[16px] font-semibold text-on-surface leading-tight mb-1 truncate"><?= htmlspecialchars($report['student_name']) ?></h4>
                    <p class="font-body-md text-[13px] text-on-surface-variant line-clamp-2"><?= htmlspecialchars($report['content']) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if(empty($reports)): ?>
                <div class="p-10 text-center text-slate-400 italic text-sm"><?= \App\Core\Lang::t('dashboard.no_activity') ?></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Right Pane (Detail) -->
    <section id="report-detail-container" class="flex-1 h-full flex flex-col bg-surface-bright hidden md:flex relative z-20">
        <div class="flex-1 flex flex-col items-center justify-center text-outline">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-50">forum</span>
            <h3 class="font-h2 text-[20px] font-medium text-on-surface-variant"><?= \App\Core\Lang::t('staff.select_report') ?></h3>
        </div>
    </section>
</main>

<!-- Bottom Nav (Suppressed for detail focus on mobile) -->
<nav id="mobile-bottom-nav" class="lg:hidden fixed bottom-0 w-full z-40 flex justify-around items-center px-4 pb-6 pt-3 bg-white/90 backdrop-blur-lg shadow-lg rounded-t-[32px] border-t border-surface-variant/30">
    <a class="flex flex-col items-center text-slate-400 p-2" href="#"><span class="material-symbols-outlined">home</span><span class="text-[11px]"><?= \App\Core\Lang::t('nav.home') ?></span></a>
    <a class="flex flex-col items-center bg-teal-100 text-teal-800 rounded-full w-12 h-12 justify-center" href="/staff/inbox"><span class="material-symbols-outlined">chat_bubble</span></a>
    <form action="/logout" method="POST" class="flex flex-col items-center justify-center p-2">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/>
        <button type="submit" class="text-slate-400"><span class="material-symbols-outlined">logout</span></button>
    </form>
</nav>

<div id="premium-modal" class="fixed inset-0 z-[100] hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 md:p-8 text-center max-w-md w-full">
        <h3 class="font-h1 text-[24px] font-bold mb-2"><?= \App\Core\Lang::t('staff.premium_title') ?></h3>
        <p class="text-slate-500 mb-8"><?= \App\Core\Lang::t('staff.premium_desc') ?></p>
        <button onclick="closePremiumModal()" class="w-full py-3 bg-primary text-white rounded-full font-bold mb-2">Contactar Soporte</button>
        <button onclick="closePremiumModal()" class="w-full py-3 text-slate-500 font-medium">Cerrar</button>
    </div>
</div>

<?php ob_start(); ?>
<script>
    let currentReportId = null;
    let colleaguesList = [];

    // Lógica de Autocompletado de Menciones
    document.addEventListener('input', (e) => {
        if (e.target.id === 'reply-message') {
            handleMentionInput(e.target);
        }
    });

    function handleMentionInput(textarea) {
        const value = textarea.value;
        const cursorPosition = textarea.selectionStart;
        const textBeforeCursor = value.substring(0, cursorPosition);
        const mentionMatch = textBeforeCursor.match(/@([^@\s]*)$/);
        const suggestionsDiv = document.getElementById('mentions-suggestions');

        if (mentionMatch) {
            const query = mentionMatch[1].toLowerCase();
            const filtered = colleaguesList.filter(c => c.name.toLowerCase().includes(query)).slice(0, 5);
            
            if (filtered.length > 0) {
                renderSuggestions(filtered, mentionMatch[0], textarea);
            } else {
                suggestionsDiv.classList.add('hidden');
            }
        } else {
            suggestionsDiv.classList.add('hidden');
        }
    }

    function renderSuggestions(list, fullMention, textarea) {
        const div = document.getElementById('mentions-suggestions');
        div.innerHTML = list.map(user => `
            <div class="p-3 hover:bg-teal-50 cursor-pointer flex items-center gap-3 border-b border-slate-50 last:border-0" 
                 onclick="selectMention('${user.name}', '${fullMention}')">
                <div class="w-7 h-7 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center text-[10px] font-bold">
                    ${user.name.charAt(0)}
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">${user.name}</p>
                    <p class="text-[9px] text-slate-400 uppercase tracking-tighter">${user.role}</p>
                </div>
            </div>
        `).join('');
        div.classList.remove('hidden');
    }

    window.selectMention = function(name, fullMention) {
        const textarea = document.getElementById('reply-message');
        const value = textarea.value;
        const cursorPosition = textarea.selectionStart;
        const textBeforeCursor = value.substring(0, cursorPosition);
        const textAfterCursor = value.substring(cursorPosition);
        
        // Reemplazar la mención parcial por el nombre completo
        const newValue = textBeforeCursor.replace(/@([^@\s]*)$/, '@' + name + ' ') + textAfterCursor;
        textarea.value = newValue;
        textarea.focus();
        document.getElementById('mentions-suggestions').classList.add('hidden');
    };


    function toggleSidebar() {
        const s = document.getElementById('app-sidebar');
        const o = document.getElementById('sidebar-overlay');
        const isOpen = !s.classList.contains('-translate-x-full');
        if (isOpen) {
            s.classList.add('-translate-x-full');
            o.classList.add('hidden');
        } else {
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        loadMentions();
        loadColleagues();
    });

    async function loadColleagues() {
        try {
            const res = await fetchJson('/staff/colleagues');
            if (res.success) colleaguesList = res.colleagues;
        } catch (e) {}
    }

    async function loadMentions() {
        try {
            const res = await fetchJson('/staff/mentions');
            if (res.success && res.mentions.length > 0) {
                document.getElementById('mentions-badge-mobile')?.classList.remove('hidden');
                document.getElementById('mentions-list').innerHTML = res.mentions.map(m => `
                    <div class="p-4 hover:bg-surface cursor-pointer border-b" onclick="readMention(${m.id}, ${m.report_id})">
                        <p class="text-[10px] text-outline">CASO-${m.report_id}</p>
                        <p class="text-sm font-bold text-primary">${m.sender_name}</p>
                    </div>`).join('');
            } else {
                document.getElementById('mentions-list').innerHTML = '<p class="p-8 text-center text-slate-400 text-xs italic">No hay menciones nuevas</p>';
            }
        } catch (e) {}
    }

    function toggleMentions() { document.getElementById('mentions-dropdown').classList.toggle('hidden'); }

    async function readMention(mid, rid) {
        await fetchJson('/staff/mentions/read', { method: 'POST', body: { id: mid } });
        toggleMentions(); loadMentions(); loadReport(rid);
    }

    async function loadReport(id) {
        currentReportId = id;
        const container = document.getElementById('report-detail-container');
        const inbox = document.getElementById('inbox-pane');
        const bottomNav = document.getElementById('mobile-bottom-nav');
        
        container.innerHTML = '<div class="flex h-full items-center justify-center text-primary"><span class="material-symbols-outlined animate-spin text-4xl">refresh</span></div>';
        container.classList.remove('hidden', 'md:flex');
        container.classList.add('flex');

        if (window.innerWidth < 768) { 
            container.classList.add('fixed', 'inset-0', 'bg-white'); 
            inbox.classList.add('hidden');
            bottomNav.classList.add('hidden');
        } else {
            container.classList.add('md:flex');
        }

        const res = await fetchJson(`/staff/reports/${id}`);
        if (!res.error) renderDetail(res.report, res.messages);
        else container.innerHTML = `<div class="p-10 text-center"><p class="text-error font-bold mb-4">${res.error}</p><button onclick="closeDetailMobile()" class="bg-primary text-white px-6 py-2 rounded-full">Volver</button></div>`;
    }

    function closeDetailMobile() {
        const container = document.getElementById('report-detail-container');
        const inbox = document.getElementById('inbox-pane');
        const bottomNav = document.getElementById('mobile-bottom-nav');
        
        container.classList.add('hidden', 'md:flex');
        container.classList.remove('fixed', 'inset-0', 'bg-white', 'flex');
        inbox.classList.remove('hidden');
        bottomNav.classList.remove('hidden');
    }

    async function renderDetail(report, messages) {
        const container = document.getElementById('report-detail-container');
        
        // Cargar datos del caso legal
        const caseRes = await fetchJson(`/api/protocol/case/${report.id}`);
        const protocolCase = caseRes.case;

        let mHtml = messages.map(m => {
            const isMe = m.is_current_user;
            const isInt = parseInt(m.is_internal) === 1;
            return `<div class="flex gap-3 ${isMe?'flex-row-reverse':''} mb-4">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">${m.sender_name.charAt(0)}</div>
                <div class="${isInt?'bg-amber-50 border-amber-100 border':(isMe?'bg-primary text-white':'bg-white border')} p-3 rounded-2xl ${isMe?'rounded-tr-none':'rounded-tl-none'} shadow-sm max-w-[85%]">
                    ${isInt?'<p class="text-[9px] font-black text-amber-800 mb-1 uppercase tracking-tighter">🔒 Nota Interna</p>':''}
                    <p class="text-[13px] whitespace-pre-wrap">${m.message}</p>
                    <span class="text-[9px] opacity-60 block mt-1 ${isMe?'text-right':''}">${isMe?'Tú':m.sender_name} • ${m.created_at}</span>
                </div>
            </div>`;
        }).join('');

        container.innerHTML = `
            <div class="h-16 md:h-20 px-4 md:px-8 flex items-center justify-between bg-white border-b z-20 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="closeDetailMobile()" class="md:hidden text-slate-500"><span class="material-symbols-outlined">arrow_back</span></button>
                    <div class="min-w-0"><h3 class="font-bold text-sm truncate">Caso #${report.id}</h3><p class="text-[10px] text-outline uppercase font-bold truncate">${report.classroom_name}</p></div>
                </div>
                <div class="flex gap-2">
                    <select onchange="handleStatusChange(${report.id}, this.value)" id="status-select" class="bg-slate-100 border-0 rounded-full py-1.5 px-3 md:px-4 text-[9px] md:text-[10px] font-black uppercase outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="new" ${report.status==='new'?'selected':''}>${'<?= \App\Core\Lang::t('staff.status_received') ?>'}</option>
                        <option value="in_progress" ${report.status==='in_progress'?'selected':''}>${'<?= \App\Core\Lang::t('staff.status_review') ?>'}</option>
                        <option value="resolved" ${report.status==='resolved'?'selected':''}>${'<?= \App\Core\Lang::t('staff.status_resolved') ?>'}</option>
                    </select>
                </div>
            </div>

            <!-- LEGAL PROTOCOL TIMELINE -->
            <div id="protocol-timeline" class="bg-white border-b px-4 md:px-8 py-4 flex items-center justify-between overflow-x-auto no-scrollbar gap-4">
                ${renderTimelineHtml(protocolCase)}
            </div>

            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6 bg-slate-50 no-scrollbar">
                <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h4 class="text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest"><?= \App\Core\Lang::t('staff.student_story') ?></h4>
                    <p class="text-sm text-slate-800 whitespace-pre-wrap">${report.content}</p>
                </div>
                
                <!-- PROTOCOL ACTIONS CARD (Si está activo) -->
                ${renderProtocolActionsCard(protocolCase)}

                <div id="messages-flow">${mHtml || '<p class="text-center text-slate-400 py-10 text-xs italic"><?= \App\Core\Lang::t('staff.no_responses') ?></p>'}</div>
            </div>
            <div class="p-4 bg-white border-t shrink-0 relative">
                <!-- Contenedor de Sugerencias de Menciones -->
                <div id="mentions-suggestions" class="hidden absolute bottom-full left-4 mb-2 w-64 bg-white rounded-xl shadow-2xl border border-slate-100 z-[100] overflow-hidden"></div>
                <div class="flex gap-2 mb-3">
                    <input id="reply-message" class="flex-1 bg-slate-100 border-0 rounded-full py-3 px-6 text-sm outline-none focus:ring-2 focus:ring-primary/20" placeholder="<?= \App\Core\Lang::t('dashboard.chat_placeholder') ?>"/>
                    <button onclick="sendMessage()" class="bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg shadow-primary/20 shrink-0 active:scale-90 transition-transform"><span class="material-symbols-outlined">send</span></button>
                </div>
                <label class="text-[11px] font-bold text-slate-500 flex items-center gap-2 pl-4 cursor-pointer select-none">
                    <input type="checkbox" id="reply-internal" class="rounded text-primary border-slate-300 focus:ring-primary/20"/> <?= \App\Core\Lang::t('staff.mark_internal') ?>
                </label>
            </div>
        `;
    }

    function renderTimelineHtml(c) {
        if (!c) return '<p class="text-[10px] text-slate-400 italic">Protocolo legal no activado para este caso.</p>';
        
        const phases = [
            { id: 'deteccion', label: 'Detección' },
            { id: 'valoracion', label: 'Valoración' },
            { id: 'violencia_sexual_actiu', label: 'BARNAHUS', special: true },
            { id: 'comunicacio', label: 'Comunicación' },
            { id: 'intervencio', label: 'Intervención' },
            { id: 'tancament', label: 'Cierre' }
        ];

        // Filtrar fases según el tipo de caso
        let activePhases = phases;
        if (c.severity_preliminary === 'violencia_sexual') {
            activePhases = phases.filter(p => !['valoracion', 'intervencio'].includes(p.id));
        } else {
            activePhases = phases.filter(p => p.id !== 'violencia_sexual_actiu');
        }

        return activePhases.map((p, idx) => {
            const isActive = c.current_phase === p.id;
            const isPast = activePhases.findIndex(x => x.id === c.current_phase) > activePhases.findIndex(x => x.id === p.id);
            const colorClass = p.special ? 'bg-red-600 text-white' : (isActive ? 'bg-primary text-white' : (isPast ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400'));
            
            return `
                <div class="flex items-center gap-2 shrink-0">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold ${colorClass}">
                        ${isPast ? '<span class="material-symbols-outlined text-xs">check</span>' : idx + 1}
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-tight ${isActive || p.special && isActive ? (p.special ? 'text-red-700' : 'text-primary') : 'text-slate-400'}">${p.label}</span>
                    ${idx < activePhases.length - 1 ? '<span class="material-symbols-outlined text-slate-200 text-sm">chevron_right</span>' : ''}
                </div>
            `;
        }).join('');
    }

    function renderProtocolActionsCard(c) {
        if (!c) return '';
        
        const isBarnahus = c.current_phase === 'violencia_sexual_actiu';
        const isClosed = c.current_phase === 'tancament';
        const borderColor = isBarnahus ? 'border-red-500' : (isClosed ? 'border-emerald-500' : 'border-primary');
        const textColor = isBarnahus ? 'text-red-700' : (isClosed ? 'text-emerald-700' : 'text-primary');
        const comms = typeof c.communications === 'string' ? JSON.parse(c.communications || '{}') : (c.communications || {});
        const checks = typeof c.closure_checks === 'string' ? JSON.parse(c.closure_checks || '{}') : (c.closure_checks || {});

        return `
            <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border-l-4 ${borderColor} border-t border-r border-b space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[10px] font-black uppercase ${textColor} tracking-widest">
                        ${isBarnahus ? '⚠️ ALERTA BARNAHUS: VIOLÈNCIA SEXUAL' : 'Protocolo Legal: ' + c.current_phase.toUpperCase()}
                    </h4>
                    <div class="flex gap-2">
                        ${(isClosed || c.current_phase === 'intervencio') ? `<a href="/protocol/case/${c.id}/export" target="_blank" class="text-[10px] font-bold text-slate-400 hover:text-primary flex items-center gap-1"><span class="material-symbols-outlined text-sm">picture_as_pdf</span> Exportar</a>` : ''}
                        <button onclick="window.open('/protocolo-acoso', '_blank')" class="text-[10px] font-bold text-slate-400 hover:text-primary underline">Ver Guía CCAA</button>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    ${c.current_phase === 'deteccion' ? `
                        <button onclick="protocolClassify(${c.id}, 'grave', 'bullying')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-full text-[11px] font-bold text-slate-700 transition-colors">Confirmar Indicios</button>
                        <button onclick="protocolClassify(${c.id}, 'violencia_sexual', 'sexual')" class="px-4 py-2 bg-red-50 hover:bg-red-100 rounded-full text-[11px] font-bold text-red-700 transition-colors">⚠️ Violencia Sexual (Barnahus)</button>
                    ` : ''}
                    
                    ${c.current_phase === 'valoracion' ? `
                        <button class="px-4 py-2 bg-primary text-white rounded-full text-[11px] font-bold" onclick="alert('Asignación de equipo disponible en la versión PRO')">Asignar Equipo</button>
                        <button onclick="nextPhase(${c.id}, 'comunicacio')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-full text-[11px] font-bold text-slate-700 transition-colors">Finalizar Valoración</button>
                    ` : ''}

                    ${c.current_phase === 'comunicacio' ? `
                        <div class="w-full space-y-4">
                            <p class="text-[11px] font-bold text-slate-500 italic">Fase 3: Realitzeu les comunicacions legals obligatòries per avançar.</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                                    <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.inspeccio ? 'checked' : ''} onchange="toggleComm(${c.id}, 'inspeccio', this.checked)">
                                    <span class="text-xs font-bold text-slate-700">Comunicat a la Inspecció d'Educació (REVA)</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                                    <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.familia_victima ? 'checked' : ''} onchange="toggleComm(${c.id}, 'familia_victima', this.checked)">
                                    <span class="text-xs font-bold text-slate-700">Comunicat a la família de la víctima</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                                    <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.familia_agressor ? 'checked' : ''} onchange="toggleComm(${c.id}, 'familia_agressor', this.checked)">
                                    <span class="text-xs font-bold text-slate-700">Comunicat a la família de l'agressor</span>
                                </label>
                            </div>
                            <button id="btn-next-intervention" class="w-full py-3 bg-primary text-white rounded-xl text-xs font-bold" onclick="nextPhase(${c.id}, 'intervencio')">Avançar a Intervenció</button>
                        </div>
                    ` : ''}

                    ${(c.current_phase === 'intervencio' || c.current_phase === 'seguiment_tancament') ? `
                        <div class="w-full space-y-6">
                            <div class="flex gap-2">
                                <button onclick="openSecurityMap(${c.id})" class="flex-1 py-3 bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-2 justify-center shadow-lg shadow-emerald-500/20">
                                    <span class="material-symbols-outlined text-sm">map</span> Mapa de Seguretat
                                </button>
                                <button onclick="openFollowupModal(${c.id})" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold flex items-center gap-2 justify-center">
                                    <span class="material-symbols-outlined text-sm">event_note</span> Nou Seguiment
                                </button>
                            </div>

                            <div class="space-y-3">
                                <h5 class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Historial de Seguiment</h5>
                                <div class="max-h-40 overflow-y-auto no-scrollbar space-y-2" id="followup-list">
                                    ${(c.followups || []).map(f => `
                                        <div class="p-3 bg-slate-50 rounded-xl text-[11px] border border-slate-100">
                                            <div class="flex justify-between font-bold mb-1">
                                                <span class="text-primary uppercase">${f.target_type}</span>
                                                <span class="text-slate-400">${f.session_date}</span>
                                            </div>
                                            <p class="text-slate-600">${f.notes}</p>
                                        </div>
                                    `).join('') || '<p class="text-[10px] text-slate-400 italic">No hi ha sessions registrades.</p>'}
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 space-y-4">
                                <h5 class="text-[9px] font-black uppercase text-primary tracking-widest">Checklist de Tancament Oficial</h5>
                                <div class="space-y-2">
                                    ${renderClosureCheck(c.id, 'eradicated', 'La violència s\'ha eradicat definitivament', checks.eradicated)}
                                    ${renderClosureCheck(c.id, 'reparation', 'S\'ha dut a terme un procés de reparació', checks.reparation)}
                                    ${renderClosureCheck(c.id, 'students_confirm', 'L\'alumnat confirma la millora', checks.students_confirm)}
                                    ${renderClosureCheck(c.id, 'teachers_valorate', 'L\'equip docent valora resolució', checks.teachers_valorate)}
                                </div>
                                <div class="flex gap-2">
                                    <button class="flex-1 py-3 bg-primary text-white rounded-xl text-xs font-bold" onclick="nextPhase(${c.id}, 'tancament')">Tancar Protocol</button>
                                    <button class="px-4 py-3 bg-slate-100 text-slate-500 rounded-xl text-xs font-bold" onclick="nextPhase(${c.id}, 'intervencio')">Redefinir</button>
                                </div>
                            </div>
                        </div>
                    ` : ''}

                    ${isClosed ? `
                        <div class="w-full bg-emerald-50 p-6 rounded-2xl text-center space-y-4">
                            <span class="material-symbols-outlined text-4xl text-emerald-500">verified</span>
                            <div class="space-y-1">
                                <p class="text-sm font-black text-emerald-800 uppercase">Protocol Tancat i Resolt</p>
                                <p class="text-xs text-emerald-600">L'expedient ha estat arxivat correctament segons la normativa vigent.</p>
                            </div>
                            <a href="/protocol/case/${c.id}/export" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-2.5 rounded-full text-xs font-bold shadow-lg shadow-emerald-600/20">
                                <span class="material-symbols-outlined text-sm">download</span> Descarregar Informe Final
                            </a>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function renderClosureCheck(caseId, key, label, checked) {
        return `
            <label class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                <input type="checkbox" class="closure-check w-4 h-4 rounded text-emerald-500" ${checked ? 'checked' : ''} onchange="toggleClosure(${caseId}, '${key}', this.checked)">
                <span class="text-[11px] font-bold text-slate-600">${label}</span>
            </label>
        `;
    }

    let currentClosureChecks = {};
    async function toggleClosure(caseId, key, checked) {
        currentClosureChecks[key] = checked;
        await fetchJson(`/api/protocol/case/${caseId}/closure`, { method: 'POST', body: { checks: currentClosureChecks } });
    }

    function openFollowupModal(caseId) {
        const html = `
            <div id="modal-followup" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden">
                    <div class="p-6 border-b flex items-center justify-between">
                        <h3 class="font-black">Nou Registre de Seguiment</h3>
                        <button onclick="document.getElementById('modal-followup').remove()"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <select id="f-target" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm">
                            <option value="victima">Víctima</option>
                            <option value="agressor">Agressor</option>
                            <option value="familia">Família</option>
                            <option value="grup_classe">Grup Classe</option>
                        </select>
                        <input type="date" id="f-date" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm" value="${new Date().toISOString().split('T')[0]}">
                        <textarea id="f-notes" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm" rows="4" placeholder="Notes de la sessió..."></textarea>
                        <button onclick="saveFollowup(${caseId})" class="w-full py-4 bg-primary text-white rounded-full font-bold shadow-lg">Guardar Sessió</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    async function saveFollowup(caseId) {
        const data = {
            target_type: document.getElementById('f-target').value,
            session_date: document.getElementById('f-date').value,
            notes: document.getElementById('f-notes').value
        };
        const res = await fetchJson(`/api/protocol/case/${caseId}/followup`, { method: 'POST', body: data });
        if (res.success) {
            document.getElementById('modal-followup').remove();
            window.location.reload();
        }
    }

    let currentComms = {};
    async function toggleComm(caseId, key, checked) {
        currentComms[key] = checked;
        await fetchJson(`/api/protocol/case/${caseId}/communications`, { method: 'POST', body: { comms: currentComms } });
        
        // Comprobar si todos están marcados para habilitar el botón
        const checks = document.querySelectorAll('.comm-check');
        const allChecked = Array.from(checks).every(c => c.checked);
        const btn = document.getElementById('btn-next-intervention');
        if (btn) btn.disabled = !allChecked;
    }

    async function openSecurityMap(caseId) {
        const res = await fetchJson(`/api/protocol/case/${caseId}/security-map`);
        const map = res.map || {};
        
        const html = `
            <div id="modal-security-map" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                <div class="bg-white rounded-[2rem] w-full max-w-2xl shadow-2xl overflow-hidden animate-[scaleIn_0.3s_ease-out]">
                    <div class="p-6 border-b flex items-center justify-between bg-emerald-500 text-white">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">map</span>
                            <h3 class="font-black text-lg">Mapa de Seguretat (Fase 4)</h3>
                        </div>
                        <button onclick="document.getElementById('modal-security-map').remove()" class="hover:bg-white/20 p-2 rounded-full transition-colors"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="p-6 md:p-8 space-y-6 max-h-[70vh] overflow-y-auto no-scrollbar">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Espais Segurs per a la víctima</label>
                            <textarea id="map-segurs" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-emerald-500/20 outline-none" rows="3" placeholder="Ej: Biblioteca, Despatx d'Orientació, Aula 204...">${map.espais_segurs || ''}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Espais de Risc (Evitar coincidència)</label>
                            <textarea id="map-risc" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-emerald-500/20 outline-none" rows="3" placeholder="Ej: Lavabos planta 1, passadís nord al pati...">${map.espais_de_risc || ''}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Persones de suport referents</label>
                            <input id="map-persones" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm focus:ring-2 focus:ring-emerald-500/20 outline-none" value="${map.persones_de_suport || ''}" placeholder="Ej: Tutor de 3A, Mónica (6A)...">
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 border-t flex justify-end gap-3">
                        <button onclick="document.getElementById('modal-security-map').remove()" class="px-6 py-3 text-xs font-bold text-slate-500">Cancel·lar</button>
                        <button onclick="saveSecurityMap(${caseId})" class="px-8 py-3 bg-emerald-500 text-white rounded-full text-xs font-bold shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all">Guardar Mapa</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    async function saveSecurityMap(caseId) {
        const data = {
            map: {
                espais_segurs: document.getElementById('map-segurs').value,
                espais_de_risc: document.getElementById('map-risc').value,
                persones_de_suport: document.getElementById('map-persones').value
            }
        };
        const res = await fetchJson(`/api/protocol/case/${caseId}/security-map`, { method: 'POST', body: data });
        if (res.success) {
            document.getElementById('modal-security-map').remove();
            alert('Mapa de Seguretat guardat amb èxit.');
        }
    }

    async function protocolClassify(id, severity, classification) {
        if (!confirm('¿Confirmas esta clasificación preliminar? Esto activará las fases legales correspondientes.')) return;
        const res = await fetchJson(`/api/protocol/case/${id}/classify`, { method: 'POST', body: { severity, classification } });
        if (res.success) {
            if (severity === 'violencia_sexual') alert('ALERTA: Se ha activado el bypass Barnahus. El caso ha saltado directamente a la fase de Comunicación/Protección.');
            window.location.reload();
        }
    }

    async function nextPhase(id, phase) {
        if (!confirm('¿Deseas avanzar a la siguiente fase del protocolo?')) return;
        const res = await fetchJson(`/api/protocol/case/${id}/phase`, { method: 'POST', body: { phase } });
        if (res.success) window.location.reload();
    }

    async function handleStatusChange(id, status) {
        if (status === 'resolved') {
            const sum = prompt("<?= \App\Core\Lang::t('staff.resolution_prompt') ?>");
            if (!sum) { document.getElementById('status-select').value = 'in_progress'; return; }
            updateStatus(id, status, sum);
        } else {
            // Si el estado cambia a in_progress, mostrar sugerencia de protocolo si está activo
            if (status === 'in_progress') {
                checkProtocolGuide();
            }
            updateStatus(id, status);
        }
    }

    async function checkProtocolGuide() {
        try {
            const res = await fetchJson(`/api/protocol/case/${currentReportId}`);
            if (!res.success || !res.case) return;
            
            const protocolRes = await fetchJson('/api/protocol');
            if (protocolRes.error) return;
            
            // Mostrar una notificación o modal con el enlace a la guía
            if (confirm('Este caso requiere seguimiento. ¿Deseas consultar el protocolo oficial de ' + protocolRes.metadata.name + ' para guiar tu actuación?')) {
                window.open('/protocolo-acoso', '_blank');
            }
        } catch (e) {}
    }

    async function updateStatus(id, status, sum = null) {
        await fetchJson(`/staff/reports/${id}`, { method: 'PATCH', body: { status, resolution_summary: sum } });
        window.location.reload();
    }

    async function sendMessage() {
        const i = document.getElementById('reply-message');
        const msg = i.value.trim();
        const isInt = document.getElementById('reply-internal').checked;
        if (!msg || !currentReportId) return;
        const res = await fetchJson(`/staff/reports/${currentReportId}/messages`, { method: 'POST', body: { message: msg, is_internal: isInt } });
        if (!res.error) { i.value = ''; document.getElementById('reply-internal').checked = false; loadReport(currentReportId); }
        else alert(res.error);
    }
    
    function showPremiumModal() { document.getElementById('premium-modal').classList.remove('hidden'); document.getElementById('premium-modal').classList.add('flex'); }
    function closePremiumModal() { document.getElementById('premium-modal').classList.add('hidden'); document.getElementById('premium-modal').classList.remove('flex'); }
</script>
<?php $scripts = ob_get_clean(); ?>

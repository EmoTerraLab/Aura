<?php $bodyClass = "bg-surface text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col overflow-hidden"; ?>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-[50] flex justify-between items-center px-4 h-14 bg-surface/90 backdrop-blur-md border-b border-surface-variant/40 font-display">
    <div class="flex items-center gap-2.5">
        <button onclick="toggleSidebar()" class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-primary cursor-pointer">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" class="w-7 h-7 rounded-lg object-contain shadow-xs" alt="Prisma">
        <span class="font-bold text-base text-primary tracking-tight">Prisma</span>
    </div>
    <div class="flex items-center gap-1">
        <button onclick="toggleMentions()" class="w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-primary relative cursor-pointer" aria-label="Notificaciones">
            <span class="material-symbols-outlined text-xl">notifications</span>
            <div id="mentions-badge-mobile" class="hidden absolute top-1.5 right-1.5 w-2 h-2 bg-prisma-mint rounded-full ring-2 ring-white"></div>
        </button>
    </div>
</nav>

<!-- App Shell -->
<div class="flex flex-1 h-screen pt-14 lg:pt-0 overflow-hidden relative">
    
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-primary/40 backdrop-blur-xs z-[55] lg:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="app-sidebar" class="fixed lg:static inset-y-0 left-0 w-64 bg-surface-container-lowest border-r border-surface-variant/40 z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col shadow-xs font-display">
        <div class="p-6 flex items-center gap-3">
            <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" class="w-8 h-8 rounded-lg object-contain shadow-xs" alt="Prisma">
            <div>
                <h1 class="font-bold text-base text-primary tracking-tight">Prisma</h1>
                <p class="text-[11px] text-on-surface-variant/70">Gestión de Convivencia</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-2 space-y-1 px-3">
            <a class="flex items-center gap-3 bg-prisma-mint/25 text-teal-900 font-bold px-3.5 py-2.5 rounded-xl transition-all active:scale-98" href="/staff/inbox">
                <span class="material-symbols-outlined text-[20px] text-teal-800" style="font-variation-settings: 'FILL' 1">inbox</span>
                <span class="text-xs font-bold"><?= \App\Core\Lang::t('staff.inbox_title') ?></span>
            </a>
            
            <a class="flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl transition-colors" href="/staff/inbox">
                <span class="material-symbols-outlined text-[20px]">folder_open</span>
                <span class="text-xs font-medium"><?= \App\Core\Lang::t('nav.active_cases') ?></span>
            </a>

            <?php 
            $ccaaProtocol = \App\Services\Protocol\ProtocolFactory::make(\App\Core\Config::get('ccaa_code'));
            if ($ccaaProtocol->isFullyImplemented()): ?>
            <a class="flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl transition-colors" href="/protocolos/dashboard">
                <span class="material-symbols-outlined text-[20px]">dashboard_customize</span>
                <span class="text-xs font-medium"><?= \App\Core\Lang::t('protocol.dashboard_title') ?> (<?= $ccaaProtocol->getName() ?>)</span>
            </a>
            <?php endif; ?>

            <?php if (\App\Core\Config::get('ccaa_protocol_active', '1') === '1'): ?>
                <a class="flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl transition-colors" href="/protocolo-acoso">
                    <span class="material-symbols-outlined text-[20px]">gavel</span>
                    <span class="text-xs font-medium"><?= \App\Core\Lang::t('protocol.title') ?></span>
                </a>
            <?php endif; ?>

            <a class="flex items-center gap-3 text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl transition-colors" href="/profile/password">
                <span class="material-symbols-outlined text-[20px]">lock</span>
                <span class="text-xs font-medium"><?= \App\Core\Lang::t('auth.change_password') ?></span>
            </a>

            <div class="mt-6 pt-2">
                <a href="/staff/sociogramas/demo" class="block bg-prisma-lavender/25 rounded-xl p-3.5 border border-prisma-lavender/40 hover:scale-[1.02] transition-transform">
                    <div class="flex items-center gap-2 text-purple-900 mb-1">
                        <span class="material-symbols-outlined text-lg">hub</span>
                        <h3 class="font-bold text-xs"><?= \App\Core\Lang::t('nav.sociograms') ?></h3>
                    </div>
                    <p class="text-[11px] text-purple-950/80 leading-tight"><?= \App\Core\Lang::t('nav.hidden_dynamics') ?></p>
                </a>
            </div>
        </div>

        <div class="p-3 border-t border-surface-variant/40 bg-surface-container-low">
            <div class="flex items-center gap-2.5 p-2 rounded-xl bg-surface-container-lowest border border-surface-variant/40">
                <div class="w-8 h-8 rounded-lg bg-primary text-on-primary flex items-center justify-center font-bold text-xs shadow-xs"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-on-surface truncate"><?= htmlspecialchars($user['name']) ?></p>
                    <p class="text-[9px] text-on-surface-variant uppercase font-bold tracking-wider opacity-75"><?= htmlspecialchars($user['role']) ?></p>
                </div>
                <form action="/logout" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
                    <button class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-error hover:bg-error/10 rounded-lg transition-colors cursor-pointer" title="Cerrar sesión">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-1 flex overflow-hidden bg-surface">
        
        <!-- List Pane -->
        <div id="inbox-pane" class="w-full md:w-[380px] border-r border-surface-variant/40 flex flex-col shrink-0 bg-surface-container-lowest font-display">
            <div class="p-4 md:p-5 flex flex-col gap-3 border-b border-surface-variant/40 sticky top-0 z-10 bg-surface-container-lowest">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-base md:text-lg text-on-surface"><?= \App\Core\Lang::t('staff.inbox_title') ?></h2>
                    <span class="text-[11px] font-bold text-on-surface-variant/70 bg-surface-container-low px-2 py-0.5 rounded-full"><?= count($reports ?? []) ?> casos</span>
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline-variant text-[18px]">search</span>
                    <input type="text" placeholder="<?= \App\Core\Lang::t('staff.search_placeholder') ?>" class="w-full h-10 bg-surface-container-low border border-surface-variant/30 rounded-xl pl-10 pr-4 text-xs font-medium outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-outline-variant text-on-surface">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                <?php if (empty($reports)): ?>
                    <div class="p-12 text-center space-y-3">
                        <div class="w-14 h-14 bg-surface-container-low rounded-2xl flex items-center justify-center mx-auto text-outline-variant">
                            <span class="material-symbols-outlined text-3xl">inbox</span>
                        </div>
                        <p class="text-xs text-on-surface-variant italic"><?= \App\Core\Lang::t('staff.inbox_empty') ?></p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-surface-variant/30">
                        <?php foreach ($reports as $r): ?>
                        <div onclick="loadReport(<?= $r['id'] ?>)" class="p-4 hover:bg-surface-container-low cursor-pointer transition-all border-l-3 <?= $r['status'] === 'new' ? 'border-primary bg-primary/5' : 'border-transparent' ?> group">
                            <div class="flex justify-between items-center mb-1.5">
                                <?php $urgency = (!empty($r['urgency_level'])) ? $r['urgency_level'] : 'low'; ?>
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full <?= $urgency === 'high' ? 'bg-error/15 text-red-700' : 'bg-surface-container-high text-on-surface-variant' ?>">
                                    <?= \App\Core\Lang::t('dashboard.urgency_' . $urgency) ?>
                                </span>
                                <span class="text-[10px] text-on-surface-variant/70 font-medium"><?= date('H:i', strtotime($r['created_at'])) ?></span>
                            </div>
                            <h3 class="font-bold text-xs md:text-sm text-on-surface group-hover:text-primary transition-colors mb-1 truncate"><?= htmlspecialchars($r['student_name'] ?? 'Anónimo') ?></h3>
                            <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed mb-2.5 font-normal"><?= htmlspecialchars($r['content'] ?? '') ?></p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1 text-[10px] text-on-surface-variant/80 font-bold">
                                        <span class="material-symbols-outlined text-xs">chat_bubble</span>
                                        <span><?= (int)($r['message_count'] ?? 0) ?></span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[10px] text-on-surface-variant/80 font-bold">
                                        <span class="material-symbols-outlined text-xs">meeting_room</span>
                                        <span><?= htmlspecialchars($r['classroom_name'] ?? 'Aula') ?></span>
                                    </div>
                                </div>
                                <?php 
                                $protocol = \App\Services\Protocol\ProtocolFactory::make(\App\Core\Config::get('ccaa_code'));
                                ?>
                                <a href="<?= $protocol->getManageUrl($r['id']) ?>" class="text-[9px] font-bold uppercase bg-primary/10 text-primary px-2.5 py-1 rounded-lg hover:bg-primary hover:text-white transition-all stop-propagation" onclick="event.stopPropagation()">
                                    <?php if ($protocol->isFullyImplemented()): ?>
                                        Protocolo <?= $protocol->getName() ?>
                                    <?php else: ?>
                                        Normativa <?= $protocol->getName() ?>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Pane -->
        <div id="report-detail-container" class="hidden md:flex flex-1 flex-col bg-surface relative font-body-md">
            <div class="flex-1 flex flex-col items-center justify-center text-outline-variant p-12 text-center space-y-4">
                <div class="w-20 h-20 bg-surface-container-lowest rounded-2xl flex items-center justify-center border border-dashed border-surface-variant/60 shadow-xs">
                    <span class="material-symbols-outlined text-4xl text-outline-variant">chat</span>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm md:text-base text-on-surface"><?= \App\Core\Lang::t('staff.select_case_title') ?></h3>
                    <p class="text-xs text-on-surface-variant mt-1"><?= \App\Core\Lang::t('staff.select_case_desc') ?></p>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Mentions Dropdown -->
<div id="mentions-dropdown" class="hidden fixed top-16 right-4 w-80 bg-surface-container-lowest rounded-2xl shadow-card border border-surface-variant/40 z-[100] overflow-hidden font-display">
    <div class="p-3.5 border-b border-surface-variant/30 flex justify-between items-center bg-surface-container-low">
        <h3 class="font-bold text-xs uppercase tracking-wider text-on-surface-variant"><?= \App\Core\Lang::t('staff.mentions_title') ?></h3>
        <button onclick="toggleMentions()" class="text-on-surface-variant hover:text-on-surface cursor-pointer"><span class="material-symbols-outlined text-base">close</span></button>
    </div>
    <div id="mentions-list" class="max-h-80 overflow-y-auto"></div>
</div>

<div id="premium-modal" class="fixed inset-0 z-[100] hidden bg-primary/40 backdrop-blur-xs flex items-center justify-center p-4 font-display">
    <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 text-center max-w-md w-full shadow-card border border-surface-variant/40">
        <h3 class="font-bold text-lg mb-2 text-on-surface"><?= \App\Core\Lang::t('staff.premium_title') ?></h3>
        <p class="text-xs text-on-surface-variant mb-6 leading-relaxed"><?= \App\Core\Lang::t('staff.premium_desc') ?></p>
        <button onclick="closePremiumModal()" class="w-full h-11 bg-primary text-on-primary rounded-xl font-bold text-xs mb-2 shadow-xs cursor-pointer">Contactar Soporte</button>
        <button onclick="closePremiumModal()" class="w-full h-10 text-on-surface-variant hover:text-on-surface font-semibold text-xs cursor-pointer">Cerrar</button>
    </div>
</div>

<?php ob_start(); ?>
<script>
    let currentReportId = null;
    let currentCaseId = null;
    let colleaguesList = [];

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
                suggestionsDiv?.classList.add('hidden');
            }
        } else {
            suggestionsDiv?.classList.add('hidden');
        }
    }

    function renderSuggestions(list, fullMention, textarea) {
        const div = document.getElementById('mentions-suggestions');
        if (!div) return;
        div.innerHTML = list.map(user => `
            <div class="p-2.5 hover:bg-surface-container-low cursor-pointer flex items-center gap-2.5 border-b border-surface-variant/20 last:border-0" 
                 onclick="selectMention('${user.name}', '${fullMention}')">
                <div class="w-6 h-6 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold">
                    ${user.name.charAt(0)}
                </div>
                <div>
                    <p class="text-xs font-bold text-on-surface">${user.name}</p>
                    <p class="text-[9px] text-on-surface-variant uppercase tracking-wider">${user.role}</p>
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
        
        const newValue = textBeforeCursor.replace(/@([^@\s]*)$/, '@' + name + ' ') + textAfterCursor;
        textarea.value = newValue;
        textarea.focus();
        document.getElementById('mentions-suggestions')?.classList.add('hidden');
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

        const urlParams = new URLSearchParams(window.location.search);
        const reportId = urlParams.get('report_id');
        if (reportId) {
            loadReport(reportId);
        }
    });

    async function loadColleagues() {
        try {
            const res = await fetchJson('/staff/colleagues');
            if (res.success) colleaguesList = res.colleagues;
        } catch (e) {}
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function loadMentions() {
        try {
            const res = await fetchJson('/staff/mentions');
            if (res.success && res.mentions.length > 0) {
                document.getElementById('mentions-badge-mobile')?.classList.remove('hidden');
                document.getElementById('mentions-list').innerHTML = res.mentions.map(m => `
                    <div class="p-3 hover:bg-surface-container-low cursor-pointer border-b border-surface-variant/20" onclick="readMention(${m.id}, ${m.report_id})">
                        <p class="text-[9px] font-bold text-on-surface-variant uppercase">CASO-${m.report_id}</p>
                        <p class="text-xs font-bold text-primary">${escapeHtml(m.sender_name)}</p>
                    </div>`).join('');
            } else {
                document.getElementById('mentions-list').innerHTML = '<p class="p-6 text-center text-on-surface-variant text-xs italic">No hay menciones nuevas</p>';
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
        
        container.innerHTML = '<div class="flex h-full items-center justify-center text-primary"><span class="material-symbols-outlined animate-spin text-3xl">refresh</span></div>';
        container.classList.remove('hidden', 'md:flex');
        container.classList.add('flex');

        if (window.innerWidth < 768) { 
            container.classList.add('fixed', 'inset-0', 'bg-surface', 'z-50'); 
            inbox.classList.add('hidden');
        } else {
            container.classList.add('md:flex');
        }

        const res = await fetchJson(`/staff/reports/${id}`);
        if (!res.error) await renderDetail(res.report, res.messages);
        else container.innerHTML = `<div class="p-8 text-center"><p class="text-error font-bold text-xs mb-3">${escapeHtml(res.error)}</p><button onclick="closeDetailMobile()" class="h-10 bg-primary text-on-primary px-6 rounded-xl text-xs font-bold">Volver</button></div>`;
    }

    function closeDetailMobile() {
        const container = document.getElementById('report-detail-container');
        const inbox = document.getElementById('inbox-pane');
        
        container.classList.add('hidden', 'md:flex');
        container.classList.remove('fixed', 'inset-0', 'bg-surface', 'z-50', 'flex');
        inbox.classList.remove('hidden');
    }

    async function renderDetail(report, messages) {
        const container = document.getElementById('report-detail-container');
        
        let caseRes = { success: false, error: 'Iniciando...' };
        try {
            caseRes = await fetchJson(`/api/protocol/case/${report.id}`);
        } catch (e) {
            caseRes = { success: false, error: e.message };
        }

        const protocolCase = caseRes.success ? caseRes.case : null;
        const protocolMeta = caseRes.success ? caseRes.protocol_meta : null;
        const protocolError = caseRes.success ? null : (caseRes.error || 'Respuesta no válida');
        const isAdvancedProtocol = caseRes.success && protocolMeta && protocolMeta.current_actions && !protocolMeta.current_actions.some(a => a.key === 'not_implemented');

        let mHtml = messages.map(m => {
            const isMe = m.is_current_user;
            const isInt = parseInt(m.is_internal) === 1;
            return `<div class="flex gap-2.5 ${isMe?'flex-row-reverse':''} mb-3 font-display">
                <div class="w-7 h-7 rounded-lg bg-surface-container-high flex items-center justify-center text-[10px] font-bold text-on-surface-variant shrink-0">${escapeHtml(m.sender_name.charAt(0))}</div>
                <div class="${isInt?'bg-amber-500/10 border-amber-500/30 border text-amber-950':(isMe?'bg-primary text-on-primary':'bg-surface-container-lowest border border-surface-variant/40 text-on-surface')} p-3 rounded-2xl ${isMe?'rounded-tr-none':'rounded-tl-none'} shadow-xs max-w-[85%]">
                    ${isInt?'<p class="text-[9px] font-bold text-amber-800 mb-0.5 uppercase tracking-wider flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">lock</span> Nota Interna</p>':''}
                    <p class="text-xs md:text-sm font-normal whitespace-pre-wrap leading-relaxed">${escapeHtml(m.message)}</p>
                    <span class="text-[9px] opacity-70 block mt-1 ${isMe?'text-right':''}">${isMe?'Tú':escapeHtml(m.sender_name)} • ${m.created_at}</span>
                </div>
            </div>`;
        }).join('');

        container.innerHTML = `
            <div class="h-14 md:h-16 px-4 md:px-6 flex items-center justify-between bg-surface-container-lowest border-b border-surface-variant/40 z-20 shrink-0 font-display">
                <div class="flex items-center gap-2.5 min-w-0">
                    <button onclick="closeDetailMobile()" class="md:hidden text-on-surface-variant hover:text-primary cursor-pointer"><span class="material-symbols-outlined text-xl">arrow_back</span></button>
                    <div class="min-w-0">
                        <h3 class="font-bold text-xs md:text-sm text-on-surface truncate">Caso #${report.id}</h3>
                        <p class="text-[10px] text-on-surface-variant uppercase font-semibold truncate">${escapeHtml(report.classroom_name || 'Aula')}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <select onchange="handleStatusChange(${report.id}, this.value)" id="status-select" class="bg-surface-container-low border border-surface-variant/40 rounded-xl py-1 px-3 text-[10px] font-bold uppercase outline-none focus:border-primary">
                        <option value="new" ${report.status==='new'?'selected':''}>${'<?= \App\Core\Lang::t('staff.status_received') ?>'}</option>
                        <option value="in_progress" ${report.status==='in_progress'?'selected':''}>${'<?= \App\Core\Lang::t('staff.status_review') ?>'}</option>
                        <option value="resolved" ${report.status==='resolved'?'selected':''}>${'<?= \App\Core\Lang::t('staff.status_resolved') ?>'}</option>
                    </select>
                </div>
            </div>

            <!-- LEGAL PROTOCOL TIMELINE -->
            ${isAdvancedProtocol ? protocolMeta.timeline_html : ''}

            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 bg-surface">
                <div class="bg-surface-container-lowest p-4 md:p-5 rounded-2xl shadow-card border border-surface-variant/40">
                    <h4 class="text-[10px] font-bold uppercase text-on-surface-variant mb-2 tracking-wider font-display"><?= \App\Core\Lang::t('staff.student_story') ?></h4>
                    <p class="text-xs md:text-sm text-on-surface whitespace-pre-wrap leading-relaxed">${escapeHtml(report.content)}</p>
                </div>
                
                <!-- PROTOCOL ACTIONS CARD -->
                ${protocolError ? `
                    <div class="bg-error/10 p-4 rounded-2xl border border-error/20 text-error text-xs font-semibold">
                        Protocolo no disponible: ${escapeHtml(protocolError)}
                    </div>
                ` : (protocolMeta ? renderProtocolActionsCard(protocolCase, protocolMeta) : '')}

                <!-- MÒDUL RESTAURATIU -->
                <div id="restorative-panel-container"></div>
                <div id="messages-flow" class="space-y-3 pt-2">${mHtml || '<p class="text-center text-on-surface-variant py-8 text-xs italic"><?= \App\Core\Lang::t('staff.no_responses') ?></p>'}</div>
            </div>

            <div class="p-3 md:p-4 bg-surface-container-lowest border-t border-surface-variant/40 shrink-0 relative font-display">
                <div id="mentions-suggestions" class="hidden absolute bottom-full left-4 mb-2 w-64 bg-surface-container-lowest rounded-xl shadow-card border border-surface-variant/40 z-[100] overflow-hidden"></div>
                <div class="flex gap-2 mb-2">
                    <input id="reply-message" class="flex-1 h-11 bg-surface-container-low border border-surface-variant/40 rounded-xl px-4 text-xs md:text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-outline-variant" placeholder="<?= \App\Core\Lang::t('dashboard.chat_placeholder') ?>"/>
                    <button onclick="sendMessage()" class="w-11 h-11 bg-primary text-on-primary rounded-xl flex items-center justify-center shadow-xs active:scale-95 transition-transform cursor-pointer shrink-0"><span class="material-symbols-outlined text-[18px]">send</span></button>
                </div>
                <label class="text-[11px] font-semibold text-on-surface-variant flex items-center gap-2 pl-2 cursor-pointer select-none">
                    <input type="checkbox" id="reply-internal" class="rounded text-primary border-surface-variant/60 focus:ring-primary/20"/> <?= \App\Core\Lang::t('staff.mark_internal') ?>
                </label>
            </div>
        `;

        if (protocolCase) {
            currentCaseId = protocolCase.id;
            const resPanel = document.getElementById('restorative-panel-container');
            const originalModule = document.getElementById('restorative-module');
            
            if (resPanel && originalModule) {
                const activeProtocol = caseRes.ccaa;
                let showRestorative = (activeProtocol === 'CAT' || activeProtocol === 'ARA');

                if (showRestorative) {
                    resPanel.appendChild(originalModule);
                    originalModule.classList.remove('hidden');
                    loadRestorativeModule(currentCaseId);
                } else {
                    originalModule.classList.add('hidden');
                    document.body.appendChild(originalModule);
                }
            }
        }
    }

    function renderProtocolActionsCard(c, meta) {
        if (!c || !meta || !meta.current_actions || !Array.isArray(meta.current_actions) || meta.current_actions.length === 0) {
             return '';
        }

        const actions = meta.current_actions;
        
        const getStyleClass = (style) => {
            switch(style) {
                case 'primary': return 'bg-primary text-on-primary shadow-xs hover:bg-primary/90';
                case 'secondary': return 'bg-surface-container-high text-on-surface hover:bg-surface-container-highest';
                case 'danger': return 'bg-error text-white shadow-xs hover:bg-error/90';
                case 'danger-outline': return 'border border-error text-error hover:bg-error/10';
                case 'success': return 'bg-teal-700 text-white shadow-xs hover:bg-teal-800';
                case 'warning': return 'bg-amber-600 text-white shadow-xs hover:bg-amber-700';
                case 'alert': return 'bg-amber-500/10 border border-amber-500/30 text-amber-900 text-xs p-4 rounded-xl';
                default: return 'bg-surface-container-high text-on-surface';
            }
        };

        const comms = c.communications ? (typeof c.communications === 'string' ? JSON.parse(c.communications) : c.communications) : {};
        const checks = c.closure_checks ? (typeof c.closure_checks === 'string' ? JSON.parse(c.closure_checks) : c.closure_checks) : {};

        const buttonActions = actions.filter(a => !['reva_checklist', 'closure_checklist'].includes(a.style));
        const revaAction = actions.find(a => a.style === 'reva_checklist');
        const closureAction = actions.find(a => a.style === 'closure_checklist');

        return `
            <div class="bg-surface-container-lowest p-5 md:p-6 rounded-2xl border border-surface-variant/40 shadow-card space-y-4 font-display">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider leading-none mb-1">Fase Actual Protocolo</h4>
                        <p class="text-xs md:text-sm font-bold text-primary uppercase tracking-tight">${c.current_phase.replace(/_/g, ' ')}</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">gavel</span>
                    </div>
                </div>

                <div class="${buttonActions.some(a => a.style === 'alert') ? '' : 'grid grid-cols-1 sm:grid-cols-2 gap-2.5'}">
                    ${buttonActions.map(a => {
                        if (a.style === 'alert') {
                            return `<div class="${getStyleClass(a.style)}">${a.label}</div>`;
                        }
                        return `
                            <button onclick="${a.onclick}" class="h-11 px-4 rounded-xl text-xs font-bold tracking-wide transition-all flex items-center justify-center text-center gap-1.5 cursor-pointer ${getStyleClass(a.style)}">
                                ${a.label}
                            </button>
                        `;
                    }).join('')}
                </div>
                
                ${revaAction ? `
                    <div class="pt-3 border-t border-surface-variant/30 space-y-2">
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant">Requerimientos REVA</p>
                        <div class="space-y-1.5">
                             <label class="flex items-center gap-2.5 p-2.5 bg-surface-container-low rounded-xl cursor-pointer">
                                <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.inspeccio ? 'checked' : ''} onchange="toggleComm(${c.id}, 'inspeccio', this.checked)">
                                <span class="text-xs font-semibold text-on-surface">Comunicado a Inspección (REVA)</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-2.5 bg-surface-container-low rounded-xl cursor-pointer">
                                <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.familia_victima ? 'checked' : ''} onchange="toggleComm(${c.id}, 'familia_victima', this.checked)">
                                <span class="text-xs font-semibold text-on-surface">Comunicado a la familia de la víctima</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-2.5 bg-surface-container-low rounded-xl cursor-pointer">
                                <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.familia_agressor ? 'checked' : ''} onchange="toggleComm(${c.id}, 'familia_agressor', this.checked)">
                                <span class="text-xs font-semibold text-on-surface">Comunicado a la familia del presunto agresor</span>
                            </label>
                        </div>
                    </div>
                ` : ''}

                ${closureAction ? `
                    <div class="pt-3 border-t border-surface-variant/30 space-y-2">
                        <h5 class="text-[10px] font-bold uppercase text-primary tracking-wider">Checklist de Cierre Oficial</h5>
                        <div class="space-y-1">
                            ${renderClosureCheck(c.id, 'eradicated', 'La violencia se ha erradicado definitivamente', checks.eradicated)}
                            ${renderClosureCheck(c.id, 'reparation', 'Se ha completado el proceso de reparación', checks.reparation)}
                            ${renderClosureCheck(c.id, 'students_confirm', 'El alumnado confirma la mejora', checks.students_confirm)}
                            ${renderClosureCheck(c.id, 'teachers_valorate', 'El equipo docente valora la resolución', checks.teachers_valorate)}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function renderClosureCheck(caseId, key, label, checked) {
        return `
            <label class="flex items-center gap-2.5 p-2 hover:bg-surface-container-low rounded-lg cursor-pointer transition-colors">
                <input type="checkbox" class="closure-check w-4 h-4 rounded text-teal-600" ${checked ? 'checked' : ''} onchange="toggleClosure(${caseId}, '${key}', this.checked)">
                <span class="text-xs font-medium text-on-surface">${label}</span>
            </label>
        `;
    }

    let currentClosureChecks = {};
    async function toggleClosure(caseId, key, checked) {
        currentClosureChecks[key] = checked;
        await fetchJson(`/api/protocol/case/${caseId}/closure`, { method: 'POST', body: { checks: currentClosureChecks } });
    }

    let currentComms = {};
    async function toggleComm(caseId, key, checked) {
        currentComms[key] = checked;
        await fetchJson(`/api/protocol/case/${caseId}/communications`, { method: 'POST', body: { comms: currentComms } });
    }

    async function handleStatusChange(id, status) {
        if (status === 'resolved') {
            const sum = prompt("<?= \App\Core\Lang::t('staff.resolution_prompt') ?>");
            if (!sum) { document.getElementById('status-select').value = 'in_progress'; return; }
            updateStatus(id, status, sum);
        } else {
            updateStatus(id, status);
        }
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

<?php require __DIR__ . '/partials/restorative_panel.php'; ?>

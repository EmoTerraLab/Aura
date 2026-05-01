<?php $bodyClass = "bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col overflow-hidden"; ?>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-[50] flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b">
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-600">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <img src="/icono-sinfondo.png" class="w-8 h-8" alt="Aura">
    </div>
    <div class="flex items-center gap-2">
        <button onclick="toggleMentions()" class="p-2 text-slate-500 relative">
            <span class="material-symbols-outlined">notifications</span>
            <div id="mentions-badge-mobile" class="hidden absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"></div>
        </button>
    </div>
</nav>

<!-- App Shell -->
<div class="flex flex-1 h-screen pt-16 lg:pt-0 overflow-hidden relative">
    
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-black/20 backdrop-blur-sm z-[55] lg:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="app-sidebar" class="fixed lg:static inset-y-0 left-0 w-72 bg-slate-50 dark:bg-slate-950 border-r z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col shadow-2xl lg:shadow-none">
        <div class="p-8 flex items-center gap-3">
            <img src="/icono-sinfondo.png" class="w-10 h-10" alt="Aura">
            <h1 class="font-h2 text-h2 text-primary">Aura</h1>
        </div>

        <div class="flex-1 overflow-y-auto no-scrollbar py-4 space-y-1">
            <a class="flex items-center gap-3 bg-primary/10 text-primary px-4 py-3 mx-2 rounded-full transition-colors" href="/staff/inbox">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">inbox</span>
                <span class="font-body-md text-body-md font-bold"><?= \App\Core\Lang::t('staff.inbox_title') ?></span>
            </a>
            
            <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="#">
                <span class="material-symbols-outlined">folder_open</span>
                <span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.active_cases') ?></span>
            </a>

            <?php 
            $ccaaProtocol = \App\Services\Protocol\ProtocolFactory::make(\App\Core\Config::get('ccaa_code'));
            if ($ccaaProtocol->isFullyImplemented()): ?>
            <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="/protocolos/dashboard">
                <span class="material-symbols-outlined">dashboard_customize</span>
                <span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('protocol.dashboard_title') ?> (<?= $ccaaProtocol->getName() ?>)</span>
            </a>
            <?php endif; ?>

            <?php if (\App\Core\Config::get('ccaa_protocol_active', '1') === '1'): ?>
                <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="/protocolo-acoso">
                    <span class="material-symbols-outlined">gavel</span>
                    <span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('protocol.title') ?></span>
                </a>
            <?php endif; ?>

            <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="/profile/password">
                <span class="material-symbols-outlined">lock</span>
                <span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('auth.change_password') ?></span>
            </a>

            <div class="mt-8 px-4">
                <div class="bg-secondary-container rounded-DEFAULT p-4 ambient-shadow relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/20 rounded-full blur-xl"></div>
                    <span class="material-symbols-outlined text-secondary mb-2">hub</span>
                    <h3 class="font-body-md text-body-md font-semibold text-on-secondary-container leading-tight"><?= \App\Core\Lang::t('nav.sociograms') ?></h3>
                    <p class="font-label-caps text-label-caps text-secondary mt-1 normal-case"><?= \App\Core\Lang::t('nav.hidden_dynamics') ?></p>
                </div>
            </div>
        </div>

        <div class="p-4 border-t bg-white dark:bg-slate-900">
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-surface-container-low border border-surface-variant/30">
                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold shadow-md"><?= substr($user['name'], 0, 1) ?></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold truncate"><?= $user['name'] ?></p>
                    <p class="text-[10px] text-outline uppercase font-black tracking-tighter opacity-60"><?= $user['role'] ?></p>
                </div>
                <form action="/logout" method="POST">
                    <button class="p-2 text-error hover:bg-error-container rounded-full transition-colors"><span class="material-symbols-outlined">logout</span></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden bg-white">
        
        <!-- List Pane -->
        <div id="inbox-pane" class="w-full md:w-[400px] border-r flex flex-col shrink-0 bg-slate-50/30">
            <div class="p-6 md:p-8 flex flex-col gap-4 border-b bg-white/50 backdrop-blur-sm sticky top-0 z-10">
                <h2 class="font-h2 text-h2 text-on-surface"><?= \App\Core\Lang::t('staff.inbox_title') ?></h2>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-3 text-slate-400 text-lg">search</span>
                    <input type="text" placeholder="<?= \App\Core\Lang::t('staff.search_placeholder') ?>" class="w-full bg-slate-100/80 border-0 rounded-full py-3 pl-12 pr-6 text-sm outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto no-scrollbar">
                <?php if (empty($reports)): ?>
                    <div class="p-20 text-center space-y-4">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-300">
                            <span class="material-symbols-outlined text-4xl">inbox</span>
                        </div>
                        <p class="text-sm text-slate-400 italic"><?= \App\Core\Lang::t('staff.inbox_empty') ?></p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($reports as $r): ?>
                        <div onclick="loadReport(<?= $r['id'] ?>)" class="p-6 hover:bg-white cursor-pointer transition-all border-l-4 <?= $r['status'] === 'new' ? 'border-primary' : 'border-transparent' ?> group">
                            <div class="flex justify-between items-start mb-2">
                                <?php $urgency = (!empty($r['urgency_level'])) ? $r['urgency_level'] : 'low'; ?>
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full <?= $urgency === 'high' ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-500' ?>">
                                    <?= \App\Core\Lang::t('dashboard.urgency_' . $urgency) ?>
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold"><?= date('H:i', strtotime($r['created_at'])) ?></span>
                            </div>
                            <h3 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors mb-1"><?= $r['student_name'] ?></h3>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-3"><?= $r['content'] ?></p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1 text-[10px] text-slate-400 uppercase font-black tracking-tighter">
                                        <span class="material-symbols-outlined text-xs">chat_bubble</span>
                                        <span><?= $r['message_count'] ?? 0 ?></span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[10px] text-slate-400 uppercase font-black tracking-tighter">
                                        <span class="material-symbols-outlined text-xs">meeting_room</span>
                                        <span><?= $r['classroom_name'] ?></span>
                                    </div>
                                </div>
                                <?php 
                                $protocol = \App\Services\Protocol\ProtocolFactory::make(\App\Core\Config::get('ccaa_code'));
                                ?>
                                <a href="<?= $protocol->getManageUrl($r['id']) ?>" class="text-[9px] font-black uppercase bg-primary/10 text-primary px-3 py-1 rounded-full hover:bg-primary hover:text-white transition-all stop-propagation" onclick="event.stopPropagation()">
                                    <?php if ($protocol->isFullyImplemented()): ?>
                                        Gestionar Protocolo (<?= $protocol->getName() ?>)
                                    <?php else: ?>
                                        Consultar Normativa de <?= $protocol->getName() ?>
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
        <div id="report-detail-container" class="hidden md:flex flex-1 flex-col bg-white relative">
            <div class="flex-1 flex flex-col items-center justify-center text-slate-300 p-20 text-center space-y-6">
                <div class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center border-2 border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-6xl">chat</span>
                </div>
                <div>
                    <h3 class="font-h2 text-lg text-slate-400"><?= \App\Core\Lang::t('staff.select_case_title') ?></h3>
                    <p class="text-sm text-slate-400 mt-2"><?= \App\Core\Lang::t('staff.select_case_desc') ?></p>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Mentions Dropdown -->
<div id="mentions-dropdown" class="hidden fixed top-16 right-4 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 z-[100] overflow-hidden">
    <div class="p-4 border-b bg-slate-50/50 flex justify-between items-center">
        <h3 class="font-bold text-xs uppercase tracking-widest text-slate-500"><?= \App\Core\Lang::t('staff.mentions_title') ?></h3>
        <button onclick="toggleMentions()" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined text-sm">close</span></button>
    </div>
    <div id="mentions-list" class="max-h-96 overflow-y-auto no-scrollbar"></div>
</div>

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
    let currentCaseId = null;
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
        if (!res.error) await renderDetail(res.report, res.messages);
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
        const isAdvancedProtocol = !caseRes.protocol_meta.current_actions.some(a => a.key === 'not_implemented');

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
            ${isAdvancedProtocol ? `
            <div id="protocol-timeline" class="bg-white border-b px-4 md:px-8 py-4 flex items-center justify-between overflow-x-auto no-scrollbar gap-4">
                ${renderTimelineHtml(protocolCase, caseRes.protocol_meta)}
            </div>
            ` : ''}

            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6 bg-slate-50 no-scrollbar">
                <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h4 class="text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest"><?= \App\Core\Lang::t('staff.student_story') ?></h4>
                    <p class="text-sm text-slate-800 whitespace-pre-wrap">${report.content}</p>
                </div>
                
                <!-- PROTOCOL ACTIONS CARD -->
                ${renderProtocolActionsCard(protocolCase, caseRes.protocol_meta)}

                
            <!-- MÒDUL RESTAURATIU -->
            <div id="restorative-panel-container"></div>
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

        // Inicializar Módulo Restaurativo
        if (protocolCase) {
            currentCaseId = protocolCase.id;
            const resPanel = document.getElementById('restorative-panel-container');
            const originalModule = document.getElementById('restorative-module');
            
            if (resPanel && originalModule) {
                const activeProtocol = caseRes.ccaa;
                
                // Lógica de visibilidad del módulo restaurativo
                let showRestorative = (activeProtocol === 'CAT');
                
                // En Aragón también se usa el módulo, pero solo a partir de la fase de Valoración
                if (activeProtocol === 'ARA') {
                    const earlyPhases = ['comunicacion_recibida', 'protocolo_iniciado', 'protocolo_no_iniciado'];
                    showRestorative = !earlyPhases.includes(protocolCase.current_phase);
                }

                if (showRestorative) {
                    resPanel.appendChild(originalModule);
                    originalModule.classList.remove('hidden');
                    loadRestorativeModule(currentCaseId);
                } else {
                    originalModule.classList.add('hidden');
                    document.body.appendChild(originalModule); // Lo devolvemos al body oculto
                }
            }
        }
    }

    function renderTimelineHtml(c, meta) {
        if (!c || !meta || !meta.timeline_steps) return '<p class="text-[10px] text-slate-400 italic">Protocolo legal no activado para este caso.</p>';

        const steps = meta.timeline_steps;
        const activeIndex = meta.active_step_index;

        const timelineHtml = steps.map((s, idx) => {
            const isActual = idx === activeIndex;
            const isPast = idx < activeIndex;
            
            let colorClass = isActual ? 'bg-primary text-white shadow-lg shadow-primary/20 scale-110' : (isPast ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400');
            let textColor = isActual ? 'text-primary' : (isPast ? 'text-emerald-600' : 'text-slate-400');

            return `
                <div class="flex items-center gap-2 shrink-0 transition-all">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-bold ${colorClass}">
                        <span class="material-symbols-outlined text-sm">${isPast ? 'check' : s.icon}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase tracking-tighter ${textColor}">${s.label}</span>
                        ${s.deadline_days ? `<span class="text-[8px] font-bold text-slate-300">Día ${s.deadline_days}</span>` : ''}
                    </div>
                    ${idx < steps.length - 1 ? '<span class="material-symbols-outlined text-slate-200 text-sm mx-1">chevron_right</span>' : ''}
                </div>
            `;
        }).join('');

        if (meta.deadline_alert) {
            return timelineHtml + `
                <div class="ml-auto px-4 py-2 rounded-xl text-[9px] font-black uppercase border ${getAlertColorClass(meta.deadline_alert.level)} flex items-center gap-2 shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xs">schedule</span> ${meta.deadline_alert.message}
                </div>
            `;
        }

        return timelineHtml;
    }

    function getAlertColorClass(level) {
        switch(level) {
            case 'ok': return 'text-emerald-600 bg-emerald-50 border-emerald-100';
            case 'warning': return 'text-amber-600 bg-amber-50 border-amber-100';
            case 'danger': return 'text-red-600 bg-red-50 border-red-100';
            case 'overdue': return 'bg-red-900 text-white animate-pulse border-red-900';
            default: return 'text-slate-500 bg-slate-50 border-slate-100';
        }
    }

    function renderProtocolActionsCard(c, meta) {
        if (!c || !meta || !meta.current_actions || meta.current_actions.length === 0) {
             return `
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 italic text-slate-400 text-xs text-center">
                    No hay acciones disponibles para esta fase.
                </div>
            `;
        }

        const actions = meta.current_actions;
        
        const getStyleClass = (style) => {
            switch(style) {
                case 'primary': return 'bg-primary text-white shadow-lg shadow-primary/20 hover:scale-105';
                case 'secondary': return 'bg-slate-100 text-slate-600 hover:bg-slate-200';
                case 'danger': return 'bg-red-500 text-white shadow-lg shadow-red-500/20 hover:bg-red-600';
                case 'danger-outline': return 'border-2 border-red-500 text-red-600 hover:bg-red-50';
                case 'success': return 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-600';
                case 'warning': return 'bg-amber-500 text-white shadow-lg shadow-amber-500/20 hover:bg-amber-600';
                case 'indigo': return 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-700';
                case 'dark': return 'bg-slate-800 text-white hover:bg-slate-900';
                case 'link': return 'text-primary underline font-bold hover:text-primary/80';
                case 'alert': return 'bg-amber-50 border border-amber-100 text-amber-700 italic text-xs p-6 rounded-2xl';
                default: return 'bg-slate-100 text-slate-600';
            }
        };

        const comms = typeof c.communications === 'string' ? JSON.parse(c.communications || '{}') : (c.communications || {});
        const checks = typeof c.closure_checks === 'string' ? JSON.parse(c.closure_checks || '{}') : (c.closure_checks || {});

        const buttonActions = actions.filter(a => !['reva_checklist', 'closure_checklist'].includes(a.style));
        const revaAction = actions.find(a => a.style === 'reva_checklist');
        const closureAction = actions.find(a => a.style === 'closure_checklist');

        return `
            <div class="bg-white p-8 rounded-[2.5rem] border-2 border-primary/10 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest leading-none mb-1">Fase Actual</h4>
                        <p class="text-sm font-black text-slate-800 uppercase tracking-tight">${c.current_phase.replace(/_/g, ' ')}</p>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-primary/5 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">auto_fix</span>
                    </div>
                </div>

                <div class="${buttonActions.some(a => a.style === 'alert') ? '' : 'grid grid-cols-1 sm:grid-cols-2 gap-3'}">
                    ${buttonActions.map(a => {
                        if (a.style === 'alert') {
                            return `<div class="${getStyleClass(a.style)}">${a.label}</div>`;
                        }
                        return `
                            <button onclick="${a.onclick}" class="px-5 py-4 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all flex items-center justify-center text-center gap-2 ${getStyleClass(a.style)}">
                                ${a.label}
                            </button>
                        `;
                    }).join('')}
                </div>
                
                ${revaAction ? `
                    <div class="pt-4 border-t border-slate-50">
                        <p class="text-[9px] font-black uppercase text-slate-400 mb-2"><?= \App\Core\Lang::t('protocol.reva_requirements') ?></p>
                        <div class="space-y-2">
                             <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                                <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.inspeccio ? 'checked' : ''} onchange="toggleComm(${c.id}, 'inspeccio', this.checked)">
                                <span class="text-xs font-bold text-slate-700"><?= \App\Core\Lang::t('protocol.comm_inspeccion') ?></span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                                <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.familia_victima ? 'checked' : ''} onchange="toggleComm(${c.id}, 'familia_victima', this.checked)">
                                <span class="text-xs font-bold text-slate-700"><?= \App\Core\Lang::t('protocol.comm_familia_victima') ?></span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer">
                                <input type="checkbox" class="comm-check w-4 h-4 rounded text-primary" ${comms.familia_agressor ? 'checked' : ''} onchange="toggleComm(${c.id}, 'familia_agressor', this.checked)">
                                <span class="text-xs font-bold text-slate-700"><?= \App\Core\Lang::t('protocol.comm_familia_agresor') ?></span>
                            </label>
                        </div>
                    </div>
                ` : ''}

                ${closureAction ? `
                    <div class="pt-4 border-t border-slate-50 space-y-4">
                        <h5 class="text-[9px] font-black uppercase text-primary tracking-widest"><?= \App\Core\Lang::t('protocol.closure_checklist') ?></h5>
                        <div class="space-y-2">
                            ${renderClosureCheck(c.id, 'eradicated', '<?= \App\Core\Lang::t('protocol.closure_eradicated') ?>', checks.eradicated)}
                            ${renderClosureCheck(c.id, 'reparation', '<?= \App\Core\Lang::t('protocol.closure_reparation') ?>', checks.reparation)}
                            ${renderClosureCheck(c.id, 'students_confirm', '<?= \App\Core\Lang::t('protocol.closure_students_confirm') ?>', checks.students_confirm)}
                            ${renderClosureCheck(c.id, 'teachers_valorate', '<?= \App\Core\Lang::t('protocol.closure_teachers_valorate') ?>', checks.teachers_valorate)}
                        </div>
                    </div>
                ` : ''}

                ${meta.ccaa_code === 'CAT' && c.current_phase === 'violencia_sexual_actiu' ? `
                    <div class="w-full bg-red-50 p-4 rounded-xl space-y-3">
                        <p class="text-xs text-red-800 font-medium"><?= \App\Core\Lang::t('protocol.sexual_violence_alert_cat') ?></p>
                    </div>
                ` : ''}

                ${meta.ccaa_code === 'ARA' && c.current_phase === 'violencia_sexual_activa' ? `
                    <div class="w-full bg-red-50 p-4 rounded-xl space-y-3">
                        <p class="text-xs text-red-800 font-medium"><?= \App\Core\Lang::t('protocol.sexual_violence_alert_ara') ?></p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function openFollowupModalAragon(caseId) {
        const html = `
            <div id="modal-followup-aragon" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden animate-[scaleIn_0.3s_ease-out]">
                    <div class="p-6 border-b flex items-center justify-between">
                        <h3 class="font-black">Registro de Seguimiento (ANEXO IX)</h3>
                        <button onclick="document.getElementById('modal-followup-aragon').remove()"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 pl-4">Tipo de Sesión / Periodicidad</label>
                            <select id="f-target-ar" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm">
                                <option value="semanal_m1">Semanal (Primer mes)</option>
                                <option value="quincenal_m2">Quincenal (Segundo mes)</option>
                                <option value="mensual_m3">Mensual (A partir del tercer mes)</option>
                                <option value="extraordinaria">Sesión Extraordinaria</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 pl-4">Fecha de la sesión</label>
                            <input type="date" id="f-date-ar" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 pl-4">Actuaciones y acuerdos</label>
                            <textarea id="f-notes-ar" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm" rows="4" placeholder="Escriba aquí los acuerdos alcanzados..."></textarea>
                        </div>
                        <button onclick="saveFollowupAragon(${caseId})" class="w-full py-4 bg-primary text-white rounded-full font-bold shadow-lg hover:scale-105 transition-transform">Guardar Registro Anexo IX</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    async function saveFollowupAragon(caseId) {
        const data = {
            target_type: document.getElementById('f-target-ar').value,
            session_date: document.getElementById('f-date-ar').value,
            notes: document.getElementById('f-notes-ar').value
        };
        const res = await fetchJson(`/api/protocol/case/${caseId}/followup`, { method: 'POST', body: data });
        if (res.success) {
            document.getElementById('modal-followup-aragon').remove();
            window.location.reload();
        }
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
                        <h3 class="font-black"><?= \App\Core\Lang::t('protocol.followup_new_title') ?></h3>
                        <button onclick="document.getElementById('modal-followup').remove()"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <select id="f-target" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm">
                            <option value="victima"><?= \App\Core\Lang::t('protocol.followup_target_victima') ?></option>
                            <option value="agressor"><?= \App\Core\Lang::t('protocol.followup_target_agresor') ?></option>
                            <option value="familia"><?= \App\Core\Lang::t('protocol.followup_target_familia') ?></option>
                            <option value="grup_classe"><?= \App\Core\Lang::t('protocol.followup_target_clase') ?></option>
                        </select>
                        <input type="date" id="f-date" class="w-full bg-slate-50 border-0 rounded-full py-3 px-6 text-sm" value="${new Date().toISOString().split('T')[0]}">
                        <textarea id="f-notes" class="w-full bg-slate-50 border-0 rounded-2xl p-4 text-sm" rows="4" placeholder="<?= \App\Core\Lang::t('protocol.followup_notes_placeholder') ?>"></textarea>
                        <button onclick="saveFollowup(${caseId})" class="w-full py-4 bg-primary text-white rounded-full font-bold shadow-lg"><?= \App\Core\Lang::t('protocol.followup_save_btn') ?></button>
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

    async function copyRevaSummary(caseId) {
        const res = await fetchJson(`/api/protocol/case/${caseId}/reva`);
        if (res.success) {
            await navigator.clipboard.writeText(res.summary);
            alert('Resum per al REVA copiat al porta-retalls.');
        }
    }

    async function uploadEvidence(caseId, input) {
        if (!input.files || input.files.length === 0) return;
        
        const formData = new FormData();
        formData.append('evidence', input.files[0]);

        try {
            const res = await fetch(`/api/protocol/case/${caseId}/evidence`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) alert('Evidència guardada correctament en custòdia.');
            else alert('Error: ' + data.error);
        } catch (e) {
            alert('Error en la pujada.');
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

<?php require __DIR__ . '/partials/restorative_panel.php'; ?>
p'; ?>
hp'; ?>
p'; ?>

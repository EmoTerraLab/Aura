<?php $bodyClass = "bg-background text-on-surface font-body-md text-body-md antialiased min-h-screen flex flex-col md:flex-row overflow-hidden"; ?>

<!-- SideNavBar -->
<nav class="bg-slate-50 dark:bg-slate-950 shadow-[4px_0_24px_rgba(6,105,114,0.04)] hidden lg:flex flex-col py-6 h-screen w-64 fixed left-0 top-0 z-40">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">spa</span></div>
        <div><h1 class="font-h2 text-h2 text-teal-700 font-black tracking-tight leading-none">Aura</h1><p class="font-label-caps text-label-caps text-surface-tint opacity-70 mt-1">School Sanctuary</p></div>
    </div>
    <div class="flex-1 overflow-y-auto no-scrollbar space-y-1">
        <a class="flex items-center gap-3 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-full mx-2 px-4 py-3 transition-colors scale-95 duration-150" href="/staff/inbox"><span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inbox</span><span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.inbox') ?></span></a>
        <?php if (\App\Core\Auth::role() === 'admin'): ?><a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="/admin"><span class="material-symbols-outlined">admin_panel_settings</span><span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.admin_panel') ?></span></a><?php endif; ?>
        <a class="flex items-center gap-3 text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full transition-colors" href="#"><span class="material-symbols-outlined">folder_open</span><span class="font-body-md text-body-md font-medium"><?= \App\Core\Lang::t('nav.active_cases') ?></span></a>
        <div class="mt-8 px-4"><div class="bg-secondary-container rounded-DEFAULT p-4 ambient-shadow relative overflow-hidden"><div class="absolute -right-4 -top-4 w-16 h-16 bg-white/20 rounded-full blur-xl"></div><span class="material-symbols-outlined text-secondary mb-2">hub</span><h3 class="font-body-md text-body-md font-semibold text-on-secondary-container leading-tight"><?= \App\Core\Lang::t('nav.sociograms') ?></h3><p class="font-label-caps text-label-caps text-secondary mt-1 normal-case"><?= \App\Core\Lang::t('nav.hidden_dynamics') ?></p></div></div>
    </div>
    <div class="mt-auto pt-4 border-t border-surface-variant/50 mx-4 flex flex-col gap-1">
        <div class="px-4 py-2">
            <?= \App\Core\Lang::renderSelector() ?>
        </div>
        <div class="px-4 py-2 flex items-center justify-between text-xs text-slate-500"><span><?= htmlspecialchars(\App\Core\Auth::user()['name']) ?></span><span class="font-bold uppercase"><?= htmlspecialchars(\App\Core\Auth::role()) ?></span></div>
        <form action="/logout" method="POST"><input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/><button type="submit" class="w-full text-left flex items-center gap-3 text-slate-500 px-4 py-3 hover:bg-red-50 hover:text-red-600 rounded-full transition-colors"><span class="material-symbols-outlined">logout</span><span class="text-sm font-medium"><?= \App\Core\Lang::t('nav.logout_btn') ?></span></button></form>
    </div>
</nav>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-50 flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b border-surface-variant font-manrope">
    <h1 class="text-xl font-bold text-teal-700">Aura Staff</h1>
    <button onclick="toggleMentions()" class="relative text-slate-500"><span class="material-symbols-outlined">notifications</span><span id="mentions-badge-mobile" class="hidden absolute top-0 right-0 h-2 w-2 rounded-full bg-error"></span></button>
</nav>

<!-- Dropdown Menciones -->
<div id="mentions-dropdown" class="hidden fixed lg:absolute right-4 top-16 lg:top-20 mt-2 w-80 rounded-xl shadow-lg bg-surface-container-lowest ring-1 ring-black ring-opacity-5 z-[60] ambient-shadow overflow-hidden">
    <div class="p-4 bg-surface-container-low border-b border-surface-variant flex justify-between items-center"><h3 class="text-sm font-bold text-on-surface"><?= \App\Core\Lang::t('staff.mentions_title') ?></h3></div>
    <div id="mentions-list" class="max-h-64 overflow-y-auto no-scrollbar"></div>
</div>

<main class="flex-1 lg:ml-64 flex flex-col md:flex-row h-screen pt-16 lg:pt-0 bg-surface">
    <!-- Left Pane -->
    <section class="w-full md:w-[35%] lg:w-[30%] h-full flex flex-col bg-surface-container-lowest ambient-shadow z-10 overflow-hidden">
        <div class="p-6 pb-2">
            <h2 class="font-h2 text-h2 text-on-surface mb-4"><?= \App\Core\Lang::t('staff.inbox_title') ?></h2>
            <div class="flex gap-2 mb-4 overflow-x-auto no-scrollbar pb-2">
                <button class="whitespace-nowrap px-4 py-1.5 rounded-full bg-primary-container text-on-primary-container font-label-caps text-label-caps tracking-wide"><?= \App\Core\Lang::t('staff.filter_all') ?></button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto no-scrollbar px-4 pb-24 lg:pb-6 space-y-2">
            <?php foreach($reports as $report): ?>
                <?php $badge = ($report['status'] === 'new') ? 'bg-[#f8d7da] text-[#721c24]' : (($report['status'] === 'in_progress') ? 'bg-[#fff3cd] text-[#856404]' : 'bg-[#d4edda] text-[#155724]'); ?>
                <div class="bg-surface-container-lowest hover:bg-surface border-l-4 <?= $report['status']==='new'?'border-primary':'border-transparent' ?> p-4 rounded-DEFAULT cursor-pointer transition-colors" onclick="loadReport(<?= $report['id'] ?>)">
                    <div class="flex justify-between items-start mb-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full <?= $badge ?> font-label-caps text-[10px] tracking-wider uppercase font-bold"><?= \App\Core\Lang::t('staff.aula') ?> <?= htmlspecialchars($report['classroom_name']) ?></span>
                        <span class="font-label-caps text-[10px] text-outline"><?= date('d/m/y', strtotime($report['created_at'])) ?></span>
                    </div>
                    <h4 class="font-body-md text-[16px] font-semibold text-on-surface leading-tight mb-1 truncate"><?= htmlspecialchars($report['student_name']) ?></h4>
                    <p class="font-body-md text-[13px] text-on-surface-variant line-clamp-2"><?= htmlspecialchars($report['content']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Right Pane -->
    <section class="flex-1 h-full flex flex-col bg-surface-bright hidden md:flex relative" id="report-detail-container">
        <div class="flex-1 flex flex-col items-center justify-center text-outline">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-50">forum</span>
            <h3 class="font-h2 text-[20px] font-medium text-on-surface-variant"><?= \App\Core\Lang::t('staff.select_report') ?></h3>
        </div>
    </section>
</main>

<!-- Bottom Nav -->
<nav class="lg:hidden fixed bottom-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-white/90 backdrop-blur-lg shadow-lg rounded-t-[32px]">
    <a class="flex flex-col items-center text-slate-400" href="#"><span class="material-symbols-outlined">home</span><span class="text-[11px]"><?= \App\Core\Lang::t('nav.home') ?></span></a>
    <a class="flex flex-col items-center bg-teal-100 text-teal-800 rounded-full w-12 h-12" href="/staff/inbox"><span class="material-symbols-outlined">chat_bubble</span></a>
    <form action="/logout" method="POST" class="flex flex-col items-center justify-center">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>"/><button type="submit" class="text-slate-400"><span class="material-symbols-outlined">logout</span></button></form>
</nav>

<div id="premium-modal" class="fixed inset-0 z-[100] hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 text-center max-w-md m-4">
        <h3 class="font-h1 text-[24px] font-bold mb-2"><?= \App\Core\Lang::t('staff.premium_title') ?></h3>
        <p class="mb-8"><?= \App\Core\Lang::t('staff.premium_desc') ?></p>
        <button onclick="closePremiumModal()" class="w-full py-3 bg-primary text-white rounded-full mb-2"><?= \App\Core\Lang::t('staff.contact') ?></button>
        <button onclick="closePremiumModal()" class="w-full py-3 text-slate-500"><?= \App\Core\Lang::t('staff.close') ?></button>
    </div>
</div>

<?php ob_start(); ?>
<script>
    let currentReportId = null;
    let colleaguesList = [];

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
                        <p class="text-[10px] text-outline"><?= \App\Core\Lang::t('staff.case') ?>-${m.report_id}</p>
                        <p class="text-sm font-bold text-primary">${m.sender_name}</p>
                    </div>`).join('');
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
        container.innerHTML = '<div class="flex h-full items-center justify-center text-primary"><span class="material-symbols-outlined animate-spin text-4xl">refresh</span></div>';
        container.classList.remove('hidden');
        if (window.innerWidth < 768) { container.classList.add('absolute', 'inset-0', 'z-40'); }
        const res = await fetchJson(`/staff/reports/${id}`);
        if (!res.error) renderDetail(res.report, res.messages);
        else container.innerHTML = `<p class="p-10 text-error text-center font-bold">${res.error}</p>`;
    }

    function closeDetailMobile() {
        const container = document.getElementById('report-detail-container');
        container.classList.add('hidden'); container.classList.remove('absolute', 'inset-0', 'z-40');
    }

    function renderDetail(report, messages) {
        const container = document.getElementById('report-detail-container');
        let mHtml = messages.map(m => {
            const isMe = m.is_current_user;
            const isInt = parseInt(m.is_internal) === 1;
            return `<div class="flex gap-3 ${isMe?'flex-row-reverse':''} mb-4">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">${m.sender_name.charAt(0)}</div>
                <div class="${isInt?'bg-amber-50':(isMe?'bg-primary text-white':'bg-white border')} p-3 rounded-2xl ${isMe?'rounded-tr-none':'rounded-tl-none'} shadow-sm max-w-[85%]">
                    ${isInt?'<p class="text-[9px] font-black text-amber-800 mb-1">🔒 <?= \App\Core\Lang::t('staff.internal_note') ?></p>':''}
                    <p class="text-[13px] whitespace-pre-wrap">${m.message}</p>
                    <span class="text-[9px] opacity-60 block mt-1 ${isMe?'text-right':''}">${isMe?'Tú':m.sender_name} • ${m.created_at}</span>
                </div>
            </div>`;
        }).join('');

        container.innerHTML = `
            <div class="h-20 px-8 flex items-center justify-between bg-white border-b z-20">
                <div class="flex items-center gap-4">
                    <button onclick="closeDetailMobile()" class="md:hidden"><span class="material-symbols-outlined">arrow_back</span></button>
                    <div><h3 class="font-bold text-sm"><?= \App\Core\Lang::t('staff.case') ?> #\${report.id}</h3><p class="text-[10px] text-outline uppercase font-bold"><?= \App\Core\Lang::t('staff.aula') ?> \${report.classroom_name}</p></div>
                </div>
                <div class="flex gap-2">
                    <button onclick="showPremiumModal()" class="bg-slate-100 text-slate-700 px-3 py-1.5 rounded-full text-[10px] font-black uppercase"><?= \App\Core\Lang::t('staff.ia_elos') ?></button>
                    <select onchange="handleStatusChange(\${report.id}, this.value)" id="status-select" class="bg-slate-100 border-0 rounded-full py-1.5 px-4 text-[10px] font-black uppercase">
                        <option value="new" \${report.status==='new'?'selected':''}>\${'<?= \App\Core\Lang::t('staff.status_received') ?>'}</option>
                        <option value="in_progress" \${report.status==='in_progress'?'selected':''}>\${'<?= \App\Core\Lang::t('staff.status_review') ?>'}</option>
                        <option value="resolved" \${report.status==='resolved'?'selected':''}>\${'<?= \App\Core\Lang::t('staff.status_resolved') ?>'}</option>
                    </select>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-widest"><?= \App\Core\Lang::t('staff.student_story') ?></h4>
                    <p class="text-sm text-slate-800 whitespace-pre-wrap">\${report.content}</p>
                </div>
                <div id="messages-flow">\${mHtml || '<p class="text-center text-slate-400 py-10 text-xs italic"><?= \App\Core\Lang::t('staff.no_responses') ?></p>'}</div>
            </div>
            <div class="p-4 bg-white border-t">
                <div class="flex gap-2 mb-3">
                    <input id="reply-message" class="flex-1 bg-slate-100 border-0 rounded-full py-3 px-6 text-sm" placeholder="<?= \App\Core\Lang::t('dashboard.chat_placeholder') ?>"/>
                    <button onclick="sendMessage()" class="bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg shadow-primary/20"><span class="material-symbols-outlined">send</span></button>
                </div>
                <label class="text-[11px] font-bold text-slate-500 flex items-center gap-2 pl-4 cursor-pointer">
                    <input type="checkbox" id="reply-internal" class="rounded text-primary"/> <?= \App\Core\Lang::t('staff.mark_internal') ?>
                </label>
            </div>
        `;
    }

    async function handleStatusChange(id, status) {
        if (status === 'resolved') {
            const sum = prompt("<?= \App\Core\Lang::t('staff.resolution_prompt') ?>");
            if (!sum) { document.getElementById('status-select').value = 'in_progress'; return; }
            updateStatus(id, status, sum);
        } else updateStatus(id, status);
    }

    async function updateStatus(id, status, sum = null) {
        await fetchJson(`/staff/reports/\${id}`, { method: 'PATCH', body: { status, resolution_summary: sum } });
        window.location.reload();
    }

    async function sendMessage() {
        const msg = document.getElementById('reply-message').value.trim();
        const isInt = document.getElementById('reply-internal').checked;
        if (!msg || !currentReportId) return;
        const res = await fetchJson(`/staff/reports/\${currentReportId}/messages`, { method: 'POST', body: { message: msg, is_internal: isInt } });
        if (!res.error) loadReport(currentReportId);
        else alert(res.error);
    }
    
    function showPremiumModal() { document.getElementById('premium-modal').classList.remove('hidden'); document.getElementById('premium-modal').classList.add('flex'); }
    function closePremiumModal() { document.getElementById('premium-modal').classList.add('hidden'); document.getElementById('premium-modal').classList.remove('flex'); }
</script>
<?php $scripts = ob_get_clean(); ?>

<?php $bodyClass = "antialiased min-h-screen flex flex-col bg-surface"; ?>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-[50] flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b border-surface-variant font-manrope">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">eco</span>
        </div>
        <h1 class="text-lg font-bold text-teal-700">Aura</h1>
    </div>
    <button onclick="toggleSidebar()" class="p-2 text-slate-500">
        <span class="material-symbols-outlined" id="menu-icon">menu</span>
    </button>
</nav>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[55] hidden lg:hidden"></div>

<!-- Sidebar -->
<nav id="app-sidebar" class="bg-slate-50 dark:bg-slate-950 font-manrope font-medium h-screen w-64 fixed left-0 top-0 no-border shadow-right shadow-[4px_0_24px_rgba(6,105,114,0.04)] z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col py-6">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">eco</span>
        </div>
        <div>
            <h1 class="text-xl font-black text-teal-700">Aura</h1>
            <p class="text-xs text-slate-500">School Sanctuary</p>
        </div>
    </div>
    <div class="px-4 mb-6">
        <button onclick="resetForm(); toggleSidebar()" class="w-full bg-primary text-on-primary rounded-full py-3 px-4 flex items-center justify-center gap-2 shadow-sm shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined">add</span>
            <span class="font-semibold text-sm"><?= \App\Core\Lang::t('nav.new_report') ?></span>
        </button>
    </div>
    <div class="flex-1 flex flex-col gap-1 overflow-y-auto no-scrollbar">
        <a class="bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-full mx-2 px-4 py-3 flex items-center gap-3 active:scale-95 duration-150" href="/alumno/dashboard">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
            <span><?= \App\Core\Lang::t('nav.dashboard') ?></span>
        </a>
        <?php if (\App\Core\Config::get('ccaa_protocol_active', '1') === '1' && \App\Core\Config::get('ccaa_show_to_students', '1') === '1'): ?>
            <a class="text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full flex items-center gap-3 transition-colors" href="/protocolo-acoso">
                <span class="material-symbols-outlined">gavel</span>
                <span><?= \App\Core\Lang::t('protocol.title') ?></span>
            </a>
        <?php endif; ?>
        <button onclick="openBreathingApp(); toggleSidebar()" class="text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full flex items-center gap-3 transition-colors text-left">
            <span class="material-symbols-outlined">spa</span>
            <span><?= \App\Core\Lang::t('nav.breathe') ?></span>
        </button>
    </div>
    <div class="mt-auto flex flex-col gap-1">
        <div class="px-6 mb-4">
            <?= \App\Core\Lang::renderSelector() ?>
        </div>
        <form action="/logout" method="POST" class="mx-2">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <button type="submit" class="w-full text-left text-slate-500 dark:text-slate-400 px-4 py-3 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/10 rounded-full flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined">logout</span>
                <span><?= \App\Core\Lang::t('nav.logout') ?></span>
            </button>
        </form>
    </div>
</nav>

<!-- Main Content Canvas -->
<main class="flex-1 w-full lg:pl-64 flex flex-col pt-16 lg:pt-0">
    <div class="px-4 py-8 md:px-margin-page md:py-12 max-w-6xl mx-auto w-full flex-1 flex flex-col gap-stack-gap">
        
        <?php
        $db = \App\Core\Database::getInstance();
        $userId = \App\Core\Auth::id();
        $profile = $db->query("SELECT classroom_id FROM student_profiles WHERE user_id = $userId")->fetch();
        $isCataluna = \App\Core\Config::get('ccaa_code') === 'cataluna';
        
        if ($profile && $isCataluna) {
            $survey = $db->query("SELECT * FROM sociometric_surveys WHERE classroom_id = {$profile['classroom_id']} AND status = 'active' LIMIT 1")->fetch();
            if ($survey) {
                $hasResponded = $db->query("SELECT COUNT(*) FROM sociometric_responses WHERE survey_id = {$survey['id']} AND student_id = $userId")->fetchColumn();
                if (!$hasResponded) {
                    echo '
                    <div class="bg-primary-container text-on-primary-container p-6 rounded-xl shadow-sm flex items-center justify-between gap-4 mb-6 border border-primary/20">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-3xl">hub</span>
                            <div>
                                <p class="font-bold text-sm">Qüestionari de Clima d\'Aula pendent</p>
                                <p class="text-xs opacity-80">La teva participació ens ajuda a millorar la convivència a classe.</p>
                            </div>
                        </div>
                        <a href="/alumno/sociograma" class="bg-primary text-on-primary px-6 py-2.5 rounded-full text-xs font-bold shrink-0">Començar</a>
                    </div>';
                }
            }
        }
        ?>

        <header class="mb-4">
            <h2 class="font-h1 text-h1 text-primary"><?= \App\Core\Lang::t('dashboard.safe_space_title') ?></h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2 max-w-2xl"><?= \App\Core\Lang::t('dashboard.safe_space_desc') ?></p>
        </header>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
            
            <!-- Main Form Area -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-4 md:p-card-padding flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-primary-fixed/20 to-transparent pointer-events-none"></div>
                    <div class="relative z-10">
                        
                        <!-- Progress Bar -->
                        <div class="flex items-center justify-between mb-8" id="progress-header">
                            <div class="flex items-center gap-3 shrink-0">
                                <div id="indicator-1" class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm transition-colors">1</div>
                                <span id="label-1" class="hidden sm:inline font-body-md text-body-md text-primary font-medium transition-colors"><?= \App\Core\Lang::t('dashboard.step1_label') ?></span>
                            </div>
                            <div class="flex-1 mx-4 h-1 bg-surface-variant rounded-full overflow-hidden">
                                <div id="progress-bar" class="h-full bg-primary w-1/3 rounded-full transition-all duration-300"></div>
                            </div>
                            <div class="flex items-center gap-3 opacity-50 shrink-0" id="indicator-group-2">
                                <div id="indicator-2" class="w-8 h-8 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold text-sm transition-colors">2</div>
                            </div>
                        </div>

                        <!-- Form Steps -->
                        <div id="form-container">
                            <!-- Step 1: Content -->
                            <div id="step-1" class="space-y-6">
                                <div>
                                    <label class="block font-h2 text-h2 text-on-surface mb-2"><?= \App\Core\Lang::t('dashboard.step1_title') ?></label>
                                    <p class="font-body-md text-body-md text-on-surface-variant mb-4"><?= \App\Core\Lang::t('dashboard.step1_desc') ?></p>
                                    <textarea id="report-content" class="w-full h-48 bg-surface-container-highest rounded-lg p-4 font-body-md text-body-md text-on-surface placeholder:text-outline border-0 focus:ring-0 focus:shadow-[0_0_15px_rgba(6,105,114,0.15)] transition-shadow resize-none" placeholder="<?= \App\Core\Lang::t('dashboard.step1_placeholder') ?>"></textarea>
                                    <p id="error-step-1" class="text-error text-sm mt-2 hidden"><?= \App\Core\Lang::t('dashboard.step1_error') ?></p>
                                </div>
                                <div class="flex justify-end pt-4">
                                    <button type="button" onclick="nextStep(2)" class="w-full sm:w-auto bg-primary text-on-primary rounded-full px-8 py-3 font-body-md text-body-md font-medium shadow-[0_4px_14px_rgba(0,79,86,0.2)] hover:shadow-[0_6px_20px_rgba(0,79,86,0.3)] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                        <?= \App\Core\Lang::t('dashboard.next_step') ?>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Details -->
                            <div id="step-2" class="space-y-6 hidden">
                                <div class="space-y-3">
                                    <p class="font-body-md text-[14px] text-on-surface font-medium"><?= \App\Core\Lang::t('dashboard.step2_target_label') ?></p>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="target-btn px-4 py-2 rounded-full border border-primary bg-primary-fixed/20 text-on-primary-fixed-variant text-sm font-medium transition-colors" data-value="yo_mismo" onclick="setTarget(this)"><?= \App\Core\Lang::t('dashboard.step2_target_me') ?></button>
                                        <button type="button" class="target-btn px-4 py-2 rounded-full border border-outline-variant bg-transparent text-on-surface-variant hover:bg-surface-variant text-sm font-medium transition-colors" data-value="otro" onclick="setTarget(this)"><?= \App\Core\Lang::t('dashboard.step2_target_other') ?></button>
                                    </div>
                                    <input type="hidden" id="report-target" value="yo_mismo">
                                </div>

                                <div class="space-y-3 pt-2">
                                    <p class="font-body-md text-[14px] text-on-surface font-medium"><?= \App\Core\Lang::t('dashboard.step2_priority_label') ?></p>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="urgency-btn px-4 py-2 rounded-full border border-primary bg-primary-fixed/20 text-on-primary-fixed-variant text-sm font-medium transition-colors" data-value="low" onclick="setUrgency(this)"><?= \App\Core\Lang::t('dashboard.step2_priority_low') ?></button>
                                        <button type="button" class="urgency-btn px-4 py-2 rounded-full border border-outline-variant bg-transparent text-on-surface-variant hover:bg-surface-variant text-sm font-medium transition-colors" data-value="medium" onclick="setUrgency(this)"><?= \App\Core\Lang::t('dashboard.step2_priority_med') ?></button>
                                        <button type="button" class="urgency-btn px-4 py-2 rounded-full border border-outline-variant bg-transparent text-on-surface-variant hover:bg-surface-variant text-sm font-medium transition-colors" data-value="high" onclick="setUrgency(this)"><?= \App\Core\Lang::t('dashboard.step2_priority_high') ?></button>
                                    </div>
                                    <input type="hidden" id="report-urgency" value="low">
                                </div>

                                <div class="bg-surface rounded-lg p-4 flex items-start gap-4 shadow-sm shadow-primary/5 mt-4 border border-surface-variant">
                                    <div class="pt-1"><input checked class="w-5 h-5 rounded-md text-primary bg-surface-container-highest border-0 focus:ring-primary focus:ring-offset-0" id="report-anonymous" type="checkbox"/></div>
                                    <div>
                                        <label class="font-body-lg text-body-lg text-on-surface font-medium cursor-pointer" for="report-anonymous"><?= \App\Core\Lang::t('dashboard.step2_anonymous_label') ?></label>
                                        <p class="font-body-md text-[14px] text-on-surface-variant mt-1"><?= \App\Core\Lang::t('dashboard.step2_anonymous_desc') ?></p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
                                    <button type="button" onclick="prevStep(1)" class="w-full sm:w-auto bg-surface-container text-on-surface-variant rounded-full px-6 py-3 font-body-md text-body-md font-medium hover:bg-surface-variant transition-colors"><?= \App\Core\Lang::t('dashboard.back') ?></button>
                                    <button type="button" id="btn-submit-report" onclick="submitReport()" class="w-full sm:w-auto bg-primary text-on-primary rounded-full px-8 py-3 font-body-md text-body-md font-medium shadow-[0_4px_14px_rgba(0,79,86,0.2)] hover:shadow-[0_6px_20px_rgba(0,79,86,0.3)] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                        <?= \App\Core\Lang::t('dashboard.submit') ?> <span class="material-symbols-outlined text-sm">send</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Success Step -->
                            <div id="step-success" class="text-center py-12 hidden flex-col items-center gap-4">
                                <div class="w-16 h-16 rounded-full bg-[#d4edda] text-[#155724] flex items-center justify-center mb-2"><span class="material-symbols-outlined text-3xl">check_circle</span></div>
                                <h3 class="font-h2 text-h2 text-on-surface"><?= \App\Core\Lang::t('dashboard.success_title') ?></h3>
                                <p class="font-body-md text-body-md text-on-surface-variant max-w-sm"><?= \App\Core\Lang::t('dashboard.success_desc') ?></p>
                                <button type="button" onclick="resetForm()" class="mt-4 bg-surface-container text-on-surface-variant rounded-full px-8 py-3 font-body-md text-body-md font-medium hover:bg-surface-variant transition-colors"><?= \App\Core\Lang::t('dashboard.back_to_panel') ?></button>
                            </div>

                            <!-- Chat View -->
                            <div id="chat-view" class="hidden flex-col min-h-[500px]">
                                <div class="flex items-center justify-between border-b border-surface-variant pb-4 mb-4">
                                    <button onclick="resetForm()" class="flex items-center text-primary font-medium text-sm hover:underline"><span class="material-symbols-outlined text-sm mr-1">arrow_back</span> <?= \App\Core\Lang::t('nav.back_to_menu') ?></button>
                                    <span id="chat-status" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase">--</span>
                                </div>
                                <div id="resolved-note" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg p-4 mb-4 text-sm italic"></div>
                                <div id="chat-messages" class="flex-1 overflow-y-auto no-scrollbar space-y-4 p-2 mb-4"></div>
                                <div id="chat-input-container" class="relative">
                                    <input id="reply-message" class="w-full bg-surface-container border-0 rounded-full py-3 pl-4 pr-12 font-body-md text-[14px] text-on-surface focus:ring-0 transition-shadow" placeholder="<?= \App\Core\Lang::t('dashboard.chat_placeholder') ?>" type="text"/>
                                    <button onclick="sendStudentMessage()" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center hover:bg-on-primary-fixed-variant transition-colors"><span class="material-symbols-outlined text-[16px]">send</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón Principal Respira Conmigo -->
                <div class="mt-8 flex justify-center">
                    <button onclick="openBreathingApp()" class="w-full sm:w-auto bg-gradient-to-br from-teal-600 to-blue-700 text-white px-8 py-6 md:px-12 md:py-6 rounded-3xl font-black text-xl md:text-2xl shadow-2xl shadow-teal-900/20 hover:scale-105 transition-all flex items-center justify-center gap-4 group">
                        <span class="material-symbols-outlined text-4xl md:text-5xl group-hover:rotate-12 transition-transform">spa</span>
                        <?= \App\Core\Lang::t('breathing.title') ?>
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-4 md:p-card-padding">
                    <h3 class="font-h2 text-[18px] md:text-[20px] text-on-surface mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-primary">history</span> <?= \App\Core\Lang::t('dashboard.history_title') ?></h3>
                    <div class="space-y-4">
                        <?php if (empty($reports)): ?><div class="p-6 text-center text-outline text-sm italic"><?= \App\Core\Lang::t('dashboard.no_activity') ?></div><?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <div class="p-4 rounded-lg bg-surface hover:bg-surface-container transition-colors group border border-surface-variant/50 cursor-pointer" onclick="loadStudentReport(<?= $report['id'] ?>)">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-label-caps text-[10px] text-outline uppercase"><?= date('d M', strtotime($report['created_at'])) ?></span>
                                        <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full <?= $report['status']==='new'?'bg-primary-fixed text-on-primary-fixed-variant':($report['status']==='in_progress'?'bg-[#fff3cd] text-[#856404]':'bg-[#d4edda] text-[#155724]') ?>"><?= $report['status'] ?></span>
                                    </div>
                                    <p class="font-body-md text-[14px] text-on-surface line-clamp-2"><?= htmlspecialchars($report['content']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bg-secondary-container rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-4 md:p-card-padding flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-surface-container-lowest flex items-center justify-center mb-4 shadow-sm shadow-primary/10"><span class="material-symbols-outlined text-3xl text-secondary" style="font-variation-settings: 'FILL' 1;">volunteer_activism</span></div>
                    <h4 class="font-body-lg text-[18px] font-semibold text-on-secondary-container mb-2"><?= \App\Core\Lang::t('dashboard.need_talk') ?></h4>
                    <button class="bg-surface-container-lowest text-secondary rounded-full px-6 py-2 font-body-md text-body-md font-medium shadow-sm hover:shadow-md transition-shadow"><?= \App\Core\Lang::t('dashboard.help_chat') ?></button>
                </div>

                <!-- WebAuthn 2FA Block -->
                <?php if(\App\Core\Config::get('2fa_students_method') === 'webauthn'): ?>
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-4 md:p-card-padding">
                    <h3 class="font-h2 text-[16px] text-on-surface mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">fingerprint</span> Acceso Seguro</h3>
                    
                    <?php if (empty($webauthnDevices)): ?>
                        <p class="text-xs text-on-surface-variant mb-4">Registra tu dispositivo para un acceso biométrico más rápido.</p>
                    <?php else: ?>
                        <ul class="space-y-2 mb-4">
                            <?php foreach($webauthnDevices as $dev): ?>
                                <li class="flex justify-between items-center bg-surface p-2 rounded-lg text-xs">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-on-surface truncate"><?= htmlspecialchars($dev['device_name']) ?></p>
                                        <p class="text-slate-400 text-[10px]"><?= date('d/m/Y', strtotime($dev['created_at'])) ?></p>
                                    </div>
                                    <button onclick="deleteWebAuthn(<?= $dev['id'] ?>)" class="text-error hover:bg-error/10 p-1.5 rounded-full transition-colors shrink-0" title="Eliminar"><span class="material-symbols-outlined text-sm">delete</span></button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <button onclick="registerWebAuthn()" class="w-full bg-primary-container text-on-primary-container rounded-full px-4 py-2 font-body-md text-[13px] font-medium shadow-sm hover:shadow-md transition-shadow flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">add</span> Añadir dispositivo
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- ============================== RESPIRA CONMIGO (APP INMERSIVA) ============================== -->
<div id="breathing-app-container" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center overflow-hidden" style="font-family: 'Outfit', sans-serif; color: #f4ede4; background: #1a2f3a;">
    <button onclick="closeBreathingApp()" class="absolute top-8 right-8 text-white/50 hover:text-white transition-colors z-[110]"><span class="material-symbols-outlined text-4xl">close</span></button>

    <!-- Noise layer -->
    <div class="fixed inset-0 pointer-events-none opacity-30 z-[105]" style="mix-blend-mode: overlay; background-image: url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.9\' numOctaves=\'2\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23n)\'/%3E%3C/svg%3E');"></div>

    <section id="b-landing" class="absolute inset-0 flex items-center justify-center p-8 transition-all duration-700 z-[106]" style="background: radial-gradient(ellipse at 20% 20%, rgba(168, 197, 181, 0.22) 0%, transparent 55%), radial-gradient(ellipse at 80% 90%, rgba(197, 217, 229, 0.18) 0%, transparent 60%), linear-gradient(165deg, #0f1e26 0%, #1a2f3a 50%, #2d5160 100%);">
        <div class="text-center max-w-lg">
            <p class="text-[0.7rem] uppercase tracking-[0.3em] text-[#a8c5b5] mb-6"><?= \App\Core\Lang::t('breathing.landing_subtitle') ?></p>
            <h1 class="text-4xl md:text-7xl font-light mb-4" style="font-family: 'Fraunces', serif;">Respira<em class="italic text-[#a8c5b5] block not-italic">Conmigo</em></h1>
            <p class="text-[#c5d9e5] opacity-80 mb-10 text-base md:text-lg"><?= \App\Core\Lang::t('breathing.landing_desc') ?></p>
            <div class="flex flex-wrap gap-2 justify-center mb-10">
                <button onclick="selectRhythm('calm', this)" class="r-btn active px-4 py-2 rounded-full border border-white/20 bg-white/5 text-[#c5d9e5] text-sm hover:bg-white/10 transition-all"><?= \App\Core\Lang::t('breathing.calm') ?><small class="block opacity-60 text-[10px]">4 · 6</small></button>
                <button onclick="selectRhythm('box', this)" class="r-btn px-4 py-2 rounded-full border border-white/20 bg-white/5 text-[#c5d9e5] text-sm hover:bg-white/10 transition-all"><?= \App\Core\Lang::t('breathing.focus') ?><small class="block opacity-60 text-[10px]">4 · 4 · 4 · 4</small></button>
                <button onclick="selectRhythm('sleep', this)" class="r-btn px-4 py-2 rounded-full border border-white/20 bg-white/5 text-[#c5d9e5] text-sm hover:bg-white/10 transition-all"><?= \App\Core\Lang::t('breathing.rest') ?><small class="block opacity-60 text-[10px]">4 · 7 · 8</small></button>
            </div>
            <button onclick="startSession()" class="bg-[#f4ede4] text-[#0f1e26] px-12 py-4 rounded-full font-bold text-lg shadow-2xl hover:translate-y-[-2px] transition-all"><?= \App\Core\Lang::t('breathing.title') ?></button>
        </div>
    </section>

    <section id="b-scene" class="hidden absolute inset-0 flex items-center justify-center transition-all duration-1000 z-[107]">
        <div class="absolute top-10 left-1/2 -translate-x-1/2 text-[0.7rem] tracking-[0.4em] opacity-40 uppercase"><?= \App\Core\Lang::t('breathing.cycle') ?> <strong id="b-cycle" class="text-[#a8c5b5]">1</strong></div>
        <div class="relative flex items-center justify-center w-[250px] h-[250px] md:w-[500px] md:h-[500px]">
            <div id="b-halo" class="absolute inset-0 rounded-full blur-3xl opacity-40 transition-all duration-1000"></div>
            <div id="b-circle" class="relative w-3/5 h-3/5 rounded-full flex flex-col items-center justify-center text-center shadow-2xl transition-all ease-in-out" style="background: radial-gradient(circle at 35% 30%, rgba(244, 237, 228, 0.25) 0%, rgba(168, 197, 181, 0.4) 40%, rgba(45, 81, 96, 0.6) 100%);">
                <div id="b-label" class="text-2xl md:text-5xl font-light italic" style="font-family: 'Fraunces', serif;"><?= \App\Core\Lang::t('breathing.prepare') ?></div>
                <div id="b-timer" class="text-xs opacity-50 mt-2 tracking-widest"></div>
            </div>
        </div>
        <p class="absolute bottom-12 uppercase tracking-[0.4em] text-[0.6rem] opacity-40"><?= \App\Core\Lang::t('breathing.follow_rhythm') ?></p>
    </section>
</div>

<style>
    .r-btn.active { background: #a8c5b5 !important; border-color: #a8c5b5 !important; color: #0f1e26 !important; font-weight: 600; }
    #b-halo.inhale { background: #c5d9e5; transform: scale(1.5); }
    #b-halo.exhale { background: #a8c5b5; transform: scale(0.8); }
</style>

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
            document.body.style.overflow = '';
        } else {
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
            i.innerText = 'close';
            document.body.style.overflow = 'hidden';
        }
    }

    const BREATH = { calm: [4, 0, 6, 0], box: [4, 4, 4, 4], sleep: [4, 7, 8, 0] };
    const LABELS = ['<?= \App\Core\Lang::t('breathing.inhale') ?>', '<?= \App\Core\Lang::t('breathing.hold') ?>', '<?= \App\Core\Lang::t('breathing.exhale') ?>', '<?= \App\Core\Lang::t('breathing.hold') ?>'];
    let bState = { running: false, cycle: 1, pIdx: 0, start: 0, dur: 0, raf: null, rhythm: 'calm' };

    function openBreathingApp() { document.getElementById('breathing-app-container').classList.remove('hidden'); document.getElementById('b-landing').classList.remove('hidden'); document.getElementById('b-scene').classList.add('hidden'); }
    function closeBreathingApp() { bState.running = false; cancelAnimationFrame(bState.raf); document.getElementById('breathing-app-container').classList.add('hidden'); }
    function selectRhythm(r, btn) { bState.rhythm = r; document.querySelectorAll('.r-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); }
    function startSession() { document.getElementById('b-landing').classList.add('hidden'); document.getElementById('b-scene').classList.remove('hidden'); bState.running = true; bState.cycle = 1; runPhase(0); }

    function runPhase(idx) {
        if (!bState.running) return;
        const p = BREATH[bState.rhythm];
        while (p[idx] === 0) { idx = (idx + 1) % 4; if (idx === 0) bState.cycle++; }
        bState.pIdx = idx; bState.dur = p[idx] * 1000; bState.start = performance.now();
        document.getElementById('b-label').innerText = LABELS[idx];
        document.getElementById('b-cycle').innerText = bState.cycle;
        const halo = document.getElementById('b-halo');
        halo.className = 'absolute inset-0 rounded-full blur-3xl opacity-40 transition-all ' + (idx === 0 ? 'inhale' : (idx === 2 ? 'exhale' : ''));
        animate();
    }

    function animate() {
        if (!bState.running) return;
        const elapsed = performance.now() - bState.start;
        const prog = Math.min(elapsed / bState.dur, 1);
        const eased = 0.5 - 0.5 * Math.cos(prog * Math.PI);
        let s = 1;
        if (bState.pIdx === 0) s = 0.6 + 0.6 * eased;
        else if (bState.pIdx === 1) s = 1.2;
        else if (bState.pIdx === 2) s = 1.2 - 0.6 * eased;
        else s = 0.6;
        document.getElementById('b-circle').style.transform = `scale(${s})`;
        const rem = Math.max(0, Math.ceil((bState.dur - elapsed) / 1000));
        document.getElementById('b-timer').innerText = rem > 0 ? '· ' + rem + ' ·' : '';
        if (prog < 1) bState.raf = requestAnimationFrame(animate); else runPhase((bState.pIdx + 1) % 4);
    }

    /* Dashboard Logic */
    function nextStep(s) { 
        if (document.getElementById('report-content').value.trim().length < 5) { document.getElementById('error-step-1').classList.remove('hidden'); return; }
        document.getElementById('step-1').classList.add('hidden'); document.getElementById('step-2').classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    function prevStep(s) { document.getElementById('step-2').classList.add('hidden'); document.getElementById('step-1').classList.remove('hidden'); }
    function setTarget(b) { document.querySelectorAll('.target-btn').forEach(x => x.className = 'target-btn px-4 py-2 rounded-full border border-outline-variant bg-transparent text-on-surface-variant hover:bg-surface-variant text-sm font-medium transition-colors'); b.className = 'target-btn px-4 py-2 rounded-full border border-primary bg-primary-fixed/20 text-on-primary-fixed-variant text-sm font-medium transition-colors'; document.getElementById('report-target').value = b.dataset.value; }
    function setUrgency(b) { document.querySelectorAll('.urgency-btn').forEach(x => x.className = 'urgency-btn px-4 py-2 rounded-full border border-outline-variant bg-transparent text-on-surface-variant hover:bg-surface-variant text-sm font-medium transition-colors'); b.className = 'urgency-btn px-4 py-2 rounded-full border border-primary bg-primary-fixed/20 text-on-primary-fixed-variant text-sm font-medium transition-colors'; document.getElementById('report-urgency').value = b.dataset.value; }
    async function submitReport() {
        const payload = { content: document.getElementById('report-content').value, target: document.getElementById('report-target').value, urgency_level: document.getElementById('report-urgency').value, is_anonymous: document.getElementById('report-anonymous').checked };
        const res = await fetchJson('/alumno/report', { method: 'POST', body: payload });
        if (res.success) { document.getElementById('progress-header').classList.add('hidden'); document.getElementById('step-2').classList.add('hidden'); document.getElementById('step-success').classList.replace('hidden', 'flex'); }
    }
    function resetForm() { window.location.reload(); }
    async function loadStudentReport(id) {
        currentReportId = id;
        document.getElementById('progress-header').classList.add('hidden'); document.getElementById('form-container').classList.add('hidden');
        document.getElementById('chat-view').classList.replace('hidden', 'flex');
        const cm = document.getElementById('chat-messages'); cm.innerHTML = '<div class="flex h-full items-center justify-center text-primary"><span class="material-symbols-outlined animate-spin text-4xl">refresh</span></div>';
        const res = await fetchJson(`/alumno/reports/${id}`);
        if (!res.error) renderStudentChat(res.report, res.messages);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    function renderStudentChat(report, messages) {
        document.getElementById('chat-status').innerText = report.status;
        const ic = document.getElementById('chat-input-container');
        if (report.status === 'resolved') { ic.classList.add('hidden'); document.getElementById('resolved-note').innerText = "Resolución: " + (report.resolution_summary || "Cerrado."); document.getElementById('resolved-note').classList.remove('hidden'); }
        let h = `<div class="flex gap-3 flex-row-reverse mb-6"><div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 shrink-0">Tú</div><div class="bg-slate-100 p-4 rounded-2xl rounded-tr-none max-w-[85%] sm:max-w-[80%] text-sm">${report.content}</div></div>`;
        messages.forEach(m => {
            const me = m.is_current_user;
            h += `<div class="flex gap-3 ${me ? 'flex-row-reverse' : ''} mb-6"><div class="w-8 h-8 rounded-full ${me ? 'bg-slate-200 text-slate-600' : 'bg-primary-container text-on-primary-container'} flex items-center justify-center text-xs font-bold shrink-0">${me ? 'Tú' : m.sender_name.charAt(0)}</div><div class="${me ? 'bg-slate-100' : 'bg-white border'} p-4 rounded-2xl ${me ? 'rounded-tr-none' : 'rounded-tl-none'} shadow-sm max-w-[85%] sm:max-w-[80%] text-sm"><p>${m.message}</p></div></div>`;
        });
        document.getElementById('chat-messages').innerHTML = h;
    }
    async function sendStudentMessage() {
        const i = document.getElementById('reply-message'); const msg = i.value.trim(); if (!msg || !currentReportId) return;
        const res = await fetchJson(`/alumno/reports/${currentReportId}/messages`, { method: 'POST', body: { message: msg } });
        if (!res.error) { i.value = ''; loadStudentReport(currentReportId); }
    }

    // WebAuthn Handlers
    function base64urlToBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const pad = base64.length % 4;
        const padded = pad ? base64 + '===='.substring(pad) : base64;
        const binary_string = window.atob(padded);
        const len = binary_string.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = binary_string.charCodeAt(i);
        }
        return bytes.buffer;
    }

    function bufferToBase64url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary)
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=/g, '');
    }

    async function registerWebAuthn() {
        if (!window.isSecureContext && window.location.hostname !== 'localhost') {
            alert('WebAuthn requiere una conexión segura (HTTPS).');
            return;
        }
        
        const deviceName = prompt('Nombre para este dispositivo (ej: Mi iPhone, PC Casa)', 'Mi dispositivo');
        if (!deviceName) return;

        try {
            const optRes = await fetchJson('/alumno/2fa/webauthn/register/options');
            if (optRes.error) throw new Error(optRes.error);
            
            // Preparar opciones para navigator.credentials.create
            const options = optRes;
            options.challenge = base64urlToBuffer(options.challenge);
            options.user.id = base64urlToBuffer(options.user.id);
            
            if (options.excludeCredentials) {
                for (let i = 0; i < options.excludeCredentials.length; i++) {
                    options.excludeCredentials[i].id = base64urlToBuffer(options.excludeCredentials[i].id);
                }
            }

            const credential = await navigator.credentials.create({ publicKey: options });
            
            const verifyRes = await fetchJson('/alumno/2fa/webauthn/register/verify', {
                method: 'POST',
                body: {
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64url(credential.response.attestationObject),
                    device_name: deviceName
                }
            });

            if (verifyRes.success) {
                alert('¡Dispositivo registrado con éxito!');
                window.location.reload();
            } else {
                throw new Error(verifyRes.error || 'Error en la verificación');
            }
        } catch (e) {
            console.error('WebAuthn Error:', e);
            if (e.name === 'NotAllowedError') alert('Operación cancelada o denegada por el usuario.');
            else alert('Error: ' + e.message);
        }
    }

    async function deleteWebAuthn(id) {
        if (!confirm('¿Seguro que deseas eliminar este dispositivo?')) return;
        const res = await fetchJson('/alumno/2fa/webauthn/credential/delete', { method: 'POST', body: { id } });
        if (res.success) window.location.reload();
    }
</script>
<?php $scripts = ob_get_clean(); ?>

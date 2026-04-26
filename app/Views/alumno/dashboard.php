<?php $bodyClass = "antialiased min-h-screen flex w-full bg-surface"; ?>

<!-- Desktop SideNavBar -->
<nav class="bg-slate-50 dark:bg-slate-950 font-manrope font-medium h-screen w-64 fixed left-0 top-0 no-border shadow-right shadow-[4px_0_24px_rgba(6,105,114,0.04)] hidden lg:flex flex-col py-6 z-40">
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
        <button onclick="resetForm()" class="w-full bg-primary text-on-primary rounded-full py-3 px-4 flex items-center justify-center gap-2 shadow-sm shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined">add</span>
            <span class="font-semibold text-sm"><?= \App\Core\Lang::t('nav.new_report') ?></span>
        </button>
    </div>
    <div class="flex-1 flex flex-col gap-1">
        <a class="bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-full mx-2 px-4 py-3 flex items-center gap-3 active:scale-95 duration-150" href="/alumno/dashboard">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
            <span><?= \App\Core\Lang::t('nav.dashboard') ?></span>
        </a>
        <button onclick="openBreathingApp()" class="text-slate-500 dark:text-slate-400 px-4 py-3 mx-2 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 rounded-full flex items-center gap-3 transition-colors">
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

<!-- Mobile BottomNavBar -->
<nav class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-lg text-[11px] font-manrope font-semibold docked full-width bottom-0 rounded-t-[32px] no-border shadow-[0_-8px_30px_rgba(0,0,0,0.05)] lg:hidden fixed bottom-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3">
    <a class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 w-12 h-12 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" href="/alumno/dashboard">
        <span class="material-symbols-outlined mb-1">home</span>
        <span><?= \App\Core\Lang::t('nav.home') ?></span>
    </a>
    <button onclick="openBreathingApp()" class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 w-12 h-12 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
        <span class="material-symbols-outlined mb-1">spa</span>
        <span><?= \App\Core\Lang::t('nav.breathe_short') ?></span>
    </button>
    <a onclick="resetForm()" class="flex flex-col items-center justify-center bg-teal-100 dark:bg-teal-900 text-teal-800 dark:text-teal-100 rounded-full w-12 h-12 active:scale-90 duration-200" href="#">
        <span class="material-symbols-outlined mb-1" style="font-variation-settings: 'FILL' 1;">add_circle</span>
        <span><?= \App\Core\Lang::t('nav.report_short') ?></span>
    </a>
</nav>

<!-- Main Content Canvas -->
<main class="flex-1 w-full lg:ml-64 flex flex-col min-h-screen pb-24 lg:pb-0">
    <div class="px-6 py-10 lg:px-margin-page lg:py-12 max-w-6xl mx-auto w-full flex-1 flex flex-col gap-stack-gap">
        <header class="mb-4">
            <h2 class="font-h1 text-h1 text-primary"><?= \App\Core\Lang::t('dashboard.safe_space_title') ?></h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-2 max-w-2xl"><?= \App\Core\Lang::t('dashboard.safe_space_desc') ?></p>
        </header>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
            
            <!-- Main Form Area -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-card-padding flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-primary-fixed/20 to-transparent pointer-events-none"></div>
                    <div class="relative z-10">
                        
                        <!-- Progress Bar -->
                        <div class="flex items-center justify-between mb-8" id="progress-header">
                            <div class="flex items-center gap-3">
                                <div id="indicator-1" class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm transition-colors">1</div>
                                <span id="label-1" class="font-body-md text-body-md text-primary font-medium transition-colors"><?= \App\Core\Lang::t('dashboard.step1_label') ?></span>
                            </div>
                            <div class="flex-1 mx-4 h-1 bg-surface-variant rounded-full overflow-hidden">
                                <div id="progress-bar" class="h-full bg-primary w-1/3 rounded-full transition-all duration-300"></div>
                            </div>
                            <div class="flex items-center gap-3 opacity-50" id="indicator-group-2">
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
                                    <button type="button" onclick="nextStep(2)" class="bg-primary text-on-primary rounded-full px-8 py-3 font-body-md text-body-md font-medium shadow-[0_4px_14px_rgba(0,79,86,0.2)] hover:shadow-[0_6px_20px_rgba(0,79,86,0.3)] hover:-translate-y-0.5 transition-all flex items-center gap-2">
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

                                <div class="flex justify-between pt-4">
                                    <button type="button" onclick="prevStep(1)" class="bg-surface-container text-on-surface-variant rounded-full px-6 py-3 font-body-md text-body-md font-medium hover:bg-surface-variant transition-colors"><?= \App\Core\Lang::t('dashboard.back') ?></button>
                                    <button type="button" id="btn-submit-report" onclick="submitReport()" class="bg-primary text-on-primary rounded-full px-8 py-3 font-body-md text-body-md font-medium shadow-[0_4px_14px_rgba(0,79,86,0.2)] hover:shadow-[0_6px_20px_rgba(0,79,86,0.3)] hover:-translate-y-0.5 transition-all flex items-center gap-2">
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
                    <button onclick="openBreathingApp()" class="bg-gradient-to-br from-teal-600 to-blue-700 text-white px-12 py-6 rounded-3xl font-black text-2xl shadow-2xl shadow-teal-900/20 hover:scale-105 transition-all flex items-center gap-4 group">
                        <span class="material-symbols-outlined text-5xl group-hover:rotate-12 transition-transform">spa</span>
                        <?= \App\Core\Lang::t('breathing.title') ?>
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-card-padding">
                    <h3 class="font-h2 text-[20px] text-on-surface mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-primary">history</span> <?= \App\Core\Lang::t('dashboard.history_title') ?></h3>
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
                <div class="bg-secondary-container rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-card-padding flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-surface-container-lowest flex items-center justify-center mb-4 shadow-sm shadow-primary/10"><span class="material-symbols-outlined text-3xl text-secondary" style="font-variation-settings: 'FILL' 1;">volunteer_activism</span></div>
                    <h4 class="font-body-lg text-[18px] font-semibold text-on-secondary-container mb-2"><?= \App\Core\Lang::t('dashboard.need_talk') ?></h4>
                    <button class="bg-surface-container-lowest text-secondary rounded-full px-6 py-2 font-body-md text-body-md font-medium shadow-sm hover:shadow-md transition-shadow"><?= \App\Core\Lang::t('dashboard.help_chat') ?></button>
                </div>

                <!-- WebAuthn 2FA Block -->
                <?php if(\App\Core\Config::get('2fa_students_method') === 'webauthn'): ?>
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-card-padding">
                    <h3 class="font-h2 text-[16px] text-on-surface mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-lg">fingerprint</span> Acceso Seguro (FaceID/Huella)</h3>
                    
                    <?php if (empty($webauthnDevices)): ?>
                        <p class="text-xs text-on-surface-variant mb-4">Registra tu dispositivo para iniciar sesión de forma más rápida y segura la próxima vez.</p>
                    <?php else: ?>
                        <ul class="space-y-2 mb-4">
                            <?php foreach($webauthnDevices as $dev): ?>
                                <li class="flex justify-between items-center bg-surface p-2 rounded-lg text-xs">
                                    <div>
                                        <p class="font-bold text-on-surface"><?= htmlspecialchars($dev['device_name']) ?></p>
                                        <p class="text-slate-400 text-[10px]">Añadido: <?= date('d/m/Y', strtotime($dev['created_at'])) ?></p>
                                    </div>
                                    <button onclick="deleteWebAuthn(<?= $dev['id'] ?>)" class="text-error hover:bg-error/10 p-1.5 rounded-full transition-colors" title="Eliminar"><span class="material-symbols-outlined text-sm">delete</span></button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <button onclick="registerWebAuthn()" class="w-full bg-primary-container text-on-primary-container rounded-full px-4 py-2 font-body-md text-[13px] font-medium shadow-sm hover:shadow-md transition-shadow flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">add</span> Añadir este dispositivo
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
            <h1 class="text-5xl md:text-7xl font-light mb-4" style="font-family: 'Fraunces', serif;">Respira<em class="italic text-[#a8c5b5] block not-italic">Conmigo</em></h1>
            <p class="text-[#c5d9e5] opacity-80 mb-10 text-lg"><?= \App\Core\Lang::t('breathing.landing_desc') ?></p>
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
        <div class="relative flex items-center justify-center w-[300px] h-[300px] md:w-[500px] md:h-[500px]">
            <div id="b-halo" class="absolute inset-0 rounded-full blur-3xl opacity-40 transition-all duration-1000"></div>
            <div id="b-circle" class="relative w-3/5 h-3/5 rounded-full flex flex-col items-center justify-center text-center shadow-2xl transition-all ease-in-out" style="background: radial-gradient(circle at 35% 30%, rgba(244, 237, 228, 0.25) 0%, rgba(168, 197, 181, 0.4) 40%, rgba(45, 81, 96, 0.6) 100%);">
                <div id="b-label" class="text-3xl md:text-5xl font-light italic" style="font-family: 'Fraunces', serif;"><?= \App\Core\Lang::t('breathing.prepare') ?></div>
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
    }
    function renderStudentChat(report, messages) {
        document.getElementById('chat-status').innerText = report.status;
        const ic = document.getElementById('chat-input-container');
        if (report.status === 'resolved') { ic.classList.add('hidden'); document.getElementById('resolved-note').innerText = "Resolución: " + (report.resolution_summary || "Cerrado."); document.getElementById('resolved-note').classList.remove('hidden'); }
        let h = `<div class="flex gap-3 flex-row-reverse mb-6"><div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold">Tú</div><div class="bg-slate-100 p-4 rounded-2xl rounded-tr-none max-w-[80%] text-sm">${report.content}</div></div>`;
        messages.forEach(m => {
            const me = m.is_current_user;
            h += `<div class="flex gap-3 ${me ? 'flex-row-reverse' : ''} mb-6"><div class="w-8 h-8 rounded-full ${me ? 'bg-slate-200' : 'bg-primary-container text-on-primary-container'} flex items-center justify-center text-xs font-bold">${me ? 'Tú' : m.sender_name.charAt(0)}</div><div class="${me ? 'bg-slate-100' : 'bg-white border'} p-4 rounded-2xl ${me ? 'rounded-tr-none' : 'rounded-tl-none'} shadow-sm max-w-[80%] text-sm"><p>${m.message}</p></div></div>`;
        });
        document.getElementById('chat-messages').innerHTML = h;
    }
    async function sendStudentMessage() {
        const i = document.getElementById('reply-message'); const msg = i.value.trim(); if (!msg || !currentReportId) return;
        const res = await fetchJson(`/alumno/reports/${currentReportId}/messages`, { method: 'POST', body: { message: msg } });
        if (!res.error) { i.value = ''; loadStudentReport(currentReportId); }
    }

    // WebAuthn Handlers
    function base64urlToBuffer(b64) {
        const bin = atob(b64.replace(/-/g,'+').replace(/_/g,'/'));
        return Uint8Array.from(bin, c => c.charCodeAt(0)).buffer;
    }
    function bufferToBase64url(buf) {
        return btoa(String.fromCharCode(...new Uint8Array(buf)))
            .replace(/\+/g,'-').replace(/\//g,'_').replace(/=/g,'');
    }

    async function registerWebAuthn() {
        if (!window.isSecureContext) {
            alert('WebAuthn requiere una conexión segura (HTTPS).');
            return;
        }
        try {
            console.log('Iniciando registro WebAuthn...');
            const optRes = await fetchJson('/alumno/2fa/webauthn/register/options', {method: 'GET'});
            if (optRes.error) { 
                console.error('Error del servidor:', optRes.error);
                alert(optRes.error); 
                return; 
            }
            
            console.log('Opciones recibidas:', optRes);
            
            // lbuchs webauthn return challenge and userId as binary base64url strings
            optRes.challenge = base64urlToBuffer(optRes.challenge);
            optRes.user.id = base64urlToBuffer(optRes.user.id);
            
            const credential = await navigator.credentials.create({ publicKey: optRes });
            console.log('Credencial creada:', credential);
            
            const verifyRes = await fetchJson('/alumno/2fa/webauthn/register/verify', {
                method: 'POST',
                body: {
                    id: credential.id,
                    rawId: bufferToBase64url(credential.rawId),
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64url(credential.response.attestationObject),
                    type: credential.type,
                    device_name: prompt('Nombre para este dispositivo', 'Mi dispositivo') || 'Mi dispositivo'
                }
            });
            if (verifyRes.success) {
                console.log('Registro verificado con éxito');
                window.location.reload();
            } else {
                console.error('Error de verificación:', verifyRes.error);
                alert(verifyRes.error || 'Error al registrar el dispositivo.');
            }
        } catch (e) {
            console.error('Error en el flujo WebAuthn:', e);
            alert('Error o acción cancelada. Verifica la consola para más detalles.');
        }
    }

    async function deleteWebAuthn(id) {
        if (!confirm('¿Seguro que deseas eliminar este dispositivo?')) return;
        const res = await fetchJson('/alumno/2fa/webauthn/credential/delete', { method: 'POST', body: { id } });
        if (res.success) window.location.reload();
    }
</script>
<?php $scripts = ob_get_clean(); ?>

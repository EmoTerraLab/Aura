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
        <button onclick="ViewManager.showReporting(); toggleSidebar()" class="w-full bg-primary text-on-primary rounded-full py-3 px-4 flex items-center justify-center gap-2 shadow-sm shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined">add</span>
            <span class="font-semibold text-sm"><?= \App\Core\Lang::t('nav.new_report') ?></span>
        </button>
    </div>
    <div class="flex-1 flex flex-col gap-1 overflow-y-auto no-scrollbar">
        <a class="bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-full mx-2 px-4 py-3 flex items-center gap-3 active:scale-95 duration-150" href="#" onclick="event.preventDefault(); ViewManager.showHome(); toggleSidebar()">
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
        
        // P0 FIX: Usar sentencias preparadas para evitar inyección SQL
        $stmtProfile = $db->prepare("SELECT classroom_id FROM student_profiles WHERE user_id = ?");
        $stmtProfile->execute([$userId]);
        $profile = $stmtProfile->fetch();
        
        $isCataluna = \App\Core\Config::get('ccaa_code') === 'CAT';
        
        if ($profile && $isCataluna) {
            $stmtSurvey = $db->prepare("SELECT * FROM sociometric_surveys WHERE classroom_id = ? AND status = 'active' LIMIT 1");
            $stmtSurvey->execute([$profile['classroom_id']]);
            $survey = $stmtSurvey->fetch();
            
            if ($survey) {
                $stmtResponse = $db->prepare("SELECT COUNT(*) FROM sociometric_responses WHERE survey_id = ? AND student_id = ?");
                $stmtResponse->execute([$survey['id'], $userId]);
                $hasResponded = $stmtResponse->fetchColumn();
                
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
            
            <!-- Main Content Area -->
            <div id="main-view-container" class="lg:col-span-8 flex flex-col gap-6">
                
                <!-- Home View -->
                <div id="home-view" class="animate-fadeIn space-y-6">
                    <!-- Quote Card -->
                    <div class="bg-primary-container bg-gradient-to-br from-primary to-primary-fixed text-on-primary p-8 md:p-10 rounded-3xl shadow-xl shadow-primary/20 relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                        <div class="relative z-10">
                            <span class="material-symbols-outlined text-5xl opacity-30 mb-4 block">auto_awesome</span>
                            <h3 id="quote-text" class="text-2xl md:text-3xl font-light italic leading-tight mb-4">"Cargando inspiración..."</h3>
                            <p id="quote-author" class="text-sm font-bold tracking-widest uppercase opacity-80">-- Autor</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Curiosity Card -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-sm border border-surface-variant/30 flex flex-col gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">lightbulb</span>
                            </div>
                            <p id="curiosity-text" class="text-on-surface-variant text-[15px] leading-relaxed italic">¿Sabías que... respirar profundamente ayuda a calmar tu mente?</p>
                        </div>

                        <!-- Action Card -->
                        <div class="bg-secondary-container p-6 rounded-3xl shadow-sm border border-secondary/10 flex flex-col justify-between group">
                            <div>
                                <h4 class="text-on-secondary-container font-bold text-lg mb-2">¿Algo te preocupa?</h4>
                                <p class="text-on-secondary-container/70 text-sm mb-6 leading-relaxed">Estamos aquí para escucharte y ayudarte. No estás solo/a.</p>
                            </div>
                            <button id="btn-start-report" onclick="ViewManager.showReporting()" class="bg-white text-secondary px-6 py-3 rounded-full font-bold text-sm shadow-md hover:scale-[1.03] transition-all flex items-center justify-center gap-2 group-hover:bg-secondary group-hover:text-white">
                                <span class="material-symbols-outlined">chat_bubble</span>
                                Necesito hablar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Wizard Container (Hidden by default) -->
                <div id="reporting-card" class="hidden bg-surface-container-lowest rounded-xl shadow-[0_8px_40px_rgba(0,79,86,0.04)] p-4 md:p-card-padding flex flex-col relative overflow-hidden min-h-[500px] animate-fadeIn">
                    <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-primary-fixed/20 to-transparent pointer-events-none"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        
                        <!-- Header / Stepper -->
                        <div id="wizard-header" class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex flex-col">
                                    <span id="wizard-step-indicator" class="text-[10px] font-bold uppercase tracking-wider text-primary mb-1">Paso 1 de 8</span>
                                    <h3 id="wizard-step-title" class="font-h2 text-[20px] text-on-surface">¿Qué está pasando?</h3>
                                </div>
                                <div id="wizard-step-icon" class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center shadow-sm">
                                    <span class="material-symbols-outlined text-2xl">help</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full h-2 bg-surface-container-highest rounded-full overflow-hidden">
                                <div id="wizard-progress-bar" class="h-full bg-primary w-[12.5%] transition-all duration-500 ease-out"></div>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <div id="wizard-content" class="flex-1">
                            <!-- Dynamic content injected by JS -->
                        </div>

                        <!-- Navigation -->
                        <div id="wizard-nav" class="flex items-center justify-between mt-8 pt-6 border-t border-surface-variant/30">
                            <button id="btn-wizard-prev" onclick="appWizard.prev()" class="invisible flex items-center gap-2 text-on-surface-variant font-medium hover:bg-surface-variant/50 px-6 py-3 rounded-full transition-all">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                <?= \App\Core\Lang::t('dashboard.back') ?>
                            </button>
                            
                            <button id="btn-wizard-next" onclick="appWizard.next()" class="bg-primary text-on-primary font-bold px-10 py-3.5 rounded-full shadow-lg shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:pointer-events-none">
                                <span id="btn-wizard-next-text"><?= \App\Core\Lang::t('dashboard.next_step') ?></span>
                                <span class="material-symbols-outlined" id="btn-wizard-next-icon">arrow_forward</span>
                            </button>
                        </div>

                        <!-- Success View (Hidden by default) -->
                        <div id="wizard-success" class="hidden flex-col items-center justify-center text-center py-12 gap-6 animate-fadeIn">
                            <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-inner">
                                <span class="material-symbols-outlined text-5xl">check_circle</span>
                            </div>
                            <div>
                                <h3 class="font-h1 text-2xl text-on-surface mb-2"><?= \App\Core\Lang::t('dashboard.success_title') ?></h3>
                                <p class="text-on-surface-variant max-w-sm mx-auto"><?= \App\Core\Lang::t('dashboard.success_desc') ?></p>
                            </div>
                            <button onclick="window.location.reload()" class="bg-surface-container text-on-surface-variant font-bold px-8 py-3 rounded-full hover:bg-surface-variant transition-all">
                                <?= \App\Core\Lang::t('dashboard.back_to_panel') ?>
                            </button>
                        </div>

                        <!-- Chat View (Hidden by default) -->
                        <div id="chat-view" class="hidden flex-col h-full animate-fadeIn">
                            <div class="flex items-center justify-between border-b border-surface-variant/30 pb-4 mb-4">
                                <button onclick="window.location.reload()" class="flex items-center text-primary font-bold text-sm hover:underline">
                                    <span class="material-symbols-outlined text-sm mr-1">arrow_back</span> 
                                    <?= \App\Core\Lang::t('nav.back_to_menu') ?>
                                </button>
                                <div class="flex items-center gap-2">
                                    <span id="chat-status-pill" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">--</span>
                                </div>
                            </div>
                            <div id="resolved-note" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl p-4 mb-4 text-sm italic shadow-sm"></div>
                            <div id="chat-messages" class="flex-1 overflow-y-auto no-scrollbar space-y-6 p-2 mb-6 scroll-smooth min-h-[300px]">
                                <!-- Messages injected by JS -->
                            </div>
                            <div id="chat-input-container" class="relative group">
                                <input id="reply-message" class="w-full bg-surface-container-highest border-0 rounded-2xl py-4 pl-5 pr-14 font-body-md text-[15px] text-on-surface focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-outline/60 shadow-sm" placeholder="<?= \App\Core\Lang::t('dashboard.chat_placeholder') ?>" type="text"/>
                                <button onclick="sendStudentMessage()" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center hover:scale-105 active:scale-95 transition-all shadow-md shadow-primary/20">
                                    <span class="material-symbols-outlined text-[20px]">send</span>
                                </button>
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
    /**
     * Aura Dashboard - Rediseño 2026
     * UX Guiada, Accesible y Robusta
     */

    const currentLang = document.documentElement.lang || 'es';
    
    // --- Traducciones de Estado (Requerimiento UX) ---
    const statusTranslations = {
        es: { pending: "Pendiente", in_progress: "En progreso", resolved: "Resuelto", new: "Nuevo" },
        ca: { pending: "Pendent", in_progress: "En progrés", resolved: "Resolt", new: "Nou" },
        gl: { pending: "Pendente", in_progress: "En progreso", resolved: "Resolto", new: "Novo" },
        eu: { pending: "Zain", in_progress: "Berrikuspenean", resolved: "Ebatzia", new: "Berria" },
        en: { pending: "Pending", in_progress: "In progress", resolved: "Resolved", new: "New" }
    };

    const i18n = (key) => {
        const translations = {
            'report.step1_q': "<?= \App\Core\Lang::t('report.step1_q') ?>",
            'report.step1_y': "<?= \App\Core\Lang::t('report.step1_y') ?>",
            'report.step1_n': "<?= \App\Core\Lang::t('report.step1_n') ?>",
            'report.step1_u': "<?= \App\Core\Lang::t('report.step1_u') ?>",
            'report.step2_q': "<?= \App\Core\Lang::t('report.step2_q') ?>",
            'report.step2_peer': "<?= \App\Core\Lang::t('report.step2_peer') ?>",
            'report.step2_adult': "<?= \App\Core\Lang::t('report.step2_adult') ?>",
            'report.step2_multiple': "<?= \App\Core\Lang::t('report.step2_multiple') ?>",
            'report.step2_outside': "<?= \App\Core\Lang::t('report.step2_outside') ?>",
            'report.step2_witness': "<?= \App\Core\Lang::t('report.step2_witness') ?>",
            'report.step3_q': "<?= \App\Core\Lang::t('report.step3_q') ?>",
            'report.step3_in_person': "<?= \App\Core\Lang::t('report.step3_in_person') ?>",
            'report.step3_social': "<?= \App\Core\Lang::t('report.step3_social') ?>",
            'report.step3_private': "<?= \App\Core\Lang::t('report.step3_private') ?>",
            'report.step3_class': "<?= \App\Core\Lang::t('report.step3_class') ?>",
            'report.step3_whatsapp': "<?= \App\Core\Lang::t('report.step3_whatsapp') ?>",
            'report.step3_games': "<?= \App\Core\Lang::t('report.step3_games') ?>",
            'report.step3_exclusion': "<?= \App\Core\Lang::t('report.step3_exclusion') ?>",
            'report.step3_insults': "<?= \App\Core\Lang::t('report.step3_insults') ?>",
            'report.step3_threats': "<?= \App\Core\Lang::t('report.step3_threats') ?>",
            'report.step3_media': "<?= \App\Core\Lang::t('report.step3_media') ?>",
            'report.step4_q': "<?= \App\Core\Lang::t('report.step4_q') ?>",
            'report.step4_once': "<?= \App\Core\Lang::t('report.step4_once') ?>",
            'report.step4_sometimes': "<?= \App\Core\Lang::t('report.step4_sometimes') ?>",
            'report.step4_often': "<?= \App\Core\Lang::t('report.step4_often') ?>",
            'report.step4_daily': "<?= \App\Core\Lang::t('report.step4_daily') ?>",
            'report.step4_unknown': "<?= \App\Core\Lang::t('report.step4_unknown') ?>",
            'report.step5_q': "<?= \App\Core\Lang::t('report.step5_q') ?>",
            'report.step5_sad': "<?= \App\Core\Lang::t('report.step5_sad') ?>",
            'report.step5_fear': "<?= \App\Core\Lang::t('report.step5_fear') ?>",
            'report.step5_nervous': "<?= \App\Core\Lang::t('report.step5_nervous') ?>",
            'report.step5_alone': "<?= \App\Core\Lang::t('report.step5_alone') ?>",
            'report.step5_angry': "<?= \App\Core\Lang::t('report.step5_angry') ?>",
            'report.step5_confused': "<?= \App\Core\Lang::t('report.step5_confused') ?>",
            'report.step5_none': "<?= \App\Core\Lang::t('report.step5_none') ?>",
            'report.step6_q': "<?= \App\Core\Lang::t('report.step6_q') ?>",
            'report.step6_family': "<?= \App\Core\Lang::t('report.step6_family') ?>",
            'report.step6_friends': "<?= \App\Core\Lang::t('report.step6_friends') ?>",
            'report.step6_teachers': "<?= \App\Core\Lang::t('report.step6_teachers') ?>",
            'report.step6_counseling': "<?= \App\Core\Lang::t('report.step6_counseling') ?>",
            'report.step6_no': "<?= \App\Core\Lang::t('report.step6_no') ?>",
            'report.step6_none': "<?= \App\Core\Lang::t('report.step6_none') ?>",
            'report.step7_q': "<?= \App\Core\Lang::t('report.step7_q') ?>",
            'report.step7_placeholder': "<?= \App\Core\Lang::t('report.step7_placeholder') ?>",
            'report.step8_q': "<?= \App\Core\Lang::t('report.step8_q') ?>",
            'report.step8_anonymous': "<?= \App\Core\Lang::t('report.step8_anonymous') ?>",
            'report.step8_contact': "<?= \App\Core\Lang::t('report.step8_contact') ?>",
            'report.step8_followup': "<?= \App\Core\Lang::t('report.step8_followup') ?>",
            'report.step8_urgent': "<?= \App\Core\Lang::t('report.step8_urgent') ?>",
            'dashboard.next_step': "<?= \App\Core\Lang::t('dashboard.next_step') ?>",
            'dashboard.submit': "<?= \App\Core\Lang::t('dashboard.submit') ?>",
            'dashboard.back': "<?= \App\Core\Lang::t('dashboard.back') ?>",
            'ticket_load_error': "Error al cargar el ticket. Inténtalo de nuevo.",
            'empty_chat': "No hay mensajes en este chat.",
            'sending': "Enviando...",
        };
        return translations[key] || key;
    };

    function translateStatus(status) {
        const labels = statusTranslations[currentLang] || statusTranslations['es'];
        return labels[status] || status;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function toggleSidebar() {
        const s = document.getElementById('app-sidebar');
        const o = document.getElementById('sidebar-overlay');
        const i = document.getElementById('menu-icon');
        const isOpen = !s.classList.contains('-translate-x-full');
        if (isOpen) {
            s.classList.add('-translate-x-full'); o.classList.add('hidden');
            i.innerText = 'menu'; document.body.style.overflow = '';
        } else {
            s.classList.remove('-translate-x-full'); o.classList.remove('hidden');
            i.innerText = 'close'; document.body.style.overflow = 'hidden';
        }
    }

    // --- Gestión de Vistas ---
    const ViewManager = {
        views: ['home-view', 'reporting-card', 'chat-view', 'wizard-success'],
        
        hideAll() {
            this.views.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            // Specific handling for wizard components
            ['wizard-header', 'wizard-content', 'wizard-nav'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            });
        },

        showHome() {
            this.hideAll();
            document.getElementById('home-view').classList.remove('hidden');
            initHomeData();
        },

        showReporting() {
            this.hideAll();
            document.getElementById('reporting-card').classList.remove('hidden');
            if (!appWizard) appWizard = new WizardFlow();
            else {
                appWizard.currentStep = 1;
                appWizard.init();
            }
        },

        showChat() {
            this.hideAll();
            document.getElementById('reporting-card').classList.remove('hidden');
            document.getElementById('chat-view').classList.remove('hidden');
            // Hide wizard-specific header/nav when in chat
            ['wizard-header', 'wizard-nav'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
        }
    };

    function initHomeData() {
        const quotes = [
            {"text": "\"Nadie tiene derecho a hacerte sentir menos por ser quien eres.\"", "author": "Anónimo"},
            {"text": "\"Tus diferencias no son defectos, son parte de lo que te hace único.\"", "author": "Anónimo"},
            {"text": "\"Ser amable es una forma silenciosa de cambiar el mundo.\"", "author": "Anónimo"},
            {"text": "\"Pedir ayuda no te hace débil, te hace valiente.\"", "author": "Anónimo"},
            {"text": "\"Las palabras pueden herir, pero también pueden sanar. Elige siempre las que ayuden.\"", "author": "Anónimo"},
            {"text": "\"Nunca estás solo, siempre hay alguien dispuesto a escucharte.\"", "author": "Anónimo"},
            {"text": "\"La verdadera fuerza está en respetar a los demás.\"", "author": "Anónimo"},
            {"text": "\"Defender a alguien que lo necesita puede cambiarle el día… o la vida.\"", "author": "Anónimo"},
            {"text": "\"Tu valor no depende de la opinión de otras personas.\"", "author": "Anónimo"},
            {"text": "\"Las bromas dejan de ser divertidas cuando hacen daño.\"", "author": "Anónimo"},
            {"text": "\"Hablar con respeto demuestra más inteligencia que insultar.\"", "author": "Anónimo"},
            {"text": "\"Un pequeño gesto de apoyo puede significar muchísimo para alguien.\"", "author": "Anónimo"}
        ];

        const curiosities = [
            "¿Sabías que... incluir a alguien que está solo en un juego o conversación puede mejorar muchísimo su estado de ánimo?",
            "¿Sabías que... un comentario positivo puede quedarse en la memoria de una persona durante años?",
            "¿Sabías que... escuchar a un compañero sin juzgar ayuda a reducir la ansiedad y el estrés?",
            "¿Sabías que... el bullying repetido puede afectar la autoestima y el rendimiento escolar de quien lo sufre?",
            "¿Sabías que... pedir ayuda a un profesor, tutor o familiar es una de las mejores formas de frenar el acoso escolar?",
            "¿Sabías que... defender a alguien que está siendo molestado puede animar a otros a hacer lo correcto también?",
            "¿Sabías que... los grupos donde existe respeto y compañerismo tienen menos conflictos y más confianza?",
            "¿Sabías que... un ambiente seguro y amable ayuda al cerebro a aprender mejor?",
            "¿Sabías que... ignorar el acoso puede hacer que continúe, pero hablarlo ayuda a detenerlo?",
            "¿Sabías que... la empatía es la capacidad de entender cómo se siente otra persona y puede prevenir el bullying?",
            "¿Sabías que... muchas personas que sufren acoso se sienten mejor cuando alguien simplemente les pregunta cómo están?",
            "¿Sabías que... todos podemos ayudar a crear una clase más segura usando palabras respetuosas y apoyando a los demás?"
        ];

        const q = quotes[Math.floor(Math.random() * quotes.length)];
        const c = curiosities[Math.floor(Math.random() * curiosities.length)];

        const quoteText = document.getElementById('quote-text');
        const quoteAuthor = document.getElementById('quote-author');
        const curiosityText = document.getElementById('curiosity-text');

        if (quoteText) quoteText.innerText = q.text;
        if (quoteAuthor) quoteAuthor.innerText = q.author;
        if (curiosityText) curiosityText.innerText = c;
    }

    // --- Motor del Wizard (Componente) ---
    class WizardFlow {
        constructor() {
            this.currentStep = 1;
            this.totalSteps = 8;
            this.data = {
                violence_situation: null,
                attacker: null,
                methods: [],
                frequency: null,
                feelings: [],
                talked_to: null,
                additional_info: "",
                config: { anonymous: true, contact: false, followup: true, urgent: false }
            };
            this.init();
        }

        init() {
            this.render();
            this.updateLabels();
        }

        render() {
            const container = document.getElementById('wizard-content');
            container.innerHTML = '';
            const stepDiv = document.createElement('div');
            stepDiv.className = 'animate-fadeIn space-y-6';
            
            switch(this.currentStep) {
                case 1: this.renderChoiceStep(stepDiv, 'violence_situation', [
                    { v: 'yes', l: i18n('report.step1_y'), i: 'warning' },
                    { v: 'no', l: i18n('report.step1_n'), i: 'check_circle' },
                    { v: 'not_sure', l: i18n('report.step1_u'), i: 'help' }
                ]); break;
                
                case 2: this.renderGridStep(stepDiv, 'attacker', [
                    { v: 'peer', l: i18n('report.step2_peer'), i: 'person' },
                    { v: 'adult', l: i18n('report.step2_adult'), i: 'school' },
                    { v: 'multiple', l: i18n('report.step2_multiple'), i: 'group' },
                    { v: 'outside', l: i18n('report.step2_outside'), i: 'public' },
                    { v: 'witness', l: i18n('report.step2_witness'), i: 'visibility' }
                ]); break;

                case 3: this.renderMultiStep(stepDiv, 'methods', [
                    { v: 'in_person', l: i18n('report.step3_in_person') },
                    { v: 'social', l: i18n('report.step3_social') },
                    { v: 'private', l: i18n('report.step3_private') },
                    { v: 'class', l: i18n('report.step3_class') },
                    { v: 'whatsapp', l: i18n('report.step3_whatsapp') },
                    { v: 'games', l: i18n('report.step3_games') },
                    { v: 'exclusion', l: i18n('report.step3_exclusion') },
                    { v: 'insults', l: i18n('report.step3_insults') },
                    { v: 'threats', l: i18n('report.step3_threats') },
                    { v: 'media', l: i18n('report.step3_media') }
                ]); break;

                case 4: this.renderChoiceStep(stepDiv, 'frequency', [
                    { v: 'once', l: i18n('report.step4_once'), i: 'filter_1' },
                    { v: 'sometimes', l: i18n('report.step4_sometimes'), i: 'calendar_month' },
                    { v: 'often', l: i18n('report.step4_often'), i: 'update' },
                    { v: 'daily', l: i18n('report.step4_daily'), i: 'event_repeat' },
                    { v: 'unknown', l: i18n('report.step4_unknown'), i: 'question_mark' }
                ]); break;

                case 5: this.renderMultiStep(stepDiv, 'feelings', [
                    { v: 'sad', l: i18n('report.step5_sad') },
                    { v: 'fear', l: i18n('report.step5_fear') },
                    { v: 'nervous', l: i18n('report.step5_nervous') },
                    { v: 'alone', l: i18n('report.step5_alone') },
                    { v: 'angry', l: i18n('report.step5_angry') },
                    { v: 'confused', l: i18n('report.step5_confused') },
                    { v: 'none', l: i18n('report.step5_none') }
                ]); break;

                case 6: this.renderGridStep(stepDiv, 'talked_to', [
                    { v: 'family', l: i18n('report.step6_family'), i: 'family_restroom' },
                    { v: 'friends', l: i18n('report.step6_friends'), i: 'diversity_3' },
                    { v: 'teachers', l: i18n('report.step6_teachers'), i: 'co_present' },
                    { v: 'counseling', l: i18n('report.step6_counseling'), i: 'psychology' },
                    { v: 'no', l: i18n('report.step6_no'), i: 'close' },
                    { v: 'none', l: i18n('report.step6_none'), i: 'block' }
                ]); break;

                case 7: this.renderTextStep(stepDiv, 'additional_info', i18n('report.step7_placeholder')); break;
                case 8: this.renderConfigStep(stepDiv); break;
            }
            container.appendChild(stepDiv);
            this.validate();
        }

        renderChoiceStep(container, field, options) {
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = `w-full flex items-center gap-4 p-5 rounded-2xl border-2 transition-all text-left group ${this.data[field] === opt.v ? 'border-primary bg-primary-container/10' : 'border-surface-variant/30 hover:border-primary/50 bg-surface'}`;
                btn.onclick = () => { this.data[field] = opt.v; this.next(); };
                btn.innerHTML = `
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-colors ${this.data[field] === opt.v ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface-variant group-hover:bg-primary/10 group-hover:text-primary'}">
                        <span class="material-symbols-outlined">${opt.i}</span>
                    </div>
                    <span class="font-bold text-lg ${this.data[field] === opt.v ? 'text-primary' : 'text-on-surface'}">${opt.l}</span>
                `;
                container.appendChild(btn);
            });
        }

        renderGridStep(container, field, options) {
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-1 sm:grid-cols-2 gap-4';
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = `flex flex-col items-center justify-center gap-3 p-6 rounded-2xl border-2 transition-all text-center group ${this.data[field] === opt.v ? 'border-primary bg-primary-container/10' : 'border-surface-variant/30 hover:border-primary/50 bg-surface'}`;
                btn.onclick = () => { this.data[field] = opt.v; this.next(); };
                btn.innerHTML = `
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mb-1 transition-all ${this.data[field] === opt.v ? 'bg-primary text-on-primary scale-110' : 'bg-surface-container-highest text-on-surface-variant group-hover:bg-primary/10 group-hover:text-primary'}">
                        <span class="material-symbols-outlined text-3xl">${opt.i}</span>
                    </div>
                    <span class="font-bold text-sm leading-tight ${this.data[field] === opt.v ? 'text-primary' : 'text-on-surface'}">${opt.l}</span>
                `;
                grid.appendChild(btn);
            });
            container.appendChild(grid);
        }

        renderMultiStep(container, field, options) {
            const grid = document.createElement('div'); grid.className = 'flex flex-wrap gap-2';
            options.forEach(opt => {
                const active = this.data[field].includes(opt.v);
                const btn = document.createElement('button');
                btn.className = `px-6 py-3 rounded-xl border-2 font-bold text-sm transition-all ${active ? 'bg-primary border-primary text-on-primary shadow-md' : 'bg-surface border-surface-variant/30 text-on-surface-variant hover:border-primary/50'}`;
                btn.onclick = () => {
                    if (active) this.data[field] = this.data[field].filter(v => v !== opt.v);
                    else this.data[field].push(opt.v);
                    this.render();
                };
                btn.innerText = opt.l;
                grid.appendChild(btn);
            });
            container.appendChild(grid);
        }

        renderTextStep(container, field, placeholder) {
            const area = document.createElement('textarea');
            area.className = 'w-full h-48 bg-surface-container-highest rounded-2xl p-6 font-body-md text-[16px] text-on-surface border-0 focus:ring-2 focus:ring-primary/20 transition-all resize-none shadow-inner';
            area.placeholder = placeholder; area.value = this.data[field];
            area.oninput = (e) => { this.data[field] = e.target.value; this.validate(); };
            container.appendChild(area);
        }

        renderConfigStep(container) {
            const configs = [
                { k: 'anonymous', l: i18n('report.step8_anonymous'), d: 'Orientación sabrá quién eres, pero tus profesores no.' },
                { k: 'contact', l: i18n('report.step8_contact'), d: 'Permites que te contacten directamente.' },
                { k: 'followup', l: i18n('report.step8_followup'), d: 'Recibirás avisos sobre el estado del caso.' },
                { k: 'urgent', l: i18n('report.step8_urgent'), d: 'Marca esto si sientes peligro inmediato.', w: true }
            ];
            configs.forEach(cfg => {
                const active = this.data.config[cfg.k];
                const row = document.createElement('div');
                row.className = `flex items-start gap-4 p-4 rounded-2xl border-2 transition-all cursor-pointer mb-3 ${active ? (cfg.w ? 'border-error bg-error/5' : 'border-primary bg-primary-container/5') : 'border-surface-variant/20 bg-surface'}`;
                row.onclick = () => { this.data.config[cfg.k] = !this.data.config[cfg.k]; this.render(); };
                row.innerHTML = `
                    <div class="mt-1"><div class="w-6 h-6 rounded-md flex items-center justify-center transition-all ${active ? (cfg.w ? 'bg-error text-white' : 'bg-primary text-white') : 'bg-surface-container-highest'}">${active ? '<span class="material-symbols-outlined text-sm font-bold">check</span>' : ''}</div></div>
                    <div class="flex-1"><p class="font-bold text-sm ${cfg.w && active ? 'text-error' : 'text-on-surface'}">${cfg.l}</p><p class="text-[11px] text-on-surface-variant mt-0.5">${cfg.d}</p></div>
                `;
                container.appendChild(row);
            });
        }

        updateLabels() {
            document.getElementById('wizard-step-indicator').innerText = `Paso ${this.currentStep} de ${this.totalSteps}`;
            const titles = [i18n('report.step1_q'), i18n('report.step2_q'), i18n('report.step3_q'), i18n('report.step4_q'), i18n('report.step5_q'), i18n('report.step6_q'), i18n('report.step7_q'), i18n('report.step8_q')];
            document.getElementById('wizard-step-title').innerText = titles[this.currentStep-1];
            const icons = ['help', 'person', 'flash_on', 'schedule', 'mood', 'forum', 'edit_note', 'settings'];
            document.getElementById('wizard-step-icon').innerHTML = `<span class="material-symbols-outlined text-2xl">${icons[this.currentStep-1]}</span>`;
            document.getElementById('wizard-progress-bar').style.width = `${(this.currentStep / this.totalSteps) * 100}%`;
            document.getElementById('btn-wizard-prev').style.visibility = this.currentStep === 1 ? 'hidden' : 'visible';
            document.getElementById('btn-wizard-next-text').innerText = (this.currentStep === this.totalSteps) ? i18n('dashboard.submit') : i18n('dashboard.next_step');
        }

        validate() {
            let valid = true;
            if (this.currentStep === 1 && !this.data.violence_situation) valid = false;
            if (this.currentStep === 2 && !this.data.attacker) valid = false;
            if (this.currentStep === 4 && !this.data.frequency) valid = false;
            if (this.currentStep === 6 && !this.data.talked_to) valid = false;
            document.getElementById('btn-wizard-next').disabled = !valid;
        }

        next() { if (this.currentStep < this.totalSteps) { this.currentStep++; this.init(); } else { this.submit(); } }
        prev() { if (this.currentStep > 1) { this.currentStep--; this.init(); } }

        async submit() {
            const btn = document.getElementById('btn-wizard-next'); btn.disabled = true;
            btn.innerHTML = `<span class="material-symbols-outlined animate-spin">refresh</span> ${i18n('sending')}`;
            let content = `[FLUJO GUIADO]\nSituación: ${this.data.violence_situation}\nAutor: ${this.data.attacker}\nMétodos: ${this.data.methods.join(', ')}\nFrecuencia: ${this.data.frequency}\nSiente: ${this.data.feelings.join(', ')}\nHablado: ${this.data.talked_to}\n${this.data.additional_info ? ('\nInfo: ' + this.data.additional_info) : ''}`;
            const payload = { content: content, target: this.data.attacker === 'peer' ? 'compañero' : 'otro', urgency_level: this.data.config.urgent ? 'high' : 'low', is_anonymous: this.data.config.anonymous };
            try {
                const res = await fetchJson('/alumno/report', { method: 'POST', body: payload });
                if (res.success) {
                    ['wizard-header', 'wizard-content', 'wizard-nav'].forEach(id => document.getElementById(id).classList.add('hidden'));
                    document.getElementById('wizard-success').classList.replace('hidden', 'flex');
                } else { alert(res.error || 'Error'); btn.disabled = false; this.updateLabels(); }
            } catch (e) { alert('Error de conexión'); btn.disabled = false; this.updateLabels(); }
        }
    }

    // --- Gestión de Chat y Reportes (FIX Blanco) ---
    async function loadStudentReport(id) {
        try {
            currentReportId = id;
            ['wizard-header', 'wizard-content', 'wizard-nav', 'wizard-success'].forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById('chat-view').classList.replace('hidden', 'flex');
            const cm = document.getElementById('chat-messages'); 
            cm.innerHTML = `<div class="flex flex-col items-center justify-center h-64 gap-4"><span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span><p class="font-bold text-primary animate-pulse">Abriendo espacio seguro...</p></div>`;
            const res = await fetchJson(`/alumno/reports/${id}`);
            if (res.error) throw new Error(res.error);
            renderStudentChat(res.report, res.messages);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (error) {
            console.error("[TicketOpenError]", error);
            document.getElementById('chat-messages').innerHTML = `<div class="flex flex-col items-center justify-center h-64 p-8 text-center bg-error/5 rounded-3xl border-2 border-dashed border-error/20"><div class="w-16 h-16 rounded-full bg-error/10 text-error flex items-center justify-center mb-4"><span class="material-symbols-outlined text-3xl">error_outline</span></div><p class="font-bold text-error mb-1">${i18n('ticket_load_error')}</p><p class="text-[10px] text-on-surface-variant">${error.message}</p><button onclick="window.location.reload()" class="mt-4 bg-surface px-6 py-2 rounded-full border-2 font-bold text-xs">Volver</button></div>`;
        }
    }

    function renderStudentChat(report, messages) {
        const pill = document.getElementById('chat-status-pill');
        pill.innerText = translateStatus(report.status);
        pill.className = "px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm " + 
            (report.status === 'new' ? 'bg-primary text-on-primary' : (report.status === 'in_progress' ? 'bg-amber-400 text-amber-950' : 'bg-emerald-400 text-emerald-950'));
        const ic = document.getElementById('chat-input-container');
        if (report.status === 'resolved') {
            ic.classList.add('hidden');
            document.getElementById('resolved-note').innerText = "Resolución: " + (report.resolution_summary || "Cerrado.");
            document.getElementById('resolved-note').classList.remove('hidden');
        } else { ic.classList.remove('hidden'); document.getElementById('resolved-note').classList.add('hidden'); }
        let h = `<div class="flex gap-4 flex-row-reverse mb-8 group"><div class="w-10 h-10 rounded-2xl bg-surface-container-highest flex items-center justify-center text-xs font-black shrink-0">Tú</div><div class="bg-surface-container-low p-5 rounded-3xl rounded-tr-none max-w-[85%] text-[15px] leading-relaxed shadow-sm border border-surface-variant/20 whitespace-pre-wrap">${escapeHtml(report.content)}</div></div>`;
        if (messages.length === 0) h += `<div class="p-8 text-center text-outline/40 text-xs italic">${i18n('empty_chat')}</div>`;
        messages.forEach(m => {
            const me = m.is_current_user;
            h += `<div class="flex gap-4 ${me ? 'flex-row-reverse' : ''} mb-8 animate-fadeIn"><div class="w-10 h-10 rounded-2xl ${me ? 'bg-surface-container-highest' : 'bg-primary text-on-primary'} flex items-center justify-center text-xs font-black shrink-0">${me ? 'Tú' : escapeHtml(m.sender_name.charAt(0))}</div><div class="${me ? 'bg-surface-container-low rounded-tr-none' : 'bg-white rounded-tl-none'} p-5 rounded-3xl shadow-sm max-w-[85%] text-[15px] border"><p class="whitespace-pre-wrap">${escapeHtml(m.message)}</p></div></div>`;
        });
        document.getElementById('chat-messages').innerHTML = h;
        setTimeout(() => { const cm = document.getElementById('chat-messages'); cm.scrollTop = cm.scrollHeight; }, 100);
    }

    async function sendStudentMessage() {
        const i = document.getElementById('reply-message'); const msg = i.value.trim(); if (!msg || !currentReportId) return;
        i.disabled = true;
        try {
            const res = await fetchJson(`/alumno/reports/${currentReportId}/messages`, { method: 'POST', body: { message: msg } });
            if (!res.error) { i.value = ''; loadStudentReport(currentReportId); } else { alert(res.error); }
        } catch (e) { alert('Error'); } finally { i.disabled = false; i.focus(); }
    }

    // --- Respira Conmigo logic ---
    const BREATH = { calm: [4, 0, 6, 0], box: [4, 4, 4, 4], sleep: [4, 7, 8, 0] };
    const LABELS = ['<?= \App\Core\Lang::t('breathing.inhale') ?>', '<?= \App\Core\Lang::t('breathing.hold') ?>', '<?= \App\Core\Lang::t('breathing.exhale') ?>', '<?= \App\Core\Lang::t('breathing.hold') ?>'];
    let bState = { running: false, cycle: 1, pIdx: 0, start: 0, dur: 0, raf: null, rhythm: 'calm' };
    function openBreathingApp() { document.getElementById('breathing-app-container').classList.remove('hidden'); }
    function closeBreathingApp() { bState.running = false; cancelAnimationFrame(bState.raf); document.getElementById('breathing-app-container').classList.add('hidden'); }
    function selectRhythm(r, btn) { bState.rhythm = r; document.querySelectorAll('.r-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); }
    function startSession() { document.getElementById('b-landing').classList.add('hidden'); document.getElementById('b-scene').classList.remove('hidden'); bState.running = true; bState.cycle = 1; runPhase(0); }
    function runPhase(idx) {
        if (!bState.running) return;
        const p = BREATH[bState.rhythm];
        while (p[idx] === 0) { idx = (idx + 1) % 4; if (idx === 0) bState.cycle++; }
        bState.pIdx = idx; bState.dur = p[idx] * 1000; bState.start = performance.now();
        document.getElementById('b-label').innerText = LABELS[idx]; document.getElementById('b-cycle').innerText = bState.cycle;
        document.getElementById('b-halo').className = 'absolute inset-0 rounded-full blur-3xl opacity-40 transition-all ' + (idx === 0 ? 'inhale' : (idx === 2 ? 'exhale' : ''));
        animate();
    }
    function animate() {
        if (!bState.running) return;
        const elapsed = performance.now() - bState.start; const prog = Math.min(elapsed / bState.dur, 1);
        const eased = 0.5 - 0.5 * Math.cos(prog * Math.PI);
        let s = 1; if (bState.pIdx === 0) s = 0.6 + 0.6 * eased; else if (bState.pIdx === 1) s = 1.2; else if (bState.pIdx === 2) s = 1.2 - 0.6 * eased; else s = 0.6;
        document.getElementById('b-circle').style.transform = `scale(${s})`;
        const rem = Math.max(0, Math.ceil((bState.dur - elapsed) / 1000));
        document.getElementById('b-timer').innerText = rem > 0 ? '· ' + rem + ' ·' : '';
        if (prog < 1) bState.raf = requestAnimationFrame(animate); else runPhase((bState.pIdx + 1) % 4);
    }

    // --- WebAuthn logic ---
    function base64urlToBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const binary_string = window.atob(base64.length % 4 ? base64 + '===='.substring(base64.length % 4) : base64);
        const bytes = new Uint8Array(binary_string.length);
        for (let i = 0; i < binary_string.length; i++) bytes[i] = binary_string.charCodeAt(i);
        return bytes.buffer;
    }
    function bufferToBase64url(buffer) {
        const bytes = new Uint8Array(buffer); let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
        return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }
    async function registerWebAuthn() {
        if (!window.isSecureContext && window.location.hostname !== 'localhost') { alert('HTTPS requerido'); return; }
        const deviceName = prompt('Nombre dispositivo', 'Mi dispositivo'); if (!deviceName) return;
        try {
            const optRes = await fetchJson('/alumno/2fa/webauthn/register/options');
            if (optRes.error) throw new Error(optRes.error);
            const options = optRes; options.challenge = base64urlToBuffer(options.challenge);
            options.user.id = base64urlToBuffer(options.user.id);
            if (options.excludeCredentials) options.excludeCredentials.forEach(c => c.id = base64urlToBuffer(c.id));
            const credential = await navigator.credentials.create({ publicKey: options });
            const verifyRes = await fetchJson('/alumno/2fa/webauthn/register/verify', {
                method: 'POST', body: {
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64url(credential.response.attestationObject),
                    device_name: deviceName
                }
            });
            if (verifyRes.success) window.location.reload(); else throw new Error(verifyRes.error);
        } catch (e) { alert('Error: ' + e.message); }
    }
    async function deleteWebAuthn(id) {
        if (!confirm('¿Eliminar?')) return;
        const res = await fetchJson('/alumno/2fa/webauthn/credential/delete', { method: 'POST', body: { id } });
        if (res.success) window.location.reload();
    }

    function resetForm() { window.location.reload(); }

    // --- Init ---
    let appWizard = null; let currentReportId = null;
    document.addEventListener('DOMContentLoaded', () => {
        appWizard = new WizardFlow();
        ViewManager.showHome();
        document.querySelectorAll('.status-label-js').forEach(el => el.innerText = translateStatus(el.dataset.status));
    });
</script>
<?php $scripts = ob_get_clean(); ?>

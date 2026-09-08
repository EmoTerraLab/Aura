<?php $bodyClass = "antialiased min-h-screen flex flex-col bg-surface font-body-md text-on-surface"; ?>

<!-- Mobile TopNavBar -->
<nav class="lg:hidden fixed top-0 w-full z-[50] flex justify-between items-center px-4 h-14 bg-surface/90 backdrop-blur-md border-b border-surface-variant/40 font-display">
    <div class="flex items-center gap-2.5">
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" class="w-7 h-7 rounded-lg object-contain shadow-xs" alt="Prisma">
        <span class="font-bold text-base text-primary tracking-tight">Prisma</span>
    </div>
    <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors cursor-pointer" aria-label="Abrir menú">
        <span class="material-symbols-outlined text-2xl" id="menu-icon">menu</span>
    </button>
</nav>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[55] hidden lg:hidden"></div>

<!-- Sidebar -->
<nav id="app-sidebar" class="bg-surface-container-lowest font-display font-medium h-screen w-64 fixed left-0 top-0 border-r border-surface-variant/40 shadow-xs z-[60] -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col py-6">
    <div class="px-6 mb-6 flex items-center gap-3">
        <img src="<?= BASE_URL ?>assets/prisma-symbol.jpeg" class="w-8 h-8 rounded-lg object-contain shadow-xs" alt="Prisma">
        <div>
            <span class="text-base font-bold text-primary tracking-tight block">Prisma</span>
            <span class="text-[11px] text-on-surface-variant/70 block">Espacio Seguro</span>
        </div>
    </div>

    <div class="px-4 mb-4">
        <button onclick="ViewManager.showReporting(); toggleSidebar()" class="w-full h-11 bg-primary text-on-primary rounded-xl px-4 flex items-center justify-center gap-2 shadow-xs hover:bg-primary/90 active:scale-[0.99] transition-all cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span class="font-bold text-xs uppercase tracking-wider"><?= \App\Core\Lang::t('nav.new_report') ?></span>
        </button>
    </div>

    <div class="flex-1 flex flex-col gap-1 overflow-y-auto px-2">
        <a class="bg-prisma-mint/25 text-teal-900 font-bold rounded-xl px-3.5 py-2.5 flex items-center gap-3 active:scale-98 transition-all" href="#" onclick="event.preventDefault(); ViewManager.showHome(); toggleSidebar()">
            <span class="material-symbols-outlined text-[20px] text-teal-800" style="font-variation-settings: 'FILL' 1;">dashboard</span>
            <span class="text-xs font-semibold"><?= \App\Core\Lang::t('nav.dashboard') ?></span>
        </a>
        <?php if (\App\Core\Config::get('ccaa_protocol_active', '1') === '1' && \App\Core\Config::get('ccaa_show_to_students', '1') === '1'): ?>
            <a class="text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl flex items-center gap-3 transition-colors" href="/protocolo-acoso">
                <span class="material-symbols-outlined text-[20px]">gavel</span>
                <span class="text-xs font-semibold"><?= \App\Core\Lang::t('protocol.title') ?></span>
            </a>
        <?php endif; ?>
        <button onclick="openBreathingApp(); toggleSidebar()" class="text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl flex items-center gap-3 transition-colors text-left cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">spa</span>
            <span class="text-xs font-semibold"><?= \App\Core\Lang::t('nav.breathe') ?></span>
        </button>
        <?php if(\App\Core\Config::get('2fa_students_method', 'webauthn') === 'webauthn'): ?>
        <button onclick="registerWebAuthn(); toggleSidebar()" class="text-on-surface-variant hover:text-primary hover:bg-surface-container-low px-3.5 py-2.5 rounded-xl flex items-center gap-3 transition-colors text-left cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">fingerprint</span>
            <span class="text-xs font-semibold">Acceso Biométrico</span>
        </button>
        <?php endif; ?>
    </div>

    <div class="mt-auto flex flex-col gap-2 px-2 border-t border-surface-variant/30 pt-4">
        <div class="px-2">
            <?= \App\Core\Lang::renderSelector() ?>
        </div>
        <form action="/logout" method="POST" class="w-full">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Csrf::generateToken() ?>">
            <button type="submit" class="w-full text-left text-on-surface-variant hover:text-error hover:bg-error/10 px-3.5 py-2 rounded-xl flex items-center gap-3 transition-colors text-xs font-semibold cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                <span><?= \App\Core\Lang::t('nav.logout') ?></span>
            </button>
        </form>
    </div>
</nav>

<!-- Main Content Canvas -->
<main class="flex-1 w-full lg:pl-64 flex flex-col pt-14 lg:pt-0 overflow-y-auto min-h-0">
    <div class="px-4 py-6 md:px-8 md:py-8 max-w-6xl mx-auto w-full flex-1 flex flex-col gap-6">
        
        <?php
        $db = \App\Core\Database::getInstance();
        $userId = \App\Core\Auth::id();
        
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
                    <div class="bg-prisma-lavender/25 text-purple-950 p-5 rounded-2xl shadow-sm flex items-center justify-between gap-4 border border-prisma-lavender/40 animate-fadeIn">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-purple-900/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl text-purple-900">hub</span>
                            </div>
                            <div>
                                <p class="font-display font-bold text-xs md:text-sm">Qüestionari de Clima d\'Aula pendent</p>
                                <p class="text-[11px] text-purple-900/80">La teva participació ens ajuda a millorar la convivència a classe.</p>
                            </div>
                        </div>
                        <a href="/alumno/sociograma" class="h-9 px-4 bg-primary text-on-primary rounded-xl text-xs font-display font-bold shrink-0 flex items-center justify-center shadow-xs hover:bg-primary/90 transition-all">Començar</a>
                    </div>';
                }
            }
        }
        ?>

        <header class="flex flex-col gap-1">
            <h1 class="font-display font-bold text-xl md:text-2xl text-on-surface tracking-tight"><?= \App\Core\Lang::t('dashboard.safe_space_title') ?></h1>
            <p class="font-body-md text-xs md:text-sm text-on-surface-variant max-w-2xl"><?= \App\Core\Lang::t('dashboard.safe_space_desc') ?></p>
        </header>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Main Content Area -->
            <div id="main-view-container" class="lg:col-span-8 flex flex-col gap-6">
                
                <!-- Home View -->
                <div id="home-view" class="animate-fadeIn space-y-6">
                    <!-- Inspiration Banner Card -->
                    <div class="bg-gradient-to-br from-primary via-[#283247] to-primary text-on-primary p-6 md:p-8 rounded-2xl shadow-card relative overflow-hidden group border border-surface-variant/20">
                        <div class="absolute -right-8 -top-8 w-36 h-36 bg-prisma-mint/15 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
                        <div class="relative z-10">
                            <span class="material-symbols-outlined text-3xl text-prisma-mint opacity-80 mb-3 block">auto_awesome</span>
                            <h2 id="quote-text" class="text-lg md:text-xl font-display font-medium italic leading-snug mb-3">"Cargando inspiración..."</h2>
                            <p id="quote-author" class="text-[11px] font-display font-bold tracking-widest uppercase text-prisma-sky opacity-90">-- Autor</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                        <!-- Action Card -->
                        <div class="bg-surface-container-lowest p-5 md:p-6 rounded-2xl shadow-card border border-surface-variant/40 flex flex-col justify-between group hover:border-primary/30 transition-all">
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-prisma-mint/25 text-teal-900 flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-2xl">chat_bubble</span>
                                </div>
                                <h3 class="text-on-surface font-display font-bold text-sm md:text-base mb-1.5">¿Algo te preocupa?</h3>
                                <p class="text-on-surface-variant text-xs mb-5 leading-relaxed">Estamos aquí para escucharte y ayudarte con total confianza.</p>
                            </div>
                            <button id="btn-start-report" onclick="ViewManager.showReporting()" class="w-full h-11 bg-primary text-on-primary rounded-xl font-display font-bold text-xs shadow-xs hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">chat_bubble</span>
                                <span>Necesito hablar</span>
                            </button>
                        </div>

                        <!-- Curiosity Card -->
                        <div class="bg-surface-container-lowest p-5 md:p-6 rounded-2xl shadow-card border border-surface-variant/40 flex flex-col gap-3 justify-between">
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-800 flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-2xl">lightbulb</span>
                                </div>
                                <span class="text-[11px] font-display font-bold uppercase tracking-wider text-on-surface-variant block mb-1">Consejo de Convivencia</span>
                                <p id="curiosity-text" class="text-on-surface-variant text-xs leading-relaxed italic">¿Sabías que... respirar profundamente ayuda a calmar tu mente?</p>
                            </div>
                            <div class="pt-2">
                                <button onclick="openBreathingApp()" class="w-full h-11 bg-surface-container-low text-primary rounded-xl font-display font-bold text-xs hover:bg-surface-container-high transition-all flex items-center justify-center gap-2 border border-surface-variant/30 cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">spa</span>
                                    <span>Pausa de calma</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wizard Container (Hidden by default) -->
                <div id="reporting-card" class="hidden bg-surface-container-lowest rounded-2xl shadow-card border border-surface-variant/40 p-5 md:p-8 flex flex-col relative min-h-[480px] animate-fadeIn">
                    
                    <div class="relative z-10 flex flex-col h-full">
                        
                        <!-- Header / Stepper -->
                        <div id="wizard-header" class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex flex-col">
                                    <span id="wizard-step-indicator" class="text-[10px] font-display font-bold uppercase tracking-wider text-primary mb-0.5">Paso 1 de 8</span>
                                    <h2 id="wizard-step-title" class="font-display font-bold text-base md:text-lg text-on-surface">¿Qué está pasando?</h2>
                                </div>
                                <div id="wizard-step-icon" class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl">help</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                                <div id="wizard-progress-bar" class="h-full bg-primary w-[12.5%] transition-all duration-500 ease-out rounded-full"></div>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <div id="wizard-content" class="flex-1">
                            <!-- Dynamic content injected by JS -->
                        </div>

                        <!-- Navigation -->
                        <div id="wizard-nav" class="flex items-center justify-between mt-6 pt-5 border-t border-surface-variant/30">
                            <button id="btn-wizard-prev" onclick="appWizard.prev()" class="invisible flex items-center gap-1.5 text-on-surface-variant font-display font-semibold text-xs hover:bg-surface-container-low px-4 py-2.5 rounded-xl transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-base">arrow_back</span>
                                <?= \App\Core\Lang::t('dashboard.back') ?>
                            </button>
                            
                            <button id="btn-wizard-next" onclick="appWizard.next()" class="h-11 bg-primary text-on-primary font-display font-bold text-xs px-6 rounded-xl shadow-xs flex items-center gap-2 hover:bg-primary/90 active:scale-[0.99] transition-all disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                                <span id="btn-wizard-next-text"><?= \App\Core\Lang::t('dashboard.next_step') ?></span>
                                <span class="material-symbols-outlined text-base" id="btn-wizard-next-icon">arrow_forward</span>
                            </button>
                        </div>

                        <!-- Success View (Hidden by default) -->
                        <div id="wizard-success" class="hidden flex-col items-center justify-center text-center py-10 gap-4 animate-fadeIn">
                            <div class="w-16 h-16 rounded-2xl bg-prisma-mint/20 text-teal-800 flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-4xl">check_circle</span>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-lg md:text-xl text-on-surface mb-1"><?= \App\Core\Lang::t('dashboard.success_title') ?></h3>
                                <p class="text-xs md:text-sm text-on-surface-variant max-w-sm mx-auto leading-relaxed"><?= \App\Core\Lang::t('dashboard.success_desc') ?></p>
                            </div>
                            <button onclick="window.location.reload()" class="h-10 bg-primary text-on-primary font-display font-bold text-xs px-6 rounded-xl hover:bg-primary/90 transition-all cursor-pointer">
                                <?= \App\Core\Lang::t('dashboard.back_to_panel') ?>
                            </button>
                        </div>

                        <!-- Chat View (Hidden by default) -->
                        <div id="chat-view" class="hidden flex-col h-full animate-fadeIn">
                            <div class="flex items-center justify-between border-b border-surface-variant/30 pb-3 mb-4">
                                <button onclick="window.location.reload()" class="flex items-center text-primary font-display font-bold text-xs hover:underline gap-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">arrow_back</span> 
                                    <?= \App\Core\Lang::t('nav.back_to_menu') ?>
                                </button>
                                <div class="flex items-center gap-2">
                                    <span id="chat-status-pill" class="px-2.5 py-1 rounded-full text-[10px] font-display font-bold uppercase tracking-wider shadow-xs">--</span>
                                </div>
                            </div>
                            <div id="resolved-note" class="hidden bg-prisma-mint/20 border border-teal-600/30 text-teal-900 rounded-xl p-3.5 mb-3 text-xs italic shadow-xs"></div>
                            <div id="chat-messages" class="flex-1 overflow-y-auto space-y-4 p-2 mb-4 min-h-[260px] max-h-[420px]">
                                <!-- Messages injected by JS -->
                            </div>
                            <div id="chat-input-container" class="relative group">
                                <input id="reply-message" class="w-full h-12 bg-surface-container-low border border-surface-variant/40 rounded-xl pl-4 pr-12 font-body-md text-xs md:text-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-outline-variant outline-none" placeholder="<?= \App\Core\Lang::t('dashboard.chat_placeholder') ?>" type="text"/>
                                <button onclick="sendStudentMessage()" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg bg-primary text-on-primary flex items-center justify-center hover:bg-primary/90 active:scale-95 transition-all cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">send</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- Activity History Card -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-card border border-surface-variant/40 p-5">
                    <h3 class="font-display font-bold text-sm md:text-base text-on-surface mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">history</span>
                        <?= \App\Core\Lang::t('dashboard.history_title') ?>
                    </h3>
                    <div class="space-y-2.5">
                        <?php if (empty($reports)): ?>
                            <div class="p-6 text-center text-on-surface-variant text-xs italic border border-dashed border-surface-variant/50 rounded-xl">
                                <?= \App\Core\Lang::t('dashboard.no_activity') ?>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <div class="p-3.5 rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-all group border border-surface-variant/30 cursor-pointer" onclick="loadStudentReport(<?= $report['id'] ?>)">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="font-display text-[10px] font-bold text-on-surface-variant/70 uppercase"><?= date('d M', strtotime($report['created_at'])) ?></span>
                                        <span class="text-[9px] font-display font-bold uppercase px-2 py-0.5 rounded-full <?= $report['status']==='new'?'bg-prisma-sky/40 text-sky-950':($report['status']==='in_progress'?'bg-amber-100 text-amber-900':'bg-prisma-mint/30 text-teal-900') ?>"><?= \App\Core\Lang::t('status.' . $report['status']) ?></span>
                                    </div>
                                    <p class="text-xs text-on-surface font-medium line-clamp-2"><?= htmlspecialchars($report['content']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- WebAuthn Hidden Support -->
                <div id="webauthn-section" class="hidden">
                    <div id="webauthn-list-container"></div>
                    <div id="webauthn-status-box" class="hidden"></div>
                    <button id="btn-register-biometric" class="hidden"></button>
                    <p id="webauthn-error-msg" class="hidden"></p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ============================== RESPIRA CONMIGO ============================== -->
<div id="breathing-app-container" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center overflow-hidden bg-primary text-[#f4ede4] font-display">
    <button onclick="closeBreathingApp()" class="absolute top-6 right-6 text-white/60 hover:text-white transition-colors z-[110] cursor-pointer">
        <span class="material-symbols-outlined text-3xl">close</span>
    </button>

    <section id="b-landing" class="absolute inset-0 flex items-center justify-center p-6 transition-all duration-700 z-[106] bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#283247] via-primary to-[#131722]">
        <div class="text-center max-w-md">
            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-prisma-mint mb-3"><?= \App\Core\Lang::t('breathing.landing_subtitle') ?></p>
            <h1 class="text-3xl md:text-5xl font-light mb-3 font-display">Respira <span class="font-bold text-prisma-mint">Conmigo</span></h1>
            <p class="text-prisma-sky/80 mb-8 text-xs md:text-sm leading-relaxed"><?= \App\Core\Lang::t('breathing.landing_desc') ?></p>
            <div class="flex flex-wrap gap-2 justify-center mb-8">
                <button onclick="selectRhythm('calm', this)" class="r-btn active px-4 py-2 rounded-xl border border-white/20 bg-white/5 text-prisma-sky text-xs hover:bg-white/10 transition-all cursor-pointer"><?= \App\Core\Lang::t('breathing.calm') ?><small class="block opacity-60 text-[10px]">4 · 6</small></button>
                <button onclick="selectRhythm('box', this)" class="r-btn px-4 py-2 rounded-xl border border-white/20 bg-white/5 text-prisma-sky text-xs hover:bg-white/10 transition-all cursor-pointer"><?= \App\Core\Lang::t('breathing.focus') ?><small class="block opacity-60 text-[10px]">4 · 4 · 4 · 4</small></button>
                <button onclick="selectRhythm('sleep', this)" class="r-btn px-4 py-2 rounded-xl border border-white/20 bg-white/5 text-prisma-sky text-xs hover:bg-white/10 transition-all cursor-pointer"><?= \App\Core\Lang::t('breathing.rest') ?><small class="block opacity-60 text-[10px]">4 · 7 · 8</small></button>
            </div>
            <button onclick="startSession()" class="h-12 bg-prisma-mint text-primary px-8 rounded-xl font-bold text-sm shadow-card hover:brightness-105 active:scale-95 transition-all cursor-pointer"><?= \App\Core\Lang::t('breathing.title') ?></button>
        </div>
    </section>

    <section id="b-scene" class="hidden absolute inset-0 flex items-center justify-center transition-all duration-1000 z-[107]">
        <div class="absolute top-8 left-1/2 -translate-x-1/2 text-[10px] tracking-[0.3em] opacity-60 uppercase font-bold"><?= \App\Core\Lang::t('breathing.cycle') ?> <strong id="b-cycle" class="text-prisma-mint">1</strong></div>
        <div class="relative flex items-center justify-center w-[220px] h-[220px] md:w-[380px] md:h-[380px]">
            <div id="b-halo" class="absolute inset-0 rounded-full blur-2xl opacity-40 transition-all duration-1000"></div>
            <div id="b-circle" class="relative w-3/5 h-3/5 rounded-full flex flex-col items-center justify-center text-center shadow-2xl transition-all ease-in-out border border-white/20" style="background: radial-gradient(circle at 35% 30%, rgba(255, 255, 255, 0.2) 0%, rgba(121, 224, 204, 0.35) 50%, rgba(30, 36, 51, 0.7) 100%);">
                <div id="b-label" class="text-xl md:text-3xl font-light italic font-display"><?= \App\Core\Lang::t('breathing.prepare') ?></div>
                <div id="b-timer" class="text-[11px] opacity-60 mt-1.5 tracking-widest font-mono"></div>
            </div>
        </div>
        <p class="absolute bottom-10 uppercase tracking-[0.3em] text-[10px] opacity-50 font-bold"><?= \App\Core\Lang::t('breathing.follow_rhythm') ?></p>
    </section>
</div>

<style>
    .r-btn.active { background: #79E0CC !important; border-color: #79E0CC !important; color: #1E2433 !important; font-weight: 700; }
    #b-halo.inhale { background: #A6E4FF; transform: scale(1.4); }
    #b-halo.exhale { background: #79E0CC; transform: scale(0.85); }
</style>

<?php ob_start(); ?>
<script>
    /**
     * Prisma Safe Space Dashboard
     */
    const currentLang = document.documentElement.lang || 'es';
    
    const statusTranslations = {
        es: { pending: "Pendiente", in_progress: "En Revisión", resolved: "Resuelto", new: "Nuevo" },
        ca: { pending: "Pendent", in_progress: "En Revisió", resolved: "Resolt", new: "Nou" },
        gl: { pending: "Pendente", in_progress: "En Revisión", resolved: "Resolto", new: "Novo" },
        eu: { pending: "Zain", in_progress: "Berrikuspenean", resolved: "Ebatzia", new: "Berria" },
        en: { pending: "Pending", in_progress: "In Review", resolved: "Resolved", new: "New" }
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
            'ticket_load_error': "Error al cargar el caso. Inténtalo de nuevo.",
            'empty_chat': "No hay mensajes en esta conversación.",
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
            if (i) i.innerText = 'menu'; document.body.style.overflow = '';
        } else {
            s.classList.remove('-translate-x-full'); o.classList.remove('hidden');
            if (i) i.innerText = 'close'; document.body.style.overflow = 'hidden';
        }
    }

    const ViewManager = {
        views: ['home-view', 'reporting-card', 'chat-view', 'wizard-success'],
        
        hideAll() {
            this.views.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            ['wizard-header', 'wizard-content', 'wizard-nav'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            });
        },

        showHome() {
            this.hideAll();
            document.getElementById('home-view').classList.remove('hidden');
            const main = document.querySelector('main');
            if (main) main.scrollTo(0, 0);
            initHomeData();
        },

        showReporting() {
            this.hideAll();
            document.getElementById('reporting-card').classList.remove('hidden');
            const main = document.querySelector('main');
            if (main) main.scrollTo(0, 0);

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
            const main = document.querySelector('main');
            if (main) main.scrollTo(0, 0);

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
            "¿Sabías que... el acoso escolar repetido puede afectar la autoestima y el bienestar de quien lo sufre?",
            "¿Sabías que... pedir ayuda al equipo de orientación o tutoría es una de las mejores formas de solucionar los conflictos?",
            "¿Sabías que... defender a alguien que está siendo molestado anima a otros a actuar con justicia?",
            "¿Sabías que... los grupos donde existe respeto mutuo y compañerismo tienen menos conflictos y más confianza?",
            "¿Sabías que... un ambiente seguro y amable ayuda al cerebro a aprender con mayor tranquilidad?",
            "¿Sabías que... ignorar las conductas dañinas las normaliza, pero comunicarlas ayuda a detenerlas?",
            "¿Sabías que... la empatía es la capacidad de conectar con cómo se siente otra persona y previene el daño?",
            "¿Sabías que... muchas personas se sienten aliviadas cuando alguien simplemente les pregunta con cariño cómo están?",
            "¿Sabías que... todos podemos ayudar a crear un centro educativo seguro apoyando a los demás?"
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
            stepDiv.className = 'animate-fadeIn space-y-4';
            
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
                btn.className = `w-full min-h-[52px] flex items-center gap-3.5 p-3.5 rounded-xl border transition-all text-left cursor-pointer ${this.data[field] === opt.v ? 'border-primary bg-primary/5' : 'border-surface-variant/40 hover:border-primary/40 bg-surface-container-low'}`;
                btn.onclick = () => { this.data[field] = opt.v; this.next(); };
                btn.innerHTML = `
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 transition-colors ${this.data[field] === opt.v ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface-variant'}">
                        <span class="material-symbols-outlined text-xl">${opt.i}</span>
                    </div>
                    <span class="font-display font-semibold text-xs md:text-sm ${this.data[field] === opt.v ? 'text-primary' : 'text-on-surface'}">${opt.l}</span>
                `;
                container.appendChild(btn);
            });
        }

        renderGridStep(container, field, options) {
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3';
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = `flex flex-col items-center justify-center gap-2 p-4 rounded-xl border transition-all text-center cursor-pointer min-h-[88px] ${this.data[field] === opt.v ? 'border-primary bg-primary/5' : 'border-surface-variant/40 hover:border-primary/40 bg-surface-container-low'}`;
                btn.onclick = () => { this.data[field] = opt.v; this.next(); };
                btn.innerHTML = `
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 transition-all ${this.data[field] === opt.v ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface-variant'}">
                        <span class="material-symbols-outlined text-xl">${opt.i}</span>
                    </div>
                    <span class="font-display font-semibold text-xs leading-tight ${this.data[field] === opt.v ? 'text-primary' : 'text-on-surface'}">${opt.l}</span>
                `;
                grid.appendChild(btn);
            });
            container.appendChild(grid);
        }

        renderMultiStep(container, field, options) {
            const grid = document.createElement('div'); 
            grid.className = 'flex flex-wrap gap-2';
            options.forEach(opt => {
                const active = this.data[field].includes(opt.v);
                const btn = document.createElement('button');
                btn.className = `min-h-[44px] px-4 py-2.5 rounded-xl border font-display font-semibold text-xs transition-all cursor-pointer ${active ? 'bg-primary border-primary text-on-primary shadow-xs' : 'bg-surface-container-low border-surface-variant/40 text-on-surface hover:border-primary/40'}`;
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
            area.className = 'w-full h-36 bg-surface-container-low rounded-xl p-4 font-body-md text-xs md:text-sm text-on-surface border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none outline-none';
            area.placeholder = placeholder; 
            area.value = this.data[field];
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
                row.className = `flex items-start gap-3.5 p-3.5 rounded-xl border transition-all cursor-pointer mb-2.5 ${active ? (cfg.w ? 'border-error bg-error/5' : 'border-primary bg-primary/5') : 'border-surface-variant/30 bg-surface-container-low'}`;
                row.onclick = () => { this.data.config[cfg.k] = !this.data.config[cfg.k]; this.render(); };
                row.innerHTML = `
                    <div class="mt-0.5"><div class="w-5 h-5 rounded-md flex items-center justify-center transition-all ${active ? (cfg.w ? 'bg-error text-white' : 'bg-primary text-white') : 'bg-surface-container-highest'}">${active ? '<span class="material-symbols-outlined text-[14px] font-bold">check</span>' : ''}</div></div>
                    <div class="flex-1"><p class="font-display font-bold text-xs ${cfg.w && active ? 'text-error' : 'text-on-surface'}">${cfg.l}</p><p class="text-[11px] text-on-surface-variant mt-0.5 leading-snug">${cfg.d}</p></div>
                `;
                container.appendChild(row);
            });
        }

        updateLabels() {
            document.getElementById('wizard-step-indicator').innerText = `Paso ${this.currentStep} de ${this.totalSteps}`;
            const titles = [i18n('report.step1_q'), i18n('report.step2_q'), i18n('report.step3_q'), i18n('report.step4_q'), i18n('report.step5_q'), i18n('report.step6_q'), i18n('report.step7_q'), i18n('report.step8_q')];
            document.getElementById('wizard-step-title').innerText = titles[this.currentStep-1];
            const icons = ['help', 'person', 'flash_on', 'schedule', 'mood', 'forum', 'edit_note', 'settings'];
            document.getElementById('wizard-step-icon').innerHTML = `<span class="material-symbols-outlined text-xl">${icons[this.currentStep-1]}</span>`;
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
            const btn = document.getElementById('btn-wizard-next'); 
            btn.disabled = true;
            btn.innerHTML = `<span class="material-symbols-outlined animate-spin text-sm">refresh</span> ${i18n('sending')}`;
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

    let currentReportId = null;

    async function loadStudentReport(id) {
        try {
            currentReportId = id;
            const mainView = document.getElementById('home-view');
            const reportingCard = document.getElementById('reporting-card');
            const chatView = document.getElementById('chat-view');

            if (mainView) mainView.classList.add('hidden');
            if (reportingCard) reportingCard.classList.remove('hidden');
            
            ['wizard-header', 'wizard-content', 'wizard-nav', 'wizard-success'].forEach(cid => {
                const el = document.getElementById(cid);
                if (el) el.classList.add('hidden');
            });
            
            if (chatView) {
                chatView.classList.remove('hidden');
                chatView.classList.add('flex');
            }

            const cm = document.getElementById('chat-messages'); 
            cm.innerHTML = `<div class="flex flex-col items-center justify-center h-48 gap-3"><span class="material-symbols-outlined animate-spin text-3xl text-primary">refresh</span><p class="font-display font-semibold text-xs text-primary">Abriendo espacio seguro...</p></div>`;
            
            const res = await fetchJson(`/alumno/reports/${id}`);
            if (res.error) throw new Error(res.error);
            
            renderStudentChat(res.report, res.messages);
            window.scrollTo({ top: 0, behavior: 'smooth' });

        } catch (error) {
            console.error("[TicketOpenError]", error);
            const cm = document.getElementById('chat-messages');
            if (cm) cm.innerHTML = `<div class="flex flex-col items-center justify-center h-48 p-6 text-center bg-error/5 rounded-2xl border border-dashed border-error/20"><span class="material-symbols-outlined text-2xl text-error mb-2">error_outline</span><p class="font-display font-bold text-xs text-error mb-1">${i18n('ticket_load_error')}</p><button onclick="window.location.reload()" class="mt-3 bg-surface px-4 py-1.5 rounded-lg border text-xs font-semibold">Volver</button></div>`;
        }
    }

    function renderStudentChat(report, messages) {
        const pill = document.getElementById('chat-status-pill');
        pill.innerText = translateStatus(report.status);
        pill.className = "px-2.5 py-0.5 rounded-full text-[10px] font-display font-bold uppercase tracking-wider " + 
            (report.status === 'new' ? 'bg-prisma-sky/40 text-sky-950' : (report.status === 'in_progress' ? 'bg-amber-100 text-amber-900' : 'bg-prisma-mint/30 text-teal-900'));
        const ic = document.getElementById('chat-input-container');
        if (report.status === 'resolved') {
            ic.classList.add('hidden');
            document.getElementById('resolved-note').innerText = "Resolución: " + (report.resolution_summary || "Cerrado.");
            document.getElementById('resolved-note').classList.remove('hidden');
        } else { ic.classList.remove('hidden'); document.getElementById('resolved-note').classList.add('hidden'); }
        
        let h = `<div class="flex gap-3 flex-row-reverse mb-4 group"><div class="w-8 h-8 rounded-lg bg-surface-container-highest flex items-center justify-center text-[11px] font-bold shrink-0">Tú</div><div class="bg-surface-container-low p-3.5 rounded-2xl rounded-tr-none max-w-[85%] text-xs md:text-sm leading-relaxed border border-surface-variant/30 whitespace-pre-wrap">${escapeHtml(report.content)}</div></div>`;
        
        if (messages.length === 0) h += `<div class="p-6 text-center text-on-surface-variant text-xs italic">${i18n('empty_chat')}</div>`;
        messages.forEach(m => {
            const me = m.is_current_user;
            h += `<div class="flex gap-3 ${me ? 'flex-row-reverse' : ''} mb-4 animate-fadeIn"><div class="w-8 h-8 rounded-lg ${me ? 'bg-surface-container-highest' : 'bg-primary text-on-primary'} flex items-center justify-center text-[11px] font-bold shrink-0">${me ? 'Tú' : escapeHtml(m.sender_name.charAt(0))}</div><div class="${me ? 'bg-surface-container-low rounded-tr-none' : 'bg-surface-container-lowest border border-surface-variant/40 rounded-tl-none'} p-3.5 rounded-2xl max-w-[85%] text-xs md:text-sm shadow-xs leading-relaxed"><p class="whitespace-pre-wrap">${escapeHtml(m.message)}</p></div></div>`;
        });
        document.getElementById('chat-messages').innerHTML = h;
        setTimeout(() => { const cm = document.getElementById('chat-messages'); cm.scrollTop = cm.scrollHeight; }, 100);
    }

    async function sendStudentMessage() {
        const i = document.getElementById('reply-message'); 
        const msg = i.value.trim(); 
        if (!msg || !currentReportId) return;
        i.disabled = true;
        try {
            const res = await fetchJson(`/alumno/reports/${currentReportId}/messages`, { method: 'POST', body: { message: msg } });
            if (!res.error) { i.value = ''; loadStudentReport(currentReportId); } else { alert(res.error); }
        } catch (e) { alert('Error'); } finally { i.disabled = false; i.focus(); }
    }

    // Respira Conmigo
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
        document.getElementById('b-halo').className = 'absolute inset-0 rounded-full blur-2xl opacity-40 transition-all ' + (idx === 0 ? 'inhale' : (idx === 2 ? 'exhale' : ''));
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

    // WebAuthn
    const WebAuthnUI = {
        isIOS: () => /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
        isMac: () => /Macintosh|MacIntel|MacPPC|Mac68K/.test(navigator.userAgent),
        isSafari: () => /^((?!chrome|android).)*safari/i.test(navigator.userAgent),
        isChrome: () => /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor),
        
        updatePlatformUI: function() {
            const section = document.getElementById('webauthn-section');
            if (!section) return;
            if (typeof window.PublicKeyCredential !== 'function') { section.classList.add('hidden'); return; }
            section.classList.remove('hidden');
        }
    };

    function base64urlToBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const binary_string = window.atob(base64.length % 4 ? base64 + '===='.substring(base64.length % 4) : base64);
        const bytes = new Uint8Array(binary_string.length);
        for (let i = 0; i < binary_string.length; i++) bytes[i] = binary_string.charCodeAt(i);
        return bytes;
    }
    function bufferToBase64url(buffer) {
        const bytes = new Uint8Array(buffer); let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
        return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    async function registerWebAuthn() {
        if (!window.isSecureContext && window.location.hostname !== 'localhost') { 
            alert('Se requiere conexión segura (HTTPS).'); 
            return; 
        }

        const deviceDefault = WebAuthnUI.isIOS() ? 'iPhone' : (WebAuthnUI.isMac() ? 'MacBook' : 'Mi Dispositivo');
        const deviceName = prompt('Nombre para este dispositivo:', deviceDefault); 
        if (!deviceName) return;

        try {
            const optRes = await fetchJson('/api/webauthn/register/options');
            if (optRes.error) throw new Error(optRes.error);
            
            const options = optRes; 
            options.challenge = base64urlToBuffer(options.challenge);
            options.user.id = base64urlToBuffer(options.user.id);
            if (options.excludeCredentials) options.excludeCredentials.forEach(c => c.id = base64urlToBuffer(c.id));
            
            const credential = await navigator.credentials.create({ publicKey: options });
            
            const verifyRes = await fetchJson('/api/webauthn/register/verify', {
                method: 'POST', body: {
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64url(credential.response.attestationObject),
                    device_name: deviceName
                }
            });

            if (verifyRes.success) {
                alert('¡Dispositivo biométrico registrado con éxito!');
            } else { 
                throw new Error(verifyRes.error); 
            }
        } catch (e) { 
            console.error('WebAuthn Error:', e);
            if (e.name !== 'NotAllowedError') alert('Error: ' + e.message);
        }
    }

    let appWizard = null;
    document.addEventListener('DOMContentLoaded', () => {
        appWizard = new WizardFlow();
        ViewManager.showHome();
        WebAuthnUI.updatePlatformUI();
    });
</script>
<?php $scripts = ob_get_clean(); ?>

<?php 
$bodyClass = "bg-prisma-cloud text-text-primary font-sans min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Gradient Accents -->
<div class="absolute inset-0 z-0 pointer-events-none bg-gradient-to-b from-[#F4F7FD] via-[#F8FAFC] to-[#EDF8FF]"></div>
<div class="absolute -top-[15%] -left-[10%] w-[50%] h-[50%] rounded-full bg-prisma-sky/25 blur-[120px] z-0 pointer-events-none"></div>
<div class="absolute -bottom-[10%] -right-[10%] w-[45%] h-[45%] rounded-full bg-prisma-lavender/20 blur-[130px] z-0 pointer-events-none"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto my-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 gap-3">
        <div class="h-14 flex items-center justify-center">
            <img src="<?= BASE_URL ?>assets/prisma-logo.png" 
                 onerror="this.onerror=null; this.src='https://prisma.emoterralab.com/assets/prisma-logo.png';" 
                 alt="Prisma" 
                 class="h-full w-auto max-h-14 object-contain">
        </div>
        <p class="font-sans text-sm text-gray-500 text-center max-w-xs">
            <?= \App\Core\Lang::t('auth.safe_space') ?>
        </p>
    </div>

    <!-- Login Card -->
    <div class="w-full bg-white rounded-2xl p-6 md:p-8 border border-gray-200 shadow-md relative overflow-hidden">
        <!-- CSS-only Tab System Setup -->
        <input checked class="peer/student hidden" id="tab_student" name="login_type" type="radio" onchange="resetForms()"/>
        <input class="peer/staff hidden" id="tab_staff" name="login_type" type="radio" onchange="resetForms()"/>
        
        <!-- Tab Selectors -->
        <div class="flex relative bg-gray-100 rounded-full p-1 mb-6 border border-gray-200/70">
            <label class="flex-1 text-center py-2.5 rounded-full cursor-pointer transition-all duration-200 font-display text-xs font-bold text-gray-500 peer-checked/student:bg-prisma-charcoal peer-checked/student:text-white peer-checked/student:shadow-sm" for="tab_student">
                <?= \App\Core\Lang::t('auth.student') ?>
            </label>
            <label class="flex-1 text-center py-2.5 rounded-full cursor-pointer transition-all duration-200 font-display text-xs font-bold text-gray-500 peer-checked/staff:bg-prisma-charcoal peer-checked/staff:text-white peer-checked/staff:shadow-sm" for="tab_staff">
                <?= \App\Core\Lang::t('auth.staff') ?>
            </label>
        </div>

        <!-- Student Form -->
        <div class="hidden peer-checked/student:flex flex-col gap-5 animate-[fadeIn_0.25s_ease-out]">
            <!-- Fase 1: Email -->
            <div id="otp-step-1" class="flex flex-col gap-5">
                <div class="flex flex-col gap-1.5">
                    <label class="font-display text-xs font-bold text-gray-700 ml-1" for="alumno-email">
                        <?= \App\Core\Lang::t('auth.institutional_email') ?>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl pointer-events-none">mail</span>
                        <input class="w-full bg-white text-gray-900 font-sans text-sm rounded-xl py-3 pl-11 pr-4 border border-gray-300 focus:border-info focus:ring-4 focus:ring-prisma-sky/30 transition-all outline-none placeholder:text-gray-400 h-11" 
                               id="alumno-email" 
                               placeholder="<?= \App\Core\Lang::t('auth.email_placeholder') ?>" 
                               type="email"
                               autocomplete="email"
                               required/>
                    </div>
                    <div class="flex items-start gap-2 mt-1 px-1">
                        <span class="material-symbols-outlined text-info text-sm mt-0.5" style="font-variation-settings: 'FILL' 1;">info</span>
                        <p class="font-sans text-xs text-gray-500 leading-relaxed"><?= \App\Core\Lang::t('auth.otp_info') ?></p>
                    </div>
                </div>
                <button id="btn-generate-otp" 
                        onclick="generateOTP()" 
                        class="w-full bg-prisma-charcoal text-white font-display text-sm font-bold py-3 px-6 rounded-xl hover:bg-gray-800 transition-all shadow-sm flex items-center justify-center gap-2 group min-h-[44px]" 
                        type="button">
                    <span><?= \App\Core\Lang::t('auth.continue') ?></span>
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>

            <!-- Fase 2: OTP Code -->
            <div id="otp-step-2" class="hidden flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <p class="font-sans text-xs text-gray-600 px-2 text-center">
                        <?= \App\Core\Lang::t('auth.otp_sent_to') ?> <br>
                        <span id="display-email" class="font-bold text-prisma-charcoal"></span>
                    </p>
                    <label class="font-display text-xs font-bold text-gray-700 ml-1" for="alumno-code">
                        <?= \App\Core\Lang::t('auth.otp_label') ?>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl pointer-events-none">password</span>
                        <input class="w-full bg-white text-gray-900 font-sans text-lg font-bold text-center tracking-[0.4em] rounded-xl py-2.5 pl-11 pr-4 border border-gray-300 focus:border-info focus:ring-4 focus:ring-prisma-sky/30 transition-all outline-none placeholder:text-gray-400 h-11" 
                               id="alumno-code" 
                               placeholder="000000" 
                               type="text" 
                               maxlength="6"
                               autocomplete="one-time-code"/>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5 mt-1">
                    <button id="btn-verify-otp" 
                            onclick="verifyOTP()" 
                            class="w-full bg-prisma-charcoal text-white font-display text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition-all shadow-sm flex items-center justify-center gap-2 group min-h-[44px]" 
                            type="button">
                        <span><?= \App\Core\Lang::t('auth.login_btn') ?></span>
                        <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">login</span>
                    </button>
                    <button onclick="resetOTP()" 
                            class="w-full bg-gray-100 text-gray-700 font-display text-xs font-semibold py-2.5 rounded-xl hover:bg-gray-200 transition-all min-h-[40px]" 
                            type="button">
                        <?= \App\Core\Lang::t('auth.restart') ?>
                    </button>
                </div>
            </div>
            <p id="alumno-error" class="text-danger bg-danger-bg border border-danger/20 text-xs text-center py-2.5 px-3 rounded-xl hidden"></p>
        </div>

        <!-- Staff Form -->
        <form id="form-staff" onsubmit="loginStaff(event)" class="hidden peer-checked/staff:flex flex-col gap-5 animate-[fadeIn_0.25s_ease-out]">
            <div class="flex flex-col gap-1.5">
                <label class="font-display text-xs font-bold text-gray-700 ml-1" for="staff-email">
                    <?= \App\Core\Lang::t('auth.staff_email') ?>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl pointer-events-none">mail</span>
                    <input class="w-full bg-white text-gray-900 font-sans text-sm rounded-xl py-3 pl-11 pr-4 border border-gray-300 focus:border-info focus:ring-4 focus:ring-prisma-sky/30 transition-all outline-none placeholder:text-gray-400 h-11" 
                           id="staff-email" 
                           placeholder="<?= \App\Core\Lang::t('auth.staff_email_placeholder') ?>" 
                           type="email"
                           autocomplete="username" 
                           required/>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="font-display text-xs font-bold text-gray-700 ml-1" for="staff-password">
                    <?= \App\Core\Lang::t('auth.password') ?>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl pointer-events-none">lock</span>
                    <input class="w-full bg-white text-gray-900 font-sans text-sm rounded-xl py-3 pl-11 pr-4 border border-gray-300 focus:border-info focus:ring-4 focus:ring-prisma-sky/30 transition-all outline-none placeholder:text-gray-400 h-11" 
                           id="staff-password" 
                           placeholder="••••••••" 
                           type="password"
                           autocomplete="current-password" 
                           required/>
                </div>
            </div>
            <div class="flex justify-end px-1 -mt-2">
                <a class="font-sans text-xs text-info hover:text-gray-900 hover:underline transition-colors" href="/password/forgot">
                    <?= \App\Core\Lang::t('auth.forgot_password') ?>
                </a>
            </div>
            <button id="btn-staff-login" 
                    class="w-full bg-prisma-charcoal text-white font-display text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition-all shadow-sm flex items-center justify-center gap-2 group min-h-[44px]" 
                    type="submit">
                <span><?= \App\Core\Lang::t('auth.login_title') ?></span>
                <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">login</span>
            </button>
            <p id="staff-error" class="text-danger bg-danger-bg border border-danger/20 text-xs text-center py-2.5 px-3 rounded-xl hidden"></p>
        </form>
    </div>
</main>

<!-- Footer Component -->
<footer class="w-full py-6 mt-auto flex flex-col items-center gap-2 relative z-10 px-4">
    <div class="mb-2">
        <?= \App\Core\Lang::renderSelector() ?>
    </div>
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-1 text-xs text-gray-500 font-sans">
        <a class="hover:text-gray-900 transition-colors" href="#"><?= \App\Core\Lang::t('footer.privacy') ?></a>
        <span>·</span>
        <a class="hover:text-gray-900 transition-colors" href="#"><?= \App\Core\Lang::t('footer.support') ?></a>
        <span>·</span>
        <a class="hover:text-gray-900 transition-colors" href="#"><?= \App\Core\Lang::t('footer.terms') ?></a>
    </div>
    <p class="text-gray-400 text-xs font-sans text-center mt-1">
        <?= \App\Core\Lang::t('footer.powered_by') ?>
    </p>
</footer>

<?php ob_start(); ?>
<script>
    function resetForms() {
        const aErr = document.getElementById('alumno-error');
        const sErr = document.getElementById('staff-error');
        if (aErr) aErr.classList.add('hidden');
        if (sErr) sErr.classList.add('hidden');
    }

    async function generateOTP(forceOtp = false) {
        const email = document.getElementById('alumno-email').value;
        const btn = document.getElementById('btn-generate-otp');
        const errorEl = document.getElementById('alumno-error');
        
        if (!email) return;

        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> <?= \App\Core\Lang::t('auth.generating') ?>';
            errorEl.classList.add('hidden');

            const res = await fetchJson('/login/otp/generate', {
                method: 'POST',
                body: { email, force_otp: forceOtp }
            });

            if (res.ok) {
                if (res.webauthn && !forceOtp) {
                    window.location.href = '/auth/2fa/webauthn';
                    return;
                }

                document.getElementById('display-email').innerText = email;
                document.getElementById('otp-step-1').classList.add('hidden');
                document.getElementById('otp-step-2').classList.remove('hidden');
                document.getElementById('otp-step-2').classList.add('flex');
                
                if (res.dev_code) {
                    console.log("DEV OTP CODE:", res.dev_code);
                    document.getElementById('alumno-code').value = res.dev_code;
                }
            } else {
                errorEl.innerText = res.error || '<?= \App\Core\Lang::t('auth.error_otp_find') ?>';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            errorEl.innerText = '<?= \App\Core\Lang::t('auth.error_connection') ?>';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span><?= \App\Core\Lang::t('auth.continue') ?></span> <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>';
        }
    }

    async function verifyOTP() {
        const email = document.getElementById('alumno-email').value;
        const code = document.getElementById('alumno-code').value;
        const btn = document.getElementById('btn-verify-otp');
        const errorEl = document.getElementById('alumno-error');

        if (!code || code.length !== 6) return;

        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> <?= \App\Core\Lang::t('auth.verifying') ?>';
            errorEl.classList.add('hidden');

            const res = await fetchJson('/login/otp/verify', {
                method: 'POST',
                body: { email, code }
            });

            if (res.ok && res.redirect) {
                window.location.href = res.redirect;
            } else {
                errorEl.innerText = res.error || '<?= \App\Core\Lang::t('auth.error_otp_invalid') ?>';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            errorEl.innerText = '<?= \App\Core\Lang::t('auth.error_connection') ?>';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span><?= \App\Core\Lang::t('auth.login_btn') ?></span> <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">login</span>';
        }
    }

    function resetOTP() {
        document.getElementById('otp-step-1').classList.remove('hidden');
        document.getElementById('otp-step-2').classList.add('hidden');
        document.getElementById('otp-step-2').classList.remove('flex');
        document.getElementById('alumno-code').value = '';
        document.getElementById('alumno-error').classList.add('hidden');
    }

    async function loginStaff(e) {
        e.preventDefault();
        const email = document.getElementById('staff-email').value;
        const password = document.getElementById('staff-password').value;
        const btn = document.getElementById('btn-staff-login');
        const errorEl = document.getElementById('staff-error');

        try {
            btn.disabled = true;
            errorEl.classList.add('hidden');

            const res = await fetchJson('/login/staff', {
                method: 'POST',
                body: { email, password }
            });

            if (res.ok && res.redirect) {
                window.location.href = res.redirect;
            } else {
                errorEl.innerText = res.error || '<?= \App\Core\Lang::t('auth.error_staff_invalid') ?>';
                errorEl.classList.remove('hidden');
            }
        } catch (err) {
            errorEl.innerText = '<?= \App\Core\Lang::t('auth.error_connection') ?>';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        <?php if (isset($force_otp_email)): ?>
            document.getElementById('alumno-email').value = "<?= htmlspecialchars($force_otp_email) ?>";
            generateOTP(true);
        <?php endif; ?>
    });
</script>
<?php $scripts = ob_get_clean(); ?>

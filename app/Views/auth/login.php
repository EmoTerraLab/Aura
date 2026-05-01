<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>
<!-- Ambient Background Element (Sanctuary Vibe) -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-secondary-container/30 via-surface to-background"></div>
<div class="absolute -top-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-primary-fixed/20 blur-[120px] z-0 pointer-events-none"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 md:mb-10 gap-4">
        <div class="w-24 h-24 flex items-center justify-center drop-shadow-sm">
            <img src="<?= BASE_URL ?>icono-sinfondo.png" alt="Aura Logo" class="w-full h-full object-contain">
        </div>
        <h1 class="font-h1 text-h1 text-primary-container tracking-tight">Aura</h1>
        <p class="font-body-md text-body-md text-on-surface-variant text-center"><?= \App\Core\Lang::t('auth.safe_space') ?></p>
    </div>

    <!-- Login Card -->
    <div class="w-full bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden">
        <!-- CSS-only Tab System Setup -->
        <input checked="" class="peer/student hidden" id="tab_student" name="login_type" type="radio" onchange="resetForms()"/>
        <input class="peer/staff hidden" id="tab_staff" name="login_type" type="radio" onchange="resetForms()"/>
        
        <!-- Tab Selectors -->
        <div class="flex relative bg-surface-container rounded-full p-1.5 mb-8">
            <label class="flex-1 text-center py-3 rounded-full cursor-pointer transition-all duration-300 font-label-caps text-label-caps text-on-surface-variant peer-checked/student:bg-primary peer-checked/student:text-on-primary peer-checked/student:shadow-sm" for="tab_student">
                <?= \App\Core\Lang::t('auth.student') ?>
            </label>
            <label class="flex-1 text-center py-3 rounded-full cursor-pointer transition-all duration-300 font-label-caps text-label-caps text-on-surface-variant peer-checked/staff:bg-primary peer-checked/staff:text-on-primary peer-checked/staff:shadow-sm" for="tab_staff">
                <?= \App\Core\Lang::t('auth.staff') ?>
            </label>
        </div>

        <!-- Student Form -->
        <div class="hidden peer-checked/student:flex flex-col gap-6 animate-[fadeIn_0.3s_ease-out]">
            <!-- Fase 1: Email -->
            <div id="otp-step-1" class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="alumno-email"><?= \App\Core\Lang::t('auth.institutional_email') ?></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">mail</span>
                        <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="alumno-email" placeholder="<?= \App\Core\Lang::t('auth.email_placeholder') ?>" type="email"/>
                    </div>
                    <div class="flex items-start gap-2 mt-2 px-4">
                        <span class="material-symbols-outlined text-surface-tint text-sm mt-0.5" style="font-variation-settings: 'FILL' 1;">info</span>
                        <p class="font-body-md text-[14px] text-surface-tint leading-snug"><?= \App\Core\Lang::t('auth.otp_info') ?></p>
                    </div>
                </div>
                <button id="btn-generate-otp" onclick="generateOTP()" class="mt-4 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="button">
                    <?= \App\Core\Lang::t('auth.continue') ?>
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>

            <!-- Fase 2: OTP Code -->
            <div id="otp-step-2" class="hidden flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <p class="font-body-md text-[14px] text-on-surface-variant px-4 text-center"><?= \App\Core\Lang::t('auth.otp_sent_to') ?> <br><span id="display-email" class="font-bold text-primary"></span></p>
                    <label class="font-body-md text-body-md text-on-surface-variant ml-4 mt-2" for="alumno-code"><?= \App\Core\Lang::t('auth.otp_label') ?></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">password</span>
                        <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg text-center tracking-[0.5em] rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="alumno-code" placeholder="000000" type="text" maxlength="6"/>
                    </div>
                </div>
                <div class="flex flex-col gap-3 mt-2">
                    <button id="btn-verify-otp" onclick="verifyOTP()" class="w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="button">
                        <?= \App\Core\Lang::t('auth.login_btn') ?>
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>
                    </button>
                    <button onclick="resetOTP()" class="w-full bg-surface-container text-on-surface-variant font-body-md text-body-md py-3 rounded-full hover:bg-surface-variant transition-all" type="button">
                        <?= \App\Core\Lang::t('auth.restart') ?>
                    </button>
                </div>
            </div>
            <p id="alumno-error" class="text-error text-sm text-center hidden px-4"></p>
        </div>

        <!-- Staff Form -->
        <form id="form-staff" onsubmit="loginStaff(event)" class="hidden peer-checked/staff:flex flex-col gap-6 animate-[fadeIn_0.3s_ease-out]">
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="staff-email"><?= \App\Core\Lang::t('auth.staff_email') ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">mail</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="staff-email" placeholder="<?= \App\Core\Lang::t('auth.staff_email_placeholder') ?>" type="email" required/>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="staff-password"><?= \App\Core\Lang::t('auth.password') ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="staff-password" placeholder="••••••••" type="password" required/>
                </div>
            </div>
            <div class="flex justify-end px-4 mt-[-8px]">
                <a class="font-body-md text-[14px] text-surface-tint hover:underline decoration-surface-tint/50 underline-offset-4" href="/password/forgot"><?= \App\Core\Lang::t('auth.forgot_password') ?></a>
            </div>
            <button id="btn-staff-login" class="mt-2 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit">
                <?= \App\Core\Lang::t('auth.login_title') ?>
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>
            </button>
            <p id="staff-error" class="text-error text-sm text-center hidden px-4"></p>
        </form>
    </div>
</main>

<!-- Footer Component -->
<footer class="bg-transparent w-full py-8 flat no shadows mt-auto flex flex-col items-center gap-2 relative z-10 px-4">
    <div class="mb-4">
        <?= \App\Core\Lang::renderSelector() ?>
    </div>
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mb-2">
        <a class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center hover:text-teal-600 transition-colors" href="#"><?= \App\Core\Lang::t('footer.privacy') ?></a>
        <a class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center hover:text-teal-600 transition-colors" href="#"><?= \App\Core\Lang::t('footer.support') ?></a>
        <a class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center hover:text-teal-600 transition-colors" href="#"><?= \App\Core\Lang::t('footer.terms') ?></a>
    </div>
    <p class="text-slate-400 dark:text-slate-600 text-[10px] font-manrope text-center"><?= \App\Core\Lang::t('footer.powered_by') ?></p>
</footer>

<?php ob_start(); ?>
<script>
    function resetForms() {
        document.getElementById('alumno-error').classList.add('hidden');
        document.getElementById('staff-error').classList.add('hidden');
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
                    await authWebAuthn();
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
            btn.innerHTML = '<?= \App\Core\Lang::t('auth.continue') ?> <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>';
        }
    }

    // WebAuthn Helpers
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

    async function authWebAuthn() {
        const errorEl = document.getElementById('alumno-error');
        const btn = document.getElementById('btn-generate-otp');
        
        try {
            console.log('Solicitando opciones de autenticación WebAuthn...');
            const optRes = await fetchJson('/auth/2fa/webauthn/options');
            if (optRes.error) throw new Error(optRes.error);
            
            const options = optRes;
            options.challenge = base64urlToBuffer(options.challenge);
            
            if (options.allowCredentials) {
                for (let i = 0; i < options.allowCredentials.length; i++) {
                    options.allowCredentials[i].id = base64urlToBuffer(options.allowCredentials[i].id);
                }
            }

            console.log('Invocando autenticador...');
            const assertion = await navigator.credentials.get({ publicKey: options });
            
            console.log('Verificando respuesta...');
            const verifyRes = await fetchJson('/auth/2fa/webauthn/verify', {
                method: 'POST',
                body: {
                    credentialId: bufferToBase64url(assertion.rawId),
                    clientDataJSON: bufferToBase64url(assertion.response.clientDataJSON),
                    authenticatorData: bufferToBase64url(assertion.response.authenticatorData),
                    signature: bufferToBase64url(assertion.response.signature)
                }
            });

            if (verifyRes.success) {
                window.location.href = verifyRes.redirect;
            } else {
                throw new Error(verifyRes.error || 'Autenticación fallida');
            }
        } catch (e) {
            console.error('WebAuthn Auth Error:', e);
            let userMsg = 'Error en verificación biométrica.';
            if (e.name === 'NotAllowedError') userMsg = 'Operación cancelada.';
            else userMsg = e.message;

            errorEl.innerText = userMsg + ' Reintentando con código...';
            errorEl.classList.remove('hidden');
            
            // Fallback manual al OTP de correo
            setTimeout(() => {
                generateOTP(true); 
            }, 2000);
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
            btn.innerHTML = '<?= \App\Core\Lang::t('auth.login_btn') ?> <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>';
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

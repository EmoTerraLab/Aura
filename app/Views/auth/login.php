<?php 
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-hidden"; 
?>
<!-- Ambient Background Element (Sanctuary Vibe) -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-secondary-container/30 via-surface to-background"></div>
<div class="absolute -top-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-primary-fixed/20 blur-[120px] z-0 pointer-events-none"></div>

<!-- Main Canvas Content -->
<main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-10 gap-4">
        <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center shadow-lg shadow-primary-container/10">
            <span class="material-symbols-outlined text-on-primary-container text-4xl" style="font-variation-settings: 'FILL' 1;">spa</span>
        </div>
        <h1 class="font-h1 text-h1 text-primary-container tracking-tight">Aura</h1>
        <p class="font-body-md text-body-md text-on-surface-variant text-center">Tu espacio seguro en el colegio</p>
    </div>

    <!-- Login Card -->
    <div class="w-full bg-surface-container-lowest rounded-xl p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] relative overflow-hidden">
        <!-- CSS-only Tab System Setup -->
        <input checked="" class="peer/student hidden" id="tab_student" name="login_type" type="radio" onchange="resetForms()"/>
        <input class="peer/staff hidden" id="tab_staff" name="login_type" type="radio" onchange="resetForms()"/>
        
        <!-- Tab Selectors -->
        <div class="flex relative bg-surface-container rounded-full p-1.5 mb-8">
            <label class="flex-1 text-center py-3 rounded-full cursor-pointer transition-all duration-300 font-label-caps text-label-caps text-on-surface-variant peer-checked/student:bg-surface-container-lowest peer-checked/student:text-primary peer-checked/student:shadow-sm" for="tab_student">
                Estudiante
            </label>
            <label class="flex-1 text-center py-3 rounded-full cursor-pointer transition-all duration-300 font-label-caps text-label-caps text-on-surface-variant peer-checked/staff:bg-surface-container-lowest peer-checked/staff:text-primary peer-checked/staff:shadow-sm" for="tab_staff">
                Personal
            </label>
        </div>

        <!-- Student Form -->
        <div class="hidden peer-checked/student:flex flex-col gap-6 animate-[fadeIn_0.3s_ease-out]">
            <!-- Fase 1: Email -->
            <div id="otp-step-1" class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="alumno-email">Correo Institucional</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">mail</span>
                        <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="alumno-email" placeholder="estudiante@colegio.edu" type="email"/>
                    </div>
                    <div class="flex items-start gap-2 mt-2 px-4">
                        <span class="material-symbols-outlined text-surface-tint text-sm mt-0.5" style="font-variation-settings: 'FILL' 1;">info</span>
                        <p class="font-body-md text-[14px] text-surface-tint leading-snug">Recibirás un código OTP en tu correo para acceder de forma segura.</p>
                    </div>
                </div>
                <button id="btn-generate-otp" onclick="generateOTP()" class="mt-4 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="button">
                    Continuar
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>

            <!-- Fase 2: OTP Code -->
            <div id="otp-step-2" class="hidden flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <p class="font-body-md text-[14px] text-on-surface-variant px-4 text-center">Hemos generado un código para <br><span id="display-email" class="font-bold text-primary"></span></p>
                    <label class="font-body-md text-body-md text-on-surface-variant ml-4 mt-2" for="alumno-code">Código de 6 dígitos</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">password</span>
                        <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg text-center tracking-[0.5em] rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="alumno-code" placeholder="000000" type="text" maxlength="6"/>
                    </div>
                </div>
                <div class="flex flex-col gap-3 mt-2">
                    <button id="btn-verify-otp" onclick="verifyOTP()" class="w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="button">
                        Entrar
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>
                    </button>
                    <button onclick="resetOTP()" class="w-full bg-surface-container text-on-surface-variant font-body-md text-body-md py-3 rounded-full hover:bg-surface-variant transition-all" type="button">
                        Volver a empezar
                    </button>
                </div>
            </div>
            <p id="alumno-error" class="text-error text-sm text-center hidden px-4"></p>
        </div>

        <!-- Staff Form -->
        <form id="form-staff" onsubmit="loginStaff(event)" class="hidden peer-checked/staff:flex flex-col gap-6 animate-[fadeIn_0.3s_ease-out]">
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="staff-email">Correo Electrónico</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">mail</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="staff-email" placeholder="docente@colegio.edu" type="email" required/>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="staff-password">Contraseña</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none placeholder:text-outline-variant" id="staff-password" placeholder="••••••••" type="password" required/>
                </div>
            </div>
            <div class="flex justify-end px-4 mt-[-8px]">
                <a class="font-body-md text-[14px] text-surface-tint hover:underline decoration-surface-tint/50 underline-offset-4" href="#">¿Olvidaste tu contraseña?</a>
            </div>
            <button id="btn-staff-login" class="mt-2 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-[0_8px_24px_-8px_rgba(0,79,86,0.3)] hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit">
                Iniciar Sesión
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>
            </button>
            <p id="staff-error" class="text-error text-sm text-center hidden px-4"></p>
        </form>
    </div>
</main>

<!-- Footer Component -->
<footer class="bg-transparent w-full py-8 flat no shadows w-full mt-auto flex flex-col items-center gap-2 relative z-10">
    <div class="flex gap-4 mb-2">
        <a class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center hover:text-teal-600 transition-colors" href="#">Privacidad</a>
        <a class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center hover:text-teal-600 transition-colors" href="#">Soporte</a>
        <a class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center hover:text-teal-600 transition-colors" href="#">Términos</a>
    </div>
    <p class="text-slate-400 dark:text-slate-600 text-xs font-manrope text-center">Aura powered by EmoTerraLab (emoterralab.com)</p>
</footer>

<?php ob_start(); ?>
<script>
    function resetForms() {
        document.getElementById('alumno-error').classList.add('hidden');
        document.getElementById('staff-error').classList.add('hidden');
    }

    async function generateOTP() {
        const email = document.getElementById('alumno-email').value;
        const btn = document.getElementById('btn-generate-otp');
        const errorEl = document.getElementById('alumno-error');
        
        if (!email) return;

        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> Generando...';
            errorEl.classList.add('hidden');

            const res = await fetchJson('/login/otp/generate', {
                method: 'POST',
                body: { email }
            });

            if (res.ok) {
                document.getElementById('display-email').innerText = email;
                document.getElementById('otp-step-1').classList.add('hidden');
                document.getElementById('otp-step-2').classList.remove('hidden');
                document.getElementById('otp-step-2').classList.add('flex');
                
                if (res.dev_code) {
                    console.log("DEV OTP CODE:", res.dev_code);
                    document.getElementById('alumno-code').value = res.dev_code;
                }
            } else {
                errorEl.innerText = res.error || 'No se encontró un alumno con ese correo.';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            errorEl.innerText = 'Error de conexión (el servidor no responde).';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Continuar <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>';
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
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> Verificando...';
            errorEl.classList.add('hidden');

            const res = await fetchJson('/login/otp/verify', {
                method: 'POST',
                body: { email, code }
            });

            if (res.ok && res.redirect) {
                window.location.href = res.redirect;
            } else {
                errorEl.innerText = res.error || 'Código inválido o expirado.';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            errorEl.innerText = 'Error de conexión (el servidor no responde).';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Entrar <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>';
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
                errorEl.innerText = res.error || 'Las credenciales proporcionadas no coinciden con nuestros registros.';
                errorEl.classList.remove('hidden');
            }
        } catch (err) {
            errorEl.innerText = 'Error de conexión (el servidor no responde).';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    }
</script>
<?php $scripts = ob_get_clean(); ?>
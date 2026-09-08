<?php
// Vista de verificación de WebAuthn (Biometría) rediseñada para Prisma
$bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-x-hidden overflow-y-auto"; 
?>

<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-prisma-sky/20 via-surface to-background"></div>

<main class="flex-1 flex flex-col items-center justify-center p-4 md:p-6 relative z-10 w-full max-w-md mx-auto">
    <!-- Logo Area -->
    <div class="flex flex-col items-center justify-center mb-6 md:mb-8 gap-3">
        <a href="/login" class="inline-block transition-transform hover:scale-[1.02] active:scale-[0.98]">
            <img src="<?= BASE_URL ?>assets/prisma-logo.png" alt="Prisma" class="h-10 md:h-12 w-auto object-contain">
        </a>
    </div>

    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 text-center relative overflow-hidden">
        
        <!-- Icono Dinámico -->
        <div class="relative inline-flex mb-6">
            <div id="auth-icon-bg" class="w-20 h-20 rounded-2xl bg-primary/5 text-primary flex items-center justify-center transition-all duration-300 border border-surface-variant/40">
                <span id="auth-icon" class="material-symbols-outlined text-4xl">fingerprint</span>
            </div>
            <div id="auth-spinner" class="absolute inset-0 border-3 border-primary/20 border-t-primary rounded-2xl animate-spin hidden"></div>
        </div>

        <h1 id="auth-title" class="text-xl md:text-2xl font-display font-bold text-on-surface mb-2 tracking-tight">Acceso Biométrico</h1>
        <p id="auth-desc" class="text-on-surface-variant text-xs md:text-sm leading-relaxed mb-6">Usa tu dispositivo para confirmar tu identidad con Prisma.</p>

        <!-- Status / Error Box -->
        <div id="auth-status-box" class="hidden mb-6 p-3.5 rounded-xl text-xs font-semibold">
            <!-- Mensajes dinámicos -->
        </div>

        <div class="flex flex-col gap-3">
            <!-- Botón Principal -->
            <button id="auth-btn" onclick="authenticateWithWebAuthn()" class="w-full h-12 bg-primary text-on-primary font-display font-bold text-sm rounded-xl shadow-sm hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span id="btn-icon" class="material-symbols-outlined text-xl">touch_app</span>
                <span id="btn-text">Iniciar Verificación</span>
            </button>

            <!-- Botón Reintento (Hidden initially) -->
            <button id="retry-btn" onclick="authenticateWithWebAuthn()" class="hidden w-full h-12 bg-surface-container-high text-on-surface font-display font-bold text-sm rounded-xl hover:bg-surface-container-highest transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-xl">refresh</span>
                Intentar de nuevo
            </button>

            <div class="relative my-2">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-surface-variant/40"></div></div>
                <div class="relative flex justify-center text-[10px] uppercase tracking-widest font-bold text-on-surface-variant/60 bg-surface-container-lowest px-3">o también</div>
            </div>

            <!-- Botón Fallback OTP -->
            <button onclick="useOtpFallback()" class="w-full h-11 bg-surface-container-low text-primary font-display font-bold text-xs rounded-xl hover:bg-surface-container-high transition-all flex items-center justify-center gap-2 border border-surface-variant/30 cursor-pointer">
                <span class="material-symbols-outlined text-base"><?= $isStudent ? 'mail' : 'lock' ?></span>
                <?= $isStudent ? 'Recibir código por email' : 'Usar código de verificación (TOTP)' ?>
            </button>
        </div>
    </div>

    <!-- Tips de Plataforma -->
    <p id="platform-tip" class="mt-6 text-xs text-on-surface-variant/70 font-medium text-center max-w-xs leading-relaxed">
        <!-- Tips dinámicos según el dispositivo -->
    </p>
</main>

<script>
    const WebAuthnAuth = {
        isIOS: () => /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
        isMac: () => /Macintosh|MacIntel|MacPPC|Mac68K/.test(navigator.userAgent),
        isChrome: () => /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor),

        init: function() {
            const btnText = document.getElementById('btn-text');
            const btnIcon = document.getElementById('btn-icon');
            const authIcon = document.getElementById('auth-icon');
            const tip = document.getElementById('platform-tip');
            const title = document.getElementById('auth-title');

            if (this.isIOS() || this.isMac()) {
                title.innerText = 'Face ID / Touch ID';
                btnText.innerText = 'Acceder con Apple';
                btnIcon.innerText = 'face';
                authIcon.innerText = 'face';
                tip.innerText = 'Coloca tu huella en el sensor o mira la pantalla para identificarte.';
            } else if (this.isChrome()) {
                btnText.innerText = 'Usar huella o llave';
                btnIcon.innerText = 'fingerprint';
                authIcon.innerText = 'fingerprint';
                tip.innerText = 'Conecta tu llave de seguridad USB o usa el lector de huellas de tu dispositivo.';
            } else {
                btnText.innerText = 'Verificar Identidad';
                btnIcon.innerText = 'security';
                authIcon.innerText = 'security';
                tip.innerText = 'Usa tu dispositivo de seguridad vinculado para continuar.';
            }
        },

        setStatus: function(msg, type = 'info') {
            const box = document.getElementById('auth-status-box');
            const btn = document.getElementById('auth-btn');
            const spinner = document.getElementById('auth-spinner');
            const retry = document.getElementById('retry-btn');

            if (!msg) { 
                box.classList.add('hidden'); 
                spinner.classList.add('hidden');
                btn.classList.remove('hidden');
                return; 
            }

            box.classList.remove('hidden', 'bg-blue-500/10', 'text-blue-700', 'bg-prisma-mint/20', 'text-teal-900', 'bg-error/10', 'text-error');
            box.innerText = msg;
            
            if (type === 'info') {
                box.classList.add('bg-blue-500/10', 'text-blue-700');
                spinner.classList.remove('hidden');
                btn.classList.add('hidden');
                retry.classList.add('hidden');
            } else if (type === 'success') {
                box.classList.add('bg-prisma-mint/20', 'text-teal-900');
                spinner.classList.add('hidden');
            } else if (type === 'error') {
                box.classList.add('bg-error/10', 'text-error');
                spinner.classList.add('hidden');
                btn.classList.add('hidden');
                retry.classList.remove('hidden');
            }
        }
    };

    function base64url_to_uint8array(base64url) {
        const padding = '='.repeat((4 - base64url.length % 4) % 4);
        const base64 = (base64url + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }

    function uint8array_to_base64url(array) {
        const base64 = btoa(String.fromCharCode(...new Uint8Array(array)));
        return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    async function authenticateWithWebAuthn() {
        WebAuthnAuth.setStatus('Iniciando sensor...', 'info');

        try {
            const optRes = await fetchJson('/auth/2fa/webauthn/options');
            if (optRes.error) throw new Error(optRes.error);

            const options = {
                publicKey: {
                    ...optRes,
                    challenge: base64url_to_uint8array(optRes.challenge),
                    allowCredentials: optRes.allowCredentials.map(c => ({
                        ...c,
                        id: base64url_to_uint8array(c.id)
                    }))
                }
            };

            WebAuthnAuth.setStatus('Confirma en tu dispositivo...', 'info');

            const credential = await navigator.credentials.get(options);

            WebAuthnAuth.setStatus('Verificando identidad...', 'info');

            const authRes = await fetchJson('/auth/2fa/webauthn/verify', {
                method: 'POST',
                body: {
                    id: credential.id,
                    clientDataJSON: uint8array_to_base64url(credential.response.clientDataJSON),
                    authenticatorData: uint8array_to_base64url(credential.response.authenticatorData),
                    signature: uint8array_to_base64url(credential.response.signature),
                    userHandle: credential.response.userHandle ? uint8array_to_base64url(credential.response.userHandle) : null
                }
            });

            if (authRes.success) {
                WebAuthnAuth.setStatus('¡Bienvenido! Entrando...', 'success');
                window.location.href = authRes.redirect || '/dashboard';
            } else {
                throw new Error(authRes.error || 'Error de verificación');
            }

        } catch (e) {
            console.error('WebAuthn Auth Error:', e);
            let msg = 'Error al verificar la identidad.';
            
            if (e.name === 'NotAllowedError') msg = 'Verificación cancelada. Toca el botón para intentarlo de nuevo.';
            else if (e.name === 'NotSupportedError') {
                WebAuthnAuth.setStatus('Biometría no soportada. Redirigiendo...', 'error');
                setTimeout(useOtpFallback, 1500);
                return;
            } else if (e.name === 'SecurityError') msg = 'Error de seguridad. Contacta con el administrador.';
            else msg = e.message;
            
            WebAuthnAuth.setStatus(msg, 'error');
        }
    }

    async function useOtpFallback() {
        window.location.href = '/auth/2fa/webauthn/fallback';
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof navigator.credentials === 'undefined') {
            WebAuthnAuth.setStatus('Tu navegador no soporta biometría. Redirigiendo...', 'error');
            setTimeout(useOtpFallback, 2000);
        } else {
            WebAuthnAuth.init();
        }
    });
</script>

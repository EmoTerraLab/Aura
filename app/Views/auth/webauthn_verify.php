<?php
// Vista de verificación de WebAuthn (Biometría) rediseñada para Aura 2026
?>

<div class="flex flex-col items-center justify-center min-h-[80vh] px-4 font-manrope">
    <div class="w-full max-w-md bg-surface-container-lowest rounded-[2rem] p-8 md:p-10 shadow-[0_32px_80px_-16px_rgba(6,105,114,0.12)] border border-surface-variant/30 text-center animate-fadeIn">
        
        <!-- Icono Dinámico -->
        <div class="relative inline-flex mb-8">
            <div id="auth-icon-bg" class="w-24 h-24 rounded-3xl bg-primary/10 text-primary flex items-center justify-center transition-all duration-500">
                <span id="auth-icon" class="material-symbols-outlined text-5xl">fingerprint</span>
            </div>
            <div id="auth-spinner" class="absolute inset-0 border-4 border-primary/20 border-t-primary rounded-3xl animate-spin hidden"></div>
        </div>

        <h1 id="auth-title" class="text-2xl font-black text-on-surface mb-3 tracking-tight">Acceso Biométrico</h1>
        <p id="auth-desc" class="text-on-surface-variant text-sm leading-relaxed mb-10">Usa tu dispositivo para confirmar que eres tú.</p>

        <!-- Status / Error Box -->
        <div id="auth-status-box" class="hidden mb-8 p-4 rounded-2xl text-sm font-medium animate-pulse">
            <!-- Mensajes dinámicos -->
        </div>

        <div class="flex flex-col gap-4">
            <!-- Botón Principal -->
            <button id="auth-btn" onclick="authenticateWithWebAuthn()" class="group relative w-full bg-gradient-to-br from-primary to-purple-600 text-on-primary font-bold py-5 rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all overflow-hidden">
                <div class="relative z-10 flex items-center justify-center gap-3">
                    <span id="btn-icon" class="material-symbols-outlined text-2xl">touch_app</span>
                    <span id="btn-text">Iniciar Verificación</span>
                </div>
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>

            <!-- Botón Reintento (Hidden initially) -->
            <button id="retry-btn" onclick="authenticateWithWebAuthn()" class="hidden w-full bg-surface-container-high text-on-surface font-bold py-5 rounded-2xl hover:bg-surface-container-highest transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">refresh</span>
                Intentar de nuevo
            </button>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-surface-variant/50"></div></div>
                <div class="relative flex justify-center text-[10px] uppercase tracking-widest font-bold text-outline bg-surface-container-lowest px-4 italic">o también</div>
            </div>

            <!-- Botón Fallback OTP -->
            <button onclick="useOtpFallback()" class="w-full bg-transparent text-primary font-bold py-4 rounded-2xl hover:bg-primary/5 transition-all flex items-center justify-center gap-3">
                <span class="material-symbols-outlined"><?= $isStudent ? 'mail' : 'lock' ?></span>
                <?= $isStudent ? 'Recibir código por email' : 'Usar código de verificación (TOTP)' ?>
            </button>
        </div>
    </div>

    <!-- Tips de Plataforma -->
    <p id="platform-tip" class="mt-8 text-xs text-on-surface-variant/60 font-medium animate-fadeIn text-center max-w-xs leading-relaxed">
        <!-- Tips dinámicos según el dispositivo -->
    </p>
</div>

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
                tip.innerText = 'Coloca tu dedo en el sensor o mira la pantalla para identificarte.';
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

            box.classList.remove('hidden', 'bg-blue-50', 'text-blue-700', 'bg-green-50', 'text-green-700', 'bg-error/10', 'text-error');
            box.innerText = msg;
            
            if (type === 'info') {
                box.classList.add('bg-blue-50', 'text-blue-700');
                spinner.classList.remove('hidden');
                btn.classList.add('hidden');
                retry.classList.add('hidden');
            } else if (type === 'success') {
                box.classList.add('bg-green-50', 'text-green-700');
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

<?php $bodyClass = "bg-surface text-on-surface font-body-md min-h-screen flex flex-col relative overflow-hidden"; ?>
<!-- Ambient Background Element -->
<div class="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-secondary-container/30 via-surface to-background"></div>

<main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-md mx-auto">
    <div class="flex flex-col items-center justify-center mb-10 gap-4">
        <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center shadow-lg shadow-primary-container/10">
            <span class="material-symbols-outlined text-on-primary-container text-4xl" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
        </div>
        <h1 class="font-h1 text-h1 text-primary-container tracking-tight">Verificación Biométrica</h1>
        <p class="font-body-md text-body-md text-on-surface-variant text-center">Usa Face ID, huella dactilar o el llavero de tu dispositivo para continuar.</p>
    </div>

    <div class="w-full bg-surface-container-lowest rounded-xl p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] text-center">
        <div id="webauthn-status" class="mb-6">
            <div class="flex items-center justify-center gap-3 text-primary">
                <span class="material-symbols-outlined animate-spin">refresh</span>
                <span class="font-bold">Iniciando verificación...</span>
            </div>
        </div>

        <p id="webauthn-error" class="text-error text-sm font-bold bg-error/10 p-4 rounded-lg mb-6 hidden"></p>

        <div class="flex flex-col gap-4">
            <button id="retry-btn" onclick="authenticateWithWebAuthn()" class="w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform hidden">
                Intentar de nuevo
            </button>
            
            <hr class="border-surface-variant/50 my-2">
            
            <div id="fallback-section" class="space-y-4">
                <p class="text-xs text-on-surface-variant">¿Problemas con la biometría?</p>
                <button onclick="useOtpFallback()" class="w-full bg-surface-container text-on-surface font-medium py-3 rounded-full hover:bg-surface-variant transition-colors">
                    Usar código por correo
                </button>
            </div>
        </div>
    </div>
</main>

<script>
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
        return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    async function authenticateWithWebAuthn() {
        const statusEl = document.getElementById('webauthn-status');
        const errorEl = document.getElementById('webauthn-error');
        const retryBtn = document.getElementById('retry-btn');

        statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-primary"><span class="material-symbols-outlined animate-spin">refresh</span><span class="font-bold">Solicitando llave...</span></div>';
        errorEl.classList.add('hidden');
        retryBtn.classList.add('hidden');

        try {
            const optRes = await fetchJson('/auth/2fa/webauthn/options');
            if (optRes.error) throw new Error(optRes.error);
            
            const options = optRes;
            options.challenge = base64urlToBuffer(options.challenge);
            if (options.allowCredentials) {
                for (let i = 0; i < options.allowCredentials.length; i++) {
                    options.allowCredentials[i].id = base64urlToBuffer(options.allowCredentials[i].id);
                }
            }

            statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-teal-600"><span class="material-symbols-outlined">touch_app</span><span class="font-bold">Usa tu lector o FaceID</span></div>';

            const assertion = await navigator.credentials.get({ publicKey: options });
            
            statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-primary"><span class="material-symbols-outlined animate-spin">refresh</span><span class="font-bold">Verificando firma...</span></div>';

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
                statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-green-600"><span class="material-symbols-outlined">check_circle</span><span class="font-bold">Acceso concedido</span></div>';
                window.location.href = verifyRes.redirect;
            } else {
                throw new Error(verifyRes.error || 'Autenticación fallida');
            }
        } catch (e) {
            console.error('WebAuthn Auth Error:', e);
            statusEl.innerHTML = '';
            errorEl.innerText = (e.name === 'NotAllowedError') ? 'Operación cancelada.' : e.message;
            errorEl.classList.remove('hidden');
            retryBtn.classList.remove('hidden');
        }
    }

    async function useOtpFallback() {
        // Redirigir al flujo de login normal pero forzando OTP
        // Necesitamos recuperar el email guardado en sesión si es posible, o pedirlo de nuevo.
        // Como ya tenemos pending_webauthn_user_id, el servidor sabe quién es.
        // Pero el AuthController::generateOTP requiere el email.
        
        // Mejor opción: una ruta que haga el fallback
        window.location.href = '/auth/2fa/webauthn/fallback';
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof navigator.credentials === 'undefined') {
            const errorEl = document.getElementById('webauthn-error');
            errorEl.innerText = 'Tu navegador no soporta biometría. Redirigiendo a código por correo...';
            errorEl.classList.remove('hidden');
            setTimeout(useOtpFallback, 2000);
        } else {
            authenticateWithWebAuthn();
        }
    });
</script>

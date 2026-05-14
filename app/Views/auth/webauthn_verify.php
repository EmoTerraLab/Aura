<?php
// Vista de verificación de WebAuthn (Biometría)
?>

<div class="flex flex-col items-center justify-center min-h-[60vh] px-4">
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 text-primary mb-4">
            <span class="material-symbols-outlined text-4xl">fingerprint</span>
        </div>
        <h1 class="font-display-md text-display-md font-bold text-on-surface mb-2">Verificación de Seguridad</h1>
        <p class="text-body-lg text-on-surface-variant">Usa tu llave de seguridad o biometría para continuar.</p>
    </div>

    <div class="w-full bg-surface-container-lowest rounded-xl p-8 shadow-[0_24px_64px_-12px_rgba(6,105,114,0.06)] text-center">
        <div id="webauthn-status" class="mb-6">
            <div class="flex items-center justify-center gap-3 text-on-surface-variant">
                <span class="material-symbols-outlined">touch_app</span>
                <span>Toca el botón para iniciar</span>
            </div>
        </div>

        <p id="webauthn-error" class="text-error text-sm font-bold bg-error/10 p-4 rounded-lg mb-6 hidden"></p>

        <div class="flex flex-col gap-4">
            <button id="start-btn" onclick="authenticateWithWebAuthn()" class="w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                Iniciar Verificación
            </button>
            <button id="retry-btn" onclick="authenticateWithWebAuthn()" class="w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform hidden">
                Intentar de nuevo
            </button>
            <button onclick="useOtpFallback()" class="w-full bg-surface-container-high text-on-surface font-body-md text-body-md font-medium py-3 rounded-full hover:bg-surface-container-highest transition-colors">
                Usar otro método (Código por email)
            </button>
        </div>
    </div>
</div>

<script>
    async function fetchJson(url, options = {}) {
        const res = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        return res.json();
    }

    function base64url_to_uint8array(base64url) {
        const padding = '='.repeat((4 - base64url.length % 4) % 4);
        const base64 = (base64url + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function uint8array_to_base64url(array) {
        const base64 = btoa(String.fromCharCode(...new Uint8Array(array)));
        return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    async function authenticateWithWebAuthn() {
        const statusEl = document.getElementById('webauthn-status');
        const errorEl = document.getElementById('webauthn-error');
        const retryBtn = document.getElementById('retry-btn');
        const startBtn = document.getElementById('start-btn');

        statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-primary"><span class="material-symbols-outlined animate-spin">refresh</span><span class="font-bold">Solicitando llave...</span></div>';
        errorEl.classList.add('hidden');
        retryBtn.classList.add('hidden');
        if (startBtn) startBtn.classList.add('hidden');

        try {
            const optRes = await fetchJson('/auth/2fa/webauthn/options');
            
            if (optRes.error) {
                throw new Error(optRes.error);
            }

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

            statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-primary"><span class="material-symbols-outlined animate-bounce">fingerprint</span><span class="font-bold">Confirma en tu dispositivo...</span></div>';

            const credential = await navigator.credentials.get(options);

            statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-primary"><span class="material-symbols-outlined animate-spin">sync</span><span class="font-bold">Verificando...</span></div>';

            const authRes = await fetchJson('/auth/2fa/webauthn/verify', {
                method: 'POST',
                body: JSON.stringify({
                    id: credential.id,
                    clientDataJSON: uint8array_to_base64url(credential.response.clientDataJSON),
                    authenticatorData: uint8array_to_base64url(credential.response.authenticatorData),
                    signature: uint8array_to_base64url(credential.response.signature),
                    userHandle: credential.response.userHandle ? uint8array_to_base64url(credential.response.userHandle) : null
                })
            });

            if (authRes.success) {
                statusEl.innerHTML = '<div class="flex items-center justify-center gap-3 text-success"><span class="material-symbols-outlined">check_circle</span><span class="font-bold">¡Verificado! Redirigiendo...</span></div>';
                window.location.href = '/dashboard';
            } else {
                throw new Error(authRes.error || 'Error de verificación');
            }

        } catch (e) {
            console.error(e);
            let message = 'Error al verificar la identidad.';
            
            if (e.name === 'NotAllowedError') {
                message = 'Se canceló la verificación o el tiempo expiró.';
            } else if (e.name === 'NotSupportedError') {
                message = 'Tu dispositivo o navegador no soporta esta función de seguridad.';
            } else {
                message = e.message;
            }
            
            errorEl.innerText = message;
            errorEl.classList.remove('hidden');
            retryBtn.classList.remove('hidden');
        }
    }

    async function useOtpFallback() {
        window.location.href = '/auth/2fa/webauthn/fallback';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const statusEl = document.getElementById('webauthn-status');
        const retryBtn = document.getElementById('retry-btn');

        if (typeof navigator.credentials === 'undefined') {
            const errorEl = document.getElementById('webauthn-error');
            errorEl.innerText = 'Tu navegador no soporta biometría. Redirigiendo a código por correo...';
            errorEl.classList.remove('hidden');
            setTimeout(useOtpFallback, 2000);
        } else {
            // Safari iOS requiere interacción explícita (clic en botón)
            statusEl.innerHTML = '<div class="text-center"><p class="mb-4 text-on-surface-variant text-sm">Haz clic en el botón para identificarte.</p></div>';
        }
    });
</script>

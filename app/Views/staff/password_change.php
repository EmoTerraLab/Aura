<main class="max-w-xl mx-auto p-6 md:p-12">
    <div class="mb-8">
        <a href="/staff/inbox" class="inline-flex items-center gap-2 text-surface-tint hover:underline mb-6">
            <span class="material-symbols-outlined">arrow_back</span>
            <?= \App\Core\Lang::t('nav.back') ?>
        </a>
        <h1 class="font-h1 text-h1 text-primary-container mb-2"><?= \App\Core\Lang::t('auth.change_password') ?></h1>
        <p class="text-on-surface-variant"><?= \App\Core\Lang::t('auth.change_password_desc') ?? 'Actualiza tu contraseña para mantener tu cuenta segura.' ?></p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-error-container text-on-error-container rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <p>
                <?php 
                switch($_GET['error']) {
                    case 'current_invalid': echo \App\Core\Lang::t('auth.error_current_password') ?? 'La contraseña actual no es correcta.'; break;
                    case 'mismatch': echo \App\Core\Lang::t('auth.error_password_mismatch') ?? 'Las contraseñas no coinciden.'; break;
                    case 'too_short': echo \App\Core\Lang::t('auth.error_password_too_short') ?? 'La contraseña es demasiado corta (mínimo 8 caracteres).'; break;
                    default: echo \App\Core\Lang::t('auth.error_generic') ?? 'Ha ocurrido un error.';
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm mb-12">
        <form action="/profile/password" method="POST" class="flex flex-col gap-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="current_password"><?= \App\Core\Lang::t('auth.current_password') ?? 'Contraseña Actual' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock_open</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none" id="current_password" name="current_password" type="password" required/>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="new_password"><?= \App\Core\Lang::t('auth.new_password') ?? 'Nueva Contraseña' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none" id="new_password" name="new_password" type="password" minlength="8" required/>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="font-body-md text-body-md text-on-surface-variant ml-4" for="confirm_password"><?= \App\Core\Lang::t('auth.confirm_password') ?? 'Confirmar Nueva Contraseña' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline-variant">lock_reset</span>
                    <input class="w-full bg-surface-variant text-on-surface font-body-lg text-body-lg rounded-full py-4 pl-14 pr-6 border-none focus:ring-2 focus:ring-primary-container/30 transition-shadow outline-none" id="confirm_password" name="confirm_password" type="password" minlength="8" required/>
                </div>
            </div>

            <button class="mt-4 w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold py-4 rounded-full shadow-sm hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit">
                <?= \App\Core\Lang::t('auth.update_password_btn') ?? 'Actualizar Contraseña' ?>
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">save</span>
            </button>
        </form>
    </div>

    <!-- Sección Biometría (WebAuthn) para Staff -->
    <div class="mb-8">
        <h2 class="font-h2 text-h2 text-primary-container mb-2">Seguridad Biométrica</h2>
        <p class="text-on-surface-variant">Añade una capa extra de seguridad usando Face ID, Touch ID o una llave física.</p>
    </div>

    <div id="webauthn-section" class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-surface-variant/30 hidden">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3 text-primary">
                <span class="material-symbols-outlined text-3xl">fingerprint</span>
                <span class="font-bold">Dispositivos Vinculados</span>
            </div>
            <span id="platform-tag" class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-surface-variant text-on-surface-variant">Detectando...</span>
        </div>

        <div id="webauthn-list" class="space-y-4 mb-8">
            <?php if (empty($webauthnDevices)): ?>
                <div class="text-center py-6 border-2 border-dashed border-surface-variant/50 rounded-2xl">
                    <p class="text-sm text-outline italic">No hay dispositivos registrados.</p>
                </div>
            <?php else: ?>
                <?php foreach($webauthnDevices as $dev): ?>
                    <div class="flex justify-between items-center bg-surface-variant/20 p-4 rounded-2xl border border-surface-variant/30">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-primary shadow-sm">
                                <span class="material-symbols-outlined"><?= str_contains(strtolower($dev['device_name']), 'iphone') || str_contains(strtolower($dev['device_name']), 'móvil') ? 'smartphone' : 'key' ?></span>
                            </div>
                            <div>
                                <p class="font-bold text-on-surface"><?= htmlspecialchars($dev['device_name']) ?></p>
                                <p class="text-[10px] text-outline uppercase font-bold tracking-wider">Registrado: <?= date('d/m/Y', strtotime($dev['created_at'])) ?></p>
                            </div>
                        </div>
                        <button onclick="deleteWebAuthn(<?= $dev['id'] ?>, '<?= htmlspecialchars($dev['device_name']) ?>')" class="w-10 h-10 rounded-full hover:bg-error/10 text-outline hover:text-error transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="webauthn-status-box" class="hidden mb-6 p-4 rounded-2xl text-center font-bold text-sm animate-pulse"></div>

        <button id="btn-register-biometric" onclick="registerWebAuthn()" class="w-full bg-gradient-to-r from-primary to-purple-600 text-white rounded-full py-4 font-bold shadow-lg shadow-primary/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-3">
            <span class="material-symbols-outlined" id="reg-icon">add_circle</span>
            <span id="reg-text">Configurar Biometría</span>
        </button>

        <p id="webauthn-error-msg" class="hidden mt-6 text-sm text-error font-bold text-center bg-error/10 p-4 rounded-xl"></p>
    </div>
</main>

<script>
    const WebAuthnUI = {
        isIOS: () => /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
        isMac: () => /Macintosh|MacIntel|MacPPC|Mac68K/.test(navigator.userAgent),
        isChrome: () => /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor),
        
        updatePlatformUI: function() {
            const section = document.getElementById('webauthn-section');
            if (!section) return;

            if (typeof window.PublicKeyCredential !== 'function') {
                section.classList.add('hidden');
                return;
            }
            section.classList.remove('hidden');

            const tag = document.getElementById('platform-tag');
            const btnText = document.getElementById('reg-text');
            const btnIcon = document.getElementById('reg-icon');
            
            if (this.isIOS() || this.isMac()) {
                tag.innerText = 'Apple Device';
                tag.className = 'text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-blue-100 text-blue-700';
                btnText.innerText = 'Registrar Face ID / Touch ID';
                btnIcon.innerText = 'face';
            } else if (this.isChrome()) {
                tag.innerText = 'Chrome Desktop';
                tag.className = 'text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-amber-100 text-amber-700';
                btnText.innerText = 'Registrar Huella o Llave';
                btnIcon.innerText = 'fingerprint';
            }
        },

        setStatus: function(msg, type = 'info') {
            const box = document.getElementById('webauthn-status-box');
            if (!msg) { box.classList.add('hidden'); return; }
            box.classList.remove('hidden', 'bg-blue-50', 'text-blue-700', 'bg-green-50', 'text-green-700');
            box.innerText = msg;
            if (type === 'info') box.classList.add('bg-blue-50', 'text-blue-700');
            if (type === 'success') box.classList.add('bg-green-50', 'text-green-700');
        },

        setError: function(msg) {
            const err = document.getElementById('webauthn-error-msg');
            if (!msg) { err.classList.add('hidden'); return; }
            err.innerText = msg;
            err.classList.remove('hidden');
        }
    };

    async function fetchJson(url, options = {}) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });
        return res.json();
    }

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
        const deviceDefault = WebAuthnUI.isIOS() ? 'iPhone' : (WebAuthnUI.isMac() ? 'MacBook' : 'Llave Staff');
        const deviceName = prompt('Nombre para este dispositivo:', deviceDefault); 
        if (!deviceName) return;

        const btn = document.getElementById('btn-register-biometric');
        btn.disabled = true;
        WebAuthnUI.setError(null);
        WebAuthnUI.setStatus('Activando sensor biométrico...');

        try {
            const optRes = await fetchJson('/api/webauthn/register/options');
            if (optRes.error) throw new Error(optRes.error);
            
            const options = optRes; 
            options.challenge = base64urlToBuffer(options.challenge);
            options.user.id = base64urlToBuffer(options.user.id);
            if (options.excludeCredentials) options.excludeCredentials.forEach(c => c.id = base64urlToBuffer(c.id));
            
            const credential = await navigator.credentials.create({ publicKey: options });
            
            WebAuthnUI.setStatus('Vinculando dispositivo...');
            
            const verifyRes = await fetchJson('/api/webauthn/register/verify', {
                method: 'POST', body: {
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64url(credential.response.attestationObject),
                    device_name: deviceName
                }
            });

            if (verifyRes.success) {
                WebAuthnUI.setStatus('¡Dispositivo registrado!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else { 
                throw new Error(verifyRes.error); 
            }
        } catch (e) { 
            console.error(e);
            btn.disabled = false;
            WebAuthnUI.setStatus(null);
            WebAuthnUI.setError('No se pudo registrar: ' + (e.name === 'NotAllowedError' ? 'Cancelado' : e.message));
        }
    }

    async function deleteWebAuthn(id, name) {
        if (!confirm(`¿Eliminar "${name}"?`)) return;
        const res = await fetchJson('/api/webauthn/credential/delete', { method: 'POST', body: { id } });
        if (res.success) window.location.reload();
    }

    document.addEventListener('DOMContentLoaded', () => WebAuthnUI.updatePlatformUI());
</script>

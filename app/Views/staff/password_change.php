<main class="max-w-xl mx-auto p-4 md:p-8 font-display">
    <div class="mb-6">
        <a href="/staff/inbox" class="inline-flex items-center gap-1.5 text-primary text-xs font-bold hover:underline mb-4">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <?= \App\Core\Lang::t('nav.back') ?>
        </a>
        <h1 class="font-bold text-xl md:text-2xl text-on-surface mb-1 tracking-tight"><?= \App\Core\Lang::t('auth.change_password') ?></h1>
        <p class="text-xs md:text-sm text-on-surface-variant"><?= \App\Core\Lang::t('auth.change_password_desc') ?? 'Actualiza tu contraseña para mantener tu cuenta segura en Prisma.' ?></p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-3.5 bg-error/10 border-l-4 border-error text-error rounded-r-xl flex items-center gap-2 text-xs font-semibold">
            <span class="material-symbols-outlined text-base">error</span>
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

    <div class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 mb-8">
        <form action="/profile/password" method="POST" class="flex flex-col gap-4">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="current_password"><?= \App\Core\Lang::t('auth.current_password') ?? 'Contraseña Actual' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline-variant text-[18px]">lock_open</span>
                    <input class="w-full h-11 bg-surface-container-low text-on-surface text-xs md:text-sm font-medium rounded-xl pl-10 pr-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-body-md" id="current_password" name="current_password" type="password" required/>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="new_password"><?= \App\Core\Lang::t('auth.new_password') ?? 'Nueva Contraseña' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline-variant text-[18px]">lock</span>
                    <input class="w-full h-11 bg-surface-container-low text-on-surface text-xs md:text-sm font-medium rounded-xl pl-10 pr-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-body-md" id="new_password" name="new_password" type="password" minlength="8" required/>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="confirm_password"><?= \App\Core\Lang::t('auth.confirm_password') ?? 'Confirmar Nueva Contraseña' ?></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline-variant text-[18px]">lock_reset</span>
                    <input class="w-full h-11 bg-surface-container-low text-on-surface text-xs md:text-sm font-medium rounded-xl pl-10 pr-4 border border-surface-variant/40 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-body-md" id="confirm_password" name="confirm_password" type="password" minlength="8" required/>
                </div>
            </div>

            <button class="mt-2 w-full h-11 bg-primary text-on-primary font-bold text-xs rounded-xl shadow-xs hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer" type="submit">
                <span><?= \App\Core\Lang::t('auth.update_password_btn') ?? 'Actualizar Contraseña' ?></span>
                <span class="material-symbols-outlined text-base">save</span>
            </button>
        </form>
    </div>

    <!-- Sección Biometría (WebAuthn) para Staff -->
    <div class="mb-4">
        <h2 class="font-bold text-lg text-on-surface mb-1">Seguridad Biométrica</h2>
        <p class="text-xs text-on-surface-variant">Añade una capa extra de seguridad usando Face ID, Touch ID o una llave física.</p>
    </div>

    <div id="webauthn-section" class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 shadow-card border border-surface-variant/40 hidden">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5 text-primary">
                <span class="material-symbols-outlined text-2xl">fingerprint</span>
                <span class="font-bold text-sm">Dispositivos Vinculados</span>
            </div>
            <span id="platform-tag" class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface-variant">Detectando...</span>
        </div>

        <div id="webauthn-list" class="space-y-2.5 mb-6">
            <?php if (empty($webauthnDevices)): ?>
                <div class="text-center py-6 border border-dashed border-surface-variant/50 rounded-xl">
                    <p class="text-xs text-on-surface-variant italic">No hay dispositivos biométricos registrados.</p>
                </div>
            <?php else: ?>
                <?php foreach($webauthnDevices as $dev): ?>
                    <div class="flex justify-between items-center bg-surface-container-low p-3.5 rounded-xl border border-surface-variant/30">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-lowest flex items-center justify-center text-primary shadow-xs">
                                <span class="material-symbols-outlined text-base"><?= str_contains(strtolower($dev['device_name']), 'iphone') || str_contains(strtolower($dev['device_name']), 'móvil') ? 'smartphone' : 'key' ?></span>
                            </div>
                            <div>
                                <p class="font-bold text-xs text-on-surface"><?= htmlspecialchars($dev['device_name']) ?></p>
                                <p class="text-[9px] text-on-surface-variant uppercase font-semibold tracking-wider">Registrado: <?= date('d/m/Y', strtotime($dev['created_at'])) ?></p>
                            </div>
                        </div>
                        <button onclick="deleteWebAuthn(<?= $dev['id'] ?>, '<?= htmlspecialchars($dev['device_name']) ?>')" class="w-8 h-8 rounded-lg hover:bg-error/10 text-on-surface-variant hover:text-error transition-all flex items-center justify-center cursor-pointer">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="webauthn-status-box" class="hidden mb-4 p-3 rounded-xl text-center font-bold text-xs"></div>

        <button id="btn-register-biometric" onclick="registerWebAuthn()" class="w-full h-11 bg-primary text-on-primary rounded-xl font-bold text-xs shadow-xs hover:bg-primary/90 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span class="material-symbols-outlined text-base" id="reg-icon">add_circle</span>
            <span id="reg-text">Configurar Biometría</span>
        </button>

        <p id="webauthn-error-msg" class="hidden mt-4 text-xs text-error font-semibold text-center bg-error/10 p-3 rounded-xl"></p>
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
                tag.className = 'text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-blue-100 text-blue-700';
                btnText.innerText = 'Registrar Face ID / Touch ID';
                btnIcon.innerText = 'face';
            } else if (this.isChrome()) {
                tag.innerText = 'Chrome';
                tag.className = 'text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-100 text-amber-800';
                btnText.innerText = 'Registrar Huella o Llave';
                btnIcon.innerText = 'fingerprint';
            }
        },

        setStatus: function(msg, type = 'info') {
            const box = document.getElementById('webauthn-status-box');
            if (!msg) { box.classList.add('hidden'); return; }
            box.classList.remove('hidden', 'bg-blue-50', 'text-blue-700', 'bg-prisma-mint/20', 'text-teal-900');
            box.innerText = msg;
            if (type === 'info') box.classList.add('bg-blue-50', 'text-blue-700');
            if (type === 'success') box.classList.add('bg-prisma-mint/20', 'text-teal-900');
        },

        setError: function(msg) {
            const err = document.getElementById('webauthn-error-msg');
            if (!msg) { err.classList.add('hidden'); return; }
            err.innerText = msg;
            err.classList.remove('hidden');
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

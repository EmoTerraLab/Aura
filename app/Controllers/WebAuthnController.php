<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Config;
use App\Models\WebAuthnCredential;
use App\Models\User;
use lbuchs\WebAuthn\WebAuthn;

/**
 * WebAuthnController - Gestión de autenticación biométrica.
 * Auditado y corregido para máxima seguridad y estabilidad en entornos VPS.
 */
class WebAuthnController
{
    private WebAuthnCredential $credentialModel;
    private WebAuthn $webauthn;
    private $db;

    public function __construct(WebAuthnCredential $credentialModel)
    {
        $this->credentialModel = $credentialModel;
        
        // Desactivar display_errors para evitar que warnings rompan el JSON
        ini_set('display_errors', 0);
        
        try {
            $this->db = \App\Core\Database::getInstance();
            
            $appName = Config::get('school_name', 'Aura');
            $rpId = $_SERVER['HTTP_HOST'] ?? 'localhost';
            
            // WA-04: Limpiar puerto del RP ID si existe
            if (strpos($rpId, ':') !== false) {
                $rpId = explode(':', $rpId)[0];
            }
            
            // Comprobar extensiones necesarias
            if (!extension_loaded('openssl')) throw new \Exception("Falta extensión 'openssl'");
            if (!extension_loaded('gmp') && !extension_loaded('bcmath')) {
                throw new \Exception("Falta extensión 'gmp' o 'bcmath'");
            }

            if (!class_exists('lbuchs\WebAuthn\WebAuthn')) {
                throw new \Exception("Librería WebAuthn no cargada. Ejecuta composer install.");
            }

            $this->webauthn = new WebAuthn($appName, $rpId, ['android-key', 'android-safetynet', 'fido-u2f', 'none', 'packed', 'tpm']);
        } catch (\Throwable $e) {
            error_log('WebAuthn Error de inicialización: ' . $e->getMessage());
            $this->sendError('Error de inicialización: ' . $e->getMessage());
        }
    }

    // GET /alumno/2fa/webauthn/register/options
    public function registerOptions(): void
    {
        try {
            $user = Auth::user();
            if (!$user) throw new \Exception("Usuario no autenticado");

            // WA-08: Identificador opaco. Si no existe, generamos uno persistente.
            $userHandle = $user['webauthn_handle'] ?? null;
            if (!$userHandle) {
                $userHandle = bin2hex(random_bytes(16));
                $stmt = $this->db->prepare('UPDATE users SET webauthn_handle = ? WHERE id = ?');
                $stmt->execute([$userHandle, $user['id']]);
                // Actualizar usuario en sesión para que tenga el handle
                Auth::login(array_merge($user, ['webauthn_handle' => $userHandle]));
            }

            // Obtener credenciales existentes para excluirlas
            $existing = $this->credentialModel->findByUserId($user['id']);
            $excludeCredentials = array_map(fn($c) => base64_decode($c['credential_id']), $existing);

            // El WA-08: userHandle debe ser binario para la librería, lo guardamos como hex en DB
            $userHandleBin = hex2bin($userHandle);

            $createArgs = $this->webauthn->getCreateArgs(
                $userHandleBin, 
                $user['email'], 
                $user['name'], 
                60,    // timeout
                false, // requireResidentKey
                'preferred', // userVerification
                null, // crossPlatformAttachment
                $excludeCredentials // excludeCredentialIds es el 8º argumento
            );

            // WA-01: Guardar challenge con expiración (60 segundos)
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_expires', time() + 60);
            
            error_log("WebAuthn registerOptions: Challenge generado para user " . $user['id']);

            // Extraer el objeto publicKey que es el que el navegador espera directamente
            $options = $createArgs->publicKey;

            // WA-09: Asegurar que RP esté presente (Vital para navegadores modernos)
            if (!isset($options->rp)) {
                $options->rp = new \stdClass();
                $options->rp->name = Config::get('school_name', 'Aura');
                $options->rp->id = $_SERVER['HTTP_HOST'] ?? 'localhost';
                if (strpos($options->rp->id, ':') !== false) {
                    $options->rp->id = explode(':', $options->rp->id)[0];
                }
            }

            // Convertir campos binarios a base64url para JSON
            $options->challenge = $this->bufferToBase64url($options->challenge);
            $options->user->id = $this->bufferToBase64url($options->user->id);
            
            if (isset($options->excludeCredentials) && is_array($options->excludeCredentials)) {
                foreach ($options->excludeCredentials as &$cred) {
                    $cred->id = $this->bufferToBase64url($cred->id);
                }
            }

            // Asegurar que pubKeyCredParams esté presente (requerido por el navegador)
            if (!isset($options->pubKeyCredParams) || empty($options->pubKeyCredParams)) {
                $options->pubKeyCredParams = [
                    ['type' => 'public-key', 'alg' => -7],  // ES256
                    ['type' => 'public-key', 'alg' => -257] // RS256
                ];
            }
            header('Content-Type: application/json');
            echo json_encode($options);
        } catch (\Throwable $e) {
            error_log("WebAuthn registerOptions Error: " . $e->getMessage());
            $this->sendError('Error al generar opciones: ' . $e->getMessage());
        }
        exit;
    }

    // POST /alumno/2fa/webauthn/register/verify
    public function registerVerify(): void
    {
        // WA-05: Validar CSRF
        Csrf::validateRequest();
        
        try {
            $user = Auth::user();
            if (!$user) throw new \Exception("Sesión expirada");

            $rawBody = file_get_contents('php://input');
            $input = json_decode($rawBody, true);
            
            if (!isset($input['clientDataJSON']) || !isset($input['attestationObject'])) {
                throw new \Exception("Datos de registro incompletos");
            }

            // WA-02: Recuperar y eliminar challenge inmediatamente
            $challenge = Session::get('webauthn_challenge');
            $expires = Session::get('webauthn_challenge_expires', 0);
            Session::remove('webauthn_challenge');
            Session::remove('webauthn_challenge_expires');

            if (!$challenge || time() > $expires) {
                throw new \Exception("Desafío expirado o no encontrado. Reinicia el proceso.");
            }

            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $attestationObject = $this->base64url_decode($input['attestationObject']);
            
            // Corregido: pasar booleanos
            $data = $this->webauthn->processCreate($clientDataJSON, $attestationObject, $challenge, true, true, false);
            
            // WA-07: Validar antes de guardar
            $credentialId = base64_encode($data->credentialId);
            $publicKey = $data->credentialPublicKey;
            $deviceName = htmlspecialchars(trim($input['device_name'] ?? 'Mi dispositivo'));

            if (empty($credentialId) || empty($publicKey)) throw new \Exception("Datos de credencial inválidos");

            // Verificar si ya existe (WA-07)
            if ($this->credentialModel->existsByCredentialId($credentialId)) {
                throw new \Exception("Este dispositivo ya está registrado.");
            }

            $this->credentialModel->create([
                'user_id'       => $user['id'],
                'credential_id' => $credentialId,
                'public_key'    => $publicKey,
                'sign_count'    => $data->signatureCounter ?? 0,
                'device_name'   => $deviceName
            ]);

            error_log("WebAuthn registerVerify: Dispositivo registrado con éxito para user " . $user['id']);

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            error_log("WebAuthn registerVerify Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
        exit;
    }

    // GET /auth/2fa/webauthn/options
    public function authOptions(): void
    {
        try {
            // WA-06: Verificar que hay un usuario pendiente
            $pendingUserId = Session::get('pending_webauthn_user_id');
            if (!$pendingUserId) throw new \Exception("No hay login pendiente");

            $creds = $this->credentialModel->findByUserId($pendingUserId);
            if (empty($creds)) throw new \Exception("No hay dispositivos registrados");

            $allowedCredentials = array_map(fn($c) => base64_decode($c['credential_id']), $creds);

            $getArgs = $this->webauthn->getGetArgs($allowedCredentials, 60, true, true, true, true, true, 'preferred');

            // WA-01: Challenge con expiración
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_expires', time() + 60);

            // Extraer el objeto publicKey que es el que el navegador espera directamente
            $options = $getArgs->publicKey;

            // WA-09: Asegurar que rpId esté presente (Crucial para Safari/iOS)
            if (!isset($options->rpId)) {
                $rpId = $_SERVER['HTTP_HOST'] ?? 'localhost';
                if (strpos($rpId, ':') !== false) {
                    $rpId = explode(':', $rpId)[0];
                }
                $options->rpId = $rpId;
            }

            $options->challenge = $this->bufferToBase64url($options->challenge);
            if (isset($options->allowCredentials) && is_array($options->allowCredentials)) {
                foreach ($options->allowCredentials as &$cred) {
                    $cred->id = $this->bufferToBase64url($cred->id);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($options);
        } catch (\Throwable $e) {
            error_log("WebAuthn authOptions Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
        exit;
    }

    // POST /auth/2fa/webauthn/verify
    public function authVerify(): void
    {
        Csrf::validateRequest();
        
        try {
            // WA-06: Verificar que hay un usuario pendiente
            $pendingUserId = Session::get('pending_webauthn_user_id');
            if (!$pendingUserId) throw new \Exception("Sesión de login expirada");

            $input = json_decode(file_get_contents('php://input'), true);
            
            // WA-02: Consumir challenge inmediatamente
            $challenge = Session::get('webauthn_challenge');
            $expires = Session::get('webauthn_challenge_expires', 0);
            Session::remove('webauthn_challenge');
            Session::remove('webauthn_challenge_expires');

            if (!$challenge || time() > $expires) throw new \Exception("Desafío inválido o expirado");

            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $authenticatorData = $this->base64url_decode($input['authenticatorData']);
            $signature = $this->base64url_decode($input['signature']);
            $credentialIdB64Url = $input['credentialId'] ?? ''; 
            
            // Convertir base64url de frontend a base64 estándar para buscar en BD
            $credentialIdB64 = base64_encode($this->base64url_decode($credentialIdB64Url));
            
            // WA-06: Buscar credencial verificando propiedad
            $cred = $this->credentialModel->findByCredentialIdAndUserId($credentialIdB64, $pendingUserId);
            if (!$cred) throw new \Exception("Dispositivo no reconocido para este usuario");

            // Corregido: pasar booleanos
            $data = $this->webauthn->processGet(
                $clientDataJSON, 
                $authenticatorData, 
                $signature, 
                base64_decode($cred['public_key']), 
                $challenge, 
                $cred['sign_count'], 
                true,
                true
            );

            // WA-03: Validar sign count para detectar clonación
            if ($data->signatureCounter > 0 && $data->signatureCounter <= $cred['sign_count']) {
                error_log("WebAuthn SECURITY ALERT: Possible cloning detected for user $pendingUserId | ID: " . $cred['id']);
                throw new \Exception("Error de seguridad del autenticador. Contacta con soporte.");
            }

            $this->credentialModel->updateSignCount($credentialIdB64, $data->signatureCounter);

            Session::remove('pending_webauthn_user_id');

            // Completar login
            $userModel = new User();
            $user = $userModel->find($pendingUserId);
            Auth::login($user);

            error_log("WebAuthn authVerify: Login biométrico exitoso para user " . $user['id']);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => '/alumno/dashboard']);
        } catch (\Throwable $e) {
            error_log("WebAuthn authVerify Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
        exit;
    }

    // POST /alumno/2fa/webauthn/credential/delete
    public function deleteCredential(): void
    {
        Csrf::validateRequest();
        $user = Auth::user();
        if (!$user) { $this->sendError("No autorizado"); }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if ($id) {
            $this->credentialModel->deleteByIdAndUserId($id, $user['id']);
            error_log("WebAuthn: Credencial $id eliminada por user " . $user['id']);
        }
        
        echo json_encode(['success' => true]);
        exit;
    }

    // GET /auth/2fa/webauthn
    public function showVerify(): void
    {
        if (!Session::get('pending_webauthn_user_id')) {
            header('Location: /login');
            exit;
        }
        View::render('auth/webauthn_verify', ['title' => 'Verificación Biométrica'], 'app');
    }

    // GET /auth/2fa/webauthn/fallback
    public function fallback(): void
    {
        $pendingUserId = Session::get('pending_webauthn_user_id');
        if (!$pendingUserId) {
            header('Location: /login');
            exit;
        }

        // Forzar el flujo OTP de correo
        $userModel = new User();
        $user = $userModel->find($pendingUserId);
        
        if ($user) {
            // Guardar flag en sesión para que AuthController sepa que debe forzar OTP
            Session::set('force_otp_for_user', $user['email']);
            Session::remove('pending_webauthn_user_id');
        }

        header('Location: /login?fallback=otp');
        exit;
    }

    private function bufferToBase64url($buffer)
    {
        if ($buffer instanceof \lbuchs\WebAuthn\Binary\ByteBuffer) {
            $buffer = $buffer->getBinaryString();
        }
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($buffer));
    }

    private function base64url_decode($data)
    {
        $base64 = str_replace(['-', '_'], ['+', '/'], $data);
        $pad = strlen($base64) % 4;
        if ($pad) {
            $base64 .= str_repeat('=', 4 - $pad);
        }
        return base64_decode($base64);
    }

    private function sendError(string $message): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode(['error' => $message]);
        exit;
    }
}

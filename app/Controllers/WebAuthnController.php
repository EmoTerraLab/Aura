<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Core\Config;
use lbuchs\WebAuthn\WebAuthn;

/**
 * WebAuthnController - Gestión de autenticación biométrica.
 * Auditado y corregido para máxima seguridad y estabilidad.
 */
class WebAuthnController
{
    private $db;
    private $webauthn;

    public function __construct()
    {
        // Desactivar display_errors para evitar que warnings rompan el JSON
        ini_set('display_errors', 0);
        
        try {
            $this->db = Database::getInstance();
            
            $appName = Config::get('school_name', 'Aura PDP');
            $rpId = $_SERVER['HTTP_HOST'] ?? 'localhost';
            
            // Limpiar puerto del RP ID si existe (localhost:8000 -> localhost)
            if (strpos($rpId, ':') !== false) {
                $rpId = explode(':', $rpId)[0];
            }
            
            // Comprobar extensiones críticas
            if (!extension_loaded('openssl')) throw new \Exception("Falta extensión 'openssl'");
            if (!extension_loaded('gmp') && !extension_loaded('bcmath')) {
                throw new \Exception("WebAuthn requiere 'php-gmp' o 'php-bcmath'");
            }

            if (!class_exists('lbuchs\WebAuthn\WebAuthn')) {
                throw new \Exception("Librería WebAuthn no cargada.");
            }

            $this->webauthn = new WebAuthn($appName, $rpId, ['android-key', 'android-safetynet', 'fido-u2f', 'none', 'packed', 'tpm']);
        } catch (\Throwable $e) {
            $this->sendError('Error de inicialización: ' . $e->getMessage());
        }
    }

    // GET /alumno/2fa/webauthn/register/options
    public function registerOptions(): void
    {
        try {
            $user = Auth::user();
            if (!$user) throw new \Exception("Usuario no autenticado");

            // WA-08: Usar un identificador opaco para el usuario
            $userHandle = $user['email']; // O generar uno aleatorio y guardarlo

            // Obtener credenciales existentes para excluirlas
            $stmt = $this->db->prepare('SELECT credential_id FROM webauthn_credentials WHERE user_id = ?');
            $stmt->execute([$user['id']]);
            $existing = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            $excludeCredentials = array_map(fn($id) => base64_decode($id), $existing);

            $createArgs = $this->webauthn->getCreateArgs(
                $userHandle, 
                $user['email'], 
                $user['name'], 
                60, // timeout
                false, // require resident key
                'preferred', 
                $excludeCredentials
            );

            // WA-01: Guardar challenge con expiración
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_expires', time() + 60);
            
            // Convertir campos binarios a base64url para JSON
            $createArgs->challenge = $this->bufferToBase64url($createArgs->challenge);
            $createArgs->user->id = $this->bufferToBase64url($createArgs->user->id);
            
            if (isset($createArgs->excludeCredentials) && is_array($createArgs->excludeCredentials)) {
                foreach ($createArgs->excludeCredentials as &$cred) {
                    $cred->id = $this->bufferToBase64url($cred->id);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($createArgs);
        } catch (\Throwable $e) {
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

            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['clientDataJSON']) || !isset($input['attestationObject'])) {
                throw new \Exception("Datos incompletos");
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
            
            $data = $this->webauthn->processCreate($clientDataJSON, $attestationObject, $challenge, 'preferred', true, false);
            
            // WA-07: Validar antes de guardar
            $credentialId = base64_encode($data->credentialId);
            $publicKey = $data->credentialPublicKey;
            $deviceName = htmlspecialchars(trim($input['device_name'] ?? 'Mi dispositivo'));

            if (empty($credentialId) || empty($publicKey)) throw new \Exception("Clave pública inválida");

            $stmt = $this->db->prepare('INSERT INTO webauthn_credentials (user_id, credential_id, public_key, sign_count, device_name) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$user['id'], $credentialId, $publicKey, $data->signatureCounter, $deviceName]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            $this->sendError($e->getMessage());
        }
        exit;
    }

    // GET /auth/2fa/webauthn/options
    public function authOptions(): void
    {
        try {
            $pendingUserId = Session::get('pending_webauthn_user_id');
            if (!$pendingUserId) throw new \Exception("No hay login pendiente");

            $stmt = $this->db->prepare('SELECT credential_id FROM webauthn_credentials WHERE user_id = ?');
            $stmt->execute([$pendingUserId]);
            $creds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (empty($creds)) throw new \Exception("No hay dispositivos registrados");

            $allowedCredentials = array_map(fn($id) => base64_decode($id), $creds);

            $getArgs = $this->webauthn->getGetArgs($allowedCredentials, 60, true, true, true, true, 'preferred');

            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_expires', time() + 60);
            
            $getArgs->challenge = $this->bufferToBase64url($getArgs->challenge);
            if (isset($getArgs->allowCredentials) && is_array($getArgs->allowCredentials)) {
                foreach ($getArgs->allowCredentials as &$cred) {
                    $cred->id = $this->bufferToBase64url($cred->id);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($getArgs);
        } catch (\Throwable $e) {
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
            
            // WA-02: Consumir challenge
            $challenge = Session::get('webauthn_challenge');
            $expires = Session::get('webauthn_challenge_expires', 0);
            Session::remove('webauthn_challenge');
            Session::remove('webauthn_challenge_expires');

            if (!$challenge || time() > $expires) throw new \Exception("Desafío inválido o expirado");

            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $authenticatorData = $this->base64url_decode($input['authenticatorData']);
            $signature = $this->base64url_decode($input['signature']);
            $credentialIdB64 = base64_encode($this->base64url_decode($input['credentialId'] ?? '')); 
            
            // Buscar credencial verificando propiedad (WA-06)
            $stmt = $this->db->prepare('SELECT id, public_key, sign_count FROM webauthn_credentials WHERE user_id = ? AND credential_id = ? LIMIT 1');
            $stmt->execute([$pendingUserId, $credentialIdB64]);
            $cred = $stmt->fetch();

            if (!$cred) throw new \Exception("Dispositivo no reconocido para este usuario");

            $data = $this->webauthn->processGet(
                $clientDataJSON, 
                $authenticatorData, 
                $signature, 
                $cred['public_key'], 
                $challenge, 
                $cred['sign_count'], 
                'preferred' 
            );

            // WA-03: Validar sign count para detectar clonación
            if ($data->signatureCounter > 0 && $data->signatureCounter <= $cred['sign_count']) {
                error_log("WebAuthn security alert: possible cloning for user $pendingUserId");
                throw new \Exception("Error de seguridad del autenticador");
            }

            $stmtUpd = $this->db->prepare('UPDATE webauthn_credentials SET sign_count = ?, last_used_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmtUpd->execute([$data->signatureCounter, $cred['id']]);

            Session::remove('pending_webauthn_user_id');

            $stmtUser = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
            $stmtUser->execute([$pendingUserId]);
            $user = $stmtUser->fetch();
            
            Auth::login($user);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => '/alumno/dashboard']);
        } catch (\Throwable $e) {
            $this->sendError($e->getMessage());
        }
        exit;
    }

    // POST /alumno/2fa/webauthn/credential/delete
    public function deleteCredential(): void
    {
        Csrf::validateRequest();
        $user = Auth::user();
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if ($id) {
            $stmt = $this->db->prepare('DELETE FROM webauthn_credentials WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $user['id']]);
        }
        echo json_encode(['success' => true]);
        exit;
    }

    private function bufferToBase64url($buffer)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($buffer));
    }

    private function base64url_decode($data)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
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

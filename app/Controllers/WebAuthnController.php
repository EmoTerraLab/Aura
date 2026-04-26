<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Core\Config;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

class WebAuthnController
{
    private $db;
    private $webauthn;

    public function __construct()
    {
        $this->db = Database::getInstance();
        
        $appName = Config::get('school_name', 'Aura PDP');
        // RP ID must be the domain without protocol or port
        $rpId = $_SERVER['HTTP_HOST'];
        if (strpos($rpId, ':') !== false) {
            $rpId = explode(':', $rpId)[0];
        }
        
        // If it's an IP address, WebAuthn might not work in some browsers, 
        // but we'll try to keep it as is.
        
        $this->webauthn = new WebAuthn($appName, $rpId, ['image/png', 'image/jpeg']);
    }

    // GET /alumno/2fa/webauthn/register/options
    public function registerOptions(): void
    {
        $user = Auth::user();
        // User ID must be binary for the library
        $userIdBinary = (string)$user['id'];
        
        try {
            $createArgs = $this->webauthn->getCreateArgs(
                $userIdBinary, 
                $user['email'], 
                $user['name'], 
                20, 
                false, 
                'preferred', 
                null 
            );

            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            
            // Convert binary fields to base64url for JSON compatibility
            $createArgs->challenge = $this->bufferToBase64url($createArgs->challenge);
            $createArgs->user->id = $this->bufferToBase64url($createArgs->user->id);
            
            if (isset($createArgs->excludeCredentials) && is_array($createArgs->excludeCredentials)) {
                foreach ($createArgs->excludeCredentials as &$cred) {
                    $cred->id = $this->bufferToBase64url($cred->id);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($createArgs);
        } catch (WebAuthnException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    // POST /alumno/2fa/webauthn/register/verify
    public function registerVerify(): void
    {
        Csrf::validateRequest();
        
        $user = Auth::user();
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['clientDataJSON']) || !isset($input['attestationObject'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }

        try {
            // Frontend sends base64url, we need to decode it
            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $attestationObject = $this->base64url_decode($input['attestationObject']);
            $challenge = Session::get('webauthn_challenge');
            
            $data = $this->webauthn->processCreate($clientDataJSON, $attestationObject, $challenge, 'preferred', true, true);
            
            // Store as base64 for easy DB handling
            $credentialId = base64_encode($data->credentialId);
            $publicKey = $data->credentialPublicKey;
            $deviceName = trim($input['device_name'] ?? 'Mi dispositivo');

            $stmt = $this->db->prepare('INSERT INTO webauthn_credentials (user_id, credential_id, public_key, device_name) VALUES (?, ?, ?, ?)');
            $stmt->execute([$user['id'], $credentialId, $publicKey, $deviceName]);

            Session::remove('webauthn_challenge');

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    // GET /auth/2fa/webauthn/options
    public function authOptions(): void
    {
        $pendingUserId = Session::get('pending_webauthn_user_id');
        if (!$pendingUserId) {
            http_response_code(403);
            echo json_encode(['error' => 'No hay autenticación pendiente']);
            exit;
        }

        try {
            $stmt = $this->db->prepare('SELECT credential_id FROM webauthn_credentials WHERE user_id = ?');
            $stmt->execute([$pendingUserId]);
            $creds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $allowedCredentials = [];
            foreach ($creds as $credIdBase64) {
                $allowedCredentials[] = base64_decode($credIdBase64);
            }

            $getArgs = $this->webauthn->getGetArgs(
                $allowedCredentials, 
                20, 
                true, 
                true, 
                true, 
                true, 
                'preferred'
            );

            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            
            // Convert binary fields for JSON
            $getArgs->challenge = $this->bufferToBase64url($getArgs->challenge);
            if (isset($getArgs->allowCredentials) && is_array($getArgs->allowCredentials)) {
                foreach ($getArgs->allowCredentials as &$cred) {
                    $cred->id = $this->bufferToBase64url($cred->id);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($getArgs);
        } catch (WebAuthnException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    // POST /auth/2fa/webauthn/verify
    public function authVerify(): void
    {
        Csrf::validateRequest();
        
        $pendingUserId = Session::get('pending_webauthn_user_id');
        if (!$pendingUserId) {
            http_response_code(403);
            echo json_encode(['error' => 'No hay autenticación pendiente']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $authenticatorData = $this->base64url_decode($input['authenticatorData']);
            $signature = $this->base64url_decode($input['signature']);
            $credentialIdBase64Url = $input['credentialId'] ?? ''; 
            
            // Convert base64url from frontend to standard base64 for DB search
            $credentialIdBase64 = base64_encode($this->base64url_decode($credentialIdBase64Url));
            
            $stmt = $this->db->prepare('SELECT id, public_key, sign_count FROM webauthn_credentials WHERE user_id = ? AND credential_id = ? LIMIT 1');
            $stmt->execute([$pendingUserId, $credentialIdBase64]);
            $cred = $stmt->fetch();

            if (!$cred) {
                throw new \Exception("Credencial no encontrada para este usuario");
            }

            $challenge = Session::get('webauthn_challenge');

            $this->webauthn->processGet(
                $clientDataJSON, 
                $authenticatorData, 
                $signature, 
                $cred['public_key'], 
                $challenge, 
                $cred['sign_count'], 
                'preferred' 
            );

            $stmtUpd = $this->db->prepare('UPDATE webauthn_credentials SET sign_count = sign_count + 1, last_used_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmtUpd->execute([$cred['id']]);

            Session::remove('webauthn_challenge');
            Session::remove('pending_webauthn_user_id');

            $stmtUser = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
            $stmtUser->execute([$pendingUserId]);
            $user = $stmtUser->fetch();
            
            Auth::login($user);

            echo json_encode(['success' => true, 'redirect' => '/alumno/dashboard']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
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
}

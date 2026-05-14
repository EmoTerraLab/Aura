<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Session;
use App\Models\User;
use App\Models\WebAuthnCredential;
use lbuchs\WebAuthn\WebAuthn;

class WebAuthnController
{
    private $webauthn;
    private $credentialModel;
    private $userModel;

    public function __construct()
    {
        try {
            $this->credentialModel = new WebAuthnCredential();
            $this->userModel = new User();
            
            $appName = Config::get('school_name', 'Aura');
            
            // SEC-014: Prevenir spoofing de Host obteniendo rpId del app_url
            $appUrl = Config::get('app_url', 'http://localhost');
            $rpId = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';
            
            // WA-04: Limpiar puerto del RP ID si existe
            if (strpos($rpId, ':') !== false) {
                $rpId = explode(':', $rpId)[0];
            }

            $this->webauthn = new WebAuthn($appName, $rpId);
        } catch (\Throwable $e) {
            error_log("WebAuthn init error: " . $e->getMessage());
        }
    }

    /**
     * GET /alumno/2fa/webauthn/register/options
     */
    public function registerOptions(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');

        try {
            $user = Auth::user();
            $userId = $user['id'];
            
            // WA-02: Nombre de usuario único para WebAuthn
            $userHandleBin = str_pad((string)$userId, 16, "\0", STR_PAD_LEFT);
            
            // WA-03: Excluir credenciales ya registradas
            $creds = $this->credentialModel->findByUserId($userId);
            $excludeCredentials = array_map(fn($c) => base64_decode($c['credential_id']), $creds);

            $options = $this->webauthn->getCreateArgs(
                $userHandleBin, 
                $user['email'], 
                $user['name'], 
                60000,       // timeout
                false,       // requireResidentKey
                'preferred', // userVerification
                null,        // crossPlatformAttachment
                $excludeCredentials
            );

            // WA-01: Guardar challenge con expiración
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_time', time());

            // Acceder al objeto publicKey retornado por lbuchs/webauthn
            $publicKey = $options->publicKey;

            // FIX: Asegurar que el objeto rp esté presente y correcto
            if (!isset($publicKey->rp)) {
                $publicKey->rp = new \stdClass();
                $publicKey->rp->name = Config::get('school_name', 'Aura');
                $appUrl = Config::get('app_url', 'http://localhost');
                $rpId = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';
                if (strpos($rpId, ':') !== false) {
                    $rpId = explode(':', $rpId)[0];
                }
                $publicKey->rp->id = $rpId;
            }

            // Aplicar compatibilidad Chrome/Safari
            $publicKey->timeout = 60000;
            if (isset($publicKey->authenticatorSelection)) {
                $publicKey->authenticatorSelection->userVerification = 'preferred';
                unset($publicKey->authenticatorSelection->authenticatorAttachment);
            }

            // Convertir campos binarios a base64url para JSON
            $publicKey->user->id = $this->base64url_encode($publicKey->user->id);
            $publicKey->challenge = $this->base64url_encode($publicKey->challenge);
            
            if (!empty($publicKey->excludeCredentials)) {
                foreach ($publicKey->excludeCredentials as &$c) {
                    $c->id = $this->base64url_encode($c->id);
                }
            }

            echo json_encode($publicKey);
        } catch (\Throwable $e) {
            error_log("WebAuthn registerOptions error: " . $e->getMessage());
            $this->sendError("Error al generar opciones de registro.");
        }
    }

    /**
     * POST /alumno/2fa/webauthn/register/verify
     */
    public function registerVerify(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $challenge = Session::get('webauthn_challenge');
            $challengeTime = Session::get('webauthn_challenge_time');

            if (!$challenge || (time() - $challengeTime > 120)) {
                throw new \Exception('El registro ha expirado. Por favor, inténtalo de nuevo.');
            }

            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $attestationObject = $this->base64url_decode($input['attestationObject']);
            
            // UV=false para máxima compatibilidad
            $data = $this->webauthn->processCreate($clientDataJSON, $attestationObject, $challenge, false, true, false);
            
            $credentialId = base64_encode($data->credentialId);
            $publicKey = base64_encode($data->credentialPublicKey);
            
            $this->credentialModel->create([
                'user_id' => Auth::user()['id'],
                'credential_id' => $credentialId,
                'public_key' => $publicKey,
                'sign_count' => $data->signatureCounter,
                'device_name' => $input['device_name'] ?? 'Llave de seguridad'
            ]);

            Session::delete('webauthn_challenge');
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            error_log("WebAuthn registerVerify error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * GET /auth/2fa/webauthn/options
     */
    public function authOptions(): void
    {
        header('Content-Type: application/json');
        
        try {
            $userId = Session::get('pending_webauthn_user_id');
            if (!$userId) {
                throw new \Exception('Sesión no válida para WebAuthn.');
            }

            $creds = $this->credentialModel->findByUserId($userId);
            if (empty($creds)) {
                throw new \Exception('No tienes llaves de seguridad registradas.');
            }

            $allowedCredentials = array_map(fn($c) => base64_decode($c['credential_id']), $creds);

            $options = $this->webauthn->getGetArgs($allowedCredentials, 60000, true, true, true, true, true, 'preferred');
            $publicKey = $options->publicKey;

            // WA-01: Challenge con expiración
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_time', time());

            // Encode for client
            $publicKey->challenge = $this->base64url_encode($publicKey->challenge);
            if (!empty($publicKey->allowCredentials)) {
                foreach ($publicKey->allowCredentials as &$c) {
                    $c->id = $this->base64url_encode($c->id);
                }
            }

            echo json_encode($publicKey);
        } catch (\Throwable $e) {
            error_log("WebAuthn authOptions error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * POST /auth/2fa/webauthn/verify
     */
    public function authVerify(): void
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = Session::get('pending_webauthn_user_id');
            $challenge = Session::get('webauthn_challenge');
            $challengeTime = Session::get('webauthn_challenge_time');

            if (!$userId || !$challenge || (time() - $challengeTime > 120)) {
                throw new \Exception('La sesión ha expirado.');
            }

            $credentialIdRaw = $this->base64url_decode($input['id']);
            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $authenticatorData = $this->base64url_decode($input['authenticatorData']);
            $signature = $this->base64url_decode($input['signature']);

            $dbCred = $this->credentialModel->findByCredentialIdAndUserId(base64_encode($credentialIdRaw), $userId);
            if (!$dbCred) {
                throw new \Exception('Llave de seguridad no reconocida.');
            }

            $publicKey = base64_decode($dbCred['public_key']);
            $prevCount = (int)$dbCred['sign_count'];

            $this->webauthn->processGet(
                $clientDataJSON, 
                $authenticatorData, 
                $signature, 
                $publicKey, 
                $challenge, 
                $prevCount, 
                false, 
                false
            );

            $this->credentialModel->updateSignCount(base64_encode($credentialIdRaw), $prevCount + 1);

            $user = $this->userModel->find($userId);
            Auth::login($user);
            
            Session::delete('pending_webauthn_user_id');
            Session::delete('webauthn_challenge');
            
            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            error_log("WebAuthn authVerify error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    public function showVerify(): void
    {
        if (!Session::get('pending_webauthn_user_id')) {
            header('Location: /login');
            exit;
        }
        
        \App\Core\View::render('auth/webauthn_verify', [
            'title' => 'Verificación Biométrica'
        ]);
    }

    public function fallback(): void
    {
        $userId = Session::get('pending_webauthn_user_id');
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        Session::set('pending_2fa_user_id', $userId);
        header('Location: /auth/2fa');
        exit;
    }

    public function deleteCredential(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID no proporcionado.');
            }

            $userId = Auth::user()['id'];
            $success = $this->credentialModel->deleteByIdAndUserId((int)$id, $userId);

            echo json_encode(['success' => $success]);
        } catch (\Throwable $e) {
            $this->sendError($e->getMessage());
        }
    }

    // --- Helpers ---

    private function base64url_encode($buffer)
    {
        if (is_object($buffer) && method_exists($buffer, 'getBinaryString')) {
            $buffer = $buffer->getBinaryString();
        }
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($buffer));
    }

    private function base64url_decode($base64)
    {
        $padding = strlen($base64) % 4;
        if ($padding) {
            $base64 .= str_repeat('=', 4 - $padding);
        }
        return base64_decode(strtr($base64, '-_', '+/'));
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

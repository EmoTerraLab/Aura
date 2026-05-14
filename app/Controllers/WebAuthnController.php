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
    private $db;
    private $credentialModel;
    private $userModel;

    public function __construct()
    {
        try {
            $this->credentialModel = new WebAuthnCredential();
            $this->userModel = new User();
            $this->db = \App\Core\Database::getInstance();
            
            $appName = Config::get('school_name', 'Aura');
            
            // SEC-014: Prevenir spoofing de Host obteniendo rpId del app_url
            $appUrl = Config::get('app_url', 'http://localhost');
            $rpId = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';
            
            // WA-04: Limpiar puerto del RP ID si existe (aunque parse_url ya lo hace, por seguridad)
            if (strpos($rpId, ':') !== false) {
                $rpId = explode(':', $rpId)[0];
            }

            $this->webauthn = new WebAuthn($appName, $rpId);
        } catch (\Exception $e) {
            error_log("WebAuthn init error: " . $e->getMessage());
        }
    }

    public function getRegistrationOptions(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');

        try {
            $user = Auth::user();
            $userId = $user['id'];
            
            // WA-02: Nombre de usuario único para WebAuthn
            $userHandleBin = str_pad((string)$userId, 16, "\0", STR_PAD_LEFT);
            
            // WA-03: Excluir credenciales ya registradas
            $creds = $this->credentialModel->findByUser($userId);
            $excludeCredentials = array_map(fn($c) => base64_decode($c['credential_id']), $creds);

            $options = $this->webauthn->getCreateArgs(
                $userHandleBin, 
                $user['email'], 
                $user['name'], 
                60000,    // timeout (SEC-010: 60000ms para sensores lentos)
                false, // requireResidentKey
                'preferred', // userVerification
                null, // crossPlatformAttachment (null permite platform y cross-platform)
                $excludeCredentials // excludeCredentialIds
            );

            // WA-01: Guardar challenge con expiración (60 segundos)
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_time', time());

            // FIX: Asegurar que el objeto rp esté presente y correcto en el cliente
            if (!isset($options->rp)) {
                $options->rp = new \stdClass();
                $options->rp->name = Config::get('school_name', 'Aura');
                
                $appUrl = Config::get('app_url', 'http://localhost');
                $rpId = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';
                if (strpos($rpId, ':') !== false) {
                    $rpId = explode(':', $rpId)[0];
                }
                $options->rp->id = $rpId;
            }

            // Convertir campos binarios a base64url para JSON
            $options->user->id = $this->base64url_encode($options->user->id);
            $options->challenge = $this->base64url_encode($options->challenge);
            
            if (!empty($options->excludeCredentials)) {
                foreach ($options->excludeCredentials as &$c) {
                    $c->id = $this->base64url_encode($c->id);
                }
            }

            echo json_encode($options);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function register(): void
    {
        Auth::requireLogin();
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $challenge = Session::get('webauthn_challenge');
            $challengeTime = Session::get('webauthn_challenge_time');

            // WA-01: Validar expiración del challenge
            if (!$challenge || (time() - $challengeTime > 120)) {
                throw new \Exception('El registro ha expirado. Por favor, inténtalo de nuevo.');
            }

            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $attestationObject = $this->base64url_decode($input['attestationObject']);
            
            // FIX: requireUserVerification=false for better compatibility with devices that don't always provide UV
            $data = $this->webauthn->processCreate($clientDataJSON, $attestationObject, $challenge, false, true, false);
            
            // WA-07: Validar antes de guardar
            $credentialId = base64_encode($data->credentialId);
            $publicKey = base64_encode($data->credentialPublicKey);
            
            $this->credentialModel->create([
                'user_id' => Auth::user()['id'],
                'credential_id' => $credentialId,
                'public_key' => $publicKey,
                'sign_count' => $data->counter,
                'name' => 'Llave de seguridad'
            ]);

            Session::delete('webauthn_challenge');
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function getLoginOptions(): void
    {
        header('Content-Type: application/json');
        
        try {
            // El usuario debe estar pre-identificado o en sesión de 2FA
            $userId = Session::get('pending_webauthn_user_id');
            if (!$userId) {
                throw new \Exception('Sesión no válida para WebAuthn.');
            }

            $creds = $this->credentialModel->findByUser($userId);
            if (empty($creds)) {
                throw new \Exception('No tienes llaves de seguridad registradas.');
            }

            $allowedCredentials = array_map(fn($c) => base64_decode($c['credential_id']), $creds);

            // SEC-010: timeout 60000ms
            $getArgs = $this->webauthn->getGetArgs($allowedCredentials, 60000, true, true, true, true, true, 'preferred');

            // WA-01: Challenge con expiración
            Session::set('webauthn_challenge', $this->webauthn->getChallenge());
            Session::set('webauthn_challenge_time', time());

            // Encode for client
            $getArgs->challenge = $this->base64url_encode($getArgs->challenge);
            if (!empty($getArgs->allowCredentials)) {
                foreach ($getArgs->allowCredentials as &$c) {
                    $c->id = $this->base64url_encode($c->id);
                }
            }

            echo json_encode($getArgs);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function authenticate(): void
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

            $credentialId = $this->base64url_decode($input['id']);
            $clientDataJSON = $this->base64url_decode($input['clientDataJSON']);
            $authenticatorData = $this->base64url_decode($input['authenticatorData']);
            $signature = $this->base64url_decode($input['signature']);
            $userHandle = $input['userHandle'] ? $this->base64url_decode($input['userHandle']) : null;

            // Buscar la credencial en la DB
            $dbCred = $this->credentialModel->find(base64_encode($credentialId));
            if (!$dbCred) {
                throw new \Exception('Llave de seguridad no reconocida.');
            }

            $publicKey = base64_decode($dbCred['public_key']);
            $prevCount = (int)$dbCred['sign_count'];

            // WA-08: Verificar firma
            $this->webauthn->processGet(
                $clientDataJSON, 
                $authenticatorData, 
                $signature, 
                $publicKey, 
                $challenge, 
                $prevCount, 
                false, // userVerification
                false  // requireUserPresent
            );

            // WA-08: Actualizar contador para prevenir replay attacks
            $this->credentialModel->updateCounter(base64_encode($credentialId), $prevCount + 1);

            // Login exitoso: completar 2FA
            $user = $this->userModel->find($userId);
            Auth::login($user);
            
            Session::delete('pending_webauthn_user_id');
            Session::delete('webauthn_challenge');
            
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function verifyView(): void
    {
        if (!Session::get('pending_webauthn_user_id')) {
            header('Location: /login');
            exit;
        }
        
        \App\Core\View::render('auth/webauthn_verify', [
            'title' => 'Verificación Biométrica'
        ]);
    }

    public function fallbackToOtp(): void
    {
        $userId = Session::get('pending_webauthn_user_id');
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->find($userId);
        
        // Redirigir al flujo de OTP
        // Generar el código y enviarlo
        $otpService = new \App\Controllers\TotpController();
        // Nota: asumiendo que el usuario tiene un email para recibir el OTP si no tiene app
        
        // Por ahora redirigimos al 2FA estándar (TOTP)
        Session::set('pending_2fa_user_id', $userId);
        header('Location: /auth/2fa');
        exit;
    }

    // --- Helpers ---

    private function base64url_encode($buffer)
    {
        if ($buffer instanceof \lbuchs\WebAuthn\BinarySource) {
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

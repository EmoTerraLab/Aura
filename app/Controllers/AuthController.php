<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\OTPCode;
use App\Core\View;
use App\Core\Auth;
use App\Core\Csrf;

class AuthController {
    private $userModel;
    private $otpModel;
    private $mailer;

    public function __construct(\App\Models\User $userModel, \App\Models\OTPCode $otpModel, \App\Core\Mailer $mailer = null) {
        $this->userModel = $userModel;
        $this->otpModel = $otpModel;
        $this->mailer = $mailer;
    }

    public function showLogin() {
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $data = ['title' => 'Aura - Iniciar Sesión'];
        
        // Comprobar si venimos de un fallback de WebAuthn
        if (\App\Core\Session::get('force_otp_for_user')) {
            $data['force_otp_email'] = \App\Core\Session::get('force_otp_for_user');
            \App\Core\Session::remove('force_otp_for_user');
        }

        View::render('auth/login', $data);
    }

    public function loginStaff() {
        Csrf::validateRequest();
        $data = json_decode(file_get_contents('php://input'), true);
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] !== 'alumno' && password_verify($password, $user['password'])) {
            if (!empty($user['totp_enabled'])) {
                \App\Core\Session::set('pending_2fa_user_id', $user['id']);
                echo json_encode([
                    'ok' => true,
                    'redirect' => '/auth/2fa/totp'
                ]);
                return;
            }

            Auth::login($user);
            echo json_encode([
                'ok' => true,
                'redirect' => $user['role'] === 'admin' ? '/admin' : '/staff/inbox'
            ]);
            return;
        }

        echo json_encode(['ok' => false, 'error' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);
    }

    public function generateOTP() {
        Csrf::validateRequest();
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $forceOtp = !empty($data['force_otp']);
        
        if ($this->isRateLimited($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')) {
            http_response_code(429);
            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] === 'alumno') {
            // Comprobar si tiene WebAuthn configurado
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM webauthn_credentials WHERE user_id = ?');
            $stmt->execute([$user['id']]);
            $hasWebAuthn = $stmt->fetch()['count'] > 0;

            if ($hasWebAuthn && !$forceOtp && \App\Core\Config::get('2fa_students_method', 'otp_email') === 'webauthn') {
                \App\Core\Session::set('pending_webauthn_user_id', $user['id']);
                echo json_encode([
                    'ok' => true,
                    'webauthn' => true,
                    'redirect' => '/auth/2fa/webauthn'
                ]);
                return;
            }

            // Fallback a OTP por email
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $this->otpModel->create($user['id'], $code);

            // Envío real si el mailer está configurado
            if ($this->mailer) {
                try {
                    $schoolName = \App\Core\Config::get('school_name', 'Aura');
                    $subject = "Tu código de acceso - {$schoolName}";
                    $body = "
                        <h2>Tu código de acceso</h2>
                        <p>Hola,</p>
                        <p>Has solicitado un código de acceso para entrar en <strong>{$schoolName}</strong>.</p>
                        <p style='font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #4F46E5;'>{$code}</p>
                        <p>Este código caducará en 10 minutos por tu seguridad.</p>
                        <p>Si no has solicitado este código, puedes ignorar este mensaje.</p>
                    ";
                    $this->mailer->send($email, $subject, $body);
                } catch (\Exception $e) {
                    error_log("Error enviando OTP a {$email}: " . $e->getMessage());
                    // En desarrollo permitimos continuar, en prod fallamos si no se puede enviar
                    if (APP_ENV !== 'dev') {
                        echo json_encode(['ok' => false, 'error' => 'No se pudo enviar el correo. Por favor, contacta con soporte.']);
                        return;
                    }
                }
            }

            // Registro en error_log para depuración
            error_log("OTP generado para {$email}: {$code}");

            $response = ['ok' => true];
            if (APP_ENV === 'dev') {
                $response['dev_code'] = $code;
            }
            echo json_encode($response);
            return;
        }

        echo json_encode(['ok' => false, 'error' => 'No se encontró un alumno con ese correo.']);
    }

    public function verifyOTP() {
        Csrf::validateRequest();
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $code = $data['code'] ?? '';
        
        if ($this->isRateLimited($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')) {
            http_response_code(429);
            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] === 'alumno') {
            $validOtp = $this->otpModel->findValidCode($user['id'], $code);

            if ($validOtp) {
                $this->otpModel->markAsUsed($validOtp['id']);
                Auth::login($user);
                echo json_encode([
                    'ok' => true,
                    'redirect' => '/alumno/dashboard'
                ]);
                return;
            }
        }

        echo json_encode(['ok' => false, 'error' => 'Código inválido o expirado.']);
    }

    private function isRateLimited($ip) {
        // Implementación básica de rate limiting (5 intentos / 15 min)
        $db = \App\Core\Database::getInstance();
        $db->exec("CREATE TABLE IF NOT EXISTS rate_limits (ip TEXT, attempts INTEGER, last_attempt DATETIME)");
        
        // Limpiar antiguos
        $db->exec("DELETE FROM rate_limits WHERE last_attempt < datetime('now', '-15 minutes')");
        
        $stmt = $db->prepare("SELECT attempts FROM rate_limits WHERE ip = :ip");
        $stmt->execute(['ip' => $ip]);
        $record = $stmt->fetch();
        
        if ($record) {
            if ($record['attempts'] >= 5) {
                return true;
            }
            $stmt = $db->prepare("UPDATE rate_limits SET attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP WHERE ip = :ip");
            $stmt->execute(['ip' => $ip]);
        } else {
            $stmt = $db->prepare("INSERT INTO rate_limits (ip, attempts, last_attempt) VALUES (:ip, 1, CURRENT_TIMESTAMP)");
            $stmt->execute(['ip' => $ip]);
        }
        
        return false;
    }

    public function logout() {
        Auth::logout();
        header('Location: /login');
    }

    private function redirectBasedOnRole() {
        $role = Auth::role();
        if ($role === 'alumno') {
            header('Location: /alumno/dashboard');
        } elseif ($role === 'admin') {
            header('Location: /admin');
        } else {
            header('Location: /staff/inbox');
        }
    }
}

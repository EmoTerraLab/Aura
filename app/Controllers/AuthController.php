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

    public function __construct(\App\Models\User $userModel, \App\Models\OTPCode $otpModel) {
        $this->userModel = $userModel;
        $this->otpModel = $otpModel;
    }

    public function showLogin() {
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }
        View::render('auth/login', ['title' => 'Aura - Iniciar Sesión']);
    }

    public function loginStaff() {
        Csrf::validateRequest();
        $data = json_decode(file_get_contents('php://input'), true);
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] !== 'alumno' && password_verify($password, $user['password'])) {
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
        
        if ($this->isRateLimited($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')) {
            http_response_code(429);
            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] === 'alumno') {
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $this->otpModel->create($user['id'], $code);

            // Registro en error_log simulando envío de mail según spec
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

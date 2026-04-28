<?php
namespace App\Controllers;

use App\Core\Csrf;
use App\Core\View;
use App\Core\Lang;
use App\Core\Mailer;
use App\Core\Config;
use App\Models\User;
use App\Models\PasswordReset;

class PasswordResetController
{
    private $userModel;
    private $passwordResetModel;
    private $mailer;

    public function __construct(User $userModel, PasswordReset $passwordResetModel, Mailer $mailer)
    {
        $this->userModel = $userModel;
        $this->passwordResetModel = $passwordResetModel;
        $this->mailer = $mailer;
    }

    public function showRequestForm(): void
    {
        View::render('auth/password_forgot', ['title' => Lang::t('auth.forgot_password_title')]);
    }

    public function sendResetLink(): void
    {
        Csrf::validateRequest();

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /password/forgot?error=invalid_email');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $this->passwordResetModel->invalidatePrevious($email);
            $token = bin2hex(random_bytes(32));

            $this->passwordResetModel->create([
                'email'      => $email,
                'token'      => $token,
                'expires_at' => date('Y-m-d H:i:s', time() + 1800)
            ]);

            $resetUrl = $this->getBaseUrl() . '/password/reset?token=' . $token;
            $schoolName = Config::get('school_name', 'Aura PDP');
            $subject = Lang::t('auth.reset_email_subject');
            $body = $this->buildResetEmailBody($user['name'], $resetUrl, $schoolName);
            try {
                $this->mailer->send($email, $subject, $body);
            } catch (\Exception $e) {
                // Loguear error pero permitir continuar en entorno local
                error_log("Error enviando correo de recuperación: " . $e->getMessage());
            }
        }

        header('Location: /password/forgot?sent=1');
        exit;
    }

    public function showResetForm(): void
    {
        $token = $_GET['token'] ?? '';
        $record = $this->passwordResetModel->findValidToken($token);

        if (!$record) {
            View::render('auth/password_reset_invalid', ['title' => Lang::t('auth.reset_link_invalid')]);
            return;
        }

        View::render('auth/password_reset_form', ['title' => Lang::t('auth.reset_new_password'), 'token' => $token ?? $token_val ?? '', 'errors' => $errors ?? []]);
    }

    public function resetPassword(): void
    {
        Csrf::validateRequest();

        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $record = $this->passwordResetModel->findValidToken($token);
        if (!$record) {
            header('Location: /password/forgot?error=expired');
            exit;
        }

        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = Lang::t('auth.password_min_length');
        }
        if ($password !== $confirm) {
            $errors[] = Lang::t('auth.password_mismatch');
        }

        if (!empty($errors)) {
            $token_val = htmlspecialchars($token);
            View::render('auth/password_reset_form', ['title' => Lang::t('auth.reset_new_password'), 'token' => $token ?? $token_val ?? '', 'errors' => $errors ?? []]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->userModel->updatePassword($record['email'], $hash);
        $this->passwordResetModel->markAsUsed($token);

        header('Location: /login?password_reset=1');
        exit;
    }

    private function buildResetEmailBody(string $name, string $resetUrl, string $schoolName): string
    {
        $nameEsc = htmlspecialchars($name);
        $schoolEsc = htmlspecialchars($schoolName);
        
        return "
        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 2rem;'>
            <h2 style='color: #4F46E5; margin-top: 0;'>{$schoolEsc}</h2>
            <p>Hola, <strong>{$nameEsc}</strong></p>
            <p>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón para continuar:</p>
            <p style='text-align: center; margin: 2.5rem 0;'>
                <a href='{$resetUrl}'
                   style='background: #4F46E5; color: white; padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block;'>
                    Restablecer contraseña
                </a>
            </p>
            <p style='color: #6b7280; font-size: 0.9rem;'>Este enlace expira en 30 minutos. Si no solicitaste esto, ignora este mensaje.</p>
            <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0;'>
            <p style='color: #6b7280; font-size: 0.8rem;'>Si el botón no funciona, copia este enlace en tu navegador:<br>
                <a href='{$resetUrl}' style='color: #4F46E5;'>{$resetUrl}</a>
            </p>
        </div>
        ";
    }

    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
}

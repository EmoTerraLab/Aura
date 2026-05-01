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
            $schoolName = Config::get('school_name', 'Aura');
            $subject = Lang::t('auth.reset_email_subject', ['school' => $schoolName]);
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
        $resetUrlEsc = htmlspecialchars($resetUrl);
        $expiresInMinutes = 30;

        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <meta name="x-apple-disable-message-reformatting">
    <title>Restablece tu contraseña de Aura</title>
    <style>
    /* Reseteo básico de cliente de correo */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

    @media (prefers-color-scheme: dark) {
      body, .email-bg  { background-color: #111827 !important; }
      .email-card      { background-color: #1f2937 !important; border-color: #374151 !important; }
      .email-text      { color: #f9fafb !important; }
      .email-muted     { color: #9ca3af !important; }
      .email-divider   { border-color: #374151 !important; }
      .url-box         { background-color: #111827 !important; border-color: #374151 !important; color: #a78bfa !important; }
      .footer-text     { color: #6b7280 !important; }
    }
    @media (max-width: 600px) {
      .email-card    { padding: 1.5rem !important; }
      .email-wrapper { width: 100% !important; padding: 0 1rem !important; }
    }
    </style>
    </head>
    <body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Helvetica, Arial, sans-serif;-webkit-font-smoothing:antialiased;" class="email-bg">

    <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;color:#f3f4f6;line-height:1px;">
    Recibiste una solicitud para cambiar tu contraseña. El enlace expira en ' . $expiresInMinutes . ' minutos. 
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
    <tr>
      <td align="center" style="padding:2rem 1rem;">

        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-wrapper" style="max-width:600px;width:100%;margin:0 auto;" role="presentation">

          <tr>
            <td style="padding-bottom:1.25rem;">
              <img src="https://app.aura.emoterralab.com/icono-sinfondo.png" alt="Aura Logo" width="30" height="30" style="width:30px;height:30px;vertical-align:middle;margin-right:8px;display:inline-block;">
              <span style="font-size:1.1rem;font-weight:700;color:#4f46e5;vertical-align:middle;">Aura</span>
            </td>
          </tr>

          <tr>
            <td class="email-card" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;padding:2rem;">

              <h2 style="color:#4f46e5;margin-top:0;margin-bottom:1.25rem;font-size:1.2rem;">' . $schoolEsc . '</h2>

              <p class="email-text" style="margin:0 0 0.75rem;color:#111827;">Hola, <strong>' . $nameEsc . '</strong>,</p>

              <p class="email-text" style="margin:0 0 2rem;color:#111827;line-height:1.6;">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el botón para continuar:
              </p>

              <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                <tr>
                  <td align="center" style="padding-bottom:2rem;">
                    <a href="' . $resetUrlEsc . '" style="background:#4f46e5;color:#ffffff;padding:14px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;border:1px solid #4f46e5;">
                      Restablecer contraseña
                    </a>
                  </td>
                </tr>
              </table>

              <p class="email-muted" style="color:#6b7280;font-size:0.875rem;margin:0 0 1.5rem;line-height:1.6;">
                Este enlace expira en <strong>' . $expiresInMinutes . ' minutos</strong>. Si no solicitaste esto, ignora este mensaje.
              </p>

              <hr class="email-divider" style="border:0;border-top:1px solid #e5e7eb;margin:0 0 1.25rem;">

              <p class="email-muted" style="color:#6b7280;font-size:0.8rem;margin:0 0 0.5rem;line-height:1.6;">
                Si el botón no funciona, copia este enlace en tu navegador:
              </p>

              <div class="url-box" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:4px;padding:0.6rem 0.75rem;margin:0 0 1.5rem;font-size:0.75rem;font-family:monospace;color:#4f46e5;word-break:break-all;line-height:1.5;">
                <a href="' . $resetUrlEsc . '" style="color:#4f46e5;text-decoration:none;">' . $resetUrlEsc . '</a>
              </div>

              <hr class="email-divider" style="border:0;border-top:1px solid #e5e7eb;margin:0 0 1.25rem;">

              <p class="footer-text" style="color:#6b7280;font-size:0.75rem;margin:0;line-height:1.6;">
                Este email fue enviado por <strong>' . $schoolEsc . '</strong> a través de Aura. Si no esperabas este correo, puedes ignorarlo.<br><br>
                <img src="https://app.aura.emoterralab.com/icono-sinfondo.png" alt="" width="12" height="12" style="vertical-align:middle;margin-right:3px;">
                <strong style="color:#4f46e5;">Aura</strong> &middot; Powered by <a href="https://emoterralab.com" style="color:#7c3aed;text-decoration:none;">EmoTerraLab</a>
              </p>

            </td>
          </tr>

        </table>

        </td>
    </tr>
    </table>

    </body>
    </html>';
    }
    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
}

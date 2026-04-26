<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Core\Config;
use OTPHP\TOTP;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TotpController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // GET /profile/2fa/totp/setup
    public function setup(): void
    {
        $user = Auth::user();
        if ($user['totp_enabled']) {
            header('Location: /staff/inbox'); // Or user profile
            exit;
        }

        // Generar un TOTP temporal (no guardado aún)
        $totp = TOTP::create();
        $appName = Config::get('school_name', 'Aura PDP');
        $totp->setLabel($user['email']);
        $totp->setIssuer($appName);

        // Guardar secret temporal en sesión
        Session::set('temp_totp_secret', $totp->getSecret());

        // Generar QR en formato SVG inline
        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($totp->getProvisioningUri());

        View::render('auth/totp_setup', [
            'title' => 'Configurar 2FA (TOTP)',
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $totp->getSecret()
        ], 'app');
    }

    // POST /profile/2fa/totp/activate
    public function activate(): void
    {
        Csrf::validateRequest();

        $code = $_POST['code'] ?? '';
        $secret = Session::get('temp_totp_secret');

        if (!$secret || empty($code)) {
            header('Location: /profile/2fa/totp/setup?error=missing_data');
            exit;
        }

        $totp = TOTP::create($secret);
        
        // Verificar el código (con ventana pequeña de tolerancia)
        if ($totp->verify($code)) {
            $userId = Auth::id();

            // Guardar secret en BD
            $stmt = $this->db->prepare('UPDATE users SET totp_secret = ?, totp_enabled = 1, totp_verified_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$secret, $userId]);

            // Eliminar secret temporal
            Session::remove('temp_totp_secret');

            // Generar códigos de recuperación (8 códigos de 8 caracteres alfanuméricos)
            $stmtCodes = $this->db->prepare('INSERT INTO totp_recovery_codes (user_id, code) VALUES (?, ?)');
            
            $recoveryCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $rawCode = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
                $recoveryCodes[] = $rawCode;
                // Guardar hasheado por seguridad
                $stmtCodes->execute([$userId, password_hash($rawCode, PASSWORD_DEFAULT)]);
            }

            // Mostrar página final de códigos de recuperación (solo una vez)
            View::render('auth/totp_recovery', [
                'title' => 'Códigos de Recuperación 2FA',
                'recoveryCodes' => $recoveryCodes
            ], 'app');
        } else {
            header('Location: /profile/2fa/totp/setup?error=invalid_code');
            exit;
        }
    }

    // POST /profile/2fa/totp/disable
    public function disable(): void
    {
        Csrf::validateRequest();
        
        // Se requiere la contraseña actual por seguridad (por hacer en UI del perfil)
        // Por simplificar, si está autenticado lo desactiva. 
        $userId = Auth::id();

        $stmt = $this->db->prepare('UPDATE users SET totp_secret = NULL, totp_enabled = 0, totp_verified_at = NULL WHERE id = ?');
        $stmt->execute([$userId]);

        // Limpiar códigos
        $stmtDel = $this->db->prepare('DELETE FROM totp_recovery_codes WHERE user_id = ?');
        $stmtDel->execute([$userId]);

        header('Location: /staff/inbox'); // o página de perfil
        exit;
    }

    // GET /auth/2fa/totp
    public function showVerify(): void
    {
        if (!Session::get('pending_2fa_user_id')) {
            header('Location: /login');
            exit;
        }
        
        View::render('auth/totp_verify', [
            'title' => 'Verificación 2FA'
        ], 'app');
    }

    // POST /auth/2fa/totp/verify
    public function verifyLogin(): void
    {
        Csrf::validateRequest();

        $pendingUserId = Session::get('pending_2fa_user_id');
        if (!$pendingUserId) {
            header('Location: /login');
            exit;
        }

        $code = $_POST['totp_code'] ?? '';
        $recoveryCode = $_POST['recovery_code'] ?? '';

        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$pendingUserId]);
        $user = $stmt->fetch();

        if (!$user || !$user['totp_enabled']) {
            Session::remove('pending_2fa_user_id');
            header('Location: /login');
            exit;
        }

        $isValid = false;

        if (!empty($code)) {
            $totp = TOTP::create($user['totp_secret']);
            $isValid = $totp->verify($code);
        } elseif (!empty($recoveryCode)) {
            // Comprobar código de recuperación
            $stmtRec = $this->db->prepare('SELECT id, code FROM totp_recovery_codes WHERE user_id = ? AND used = 0');
            $stmtRec->execute([$user['id']]);
            $codes = $stmtRec->fetchAll();

            foreach ($codes as $rc) {
                if (password_verify($recoveryCode, $rc['code'])) {
                    $isValid = true;
                    // Marcarlo como usado
                    $stmtMark = $this->db->prepare('UPDATE totp_recovery_codes SET used = 1 WHERE id = ?');
                    $stmtMark->execute([$rc['id']]);
                    break;
                }
            }
        }

        if ($isValid) {
            Session::remove('pending_2fa_user_id');
            Auth::login($user);
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: /admin');
            } else {
                header('Location: /staff/inbox');
            }
        } else {
            // Invalid code
            header('Location: /auth/2fa/totp?error=invalid');
        }
        exit;
    }
}

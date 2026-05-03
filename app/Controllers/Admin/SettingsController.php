<?php
namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Lang;
use App\Core\View;
use App\Models\Setting;

class SettingsController
{
    private Setting $settings;

    public function __construct(Setting $settings)
    {
        $this->settings = $settings;
    }

    // GET /admin/settings
    public function index(): void
    {
        $all = $this->settings->getAll();
        $tab = $_GET['tab'] ?? 'school';
        
        // P1 FIX: Whitelist de pestañas para prevenir LFI y accesos no deseados
        $allowedTabs = ['school', 'appearance', 'mail', 'security', 'protocol', 'ccaa'];
        if (!in_array($tab, $allowedTabs)) {
            $tab = 'school';
        }
        
        // Transform the key-value array for the view
        $settingsAssoc = [];
        foreach ($all as $key => $data) {
            $settingsAssoc[$key] = $data['value'];
        }

        View::render('admin/settings/index', [
            'title' => 'Configuración - Aura Admin',
            'tab' => $tab,
            'settings' => $settingsAssoc
        ], 'app');
    }

    // POST /admin/settings/school
    public function saveSchool(): void
    {
        Csrf::validateRequest();
        $this->settings->setMany([
            'school_name'          => trim($_POST['school_name'] ?? ''),
            'school_logo_url'      => trim($_POST['school_logo_url'] ?? ''),
            'school_contact_email' => trim($_POST['school_contact_email'] ?? ''),
            'school_website'       => trim($_POST['school_website'] ?? ''),
            'school_address'       => trim($_POST['school_address'] ?? '')
        ]);
        header('Location: /admin/settings?tab=school&saved=1');
        exit;
    }
    
    // POST /admin/settings/appearance
    public function saveAppearance(): void
    {
        Csrf::validateRequest();
        $this->settings->setMany([
            'footer_text'          => trim($_POST['footer_text'] ?? ''),
            'app_primary_color'    => trim($_POST['app_primary_color'] ?? '#004f56'),
            'app_accent_color'     => trim($_POST['app_accent_color'] ?? '#066972'),
        ]);
        header('Location: /admin/settings?tab=appearance&saved=1');
        exit;
    }

    // POST /admin/settings/mail
    public function saveMail(): void
    {
        Csrf::validateRequest();
        $data = [
            'mail_driver'       => trim($_POST['mail_driver'] ?? 'smtp'),
            'mail_host'         => trim($_POST['mail_host'] ?? ''),
            'mail_port'         => trim($_POST['mail_port'] ?? '587'),
            'mail_encryption'   => trim($_POST['mail_encryption'] ?? 'tls'),
            'mail_username'     => trim($_POST['mail_username'] ?? ''),
            'mail_from_address' => trim($_POST['mail_from_address'] ?? ''),
            'mail_from_name'    => trim($_POST['mail_from_name'] ?? ''),
        ];
        // Solo guardar contraseña si se ha rellenado (no sobreescribir con vacío)
        if (!empty($_POST['mail_password'])) {
            $data['mail_password'] = $_POST['mail_password'];
        }
        $this->settings->setMany($data);
        header('Location: /admin/settings?tab=mail&saved=1');
        exit;
    }

    // POST /admin/settings/mail/test
    public function testMail(): void
    {
        Csrf::validateRequest();
        $to = trim($_POST['test_email'] ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            header('Location: /admin/settings?tab=mail&error=invalid_email');
            exit;
        }
        
        // Usar el Mailer del proyecto para enviar un correo de prueba
        try {
            $mailer = new \App\Core\Mailer($this->settings);
            $mailer->send($to, 'Test de correo - Aura', '<p>El servidor de correo está configurado correctamente.</p>');
            header('Location: /admin/settings?tab=mail&test=ok');
        } catch (\Exception $e) {
            header('Location: /admin/settings?tab=mail&test=error&msg=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // POST /admin/settings/security
    public function saveSecurity(): void
    {
        Csrf::validateRequest();
        $this->settings->setMany([
            'default_lang'              => $_POST['default_lang'] ?? 'es',
            'session_lifetime_minutes'  => (int)($_POST['session_lifetime_minutes'] ?? 120),
            'max_login_attempts'        => (int)($_POST['max_login_attempts'] ?? 5),
            '2fa_students_method'       => trim($_POST['2fa_students_method'] ?? 'webauthn'),
            '2fa_staff_method'          => trim($_POST['2fa_staff_method'] ?? 'totp'),
        ]);
        header('Location: /admin/settings?tab=security&saved=1');
        exit;
    }

    // POST /admin/settings/protocol
    public function saveProtocol(): void
    {
        Csrf::validateRequest();
        $this->settings->setMany([
            'ccaa_code'             => trim($_POST['ccaa_code'] ?? ''),
            'ccaa_protocol_active'  => $_POST['ccaa_protocol_active'] ?? '0',
            'ccaa_show_to_students' => $_POST['ccaa_show_to_students'] ?? '0',
        ]);
        header('Location: /admin/settings?tab=protocol&saved=1');
        exit;
    }
    // POST /admin/settings/ccaa
    public function saveCcaa(): void
    {
        Csrf::validateRequest();
        $this->settings->setMany([
            "ccaa_code"             => trim($_POST["ccaa_code"] ?? ""),
            "ccaa_protocol_active"  => isset($_POST["ccaa_protocol_active"]) ? "1" : "0",
            "ccaa_show_to_students" => isset($_POST["ccaa_show_to_students"]) ? "1" : "0",
        ]);
        header("Location: /admin/settings?tab=ccaa&saved=1");
        exit;
    }

}

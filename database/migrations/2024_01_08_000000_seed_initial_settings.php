<?php
/**
 * Migración: Datos iniciales de settings
 */
class Migration_2024_01_08_000000_seed_initial_settings
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function up(): void
    {
        $settings = [
            ['school_name', 'Mi Escuela', 'text'],
            ['school_logo_url', '', 'text'],
            ['school_contact_email', '', 'text'],
            ['school_website', '', 'text'],
            ['school_address', '', 'text'],
            ['app_primary_color', '#004f56', 'color'],
            ['app_accent_color', '#066972', 'color'],
            ['footer_text', 'Aura powered by EmoTerraLab', 'text'],
            ['default_lang', 'es', 'text'],
            ['mail_driver', 'smtp', 'text'],
            ['mail_host', '', 'text'],
            ['mail_port', '587', 'text'],
            ['mail_encryption', 'tls', 'text'],
            ['mail_username', '', 'text'],
            ['mail_password', '', 'password'],
            ['mail_from_address', '', 'text'],
            ['mail_from_name', 'Aura', 'text'],
            ['2fa_students_method', 'webauthn', 'text'],
            ['2fa_staff_method', 'totp', 'text'],
            ['session_lifetime_minutes', '120', 'text'],
            ['max_login_attempts', '5', 'text']
        ];

        $stmt = $this->db->prepare("INSERT OR IGNORE INTO settings (key, value, type) VALUES (?, ?, ?)");
        foreach ($settings as $s) {
            $stmt->execute($s);
        }
    }
    public function down(): void {}
}

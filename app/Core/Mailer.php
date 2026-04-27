<?php
namespace App\Core;

use App\Models\Setting;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private Setting $settings;

    public function __construct(Setting $settings)
    {
        $this->settings = $settings;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            if ($this->settings->get('mail_driver') === 'smtp') {
                $mail->isSMTP();
                $mail->Host       = $this->settings->get('mail_host');
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->settings->get('mail_username');
                $mail->Password   = $this->settings->get('mail_password');
                $mail->SMTPSecure = $this->settings->get('mail_encryption') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int)$this->settings->get('mail_port', '587');
            } else {
                $mail->isMail();
            }

            // Recipients
            $fromAddress = $this->settings->get('mail_from_address', 'noreply@aura.com');
            $fromName = $this->settings->get('mail_from_name', 'Aura');
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            return $mail->send();
        } catch (Exception $e) {
            error_log("No se pudo enviar el correo a {$to}. Error: {$mail->ErrorInfo}");
            throw new \Exception("Mailer Error: {$mail->ErrorInfo}");
        }
    }
}

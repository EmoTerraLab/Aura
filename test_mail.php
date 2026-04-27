<?php
require __DIR__ . '/vendor/autoload.php';

// Definir constante APP_ENV si no existe
if (!defined('APP_ENV')) define('APP_ENV', 'prod');

$db = \App\Core\Database::getInstance();
$settingModel = new \App\Models\Setting();
$mailer = new \App\Core\Mailer($settingModel);

$email = 'brahimcah@gmail.com';
$subject = 'Prueba de Conexión SMTP - Aura';
$body = '<h1>Prueba de Aura</h1><p>Si recibes esto, la configuración de correo es correcta.</p>';

echo "Intentando enviar correo a $email...\n";

try {
    if ($mailer->send($email, $subject, $body)) {
        echo "✅ Correo enviado con éxito.\n";
    } else {
        echo "❌ Falló el envío del correo.\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

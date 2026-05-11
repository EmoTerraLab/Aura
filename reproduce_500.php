<?php
require_once __DIR__ . '/vendor/autoload.php';

// Mock objects
class MockSetting extends \App\Models\Setting {
    public function get($key, $default = '') { return $default; }
}

// Iniciar sesión
\App\Core\Session::start();

$userModel = new \App\Models\User();
$otpModel = new \App\Models\OTPCode();
$mailer = new \App\Core\Mailer(new MockSetting());

$authController = new \App\Controllers\AuthController($userModel, $otpModel, $mailer);

// Simular petición
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_X_CSRF_TOKEN'] = 'test'; // Needs to match session
$_SESSION['csrf_token'] = 'test';

// Capturar salida
ob_start();
try {
    // Simular php://input (un poco difícil de mockear sin extensiones)
    // Pero podemos pasar los datos si el controlador los lee de otra forma o si mockeamos file_get_contents
    echo "Testing generateOTP...\n";
    // $authController->generateOTP(); // Esto fallará porque intenta leer php://input
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
echo ob_get_clean();

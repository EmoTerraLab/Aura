<?php
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Config;
use App\Models\Setting;
use App\Controllers\ProtocolController;
use App\Core\Auth;
use App\Core\Session;

// Setup session/auth mock
Session::start();
$_SESSION['user_id'] = 2; // Orientadora Lucía
$_SESSION['user_role'] = 'orientador';

Config::init(new Setting());

$controller = new ProtocolController();
echo "--- START OF OUTPUT ---\n";
try {
    $controller->getCaseData(2);
} catch (\Throwable $e) {
    echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
echo "\n--- END OF OUTPUT ---\n";

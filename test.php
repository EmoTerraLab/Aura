<?php
require 'vendor/autoload.php'; // Not needed if we use the native autoloader. Let's just use native autoloader.

ini_set('display_errors', 1);
error_reporting(E_ALL);
define('APP_ENV', 'dev');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) { return; }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) { require $file; }
});

\App\Core\Session::start();
$_SESSION['user_id'] = 4; // id of 'alumno@aura.test'
$_SESSION['user_role'] = 'alumno';
$_SESSION['user_name'] = 'Juan Alumno';
$_SESSION['csrf_token'] = 'test-token';

$_POST['csrf_token'] = 'test-token';

// fake the input
$input = [
    'content' => 'Este es un reporte de prueba largo y descriptivo',
    'target' => 'yo_mismo',
    'urgency_level' => 'low',
    'is_anonymous' => true
];
file_put_contents('php://memory', json_encode($input));
// Wait, we can't easily mock php://input this way.
// Let's just override the method or use cURL.

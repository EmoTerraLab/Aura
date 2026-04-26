<?php
// =============================================================================
// Aura PDP — public/index.php
// Punto de entrada de la aplicación
// =============================================================================

// [MEJORA] Control de errores según entorno - nunca exponer trazas en producción
define('APP_ENV', $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development');
define('BASE_URL', '/'); // Cambiar si el proyecto está en un subdirectorio (ej: /aura/)

if (APP_ENV === 'development' || APP_ENV === 'local' || APP_ENV === 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
}

// Cargar dependencias de Composer si existen
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Cargar clases core automáticamente
spl_autoload_register(function ($class) {
    // Convertir App\Core\Database -> ../app/Core/Database.php
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Iniciar sesión
\App\Core\Session::start();

// Inicializar sistema de configuración (DB)
\App\Core\Config::init(new \App\Models\Setting());

// [MEJORA] Inicializar sistema de idiomas (i18n)
\App\Core\Lang::init();

// [MEJORA] Headers de seguridad HTTP - protección contra ataques comunes
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Content-Security-Policy: Permitiendo recursos necesarios (Tailwind, Bootstrap, Google Fonts)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:;");

// Inicializar y ejecutar Router
$router = new \App\Core\Router();

// Cumplimiento: Ejecución de Telemetría (Poor Man's Cron)
\App\Core\Telemetry::checkAndRunCron();

// Cargar rutas
require_once __DIR__ . '/../app/routes.php';

// Resolver la petición actual
$router->resolve($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

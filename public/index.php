<?php
ob_start();
// =============================================================================
// Aura PDP — public/index.php
// Punto de entrada de la aplicación
// =============================================================================

// [MEJORA] Control de errores según entorno - nunca exponer trazas en producción
define('APP_ENV', $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development');
define('BASE_URL', '/'); // Cambiar si el proyecto está en un subdirectorio (ej: /aura/)

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' 
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
    || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');

if (APP_ENV === 'development' || APP_ENV === 'local' || APP_ENV === 'dev') {
    // Si es AJAX, NUNCA mostrar errores en pantalla para no corromper el JSON con HTML (<br><b>...)
    ini_set('display_errors', $isAjax ? 0 : 1);
    ini_set('display_startup_errors', $isAjax ? 0 : 1);
    // Ignorar deprecaciones de dependencias externas (vendor) que rompen PHP 8.2+
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    if ($isAjax) {
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
    }
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
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

// [MANTENIMIENTO] Comprobar modo mantenimiento antes de procesar la petición
if (\App\Core\MaintenanceMode::isActive()) {
    $isAdmin = \App\Core\Auth::check() && (\App\Core\Auth::role() === 'admin' || \App\Core\Auth::role() === 'direccion');
    $isUpdateRoute = str_starts_with($_SERVER['REQUEST_URI'], '/admin/update');
    $isSecretRoute = str_contains($_SERVER['REQUEST_URI'] ?? '', $_ENV['MAINTENANCE_SECRET'] ?? getenv('MAINTENANCE_SECRET') ?: 'DISABLED');

    if ((!$isAdmin || !$isUpdateRoute) && !$isSecretRoute) {
        http_response_code(503);
        header('Retry-After: 300');

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

        if ($isAjax) {
            header('Content-Type: application/json');
            $data = \App\Core\MaintenanceMode::getData();
            echo json_encode([
                'error'      => 'maintenance',
                'message'    => $data['message'] ?? 'Sistema en mantenimiento',
                'retry_after' => 300
            ]);
        } else {
            $maintenanceData = \App\Core\MaintenanceMode::getData();
            require __DIR__ . '/../app/Views/maintenance.php';
        }
        exit;
    }
}

try {
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

    // [CRÍTICO] Evitar cacheo de HTML y Tokens CSRF por proxies (Cloudflare, Nginx, Browser)
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    // Content-Security-Policy: Permitiendo recursos necesarios (Tailwind, Bootstrap, Google Fonts)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:;");

    // Inicializar y ejecutar Router
    $router = new \App\Core\Router();

    // Cargar rutas
    require_once __DIR__ . '/../app/routes.php';

    // Resolver la petición actual
    $router->resolve($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    // Cumplimiento: Ejecución de Telemetría (Poor Man's Cron) — después del dispatch para no añadir latencia
    \App\Core\Telemetry::checkAndRunCron();

} catch (\Throwable $e) {
    error_log("FATAL ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    
    // Si es AJAX, devolver JSON
    if ($isAjax) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
        }
        echo json_encode([
            'error' => 'Error interno del servidor',
            'message' => (APP_ENV === 'development') ? $e->getMessage() : 'Ha ocurrido un error inesperado. Inténtalo de nuevo más tarde.'
        ]);
    } else {
        // Si no es AJAX, mostrar página de error genérica
        http_response_code(500);
        echo "<h1>500 Internal Server Error</h1>";
        if (APP_ENV === 'development') {
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            echo "<p>Ha ocurrido un error interno. Por favor, contacta con soporte si el problema persiste.</p>";
        }
    }
}

<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $callback, $middlewares = []) {
        $this->addRoute('GET', $path, $callback, $middlewares);
    }

    public function post($path, $callback, $middlewares = []) {
        $this->addRoute('POST', $path, $callback, $middlewares);
    }

    public function patch($path, $callback, $middlewares = []) {
        $this->addRoute('PATCH', $path, $callback, $middlewares);
    }

    public function delete($path, $callback, $middlewares = []) {
        $this->addRoute('DELETE', $path, $callback, $middlewares);
    }

    private function addRoute($method, $path, $callback, $middlewares = []) {
        // Convertir variables de ruta tipo {id} a regex
        $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $path);
        $routeRegex = "#^" . $routeRegex . "$#";
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'regex' => $routeRegex,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }

    public function resolve($method, $path) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['regex'], $path, $matches)) {
                // [MEJORA] Ejecutar middlewares antes del controlador
                if (!empty($route['middlewares'])) {
                    Middleware::handle($route['middlewares']);
                }

                // Extraer parámetros con nombre
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Extraer los valores de los params para llamarlos en orden en caso de error
                $paramValues = array_values($params);
                
                if (is_array($route['callback'])) {
                    $controllerClass = $route['callback'][0];
                    $method = $route['callback'][1];

                    // [MEJORA] Inyección de Dependencias simple
                    $controller = match($controllerClass) {
                        \App\Controllers\ReportManagementController::class => new \App\Controllers\ReportManagementController(new \App\Models\Report(), new \App\Models\ReportMessage()),
                        \App\Controllers\StudentController::class => new \App\Controllers\StudentController(new \App\Models\Report(), new \App\Models\StudentProfile(), new \App\Models\ReportMessage()),
                        \App\Controllers\StaffController::class => new \App\Controllers\StaffController(new \App\Models\Report(), new \App\Models\ReportMention()),
                        \App\Controllers\AdminController::class => new \App\Controllers\AdminController(new \App\Models\User(), new \App\Models\Classroom(), new \App\Models\Report(), new \App\Models\Setting(), new \App\Core\Mailer(new \App\Models\Setting())),
                        \App\Controllers\Admin\SettingsController::class => new \App\Controllers\Admin\SettingsController(new \App\Models\Setting()),
                        \App\Controllers\ReportController::class => new \App\Controllers\ReportController(new \App\Models\Report(), new \App\Models\StudentProfile()),
                        \App\Controllers\AuthController::class => new \App\Controllers\AuthController(new \App\Models\User(), new \App\Models\OTPCode(), new \App\Core\Mailer(new \App\Models\Setting())),
                        \App\Controllers\PasswordResetController::class => new \App\Controllers\PasswordResetController(new \App\Models\User(), new \App\Models\PasswordReset(), new \App\Core\Mailer(new \App\Models\Setting())),
                        \App\Controllers\LangController::class => new \App\Controllers\LangController(new \App\Models\User()),
                        \App\Controllers\TotpController::class => new \App\Controllers\TotpController(),
                        \App\Controllers\WebAuthnController::class => new \App\Controllers\WebAuthnController(new \App\Models\WebAuthnCredential()),
                        \App\Controllers\Admin\UpdateController::class => new \App\Controllers\Admin\UpdateController(),
                        \App\Controllers\BullyingProtocolController::class => new \App\Controllers\BullyingProtocolController(new \App\Models\Setting()),
                        \App\Controllers\ProtocolWorkflowController::class => new \App\Controllers\ProtocolWorkflowController(),
                        \App\Controllers\ProtocolDashboardController::class => new \App\Controllers\ProtocolDashboardController(),
                        \App\Controllers\EvidenceController::class => new \App\Controllers\EvidenceController(),
                        default => new $controllerClass()
                    };

                    try {
                        return call_user_func_array([$controller, $method], $params);
                    } catch (\TypeError $e) {
                        return call_user_func_array([$controller, $method], $paramValues);
                    }
                }
                
                try {
                    return call_user_func_array($route['callback'], $params);
                } catch (\TypeError $e) {
                    return call_user_func_array($route['callback'], $paramValues);
                }
            }
        }
        
        // 404
        View::error();
    }
}

<?php
namespace App\Core;

// [MEJORA] Clase base de Middleware para centralizar control de acceso
class Middleware
{
    /**
     * Ejecuta los middlewares indicados. Si alguno falla, aborta la petición.
     * @param array $middlewares Lista de strings como ['auth', 'role:admin']
     */
    public static function handle(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if ($middleware === 'auth') {
                self::requireAuth();
            } elseif (str_starts_with($middleware, 'role:')) {
                $role = substr($middleware, 5);
                self::requireRole($role);
            } elseif (str_starts_with($middleware, 'roles:')) {
                $roles = explode(',', substr($middleware, 6));
                self::requireAnyRole($roles);
            }
        }
    }

    private static function requireAuth(): void
    {
        if (!Auth::check()) {
            http_response_code(302);
            header('Location: /login');
            exit;
        }
    }

    private static function requireRole(string $role): void
    {
        self::requireAuth();
        if (Auth::role() !== $role) {
            http_response_code(403);
            View::error(403, 'error.unauthorized_title', 'error.unauthorized_message', 'error.unauthorized_desc');
            exit;
        }
    }

    private static function requireAnyRole(array $roles): void
    {
        self::requireAuth();
        if (!in_array(Auth::role(), $roles, true)) {
            http_response_code(403);
            View::error(403, 'error.unauthorized_title', 'error.unauthorized_message', 'error.unauthorized_desc');
            exit;
        }
    }
}

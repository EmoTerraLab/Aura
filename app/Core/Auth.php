<?php
namespace App\Core;

use App\Models\User;

class Auth {
    private static $cachedUser = null;

    public static function login($user) {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['name']);
        self::$cachedUser = $user;
    }

    public static function logout() {
        self::$cachedUser = null;
        Session::destroy();
    }

    public static function check() {
        return Session::get('user_id') !== null;
    }

    public static function requireLogin() {
        if (!self::check()) {
            http_response_code(302);
            header('Location: /login');
            exit;
        }
    }

    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $userModel = new User();
        $user = $userModel->find(Session::get('user_id'));
        
        // Si el usuario ya no existe en BD, forzar logout
        if (!$user) {
            self::logout();
            return null;
        }

        // Sincronizar rol en sesión por si ha cambiado en BD
        if (Session::get('user_role') !== $user['role']) {
            Session::set('user_role', $user['role']);
        }

        self::$cachedUser = $user;
        return self::$cachedUser;
    }

    public static function id() {
        return Session::get('user_id');
    }

    public static function role() {
        $user = self::user();
        return $user ? $user['role'] : null;
    }

    public static function isCocobe(): bool
    {
        $user = self::user();
        return (bool)($user['is_cocobe'] ?? false);
    }

    public static function hasRole($roles) {
        $userRole = self::role();
        if (!$userRole) return false;
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        return $userRole === $roles;
    }

    /**
     * Comprueba si una IP/identificador ha superado el límite de intentos (Rate Limiting)
     */
    public static function isRateLimited(string $ip, string $identifier = '', int $maxAttempts = 5): bool {
        try {
            $db = \App\Core\Database::getInstance();
            
            // Limpiar entradas expiradas (más de 15 minutos)
            $db->prepare("DELETE FROM rate_limits WHERE last_attempt < datetime('now', '-15 minutes')")->execute();
            
            $key = $ip . '_' . $identifier;
            $stmt = $db->prepare("SELECT attempts FROM rate_limits WHERE ip = :ip");
            $stmt->execute(['ip' => $key]);
            $record = $stmt->fetch();
            
            if ($record) {
                if ($record['attempts'] >= $maxAttempts) {
                    return true;
                }
                $stmt = $db->prepare("UPDATE rate_limits SET attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP WHERE ip = :ip");
                $stmt->execute(['ip' => $key]);
            } else {
                $stmt = $db->prepare("INSERT OR IGNORE INTO rate_limits (ip, attempts, last_attempt) VALUES (:ip, 1, CURRENT_TIMESTAMP)");
                $stmt->execute(['ip' => $key]);
            }
        } catch (\Throwable $e) {
            error_log("Error en Auth::isRateLimited: " . $e->getMessage());
        }
        
        return false;
    }
}

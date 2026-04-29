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
}

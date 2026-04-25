<?php
namespace App\Core;

use App\Models\User;

class Auth {
    public static function login($user) {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['name']);
    }

    public static function logout() {
        Session::destroy();
    }

    public static function check() {
        return Session::get('user_id') !== null;
    }

    public static function user() {
        if (!self::check()) {
            return null;
        }
        $userModel = new User();
        return $userModel->find(Session::get('user_id'));
    }

    public static function id() {
        return Session::get('user_id');
    }

    public static function role() {
        return Session::get('user_role');
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

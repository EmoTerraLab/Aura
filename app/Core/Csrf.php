<?php
namespace App\Core;

class Csrf {
    public static function generateToken() {
        Session::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token) {
        Session::start();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateRequest() {
        $headers = getallheaders();
        $token = $headers['X-CSRF-TOKEN'] ?? $_POST['csrf_token'] ?? null;
        
        if (!self::validateToken($token)) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token inválido.']);
            exit;
        }
    }
}

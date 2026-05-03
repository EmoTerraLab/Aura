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

    public static function tokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function input() {
        return self::tokenField();
    }

    public static function validateToken($token) {
        Session::start();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateRequest() {
        // Buscar token en cabeceras (Apache/Nginx friendly)
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        // Si no está en cabeceras, buscar en POST tradicional
        if (!$token) {
            $token = $_POST['csrf_token'] ?? null;
        }
        
        // Si sigue sin estar (petición JSON), intentar leer del body
        if (!$token) {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['csrf_token'] ?? null;
        }
        
        if (!self::validateToken($token)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'CSRF token inválido o expirado.']);
            exit;
        }

        // SEC-010 FIX: Rotar token después de validación exitosa para prevenir replay
        self::regenerate();
    }

    /**
     * Regenera el token CSRF. Se llama después de cada validación exitosa.
     */
    public static function regenerate(): void {
        Session::start();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

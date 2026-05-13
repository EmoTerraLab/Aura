<?php
$file = '/Users/ianchahbounielkammouni/Desktop/GIR/Aura/app/Controllers/AuthController.php';
$content = file_get_contents($file);

$search = "    public function loginStaff() {
        Csrf::validateRequest();

        // SEC-013 FIX: Rate limiting en login de staff
        if (\$this->isRateLimited(\$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', \$email ?? '')) {
            http_response_code(429);
            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);
            return;
        }

        \$data = json_decode(file_get_contents('php://input'), true);
        
        \$email = \$data['email'] ?? '';";

$replace = "    public function loginStaff() {
        Csrf::validateRequest();

        \$data = json_decode(file_get_contents('php://input'), true);
        \$email = \$data['email'] ?? '';

        // SEC-013 FIX: Rate limiting en login de staff
        if (\$this->isRateLimited(\$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', \$email)) {
            http_response_code(429);
            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);
            return;
        }";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Fixed email parsing order in AuthController";

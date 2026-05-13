<?php
$fileAuth = '/Users/ianchahbounielkammouni/Desktop/GIR/Aura/app/Controllers/AuthController.php';
$contentAuth = file_get_contents($fileAuth);

// Inject use statement
if (strpos($contentAuth, 'use App\Core\AuditLogger;') === false) {
    $contentAuth = str_replace('use App\Core\Csrf;', "use App\Core\Csrf;\nuse App\Core\AuditLogger;", $contentAuth);
}

// Log failed login
$contentAuth = str_replace(
    "echo json_encode(['ok' => false, 'error' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);",
    "AuditLogger::log('STAFF_LOGIN_FAILED', 'user', null, ['email' => \$email]);\n        echo json_encode(['ok' => false, 'error' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);",
    $contentAuth
);

// Log successful login
$contentAuth = str_replace(
    "Auth::login(\$user);\n            echo json_encode([",
    "Auth::login(\$user);\n            AuditLogger::log('STAFF_LOGIN_SUCCESS', 'user', \$user['id']);\n            echo json_encode([",
    $contentAuth
);

// Log rate limited
$contentAuth = str_replace(
    "http_response_code(429);\n            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);",
    "AuditLogger::log('RATE_LIMITED', 'ip', null, ['email' => \$email ?? '']);\n            http_response_code(429);\n            echo json_encode(['ok' => false, 'error' => 'Demasiados intentos. Por favor, espera 15 minutos.']);",
    $contentAuth
);

file_put_contents($fileAuth, $contentAuth);

$fileReport = '/Users/ianchahbounielkammouni/Desktop/GIR/Aura/app/Controllers/ReportManagementController.php';
$contentReport = file_get_contents($fileReport);

// Inject use statement
if (strpos($contentReport, 'use App\Core\AuditLogger;') === false) {
    $contentReport = str_replace('use App\Core\Auth;', "use App\Core\Auth;\nuse App\Core\AuditLogger;", $contentReport);
}

// Log status update
$contentReport = str_replace(
    "\$this->reportModel->updateStatus(\$id, \$status, \$summary);",
    "\$this->reportModel->updateStatus(\$id, \$status, \$summary);\n        AuditLogger::log('REPORT_STATUS_UPDATED', 'report', \$id, ['status' => \$status]);",
    $contentReport
);

file_put_contents($fileReport, $contentReport);

echo "Injected AuditLogger";

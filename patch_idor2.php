<?php
$file = '/Users/ianchahbounielkammouni/Desktop/GIR/Aura/app/Controllers/ProtocolController.php';
$content = file_get_contents($file);

$verifyMethod = "
    private function verifyAccess(int \$reportId): bool
    {
        \$report = \$this->reportModel->findByIdWithDetails(\$reportId, Auth::id(), Auth::role());
        return \$report !== false && \$report !== null;
    }
";

if (strpos($content, 'verifyAccess') === false) {
    $content = preg_replace('/(class ProtocolController\s*\{.*?\n)/s', "$1" . $verifyMethod, $content, 1);
}

$methods = ['changePhase', 'classify'];

foreach ($methods as $method) {
    $pattern = '/(public function ' . $method . '\(.*?\$id.*?\)\s*:\s*void\s*\{.*?header\(\'Content-Type: application\/json\'\);)/s';
    $replacement = "$1\n        \$id = (int)\$id;\n        \$case = (new \App\Models\ProtocolCase())->find(\$id);\n        if (!\$case || !\$this->verifyAccess(\$case['report_id'])) {\n            http_response_code(403);\n            echo json_encode(['success' => false, 'error' => 'Acceso denegado.']);\n            return;\n        }";
    $content = preg_replace($pattern, $replacement, $content);
}

file_put_contents($file, $content);
echo "Patched IDORs ProtocolController";

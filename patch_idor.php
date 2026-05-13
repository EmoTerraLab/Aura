<?php
$file = '/Users/ianchahbounielkammouni/Desktop/GIR/Aura/app/Controllers/ProtocolWorkflowController.php';
$content = file_get_contents($file);

// Add verifyAccess method
$verifyMethod = "
    private function verifyAccess(int \$caseId): bool
    {
        \$case = \$this->caseModel->find(\$caseId);
        if (!\$case) return false;
        \$report = \$this->reportModel->findByIdWithDetails(\$case['report_id'], Auth::id(), Auth::role());
        return \$report !== false && \$report !== null;
    }
";

if (strpos($content, 'verifyAccess') === false) {
    $content = str_replace(
        "    private function requireCat(): void",
        $verifyMethod . "\n    private function requireCat(): void",
        $content
    );
}

// Add the check to all state modifying methods that have an $id parameter
$methods = [
    'addFollowup',
    'uploadEvidence',
    'saveSecurityMapFull',
    'updateComms',
    'updateClosure',
    'saveAcknowledgment',
    'addRestorativePractice',
    'updatePracticeStatus'
];

foreach ($methods as $method) {
    $pattern = '/(public function ' . $method . '\(.*?\$id.*?\)\s*:\s*void\s*\{)(.*?)(header\(\'Content-Type: application\/json\'\);)/s';
    $replacement = "$1$2$3\n        \$id = (int)\$id;\n        if (!\$this->verifyAccess(\$id)) {\n            http_response_code(403);\n            echo json_encode(['success' => false, 'error' => 'Acceso denegado al caso.']);\n            return;\n        }";
    $content = preg_replace($pattern, $replacement, $content);
}

file_put_contents($file, $content);
echo "Patched IDORs";

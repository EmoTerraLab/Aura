<?php
$file = '/Users/ianchahbounielkammouni/Desktop/GIR/Aura/app/Controllers/AuthController.php';
$content = file_get_contents($file);

$newRateLimit = "
    private function isRateLimited(\$ip, \$identifier = '') {
        \$db = \App\Core\Database::getInstance();
        
        // Limpiar entradas expiradas (más de 15 minutos)
        \$db->prepare(\"DELETE FROM rate_limits WHERE last_attempt < datetime('now', '-15 minutes')\")->execute();
        
        \$stmt = \$db->prepare(\"SELECT attempts FROM rate_limits WHERE ip = :ip\");
        \$stmt->execute(['ip' => \$ip . '_' . \$identifier]);
        \$record = \$stmt->fetch();
        
        \$maxAttempts = 5;
        
        if (\$record) {
            if (\$record['attempts'] >= \$maxAttempts) {
                return true;
            }
            \$stmt = \$db->prepare(\"UPDATE rate_limits SET attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP WHERE ip = :ip\");
            \$stmt->execute(['ip' => \$ip . '_' . \$identifier]);
        } else {
            \$stmt = \$db->prepare(\"INSERT OR IGNORE INTO rate_limits (ip, attempts, last_attempt) VALUES (:ip, 1, CURRENT_TIMESTAMP)\");
            \$stmt->execute(['ip' => \$ip . '_' . \$identifier]);
        }
        
        return false;
    }
";

$content = preg_replace('/private function isRateLimited\(\$ip\)\s*\{.*?return false;\s*\}/s', trim($newRateLimit), $content);

// Update calls to isRateLimited
$content = str_replace(
    "\$this->isRateLimited(\$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')",
    "\$this->isRateLimited(\$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', \$email ?? '')",
    $content
);

file_put_contents($file, $content);
echo "Patched Rate Limiting";

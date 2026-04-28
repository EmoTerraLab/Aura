<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PasswordReset
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, token, expires_at) 
            VALUES (:email, :token, :expires_at)
        ");
        return $stmt->execute($data);
    }

    public function findValidToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE token = ? AND used = 0 AND expires_at > CURRENT_TIMESTAMP
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function markAsUsed(string $token): bool
    {
        $stmt = $this->db->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        return $stmt->execute([$token]);
    }

    public function invalidatePrevious(string $email): bool
    {
        $stmt = $this->db->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND used = 0");
        return $stmt->execute([$email]);
    }

    public function cleanExpired(): bool
    {
        // Limpiar tokens expirados hace más de 24 horas
        return $this->db->exec("DELETE FROM password_resets WHERE expires_at < DATETIME('now', '-1 day')") !== false;
    }
}

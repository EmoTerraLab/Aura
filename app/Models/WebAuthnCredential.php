<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * WebAuthnCredential - Modelo para gestionar las credenciales biométricas.
 */
class WebAuthnCredential
{
    private $db;
    private $table = 'webauthn_credentials';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Inserta una nueva credencial.
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table}
             (user_id, credential_id, public_key, sign_count, device_name)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['user_id'],
            $data['credential_id'],
            $data['public_key'],
            $data['sign_count'] ?? 0,
            $data['device_name'] ?? 'Mi dispositivo'
        ]);
    }

    /**
     * Obtiene todas las credenciales de un usuario.
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca una credencial verificando que pertenece al usuario.
     */
    public function findByCredentialIdAndUserId(string $credentialId, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE credential_id = ? AND user_id = ? LIMIT 1"
        );
        $stmt->execute([$credentialId, $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Actualiza el contador de firmas tras una autenticación exitosa.
     */
    public function updateSignCount(string $credentialId, int $newCount): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET sign_count = ?, last_used_at = CURRENT_TIMESTAMP
             WHERE credential_id = ?"
        );
        return $stmt->execute([$newCount, $credentialId]);
    }

    /**
     * Elimina un dispositivo verificando la propiedad.
     */
    public function deleteByIdAndUserId(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Verifica si ya existe una credencial por su ID.
     */
    public function existsByCredentialId(string $credentialId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE credential_id = ?"
        );
        $stmt->execute([$credentialId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}

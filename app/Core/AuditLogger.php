<?php
namespace App\Core;

class AuditLogger
{
    /**
     * Registra una acción de auditoría inmutable.
     * 
     * @param string $action Descripción corta de la acción (ej. 'LOGIN_FAILED', 'REPORT_STATUS_UPDATED')
     * @param string|null $entityType Tipo de entidad (ej. 'report', 'user')
     * @param int|null $entityId ID de la entidad
     * @param array $details Detalles adicionales en formato JSON
     */
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, array $details = []): void
    {
        try {
            $db = Database::getInstance();
            $userId = Auth::id();
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $detailsJson = json_encode($details);

            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, details) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :ip_address, :details)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'ip_address' => $ipAddress,
                'details' => $detailsJson
            ]);
        } catch (\Throwable $e) {
            // No dejamos que un fallo en el log rompa la ejecución principal
            error_log("Error guardando audit_log: " . $e->getMessage());
        }
    }
}

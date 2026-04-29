<?php
declare(strict_types=1);
namespace App\Models;
class AragonProtocolCase extends Model {
    public const STATE_COMUNICACION_RECIBIDA   = "comunicacion_recibida";
    public const STATE_PROTOCOLO_INICIADO      = "protocolo_iniciado";
    public const STATE_PROTOCOLO_NO_INICIADO   = "protocolo_no_iniciado";
    public const STATE_EN_VALORACION           = "en_valoracion";
    public const STATE_VALORADO                = "valorado";
    public const STATE_EN_SEGUIMIENTO          = "en_seguimiento";
    public const STATE_CERRADO                 = "cerrado";
    public const STATE_REABIERTO               = "reabierto";
    public const STATE_VIOLENCIA_SEXUAL_ACTIVA = "violencia_sexual_activa";
    protected $table = "aragon_protocol_cases";
    public function findByReportId(int $reportId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE report_id = ? LIMIT 1");
        $stmt->execute([$reportId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    public function createCase(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (report_id, status, is_sexual_violence) VALUES (:report_id, :status, :is_sexual_violence)");
        $stmt->execute(["report_id" => $data["report_id"], "status" => $data["status"] ?? self::STATE_COMUNICACION_RECIBIDA, "is_sexual_violence" => $data["is_sexual_violence"] ?? 0]);
        return (int)$this->db->lastInsertId();
    }
    public function updateStatus(int $id, string $newStatus): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }
}

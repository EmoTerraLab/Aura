<?php
declare(strict_types=1);

namespace App\Models;

use App\Services\Protocol\GaliciaProtocol;

class GaliciaProtocolCase extends Model {

    protected $table = 'galicia_protocol_cases';

    public function findByReportId(int $reportId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE report_id = ? LIMIT 1");
        $stmt->execute([$reportId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function createCase(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (report_id, status, victim_id, aggressor_id) 
             VALUES (:report_id, :status, :victim_id, :aggressor_id)"
        );
        $stmt->execute([
            'report_id'     => $data['report_id'],
            'status'        => $data['status'] ?? GaliciaProtocol::STATE_DETECCIO_COMUNICACIO,
            'victim_id'     => $data['victim_id'] ?? null,
            'aggressor_id'  => $data['aggressor_id'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $newStatus): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
        );
        return $stmt->execute([$newStatus, $id]);
    }

    public function updateCoordinator(int $id, int $coordinatorId): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET team_coordinator_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
        );
        return $stmt->execute([$coordinatorId, $id]);
    }
}

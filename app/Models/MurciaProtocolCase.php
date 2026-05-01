<?php
declare(strict_types=1);

namespace App\Models;

class MurciaProtocolCase extends Model
{
    protected $table = 'murcia_protocol_cases';

    public function findByReportId(int $reportId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE report_id = ? LIMIT 1");
        $stmt->execute([$reportId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function createCase(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (report_id, status, victim_id, aggressor_id, team_coordinator_id, start_date) 
                VALUES (:report_id, :status, :victim_id, :aggressor_id, :team_coordinator_id, :start_date)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'report_id'           => $data['report_id'],
            'status'              => $data['status'] ?? ProtocolCase::PHASE_MUR_INICIAL,
            'victim_id'           => $data['victim_id'] ?? null,
            'aggressor_id'        => $data['aggressor_id'] ?? null,
            'team_coordinator_id' => $data['team_coordinator_id'] ?? null,
            'start_date'          => $data['start_date'] ?? date('Y-m-d H:i:s')
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $newStatus): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }

    public function updateCoordinator(int $id, int $coordinatorId): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET team_coordinator_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$coordinatorId, $id]);
    }
}

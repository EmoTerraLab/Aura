<?php
namespace App\Models;

class RestorativePractice extends Model
{
    protected $table = 'restorative_practices';

    public function findByCase(int $caseId): array
    {
        $stmt = $this->db->prepare("SELECT rp.*, u.name as facilitator_name 
                                    FROM {$this->table} rp
                                    JOIN users u ON rp.facilitator_id = u.id
                                    WHERE rp.protocol_case_id = ? 
                                    ORDER BY rp.session_date ASC");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }

    public function create($data): int
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (protocol_case_id, practice_type, facilitator_id, session_date, participants, agreements, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['protocol_case_id'],
            $data['practice_type'],
            $data['facilitator_id'],
            $data['session_date'],
            $data['participants'],
            $data['agreements'],
            $data['status'] ?? 'pending'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

class MurciaAnnex extends Model
{
    protected $table = 'murcia_protocol_annexes';

    public function createAnnex(int $caseId, string $type, array $content, int $submittedBy): int
    {
        $sql = "INSERT INTO {$this->table} (protocol_case_id, annex_type, content, submitted_by) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$caseId, $type, json_encode($content, JSON_UNESCAPED_UNICODE), $submittedBy]);
        return (int)$this->db->lastInsertId();
    }

    public function findByCase(int $caseId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE protocol_case_id = ? ORDER BY created_at ASC");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }

    public function findLatestByType(int $caseId, string $type): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE protocol_case_id = ? AND annex_type = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$caseId, $type]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}

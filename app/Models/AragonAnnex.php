<?php
declare(strict_types=1);
namespace App\Models;
class AragonAnnex extends Model {
    protected $table = "aragon_protocol_annexes";
    public function createAnnex(int $caseId, string $type, array $content, int $userId): int {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (protocol_case_id, annex_type, content, submitted_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$caseId, $type, json_encode($content, JSON_UNESCAPED_UNICODE), $userId]);
        return (int)$this->db->lastInsertId();
    }
    public function findByCase(int $caseId): array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE protocol_case_id = ? ORDER BY created_at ASC");
        $stmt->execute([$caseId]);
        $results = $stmt->fetchAll();
        foreach ($results as &$row) { $row["content"] = json_decode($row["content"], true); }
        return $results;
    }
}

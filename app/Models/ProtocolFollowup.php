<?php
namespace App\Models;

class ProtocolFollowup extends Model
{
    protected $table = 'protocol_followups';

    public function findByCase(int $caseId)
    {
        $stmt = $this->db->prepare("
            SELECT f.*, u.name as creator_name 
            FROM {$this->table} f
            JOIN users u ON f.created_by = u.id
            WHERE f.protocol_case_id = :case_id 
            ORDER BY f.session_date DESC
        ");
        $stmt->execute(['case_id' => $caseId]);
        return $stmt->fetchAll();
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} 
                (protocol_case_id, target_type, session_date, notes, created_by) 
                VALUES (:protocol_case_id, :target_type, :session_date, :notes, :created_by)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'protocol_case_id' => $data['protocol_case_id'],
            'target_type'      => $data['target_type'],
            'session_date'     => $data['session_date'],
            'notes'            => $data['notes'],
            'created_by'       => $data['created_by']
        ]);
    }
}

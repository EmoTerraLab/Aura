<?php
namespace App\Models;

class ReportMessage extends Model {
    protected $table = 'report_messages';

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (report_id, sender_id, message, is_internal) VALUES (:report_id, :sender_id, :message, :is_internal)");
        $stmt->execute([
            'report_id' => $data['report_id'],
            'sender_id' => $data['sender_id'],
            'message' => $data['message'],
            'is_internal' => $data['is_internal'] ?? 0
        ]);
        return $this->db->lastInsertId();
    }

    public function findByReport($report_id) {
        $stmt = $this->db->prepare("
            SELECT m.*, u.name as sender_name 
            FROM {$this->table} m
            JOIN users u ON m.sender_id = u.id
            WHERE m.report_id = :report_id 
            ORDER BY m.created_at ASC
        ");
        $stmt->execute(['report_id' => $report_id]);
        return $stmt->fetchAll();
    }
}

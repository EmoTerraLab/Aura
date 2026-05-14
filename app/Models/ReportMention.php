<?php
namespace App\Models;

class ReportMention extends Model {
    protected $table = 'report_mentions';

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (report_id, message_id, user_id) VALUES (:report_id, :message_id, :user_id)");
        $stmt->execute([
            'report_id' => $data['report_id'],
            'message_id' => $data['message_id'],
            'user_id' => $data['user_id']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Crea múltiples menciones en una sola transacción para mejorar el rendimiento.
     */
    public function createMany(array $mentions): void
    {
        if (empty($mentions)) return;

        $sql = "INSERT INTO {$this->table} (report_id, message_id, user_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        $this->db->beginTransaction();
        try {
            foreach ($mentions as $m) {
                $stmt->execute([$m['report_id'], $m['message_id'], $m['user_id']]);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findUnreadByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function findUnreadByUserWithDetails($user_id) {
        $sql = "SELECT rm.*, r.id as report_id, m.message, u.name as sender_name 
                FROM {$this->table} rm
                JOIN report_messages m ON rm.message_id = m.id
                JOIN reports r ON rm.report_id = r.id
                JOIN users u ON m.sender_id = u.id
                WHERE rm.user_id = :user_id AND rm.is_read = 0
                ORDER BY rm.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_read = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

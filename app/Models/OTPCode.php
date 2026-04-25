<?php
namespace App\Models;

class OTPCode extends Model {
    protected $table = 'otp_codes';

    public function create($user_id, $code) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, code, expires_at) VALUES (:user_id, :code, datetime('now', '+10 minutes'))");
        $stmt->execute([
            'user_id' => $user_id,
            'code' => $code
        ]);
        return $this->db->lastInsertId();
    }

    public function findValidCode($user_id, $code) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id AND code = :code AND used = 0 AND expires_at > CURRENT_TIMESTAMP");
        $stmt->execute([
            'user_id' => $user_id,
            'code' => $code
        ]);
        return $stmt->fetch();
    }

    public function markAsUsed($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET used = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

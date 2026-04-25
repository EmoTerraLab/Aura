<?php
namespace App\Models;

class StudentProfile extends Model {
    protected $table = 'student_profiles';

    public function findByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch();
    }
}

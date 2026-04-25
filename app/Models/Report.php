<?php
namespace App\Models;

class Report extends Model {
    protected $table = 'reports';

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (student_id, classroom_id, content, target, urgency_level, is_anonymous) VALUES (:student_id, :classroom_id, :content, :target, :urgency_level, :is_anonymous)");
        $stmt->execute([
            'student_id' => $data['student_id'],
            'classroom_id' => $data['classroom_id'],
            'content' => $data['content'],
            'target' => $data['target'] ?? 'yo_mismo',
            'urgency_level' => $data['urgency_level'] ?? 'low',
            'is_anonymous' => $data['is_anonymous'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function findByStudent($student_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = :student_id ORDER BY created_at DESC");
        $stmt->execute(['student_id' => $student_id]);
        return $stmt->fetchAll();
    }

    public function findByClassroom($classroom_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE classroom_id = :classroom_id ORDER BY created_at DESC");
        $stmt->execute(['classroom_id' => $classroom_id]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status, $resolution_summary = null) {
        $query = "UPDATE {$this->table} SET status = :status, updated_at = CURRENT_TIMESTAMP";
        $params = ['status' => $status, 'id' => $id];

        if ($status === 'resolved') {
            $query .= ", resolution_summary = :summary, resolved_at = CURRENT_TIMESTAMP";
            $params['summary'] = $resolution_summary;
        }

        $query .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function getForStaff($user_id, $role) {
        $sql = "SELECT r.*, c.name as classroom_name, u.name as student_name 
                FROM {$this->table} r
                LEFT JOIN classrooms c ON r.classroom_id = c.id
                LEFT JOIN student_profiles sp ON r.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id";
                
        if ($role === 'profesor') {
            $sql .= " WHERE r.classroom_id IN (SELECT id FROM classrooms WHERE tutor_id = :user_id)";
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($role === 'profesor') {
            $stmt->execute(['user_id' => $user_id]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    public function findByIdWithDetails($id, $user_id, $role) {
        $sql = "SELECT r.*, c.name as classroom_name, u.name as student_name, sp.anonymized_id 
                FROM {$this->table} r
                LEFT JOIN classrooms c ON r.classroom_id = c.id
                LEFT JOIN student_profiles sp ON r.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                WHERE r.id = :id";
                
        if ($role === 'profesor') {
            $sql .= " AND r.classroom_id IN (SELECT id FROM classrooms WHERE tutor_id = :user_id)";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($role === 'profesor') {
            $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        } else {
            $stmt->execute(['id' => $id]);
        }
        
        return $stmt->fetch();
    }
}

<?php
namespace App\Models;

class Classroom extends Model {
    protected $table = 'classrooms';

    public function findByTutor($tutor_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tutor_id = :tutor_id");
        $stmt->execute(['tutor_id' => $tutor_id]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, tutor_id) VALUES (:name, :tutor_id)");
        $stmt->execute([
            'name' => $data['name'],
            'tutor_id' => !empty($data['tutor_id']) ? $data['tutor_id'] : null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET name = :name, tutor_id = :tutor_id WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'tutor_id' => !empty($data['tutor_id']) ? $data['tutor_id'] : null
        ]);
    }

    public function allWithTutors() {
        $stmt = $this->db->query("
            SELECT c.*, u.name as tutor_name 
            FROM {$this->table} c 
            LEFT JOIN users u ON c.tutor_id = u.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }
}

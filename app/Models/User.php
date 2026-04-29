<?php
namespace App\Models;

class User extends Model {
    protected $table = 'users';

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function allWithProfiles() {
        $stmt = $this->db->query("
            SELECT u.*, sp.classroom_id 
            FROM {$this->table} u
            LEFT JOIN student_profiles sp ON u.id = sp.user_id
            ORDER BY u.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? null,
            'role' => $data['role'] ?? 'alumno'
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET name = :name, email = :email, role = :role, updated_at = CURRENT_TIMESTAMP";
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'alumno'
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = $data['password'];
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Actualiza el idioma preferido del usuario en la base de datos.
     */
    public function updateLang(int $userId, string $lang): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET lang = ? WHERE id = ?');
        return $stmt->execute([$lang, $userId]);
    }

    public function updateCocobeStatus(int $userId, bool $status): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET is_cocobe = ? WHERE id = ?');
        return $stmt->execute([(int)$status, $userId]);
    }

    /**
     * Actualiza la contraseña de un usuario por su correo electrónico.
     */
    public function updatePassword(string $email, string $hash): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password WHERE email = :email");
        return $stmt->execute(['password' => $hash, 'email' => $email]);
    }
}

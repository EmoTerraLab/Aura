<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Report;

class AdminController {
    private $userModel;
    private $classroomModel;
    private $reportModel;

    public function __construct(\App\Models\User $userModel, \App\Models\Classroom $classroomModel, \App\Models\Report $reportModel) {
        $this->userModel = $userModel;
        $this->classroomModel = $classroomModel;
        $this->reportModel = $reportModel;
    }
    
    public function index() {
        View::render('admin/dashboard', [
            'title' => 'Aura - Panel de Administración',
            'totalUsers' => $this->userModel->count(),
            'totalClassrooms' => $this->classroomModel->count(),
            'totalReports' => $this->reportModel->count()
        ], null);
    }

    // --- API Usuarios ---

    public function getUsers() {
        echo json_encode(['data' => $this->userModel->allWithProfiles()]);
    }

    public function storeUser() {
        Csrf::validateRequest();
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || empty($data['email']) || empty($data['role'])) {
            echo json_encode(['error' => 'Faltan datos obligatorios.']);
            return;
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $data['password'] = null; // Alumnos no tienen password por defecto
        }

        try {
            $userId = $this->userModel->create($data);

            // Cumplimiento: Creación automática del perfil de estudiante anonimizado
            if ($data['role'] === 'alumno') {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare("INSERT INTO student_profiles (user_id, classroom_id, anonymized_id) VALUES (:user_id, :classroom_id, :anonymized_id)");
                $stmt->execute([
                    'user_id' => $userId,
                    'classroom_id' => !empty($data['classroom_id']) ? $data['classroom_id'] : null,
                    'anonymized_id' => 'anon-' . bin2hex(random_bytes(8)) // Simulando UUID v4
                ]);
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'El email ya existe o hubo un error.']);
        }
    }

    public function updateUser($id) {
        Csrf::validateRequest();
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || empty($data['email']) || empty($data['role'])) {
            echo json_encode(['error' => 'Faltan datos obligatorios.']);
            return;
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // No actualizar si viene vacío
        }

        try {
            $this->userModel->update($id, $data);

            // Actualizar classroom_id si es alumno
            if ($data['role'] === 'alumno' && isset($data['classroom_id'])) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare("UPDATE student_profiles SET classroom_id = :classroom_id WHERE user_id = :user_id");
                $stmt->execute([
                    'classroom_id' => !empty($data['classroom_id']) ? $data['classroom_id'] : null,
                    'user_id' => $id
                ]);
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'El email ya existe o hubo un error.']);
        }
    }

    public function deleteUser($id) {
        Csrf::validateRequest();

        if ($id == Auth::id()) {
            echo json_encode(['error' => 'No puedes eliminarte a ti mismo.']);
            return;
        }

        try {
            $this->userModel->delete($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error al eliminar usuario. Puede que tenga dependencias (ej. aulas asignadas).']);
        }
    }

    // --- API Aulas ---

    public function getClassrooms() {
        echo json_encode(['data' => $this->classroomModel->allWithTutors()]);
    }

    public function storeClassroom() {
        Csrf::validateRequest();
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name'])) {
            echo json_encode(['error' => 'El nombre del aula es obligatorio.']);
            return;
        }

        try {
            $this->classroomModel->create($data);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Hubo un error al crear el aula.']);
        }
    }

    public function updateClassroom($id) {
        Csrf::validateRequest();
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name'])) {
            echo json_encode(['error' => 'El nombre del aula es obligatorio.']);
            return;
        }

        try {
            $this->classroomModel->update($id, $data);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Hubo un error al actualizar el aula.']);
        }
    }

    public function deleteClassroom($id) {
        Csrf::validateRequest();

        try {
            $this->classroomModel->delete($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error al eliminar aula. Puede que tenga dependencias.']);
        }
    }
}

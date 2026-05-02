<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Report;
use App\Core\Mailer;

class AdminController {
    private $userModel;
    private $classroomModel;
    private $reportModel;
    private $settingModel;
    private $mailer;

    public function __construct(\App\Models\User $userModel, \App\Models\Classroom $classroomModel, \App\Models\Report $reportModel, \App\Models\Setting $settingModel, \App\Core\Mailer $mailer) {
        $this->userModel = $userModel;
        $this->classroomModel = $classroomModel;
        $this->reportModel = $reportModel;
        $this->settingModel = $settingModel;
        $this->mailer = $mailer;
    }
    
    public function index() {
        View::render('admin/dashboard', [
            'title' => 'Aura - Panel de Administración',
            'totalUsers' => $this->userModel->count(),
            'totalClassrooms' => $this->classroomModel->count(),
            'totalReports' => $this->reportModel->count()
        ], 'app');
    }

    // --- API Usuarios ---

    public function getUsers() {
        header('Content-Type: application/json');
        echo json_encode(['data' => $this->userModel->allWithProfiles()]);
    }

    public function storeUser() {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        
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

            // Enviar email de bienvenida
            try {
                $this->sendWelcomeEmail($data['name'], $data['email'], $data['role']);
            } catch (\Exception $e) {
                error_log("Error enviando email de bienvenida a {$data['email']}: " . $e->getMessage());
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'El email ya existe o hubo un error.']);
        }
    }

    private function sendWelcomeEmail(string $name, string $email, string $role): void
    {
        $schoolName = $this->settingModel->get('school_name', 'Aura PDP');
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $urlLogin = $protocol . '://' . $host . '/login';

        $subject = "Bienvenido/a a Aura, {$name}";
        
        $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <title>Bienvenido/a a Aura, ' . htmlspecialchars($name) . '</title>
  <style>
    @media (prefers-color-scheme: dark) {
      body, .email-bg    { background-color: #111827 !important; }
      .email-card        { background-color: #1f2937 !important; border-color: #374151 !important; }
      .email-text        { color: #f9fafb !important; }
      .email-muted       { color: #9ca3af !important; }
      .email-divider     { border-color: #374151 !important; }
      .data-cell         { border-color: #374151 !important; }
      .footer-text       { color: #6b7280 !important; }
    }
    @media (max-width: 600px) {
      .email-card    { padding: 1.5rem !important; }
      .email-wrapper { width: 100% !important; padding: 0 1rem !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:sans-serif;" class="email-bg">

  <div style="display:none;max-height:0;overflow:hidden;">Tu cuenta en ' . htmlspecialchars($schoolName) . ' ya está lista. Entra y empieza. ‌ ‌ ‌ ‌ ‌ ‌ ‌</div>

  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding:2rem 1rem;">
        <table width="600" cellpadding="0" cellspacing="0" border="0" class="email-wrapper" style="max-width:600px;width:100%;">

          <!-- Logo -->
          <tr>
            <td style="padding-bottom:1.25rem;">
              <img src="https://app.aura.emoterralab.com/icono-sinfondo.png" alt="Aura" width="30" height="30" style="width:30px;height:30px;vertical-align:middle;margin-right:8px;">
              <span style="font-size:1.1rem;font-weight:700;color:#4f46e5;vertical-align:middle;">Aura</span>
            </td>
          </tr>

          <!-- Card -->
          <tr>
            <td class="email-card" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;padding:2rem;">

              <h2 style="color:#4f46e5;margin-top:0;margin-bottom:1.25rem;font-size:1.2rem;">' . htmlspecialchars($schoolName) . '</h2>

              <p class="email-text" style="margin:0 0 0.75rem;color:#111827;">Hola, <strong>' . htmlspecialchars($name) . '</strong></p>

              <p class="email-text" style="margin:0 0 1.5rem;color:#111827;line-height:1.6;">
                Tu cuenta ha sido creada en Aura con el rol de <strong>' . htmlspecialchars($role) . '</strong>. Ya puedes acceder a la plataforma y empezar a trabajar.
              </p>

              <!-- Datos -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e5e7eb;border-radius:6px;margin-bottom:1.75rem;">
                <tr>
                  <td class="data-cell" style="padding:0.7rem 1rem;border-bottom:1px solid #e5e7eb;">
                    <span style="font-size:0.72rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:2px;">Email</span>
                    <span class="email-text" style="color:#111827;font-size:0.9rem;">' . htmlspecialchars($email) . '</span>
                  </td>
                </tr>
                <tr>
                  <td class="data-cell" style="padding:0.7rem 1rem;border-bottom:1px solid #e5e7eb;">
                    <span style="font-size:0.72rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:2px;">Rol</span>
                    <span class="email-text" style="color:#111827;font-size:0.9rem;">' . htmlspecialchars($role) . '</span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0.7rem 1rem;">
                    <span style="font-size:0.72rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:2px;">Centro</span>
                    <span class="email-text" style="color:#111827;font-size:0.9rem;">' . htmlspecialchars($schoolName) . '</span>
                  </td>
                </tr>
              </table>

              <!-- CTA -->
              <p style="text-align:center;margin:0 0 1.5rem;">
                <a href="' . $urlLogin . '" style="background:#4f46e5;color:#ffffff;padding:14px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">
                  Acceder a Aura
                </a>
              </p>

              <p class="email-muted" style="color:#6b7280;font-size:0.875rem;margin:0 0 1.5rem;">
                Si tienes dudas, contacta con el administrador de tu centro.
              </p>

              <hr class="email-divider" style="border:0;border-top:1px solid #e5e7eb;margin:0 0 1.25rem;">

              <p class="footer-text" style="color:#6b7280;font-size:0.75rem;margin:0;line-height:1.6;">
                Este email fue enviado por <strong>' . htmlspecialchars($schoolName) . '</strong> a través de Aura PDP. Si no esperabas este correo, puedes ignorarlo.<br><br>
                <img src="https://app.aura.emoterralab.com/icono-sinfondo.png" alt="" width="12" height="12" style="vertical-align:middle;margin-right:3px;">
                <strong style="color:#4f46e5;">Aura</strong> · Powered by <a href="https://emoterralab.com" style="color:#7c3aed;text-decoration:none;">EmoTerraLab</a>
              </p>

            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>';

        $this->mailer->send($email, $subject, $body);
    }

    public function updateUser($id) {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        
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
        header('Content-Type: application/json');

        if ((int)$id === (int)Auth::id()) {
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
        header('Content-Type: application/json');
        echo json_encode(['data' => $this->classroomModel->allWithTutors()]);
    }

    public function storeClassroom() {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        
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
        header('Content-Type: application/json');
        
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
        header('Content-Type: application/json');

        try {
            $this->classroomModel->delete($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error al eliminar aula. Puede que tenga dependencias.']);
        }
    }

    // --- API Settings ---

    public function getSettings() {
        header('Content-Type: application/json');
        echo json_encode([
            'default_lang' => $this->settingModel->get('default_lang') ?? 'es'
        ]);
    }

    public function updateDefaultLang() {
        Csrf::validateRequest();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $lang = $data['default_lang'] ?? 'es';

        if (\App\Core\Lang::isSupported($lang)) {
            $this->settingModel->set('default_lang', $lang);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Idioma no soportado.']);
        }
    }
}

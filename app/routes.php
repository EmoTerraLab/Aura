<?php
// =============================================================================
// Aura PDP — app/routes.php
// Definición de endpoints con Middleware [MEJORA]
// =============================================================================

// Uso de namespaces para controladores
use App\Controllers\AuthController;
use App\Controllers\StudentController;
use App\Controllers\ReportController;
use App\Controllers\StaffController;
use App\Controllers\ReportManagementController;
use App\Controllers\AdminController;

// -- Endpoints Públicos y de Autenticación --

// GET / : Redirige a /login
$router->get('/', function() {
    header('Location: /login');
    exit;
});

// GET /login : Vista login con tabs alumno/staff
$router->get('/login', [AuthController::class, 'showLogin']);

// POST /login/staff : Auth email + contraseña para personal
$router->post('/login/staff', [AuthController::class, 'loginStaff']);

// POST /login/otp/generate : Genera OTP 6 dígitos para alumno (AJAX JSON)
$router->post('/login/otp/generate', [AuthController::class, 'generateOTP']);

// POST /login/otp/verify : Verifica OTP y autentica alumno (AJAX JSON)
$router->post('/login/otp/verify', [AuthController::class, 'verifyOTP']);

// POST /logout : Destruye sesión
$router->post('/logout', [AuthController::class, 'logout']);


// -- Endpoints de Alumno --

// GET /alumno/dashboard : Dashboard alumno con historial de reportes
$router->get('/alumno/dashboard', [StudentController::class, 'index'], ['auth', 'role:alumno']);

// POST /alumno/report : Crea nuevo reporte desde alumno (AJAX JSON)
$router->post('/alumno/report', [ReportController::class, 'store'], ['auth', 'role:alumno']);

// GET /alumno/reports/{id} : Detalle del caso para el alumno (AJAX JSON)
$router->get('/alumno/reports/{id}', [StudentController::class, 'show'], ['auth', 'role:alumno']);

// POST /alumno/reports/{id}/messages : Añade mensaje desde el alumno (AJAX JSON)
$router->post('/alumno/reports/{id}/messages', [StudentController::class, 'addMessage'], ['auth', 'role:alumno']);


// -- Endpoints de Staff (Profesores, Orientadores, Dirección) --

// GET /staff/dashboard : Bandeja de entrada staff (Referencia original)
$router->get('/staff/dashboard', [StaffController::class, 'index'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/inbox : Alias de /staff/dashboard (Traducción a aura-pdp)
$router->get('/staff/inbox', [StaffController::class, 'index'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/reports/{id} : Detalle de caso (AJAX JSON)
$router->get('/staff/reports/{id}', [ReportManagementController::class, 'show'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// POST /staff/reports/{id}/status : Actualiza estado (Referencia original)
$router->post('/staff/reports/{id}/status', [ReportManagementController::class, 'updateStatus'], ['auth', 'roles:orientador,direccion,admin']);

// PATCH /staff/reports/{id} : Reemplaza el POST de status (Traducción a aura-pdp)
$router->patch('/staff/reports/{id}', [ReportManagementController::class, 'updateStatus'], ['auth', 'roles:orientador,direccion,admin']);

// POST /staff/reports/{id}/messages : Añade mensaje (AJAX JSON)
$router->post('/staff/reports/{id}/messages', [ReportManagementController::class, 'addMessage'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/mentions : Menciones no leídas (Traducción a aura-pdp)
$router->get('/staff/mentions', [StaffController::class, 'mentions'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// POST /staff/mentions/read : Marcar menciones como leídas (Traducción a aura-pdp)
$router->post('/staff/mentions/read', [StaffController::class, 'markMentionsRead'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/colleagues : Lista de personal para menciones
$router->get('/staff/colleagues', [StaffController::class, 'getColleagues'], ['auth', 'roles:profesor,orientador,direccion,admin']);


// -- Endpoints de Admin --

// GET /admin : Panel admin (Traducción a aura-pdp)
$router->get('/admin', [AdminController::class, 'index'], ['auth', 'role:admin']);

// Rutas CRUD admin
$router->get('/admin/api/users', [AdminController::class, 'getUsers'], ['auth', 'role:admin']);
$router->post('/admin/api/users', [AdminController::class, 'storeUser'], ['auth', 'role:admin']);
$router->patch('/admin/api/users/{id}', [AdminController::class, 'updateUser'], ['auth', 'role:admin']);
$router->delete('/admin/api/users/{id}', [AdminController::class, 'deleteUser'], ['auth', 'role:admin']);

$router->get('/admin/api/classrooms', [AdminController::class, 'getClassrooms'], ['auth', 'role:admin']);
$router->post('/admin/api/classrooms', [AdminController::class, 'storeClassroom'], ['auth', 'role:admin']);
$router->patch('/admin/api/classrooms/{id}', [AdminController::class, 'updateClassroom'], ['auth', 'role:admin']);
$router->delete('/admin/api/classrooms/{id}', [AdminController::class, 'deleteClassroom'], ['auth', 'role:admin']);

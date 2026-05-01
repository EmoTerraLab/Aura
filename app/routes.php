<?php
// =============================================================================
// Aura — app/routes.php
// Definición de endpoints con Middleware [MEJORA]
// =============================================================================

// Uso de namespaces para controladores
use App\Controllers\AuthController;
use App\Controllers\PasswordResetController;
use App\Controllers\StudentController;
use App\Controllers\ReportController;
use App\Controllers\StaffController;
use App\Controllers\ReportManagementController;
use App\Controllers\AdminController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\LangController;
use App\Controllers\TotpController;
use App\Controllers\WebAuthnController;
use App\Controllers\AragonProtocolController;

use App\Controllers\BullyingProtocolController;
use App\Controllers\ProtocolWorkflowController;

// -- Endpoints Públicos y de Autenticación --

// GET / : Redirige a /login
$router->get('/', function() {
    header('Location: /login');
    exit;
});

// POST /lang/switch : Cambia el idioma
$router->post('/lang/switch', [LangController::class, 'switch']);

// -- Protocolo de Acoso (Público/Consulta) --
$router->get('/protocolo-acoso', [BullyingProtocolController::class, 'index'], ['auth']);
$router->get('/api/protocol', [BullyingProtocolController::class, 'getApiProtocol'], ['auth']);
$router->get('/api/protocol-info', [BullyingProtocolController::class, 'apiGetInfo'], ['auth', 'roles:admin']);

// -- Workflow Legal del Protocolo --
// -- Módulo Restaurativo --
$router->post("/api/protocol/case/{id}/acknowledgment", [ProtocolWorkflowController::class, "saveAcknowledgment"], ["auth", "roles:orientador,direccion,admin"]);
$router->get("/api/protocol/case/{id}/restorative", [ProtocolWorkflowController::class, "getRestorativeData"], ["auth"]);
$router->post("/api/protocol/case/{id}/restorative/add", [ProtocolWorkflowController::class, "addRestorativePractice"], ["auth", "roles:profesor,orientador,direccion,admin"]);
$router->patch("/api/restorative/{id}/status", [ProtocolWorkflowController::class, "updatePracticeStatus"], ["auth", "roles:profesor,orientador,direccion,admin"]);

$router->get('/protocolos/dashboard', [\App\Controllers\ProtocolDashboardController::class, 'index'], ['auth', 'roles:direccion,admin']);

// -- Sociogramas (CESC) --
$router->get('/alumno/sociograma', [\App\Controllers\SociometricController::class, 'survey'], ['auth', 'role:alumno']);
$router->post('/api/sociometric/respond', [\App\Controllers\SociometricController::class, 'submitResponse'], ['auth', 'role:alumno']);
$router->get('/staff/sociogramas/{id}', [\App\Controllers\SociometricController::class, 'results'], ['auth', 'roles:profesor,orientador,direccion,admin']);

$router->get('/api/protocol/case/{report_id}', [ProtocolController::class, 'getCaseData'], ['auth', 'roles:profesor,orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/phase', [ProtocolController::class, 'changePhase'], ['auth', 'roles:orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/classify', [ProtocolController::class, 'classify'], ['auth', 'roles:orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/assign-team', [ProtocolWorkflowController::class, 'assignTeam'], ['auth', 'roles:orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/communications', [ProtocolWorkflowController::class, 'updateComms'], ['auth', 'roles:orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/security-map', [ProtocolWorkflowController::class, 'saveSecurityMapFull'], ['auth', 'roles:orientador,direccion,admin']);
$router->get('/api/protocol/case/{id}/security-map', [ProtocolWorkflowController::class, 'getSecurityMap'], ['auth', 'roles:orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/followup', [ProtocolWorkflowController::class, 'addFollowup'], ['auth', 'roles:profesor,orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/closure', [ProtocolWorkflowController::class, 'updateClosure'], ['auth', 'roles:direccion,admin']);
$router->get('/protocol/case/{id}/export', [ProtocolWorkflowController::class, 'exportPdf'], ['auth', 'roles:orientador,direccion,admin']);
$router->get('/api/protocol/case/{id}/reva', [ProtocolWorkflowController::class, 'getRevaSummary'], ['auth', 'roles:orientador,direccion,admin']);
$router->post('/api/protocol/case/{id}/evidence', [ProtocolWorkflowController::class, 'uploadEvidence'], ['auth', 'roles:orientador,direccion,admin']);
$router->get('/protocol/evidence/{id}/download', [\App\Controllers\EvidenceController::class, 'download'], ['auth']);
$router->get('/protocol/case/{id}/template/{templateName}', [ProtocolWorkflowController::class, 'exportTemplate'], ['auth', 'roles:orientador,direccion,admin']);

// -- Verificación 2FA TOTP --
$router->get('/auth/2fa/totp', [TotpController::class, 'showVerify']);
$router->post('/auth/2fa/totp/verify', [TotpController::class, 'verifyLogin']);

// -- Verificación WebAuthn --
$router->get('/auth/2fa/webauthn', [WebAuthnController::class, 'showVerify']);
$router->get('/auth/2fa/webauthn/fallback', [WebAuthnController::class, 'fallback']);
$router->get('/auth/2fa/webauthn/options', [WebAuthnController::class, 'authOptions']);
$router->post('/auth/2fa/webauthn/verify', [WebAuthnController::class, 'authVerify']);

// GET /login : Vista login con tabs alumno/staff
$router->get('/login', [AuthController::class, 'showLogin']);

// POST /login/staff : Auth email + contraseña para personal
$router->post('/login/staff', [AuthController::class, 'loginStaff']);

// POST /login/otp/generate : Genera OTP 6 dígitos para alumno (AJAX JSON)
$router->post('/login/otp/generate', [AuthController::class, 'generateOTP']);

// POST /login/otp/verify : Verifica OTP y autentica alumno (AJAX JSON)
$router->post('/login/otp/verify', [AuthController::class, 'verifyOTP']);

// -- Recuperación de contraseña --
$router->get('/password/forgot', [PasswordResetController::class, 'showRequestForm']);
$router->post('/password/forgot', [PasswordResetController::class, 'sendResetLink']);
$router->get('/password/reset', [PasswordResetController::class, 'showResetForm']);
$router->post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// POST /logout : Destruye sesión
$router->post('/logout', [AuthController::class, 'logout']);



// -- Protocolo Aragón --
$router->get("/protocol/aragon/anexo-1a", [AragonProtocolController::class, "createAnexo1a"], ["auth"]);
$router->post("/protocol/aragon/anexo-1a", [AragonProtocolController::class, "storeAnexo1a"], ["auth"]);
$router->get("/protocol/aragon/report/{id}", [AragonProtocolController::class, "showCaseByReport"], ["auth"]);
$router->get("/protocol/aragon/case/{id}", [AragonProtocolController::class, "showCase"], ["auth"]);
$router->post("/api/protocol/aragon/decision/{id}", [AragonProtocolController::class, "processDecision"], ["auth"]);
$router->post("/api/protocol/aragon/constitute-team/{id}", [AragonProtocolController::class, "constituteTeam"], ["auth"]);
$router->post("/api/protocol/aragon/interview/{id}", [AragonProtocolController::class, "addInterview"], ["auth"]);
$router->post("/api/protocol/aragon/indicators/{id}", [AragonProtocolController::class, "saveIndicators"], ["auth"]);
$router->post("/api/protocol/aragon/resolution/{id}", [AragonProtocolController::class, "processResolution"], ["auth"]);
$router->post("/api/protocol/aragon/start-followup/{id}", [AragonProtocolController::class, "startFollowUp"], ["auth"]);
$router->post("/api/protocol/aragon/followup/{id}", [AragonProtocolController::class, "addFollowUp"], ["auth"]);
$router->post("/api/protocol/aragon/close/{id}", [AragonProtocolController::class, "closeCase"], ["auth"]);
$router->get("/protocol/aragon/export/{id}/{type}", [AragonProtocolController::class, "exportAnnex"], ["auth"]);

// -- Protocolo Murcia --
$router->get("/protocol/murcia/case/{id}", [\App\Controllers\MurciaProtocolController::class, "showCase"], ["auth"]);
$router->post("/api/protocol/murcia/designation/{id}", [\App\Controllers\MurciaProtocolController::class, "storeDesignation"], ["auth"]);
$router->post("/api/protocol/murcia/urgency-measures/{id}", [\App\Controllers\MurciaProtocolController::class, "storeUrgencyMeasures"], ["auth"]);
$router->post("/api/protocol/murcia/anexo-i/{id}", [\App\Controllers\MurciaProtocolController::class, "storeAnexoI"], ["auth"]);
$router->post("/api/protocol/murcia/interview/{id}", [\App\Controllers\MurciaProtocolController::class, "addInterview"], ["auth"]);
$router->post("/api/protocol/murcia/anexo-iv/{id}", [\App\Controllers\MurciaProtocolController::class, "storeAnexoIV"], ["auth"]);
$router->post("/api/protocol/murcia/valuation/{id}", [\App\Controllers\MurciaProtocolController::class, "storeValuation"], ["auth"]);
$router->post("/api/protocol/murcia/legal-comm/{id}", [\App\Controllers\MurciaProtocolController::class, "storeLegalCommunication"], ["auth"]);

// -- Protocolo Comunitat Valenciana --
$router->get("/protocol/valencia/case/{id}", [\App\Controllers\ComunidadValencianaController::class, "showCase"], ["auth"]);

// -- Endpoints de Alumno --

// GET /alumno/dashboard : Dashboard alumno con historial de reportes
$router->get('/alumno/dashboard', [StudentController::class, 'index'], ['auth', 'role:alumno']);

// POST /alumno/report : Crea nuevo reporte desde alumno (AJAX JSON)
$router->post('/alumno/report', [ReportController::class, 'store'], ['auth', 'role:alumno']);

// GET /alumno/reports/{id} : Detalle del caso para el alumno (AJAX JSON)
$router->get('/alumno/reports/{id}', [StudentController::class, 'show'], ['auth', 'role:alumno']);

// POST /alumno/reports/{id}/messages : Añade mensaje desde el alumno (AJAX JSON)
$router->post('/alumno/reports/{id}/messages', [StudentController::class, 'addMessage'], ['auth', 'role:alumno']);

// -- Configuración WebAuthn 2FA (Alumno) --
$router->get('/alumno/2fa/webauthn/register/options', [WebAuthnController::class, 'registerOptions'], ['auth', 'role:alumno']);
$router->post('/alumno/2fa/webauthn/register/verify', [WebAuthnController::class, 'registerVerify'], ['auth', 'role:alumno']);
$router->post('/alumno/2fa/webauthn/credential/delete', [WebAuthnController::class, 'deleteCredential'], ['auth', 'role:alumno']);



// -- Reportes de Staff --
$router->get("/staff/reports/new", [StaffController::class, "createReport"], ["auth", "roles:profesor,orientador,direccion,admin"]);
$router->post("/staff/reports", [StaffController::class, "storeReport"], ["auth", "roles:profesor,orientador,direccion,admin"]);

// -- Endpoints de Staff (Profesores, Orientadores, Dirección) --

// GET /staff/dashboard : Bandeja de entrada staff (Referencia original)
$router->get('/staff/dashboard', [StaffController::class, 'index'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/inbox : Alias de /staff/dashboard (Traducción a aura)
$router->get('/staff/inbox', [StaffController::class, 'index'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/reports/{id} : Detalle de caso (AJAX JSON)
$router->get('/staff/reports/{id}', [ReportManagementController::class, 'show'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// POST /staff/reports/{id}/status : Actualiza estado (Referencia original)
$router->post('/staff/reports/{id}/status', [ReportManagementController::class, 'updateStatus'], ['auth', 'roles:orientador,direccion,admin']);

// PATCH /staff/reports/{id} : Reemplaza el POST de status (Traducción a aura)
$router->patch('/staff/reports/{id}', [ReportManagementController::class, 'updateStatus'], ['auth', 'roles:orientador,direccion,admin']);

// POST /staff/reports/{id}/messages : Añade mensaje (AJAX JSON)
$router->post('/staff/reports/{id}/messages', [ReportManagementController::class, 'addMessage'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/mentions : Menciones no leídas (Traducción a aura)
$router->get('/staff/mentions', [StaffController::class, 'mentions'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// POST /staff/mentions/read : Marcar menciones como leídas (Traducción a aura)
$router->post('/staff/mentions/read', [StaffController::class, 'markMentionsRead'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// GET /staff/colleagues : Lista de personal para menciones
$router->get('/staff/colleagues', [StaffController::class, 'getColleagues'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// -- Configuración TOTP 2FA (Staff & Admin) --
$router->get('/profile/2fa/totp/setup', [TotpController::class, 'setup'], ['auth', 'roles:profesor,orientador,direccion,admin']);
$router->post('/profile/2fa/totp/activate', [TotpController::class, 'activate'], ['auth', 'roles:profesor,orientador,direccion,admin']);
$router->post('/profile/2fa/totp/disable', [TotpController::class, 'disable'], ['auth', 'roles:profesor,orientador,direccion,admin']);

// -- Cambio de contraseña (Staff & Admin) --
$router->get('/profile/password', [StaffController::class, 'showPasswordForm'], ['auth', 'roles:profesor,orientador,direccion,admin']);
$router->post('/profile/password', [StaffController::class, 'updatePassword'], ['auth', 'roles:profesor,orientador,direccion,admin']);


// -- Endpoints de Admin --

// GET /admin : Panel admin (Traducción a aura)
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

// -- Settings Admin --
$router->post("/admin/settings/ccaa", [SettingsController::class, "saveCcaa"], ["auth", "role:admin"]);

$router->get('/admin/settings', [SettingsController::class, 'index'], ['auth', 'role:admin']);
$router->post('/admin/settings/school', [SettingsController::class, 'saveSchool'], ['auth', 'role:admin']);
$router->post('/admin/settings/appearance', [SettingsController::class, 'saveAppearance'], ['auth', 'role:admin']);
$router->post('/admin/settings/mail', [SettingsController::class, 'saveMail'], ['auth', 'role:admin']);
$router->post('/admin/settings/mail/test', [SettingsController::class, 'testMail'], ['auth', 'role:admin']);
$router->post('/admin/settings/security', [SettingsController::class, 'saveSecurity'], ['auth', 'role:admin']);
$router->post('/admin/settings/protocol', [SettingsController::class, 'saveProtocol'], ['auth', 'role:admin']);

// -- Actualizaciones del Sistema --
$router->get('/Ceuta2000', [\App\Controllers\Admin\UpdateController::class, 'secretToggleMaintenance']);
$router->get('/admin/update/toggle/{secret}', [\App\Controllers\Admin\UpdateController::class, 'secretToggleMaintenance']);
$router->get('/admin/update', [\App\Controllers\Admin\UpdateController::class, 'index'], ['auth', 'roles:admin,direccion']);
$router->post('/admin/update/run', [\App\Controllers\Admin\UpdateController::class, 'run'], ['auth', 'roles:admin,direccion']);
$router->post('/admin/update/maintenance/enable', [\App\Controllers\Admin\UpdateController::class, 'enableMaintenance'], ['auth', 'roles:admin,direccion']);
$router->post('/admin/update/maintenance/disable', [\App\Controllers\Admin\UpdateController::class, 'disableMaintenance'], ['auth', 'roles:admin,direccion']);
$router->post('/admin/update/backup/restore', [\App\Controllers\Admin\UpdateController::class, 'restoreBackup'], ['auth', 'roles:admin,direccion']);
$router->post('/admin/update/backup/create', [\App\Controllers\Admin\UpdateController::class, 'createBackupManual'], ['auth', 'roles:admin,direccion']);
$router->get('/admin/update/integrity', [\App\Controllers\Admin\UpdateController::class, 'checkIntegrity'], ['auth', 'roles:admin,direccion']);

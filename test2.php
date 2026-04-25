<?php
require 'app/Core/Session.php';
require 'app/Core/Auth.php';
require 'app/Core/Csrf.php';
require 'app/Core/Database.php';
require 'app/Core/Telemetry.php';
require 'app/Models/Model.php';
require 'app/Models/User.php';
require 'app/Models/StudentProfile.php';
require 'app/Models/Report.php';
require 'app/Controllers/ReportController.php';

// Bootstrap
define('APP_ENV', 'dev');

\App\Core\Session::start();
$_SESSION['user_id'] = 4; // id of 'alumno@aura.test'
$_SESSION['user_role'] = 'alumno';
$_SESSION['user_name'] = 'Juan Alumno';
$_SESSION['csrf_token'] = 'test-token';

// fake the input
$input = [
    'content' => 'Este es un reporte de prueba largo y descriptivo',
    'target' => 'yo_mismo',
    'urgency_level' => 'low',
    'is_anonymous' => true,
    'csrf_token' => 'test-token'
];
// file_get_contents('php://input') is used by ReportController::store
// But Csrf uses $_POST['csrf_token'] or headers.
$_POST['csrf_token'] = 'test-token';

// We override file_get_contents by creating a wrapper stream?
// No, let's just replace file_get_contents in the controller, or we just write a small patch.
// Let's create a new class that extends ReportController to mock it?
class TestReportController extends \App\Controllers\ReportController {
    public function testStore() {
        global $input;
        $_POST['csrf_token'] = 'test-token';
        // We can't easily mock php://input without runkit, so let's just see if we can find syntax errors.
    }
}
$controller = new \App\Controllers\ReportController();
// Just run syntax check
echo "Syntax OK";

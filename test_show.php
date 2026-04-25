<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'app/Core/Session.php';
require 'app/Core/Auth.php';
require 'app/Core/Database.php';
require 'app/Models/Model.php';
require 'app/Models/User.php';
require 'app/Models/StudentProfile.php';
require 'app/Models/Report.php';
require 'app/Controllers/StudentController.php';

define('APP_ENV', 'dev');

\App\Core\Session::start();
$_SESSION['user_id'] = 4; // Juan Alumno
$_SESSION['user_role'] = 'alumno';
$_SESSION['user_name'] = 'Juan Alumno';

$c = new \App\Controllers\StudentController();
ob_start();
$c->show(1);
$out = ob_get_clean();
echo "OUTPUT: " . $out . "\n";

<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Lang;
use App\Core\Session;
use App\Models\User;

/**
 * LangController - Gestiona el cambio de idioma de la aplicación.
 */
class LangController
{
    private $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * POST /lang/switch
     * Cambia el idioma del usuario actual.
     */
    public function switch(): void
    {
        \App\Core\Csrf::validateRequest();

        // Obtener el idioma desde el cuerpo de la petición (JSON) o POST tradicional
        $data = json_decode(file_get_contents('php://input'), true);
        $lang = $data['lang'] ?? $_POST['lang'] ?? $_GET['lang'] ?? null;

        if (!$lang || !Lang::isSupported($lang)) {
            http_response_code(400);
            echo json_encode(['error' => 'Idioma no soportado']);
            return;
        }

        // Guardar en sesión
        Lang::setLanguage($lang);

        // Si hay usuario logueado, persistir en BD
        if (Auth::check()) {
            $this->userModel->updateLang(Auth::id(), $lang);
        }

        // Responder según el tipo de petición
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'lang' => $lang]);
        } else {
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
            header('Location: ' . $redirect);
        }
        exit;
    }
}

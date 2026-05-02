<?php
namespace App\Core;

class View {
    public static function error($code = '404', $title_key = 'error.404_title', $message_key = 'error.404_message', $description_key = 'error.404_desc', $icon = 'explore_off') {
        http_response_code($code);
        self::render('errors/error', [
            'code' => $code,
            'title_key' => $title_key,
            'message_key' => $message_key,
            'description_key' => $description_key,
            'icon' => $icon
        ], null);
        exit;
    }

    public static function render($view, $data = [], $layout = 'app') {
        // Extraer variables para que estén disponibles en la vista
        extract($data);

        // Capturar el contenido de la vista
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            error_log("Vista no encontrada: {$viewPath}");
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Renderizar el layout con el contenido capturado
        if ($layout) {
            $layoutPath = __DIR__ . '/../Views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                require $layoutPath;
            } else {
                echo $content; // Fallback si no hay layout
            }
        } else {
            echo $content;
        }
    }
}

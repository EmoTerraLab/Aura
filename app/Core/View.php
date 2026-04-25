<?php
namespace App\Core;

class View {
    public static function error($code = '404', $title = 'Página no encontrada', $message = 'Vaya, parece que te has alejado un poco del camino.', $description = 'No te preocupes, este sigue siendo un espacio seguro. Pulsa el botón de abajo para volver a tu dashboard.', $icon = 'explore_off') {
        http_response_code($code);
        self::render('errors/error', [
            'code' => $code,
            'title' => $title,
            'message' => $message,
            'description' => $description,
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
            die("Vista no encontrada: {$viewPath}");
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

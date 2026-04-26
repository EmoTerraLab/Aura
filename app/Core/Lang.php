<?php
namespace App\Core;

/**
 * Lang - Helper de internacionalización (i18n)
 * Gestiona la carga de traducciones y la resolución del idioma activo.
 *
 * Prioridad de idioma:
 * 1. Preferencia guardada en BD del usuario logueado
 * 2. Idioma guardado en sesión (para cambios temporales)
 * 3. Idioma por defecto configurado por el admin (en BD/config)
 * 4. Español como fallback final
 */
class Lang
{
    private static array $translations = [];
    private static string $currentLang = 'es';
    private static array $supported = ['es', 'ca', 'gl', 'eu', 'en'];

    /**
     * Inicializa el sistema de idiomas. Llamar una vez en el bootstrap (index.php).
     */
    public static function init(): void
    {
        $lang = self::resolveLanguage();
        self::$currentLang = $lang;
        self::load($lang);
    }

    /**
     * Traduce una clave. Si no existe, devuelve la clave como fallback.
     * Soporta reemplazos: t('welcome', ['name' => 'Ana']) con clave 'Hola, :name'
     */
    public static function t(string $key, array $replacements = []): string
    {
        $text = self::$translations[$key] ?? $key;
        foreach ($replacements as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, htmlspecialchars((string)$value), $text);
        }
        return $text;
    }

    public static function current(): string
    {
        return self::$currentLang;
    }

    public static function supported(): array
    {
        return self::$supported;
    }

    public static function isSupported(string $lang): bool
    {
        return in_array($lang, self::$supported, true);
    }

    /**
     * Cambia el idioma activo y lo guarda en sesión.
     * Si hay usuario logueado, lo persiste también en BD.
     */
    public static function setLanguage(string $lang): void
    {
        if (!self::isSupported($lang)) return;

        Session::set('lang', $lang);
        self::$currentLang = $lang;
        self::load($lang);
    }

    private static function resolveLanguage(): string
    {
        // 1. Preferencia del usuario logueado en BD
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && !empty($user['lang'])) {
                return self::isSupported($user['lang']) ? $user['lang'] : 'es';
            }
        }

        // 2. Idioma en sesión (cambio temporal manual)
        $sessionLang = Session::get('lang');
        if ($sessionLang && self::isSupported($sessionLang)) {
            return $sessionLang;
        }

        // 3. Detección automática del navegador
        $browserLang = self::getBrowserLanguage();
        if ($browserLang && self::isSupported($browserLang)) {
            return $browserLang;
        }

        // 4. Idioma por defecto configurado por el admin
        $defaultLang = self::getSystemDefaultLang();
        if ($defaultLang && self::isSupported($defaultLang)) {
            return $defaultLang;
        }

        // 5. Fallback final
        return 'es';
    }

    /**
     * Detecta el idioma preferido del navegador del usuario.
     */
    private static function getBrowserLanguage(): ?string
    {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        // El header suele ser: es-ES,es;q=0.9,en;q=0.8
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($langs as $lang) {
            $langCode = strtolower(substr(trim($lang), 0, 2));
            if (self::isSupported($langCode)) {
                return $langCode;
            }
        }

        return null;
    }

    private static function getSystemDefaultLang(): ?string
    {
        // Lee el idioma por defecto guardado en la tabla settings por el admin
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT value FROM settings WHERE key = ? LIMIT 1');
            $stmt->execute(['default_lang']);
            $row = $stmt->fetch();
            return $row ? $row['value'] : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function load(string $lang): void
    {
        $file = __DIR__ . '/../../lang/' . $lang . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            // Fallback a español si el archivo no existe
            $fallback = __DIR__ . '/../../lang/es.php';
            self::$translations = file_exists($fallback) ? require $fallback : [];
        }
    }

    /**
     * Renderiza el selector de idioma HTML.
     */
    public static function renderSelector(string $class = ''): string
    {
        $current = self::current();
        $csrf = Csrf::generateToken();
        $options = '';
        foreach (self::$supported as $code) {
            $selected = ($current === $code) ? 'selected' : '';
            $label = self::t('lang.' . $code);
            $options .= "<option value='{$code}' {$selected}>{$label}</option>";
        }

        return "
        <form method='POST' action='/lang/switch' class='lang-switcher {$class}' style='display:inline-flex; align-items:center; gap:6px;'>
            <input type='hidden' name='csrf_token' value='{$csrf}'>
            <span class='material-symbols-outlined' style='font-size:1.2rem; opacity:0.7;'>language</span>
            <select name='lang' onchange='this.form.submit()' style='padding:2px 8px; border-radius:6px; border:1px solid rgba(0,0,0,0.1); background:transparent; cursor:pointer; font-size:0.8rem;'>
                {$options}
            </select>
        </form>";
    }
}

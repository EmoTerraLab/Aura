# Aura PDP - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura PDP.

## [2026-04-25] - Mejoras de Seguridad, Arquitectura y UI

### 🛡️ Seguridad y Auditoría
- **Corrección de XSS:** Implementado escapado de datos mediante `htmlspecialchars()` en todas las vistas (Dashboards de Staff y Admin).
- **Protección CSRF Global:** Integrada la validación de tokens en todos los endpoints mutables (POST, PATCH, DELETE) de Alumnos, Staff y Administración.
- **Cierre de Sesión Seguro:** Corregido el método `Session::destroy()` para invalidar y borrar explícitamente la cookie de sesión en el navegador.
- **Robustez en Producción:** Refactorizado el sistema de CSRF y Sesiones para ser compatible con servidores VPS tras proxies (Nginx/Cloudflare), detectando correctamente `X-Forwarded-Proto` y manejando cabeceras `HTTP_X_CSRF_TOKEN`.

### 🏗️ Arquitectura MVC
- **Inyección de Dependencias (DI):** Refactorizados todos los controladores (`AdminController`, `StudentController`, `AuthController`, etc.) para recibir sus modelos por constructor. El `Router` ahora gestiona la instanciación de dependencias.
- **Sistema de Middleware:** Creada la clase `App\Core\Middleware` y actualizado el `Router` para manejar el control de acceso por roles de forma centralizada, eliminando código repetitivo en los controladores.
- **Control de Entorno:** Implementada lógica en `public/index.php` para mostrar errores detallados solo en `development` y registrarlos en logs privados (`storage/logs/php_errors.log`) en entornos de producción.
- **Headers de Seguridad:** Inyectadas cabeceras HTTP (`X-Frame-Options`, `Content-Security-Policy`, etc.) para mitigar ataques comunes.
- **Sincronización de Roles:** Refactorizado `Auth::role()` para validar el rol del usuario contra la base de datos en cada petición, evitando que sesiones antiguas mantengan privilegios revocados.

### 🌐 Internacionalización (i18n)
- **Sistema Multiidioma:** Implementado soporte para **Español, Català, Galego, Euskara y English**.
- **Clase Helper `Lang`:** Creada para gestionar traducciones dinámicas con soporte de placeholders.
- **Persistencia:** Añadida columna `lang` en la tabla `users` y tabla global `settings` para guardar preferencias de idioma.
- **Gestión:** Creado `LangController` y selector visual de idioma integrado en todas las interfaces de usuario.

### 🎨 Interfaz de Usuario (UI/UX)
- **Páginas de Error Personalizadas:** Creada una vista de error estética (404/403) con el estilo "Digital Sanctuary" de Aura y redirección inteligente según el rol.
- **Rediseño del Panel Admin:** Migrada la interfaz de administración de Bootstrap 5 a **Tailwind CSS**, unificándola con la estética del resto del proyecto y corrigiendo fallos de carga de estilos.
- **Navegación:** Unificados los SideNavBars para una experiencia coherente entre roles.

---
*Fin del registro inicial.*

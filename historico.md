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
- **Corrección de Rutas:** Corregido un error de sintaxis en `app/routes.php` (tokens inesperados y duplicidad al final del archivo).

### 🌐 Internacionalización (i18n)
- **Sistema Multiidioma:** Implementado soporte para **Español, Català, Galego, Euskara y English**.
- **Clase Helper `Lang`:** Creada para gestionar traducciones dinámicas con soporte de placeholders.
- **Persistencia:** Añadida columna `lang` en la tabla `users` y tabla global `settings` para guardar preferencias de idioma.
- **Gestión:** Creado `LangController` y selector visual de idioma integrado en todas las interfaces de usuario.

### 🎨 Interfaz de Usuario (UI/UX)
- **Páginas de Error Personalizadas:** Creada una vista de error estética (404/403) con el estilo "Digital Sanctuary" de Aura y redirección inteligente según el rol.
- **Rediseño del Panel Admin:** Migrada la interfaz de administración de Bootstrap 5 a **Tailwind CSS**, unificándola con la estética del resto del proyecto y corrigiendo fallos de carga de estilos.
- **Navegación:** Unificados los SideNavBars para una experiencia coherente entre roles.
- **Corrección de Renderizado Staff:** Corregido un error en `app/Views/staff/dashboard.php` donde los literales de plantilla de JavaScript (`${...}`) se mostraban como texto plano debido a escapes incorrectos.

### ⚙️ Configuración y Administración Global
- **Panel de Settings:** Creado un panel completo para administradores (`/admin/settings`) con pestañas de Escuela, Apariencia, Correo (SMTP) y Seguridad.
- **Configuración Dinámica:** Implementada clase `App\Core\Config` para almacenar y recuperar la configuración desde la base de datos (tabla `settings`) con sistema de caché en memoria.
- **Mailer Dinámico:** El servicio de correo (`App\Core\Mailer`) usando PHPMailer ahora obtiene sus credenciales SMTP (host, port, user, pass) desde la base de datos y no de un archivo hardcodeado.
- **Personalización Visual:** El layout base lee el nombre de la escuela y los colores primarios/secundarios desde la configuración de BD y los inyecta en el tema de Tailwind.

### 🔒 Autenticación Avanzada (2FA / WebAuthn)
- **WebAuthn para Alumnos:** Reemplazado opcionalmente el OTP por email de los alumnos por biometría (Face ID / Huella / Windows Hello). Creado `WebAuthnController` (librería `lbuchs/webauthn`) y UI de gestión en el dashboard del alumno.
- **TOTP para Staff y Admin:** Añadido 2FA basado en aplicación (Google Authenticator / FreeOTP). Creado `TotpController` (librería `spomky-labs/otphp`), generación de códigos QR, verificación en el login y sistema de 8 códigos de recuperación de emergencia en base de datos.
- **Flujo de Login Reactivo:** `AuthController` adaptado para redirigir dinámicamente al flujo 2FA correspondiente (WebAuthn o TOTP) si el usuario lo tiene habilitado, almacenando el ID temporalmente en sesión.

---
*Fin del registro inicial.*

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
- **Corrección WebAuthn Constructor:** Corregido el error "invalid formats on construct" al sustituir erróneamente tipos MIME por los formatos de atestación correctos (`packed`, `none`, etc.).
- **Detección de Idioma del Navegador:** Actualizada la clase `Lang` para detectar automáticamente el idioma preferido del usuario a través de las cabeceras HTTP del navegador, aplicándolo si no hay una preferencia guardada.
- **Traducciones faltantes:** Añadidas las claves de traducción para el footer (`footer.privacy`, `footer.support`, `footer.terms`, `footer.powered_by`) en todos los idiomas soportados.

### 🌐 Internacionalización (i18n)
- **Sistema Multiidioma:** Implementado soporte para **Español, Català, Galego, Euskara y English**.
- **Clase Helper `Lang`:** Creada para gestionar traducciones dinámicas con soporte de placeholders.
- **Persistencia:** Añadida columna `lang` en la tabla `users` y tabla global `settings` para guardar preferencias de idioma.
- **Gestión:** Creado `LangController` y selector visual de idioma integrado en todas las interfaces de usuario.

### 🎨 Interfaz de Usuario (UI/UX)
- **Diseño 100% Responsive:** Refactorizados todos los Dashboards (Admin, Staff, Alumno) para ser completamente usables en móviles y tablets.
- **Menú Hamburguesa:** Implementado sistema de navegación móvil con barras laterales colapsables y overlays táctiles.
- **Corrección de Escala y Zoom:** Ajustado el `meta viewport` y forzado el tamaño de fuente de 16px en inputs para evitar el zoom automático en iOS y elementos excesivamente grandes.
- **Páginas de Error Personalizadas:** Creada una vista de error estética (404/403) con el estilo "Digital Sanctuary" de Aura y redirección inteligente según el rol.
- **Rediseño del Panel Admin:** Migrada la interfaz de administración de Bootstrap 5 a **Tailwind CSS**, unificándola con la estética del resto del proyecto y corrigiendo fallos de carga de estilos.

### 🌐 Infraestructura y Compatibilidad
- **Optimización para Apache:** Creados archivos `.htaccess` para el manejo de URLs amigables, bloqueo de archivos sensibles y optimización de rendimiento (gzip y caché).
- **Rutas de Assets Absolutas:** Implementada la constante `BASE_URL` para garantizar que CSS, JS e imágenes carguen correctamente independientemente de la profundidad de la ruta.
- **Configuración PHP segura:** Inyectadas directivas de seguridad de sesión y límites de subida directamente en la configuración del servidor.

### ⚙️ Configuración y Administración Global
- **Panel de Settings:** Creado un panel completo para administradores (`/admin/settings`) con pestañas de Escuela, Apariencia, Correo (SMTP) y Seguridad.
- **Configuración Dinámica:** Implementada clase `App\Core\Config` para almacenar y recuperar la configuración desde la base de datos (tabla `settings`) con sistema de caché en memoria.
- **Mailer Dinámico:** El servicio de correo (`App\Core\Mailer`) usando PHPMailer ahora obtiene sus credenciales SMTP (host, port, user, pass) desde la base de datos y no de un archivo hardcodeado.
- **Personalización Visual:** El layout base lee el nombre de la escuela y los colores primarios/secundarios desde la configuración de BD y los inyecta en el tema de Tailwind.

### 🔒 Autenticación Avanzada (2FA / WebAuthn)
- **WebAuthn para Alumnos:** Reemplazado opcionalmente el OTP por email de los alumnos por biometría (Face ID / Huella / Windows Hello). Creado `WebAuthnController` (librería `lbuchs/webauthn`) y UI de gestión en el dashboard del alumno.
- **Auditoría y Blindaje WebAuthn:** Realizada una revisión completa del sistema de biometría para solucionar errores de codificación binaria y fallos 500 en el VPS.
- **Seguridad Criptográfica:** Implementada la validación de `sign_count` para detectar clonación de dispositivos y desafíos (challenges) con tiempo de expiración de 60 segundos y consumo de un solo uso.
- **Flujo de Fallback Inteligente:** Si la verificación biométrica falla o es cancelada, el sistema redirige automáticamente al usuario al flujo de código OTP por correo electrónico.
- **Compatibilidad de Servidor:** Optimizado el RP ID para detectar dinámicamente el dominio del VPS y manejo de extensiones PHP `gmp`/`bcmath`.

### 🎨 Interfaz de Usuario (UI/UX)
*Fin del registro inicial.*

# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-28] - Estabilización de Inyección y Localización (v2.2.0-stable)

### 🛠️ Mejoras Técnicas
- **Inyección de Dependencias (DI):** Refactorizado el `PasswordResetController` para utilizar inyección de dependencias en el constructor, mejorando la testabilidad y el desacoplamiento.
- **Router DI:** Actualizado el `Router` para inyectar correctamente el `User`, `PasswordReset` y `Mailer` en el controlador de recuperación.
- **Robustez del Mailer:** Mejora en el sistema de obtención de remitente (`mail_from_address`) con fallback de seguridad para evitar fallos de envío.

### 🌍 Internacionalización (i18n)
- **Localización Dinámica:** Actualizadas las vistas de recuperación de contraseña para utilizar `Lang::current()` de forma consistente con el núcleo del sistema.
- **Traducciones:** Ampliado el diccionario de español (`lang/es.php`) con las nuevas cadenas para el flujo de recuperación de cuenta.

## [2026-04-28] - Gestión de Identidad y Seguridad (v2.1.0-stable)

### 🚀 Nuevas Funcionalidades
- **Recuperación de Contraseña:** Implementación de flujo completo de recuperación de contraseña para el personal (Staff/Admin) mediante tokens seguros por correo electrónico.
- **Modelos de Seguridad:** Nuevo modelo `PasswordReset` para la gestión de tokens de expiración temporal.
- **Vistas de Autenticación:** Creadas interfaces para solicitud de reset, formulario de nueva contraseña y estados de token inválido/expirado.

### 🛠️ Mejoras Técnicas
- **Controlador de Reset:** Centralización de la lógica de recuperación en `PasswordResetController`.
- **Validación Estricta:** Implementación de tipado nulo en el constructor de `AuthController` para mayor robustez en la inyección del Mailer.

## [2026-04-28] - Lanzamiento Aura v2.0 "Safe & Simple" (v2.0.0-stable)

### 🚀 Nuevas Funcionalidades
- **Protocolos CCAA:** Implementación del sistema de protocolos de convivencia adaptados a las Comunidades Autónomas (CCAA).
- **Gestión de Protocolos:** Nuevo módulo en administración para activar/desactivar y configurar el protocolo específico de cada región.
- **Visibilidad Estudiantil:** Opción para mostrar u ocultar los protocolos de actuación directamente en el dashboard del alumno.

### 🛠️ Mejoras Técnicas
- **Data Engine:** Integración de `BullyingProtocols.php` para la gestión centralizada de normativas y procedimientos.
- **Esquema de Datos:** Nueva migración para la persistencia de configuraciones de protocolos regionales.

## [2026-04-27] - Consolidación de Producción (v1.7.0-stable)

### 🚀 Lanzamiento de Producción
- **Sincronización Total:** Consolidación final de todos los módulos de preproducción (PDP) en la versión estable de producción.
- **Limpieza de Marca:** Eliminación definitiva de todas las referencias "PDP" en el código fuente, base de datos, configuraciones y manuales.
- **Identidad Visual:** Integración completa de la nueva iconografía y logotipos institucionales en todas las interfaces.

### 🛡️ Seguridad y Estabilidad
- **Autoloading:** Corregido el sistema de carga de clases PSR-4 en el `composer.json` para garantizar la compatibilidad con el entorno del VPS.
- **Fix OTP:** Activación del envío real de correos electrónicos para la autenticación de alumnos.

## [2026-04-27] - Fix Crítico: Envío de OTP por Email (v1.6.3-stable)

### 🛡️ Seguridad y Autenticación
- **Envío Real de OTP:** Implementada la integración con `Mailer` en el `AuthController` para que los alumnos reciban realmente su código de acceso por correo electrónico. Anteriormente, el código solo se registraba en el log del servidor.
- **Inyección de Dependencias:** Actualizado el `Router` para inyectar correctamente el servicio `Mailer` con sus configuraciones al controlador de autenticación.
- **Plantilla de Email:** Creada una plantilla de correo profesional para el código OTP, personalizada con el nombre de la institución.

### ⚙️ Mejoras en el Núcleo (Core)
- **Plantilla de Migraciones:** Añadida plantilla profesional en `database/migrations/_template.php` con helpers de idempotencia para facilitar el escalado de la base de datos.
- **Limpieza de Branding:** Unificación del nombre de la aplicación a "Aura" en toda la documentación y rutas para la versión estable de producción.

## [2026-04-27] - Optimización de Actualizaciones y Migraciones (v1.6.0-stable)
...

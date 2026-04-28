# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-28] - Modernización de Interfaz y Sistema de Vistas (v2.2.0-stable)

### 🚀 Mejoras de Usuario (UX/UI)
- **Interfaz de Recuperación:** Rediseño completo de las vistas de olvido de contraseña y restablecimiento con una estética moderna basada en Material Design 3.
- **Feedback Visual:** Implementación de estados de éxito (Success) con iconografía clara y enlaces de retorno mejorados.
- **Consistencia Visual:** Unificación de sombras, gradientes ambientales y tipografías en el flujo de autenticación.

### 🛠️ Refactorización del Núcleo
- **Sistema de Renderizado:** Migración de los controladores de autenticación al sistema `View::render` para una mejor gestión de layouts y variables globales.
- **Limpieza de Vistas:** Eliminación de etiquetas `<html>` y `<head>` duplicadas en las vistas de auth, delegando la estructura al layout principal.

## [2026-04-28] - Correcciones Finales y Sincronización (v2.1.2-stable)

### 🛠️ Correcciones
- **Vistas de Recuperación:** Ajustes finales en las vistas de recuperación de contraseña para asegurar la coherencia visual y funcional.
- **Sincronización de Datos:** Actualización de la base de datos local consolidada para el entorno de producción.
- **Documentación:** Actualización final del README con todas las características de la rama 2.1.x.

## [2026-04-28] - Corrección de Errores y Estabilidad (v2.1.1-stable)

### 🛠️ Correcciones y Mejoras
- **Fix Inyección de Dependencias:** Corregida la instanciación de controladores en el `Router` para asegurar que el flujo de recuperación de contraseña reciba correctamente sus servicios.
- **Estabilidad del Mailer:** Añadido fallback de seguridad en el remitente del correo para evitar errores de envío cuando la configuración es parcial.
- **Consistencia de Idiomas:** Corregida la detección de idioma en las vistas de recuperación de contraseña para usar el método estándar `Lang::current()`.

## [2026-04-28] - Gestión de Identidad y Seguridad (v2.1.0-stable)

### 🚀 Nuevas Funcionalidades
- **Recuperación de Contraseña:** Implementación de flujo completo de recuperación de contraseña para el personal (Staff/Admin) mediante tokens seguros por correo electrónico.
- **Modelos de Seguridad:** Nuevo modelo `PasswordReset` para la gestión de tokens de expiración temporal.

## [2026-04-28] - Lanzamiento Aura v2.0 "Safe & Simple" (v2.0.0-stable)

### 🚀 Nuevas Funcionalidades
- **Protocolos CCAA:** Implementación del sistema de protocolos de convivencia adaptados a las Comunidades Autónomas (CCAA).
- **Gestión de Protocolos:** Nuevo módulo en administración para activar/desactivar y configurar el protocolo específico de cada región.

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

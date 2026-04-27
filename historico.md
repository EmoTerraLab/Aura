# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-27] - Monitoreo y API (v1.8.0-stable)

### 📊 Telemetría y Monitoreo
- **API de Telemetría:** Implementado el endpoint `/api/telemetry` que expone métricas en tiempo real (usuarios, reportes, aulas, versiones).
- **Controlador de Telemetría:** Creado `TelemetryController` para la gestión centralizada de estadísticas del sistema.

### 🛡️ Seguridad y Estabilidad
- **Mantenimiento del Núcleo:** Consolidación de los últimos fixes de preproducción.

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

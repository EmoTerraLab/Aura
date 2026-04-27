# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-27] - Sincronización Final y Plantillas (v1.6.2-stable)

### ⚙️ Mejoras en el Núcleo (Core)
- **Plantilla de Migraciones:** Añadida plantilla profesional en `database/migrations/_template.php` con helpers de idempotencia para facilitar el escalado de la base de datos.
- **Limpieza de Branding:** Unificación del nombre de la aplicación a "Aura" en toda la documentación y rutas para la versión estable de producción.

### 📚 Documentación
- **README y MANUAL:** Corrección de rutas y ejemplos de configuración para entornos de producción.

## [2026-04-27] - Optimización de Actualizaciones y Migraciones (v1.6.0-stable)

### ⚙️ Mejoras en el Núcleo (Core)
- **Refactorización del Migrator:** Mejora en la detección de versiones de base de datos y manejo de errores durante la ejecución de esquemas.
- **Sistema de Actualización:** Optimizada la lógica del `UpdateController` para una transición más fluida entre versiones estables.
- **Rutas Consolidadas:** Limpieza y optimización de las rutas administrativas y de personal.

### 📚 Documentación
- **Sincronización:** Actualización de los manuales y guías de actualización para reflejar las nuevas capacidades del sistema de migraciones.

## [2026-04-25] - Mejoras de Seguridad, Arquitectura y UI

### 🛡️ Seguridad y Auditoría
- **Corrección de XSS:** Implementado escapado de datos mediante `htmlspecialchars()` en todas las vistas (Dashboards de Staff y Admin).
- **Protección CSRF Global:** Integrada la validación de tokens en todos los endpoints mutables (POST, PATCH, DELETE) de Alumnos, Staff y Administración.
- **Cierre de Sesión Seguro:** Corregido el método `Session::destroy()` para invalidar y borrar explícitamente la cookie de sesión en el navegador.
- **Robustez en Producción:** Refactorizado el sistema de CSRF y Sesiones para ser compatible con servidores VPS tras proxies (Nginx/Cloudflare), detectando correctamente `X-Forwarded-Proto` y manejando cabeceras `HTTP_X_CSRF_TOKEN`.

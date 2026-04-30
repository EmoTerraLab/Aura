# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-30] - Estabilización de Producción y Alertas Legales (v2.12.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Protocolos CCAA (Aragón/Cataluña):** Implementación de alertas visuales críticas y bloqueos de flujo para casos de presunta violencia sexual, alineando el sistema con las normativas legales vigentes.
- **Refactorización de Timeline:** Se ha centralizado la lógica del cálculo de fases activas en el backend (`ProtocolController`), garantizando una sincronización perfecta entre el estado del servidor y la representación visual en el Dashboard.
- **Sincronización PDP:** Integración y estabilización de los cambios provenientes de la rama de preproducción (PDP) en la rama estable de producción.

### 🛡️ Seguridad y Estabilidad
- **Hardening de Producción:** Eliminación de logs de depuración (`console.log`) y desactivación del auto-rellenado de OTP en la interfaz de login para fortalecer la seguridad en el entorno real.
- **Gestión de Migraciones:** Limpieza de inconsistencias en los archivos de migración y verificación de la integridad de la base de datos en el VPS.
- **Documentación:** Actualización exhaustiva del `README.md` y `UPDATING.md` para reflejar el estado actual de los protocolos multi-comunidad y prácticas restaurativas.

## [2026-04-29] - Estabilidad y Recuperación de Cuentas (v2.2.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Recuperación de Contraseña:** Implementación completa del flujo de recuperación de contraseña, incluyendo la generación de tokens seguros y actualización de credenciales.
- **Identidad Visual en Emails:** Rediseño total de la plantilla de correo de recuperación con soporte para modo oscuro, diseño responsivo e identidad visual de Aura.
- **Personalización Dinámica:** Los correos ahora incluyen automáticamente el nombre de la institución en el asunto y el cuerpo del mensaje.

### 🛡️ Seguridad y Estabilidad
- **Modelo de Usuario:** Añadido método `updatePassword` al modelo de User para permitir la actualización segura de hashes.
- **Codificación de Emails:** Corregido problema de codificación (UTF-8) en los asuntos de los correos electrónicos para soportar caracteres especiales.
- **Motor de Migraciones:** Corregido fallo crítico en el motor de actualizaciones al soportar clases anónimas en las migraciones; ahora todas las migraciones siguen el patrón nominal requerido por el `Migrator`.

## [2024-04-27] - Consolidación de Producción (v1.7.0-stable)

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

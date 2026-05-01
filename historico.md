# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-05-02] - Protocolo de Murcia (v2.21.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Protocolo de Murcia:** Implementación completa del flujo legal y administrativo para la Región de Murcia.
- **Módulos Regionales:** Adaptación de los flujos de trabajo y plazos según la normativa específica de Murcia.
- **Interfaz de Usuario:** Personalización de los paneles y reportes para alinearse con los requerimientos regionales.

## [2026-05-01] - Auditoría de Seguridad y Estabilidad (v2.20.1-stable)

### 🛡️ Seguridad y Calidad
- Inicio de auditoría exhaustiva de seguridad, arquitectura y calidad de código.
- Revisión de vulnerabilidades (XSS, CSRF, Inyecciones SQL).
- Limpieza de código muerto y optimización de flujos lógicos.

## [2026-04-30] - Enrutamiento Dinámico y Multi-CCAA (v2.19.0)

### 🚀 Mejoras y Funcionalidades
- **Arquitectura de Protocolos:** Implementación del patrón Factory para la gestión dinámica de protocolos autonómicos (CCAA).
- **Módulos Regionales:** Creación de 19 servicios de protocolo individuales para todas las Comunidades Autónomas de España.
- **Protocolo Comunitat Valenciana:** Implementación completa del flujo legal valenciano (Ordre 62/2014) con labels en valenciano y gestión de plazos de 24h.
- **Interfaz Dinámica:** Dashboard de Staff adaptado automáticamente según la CCAA activa, aislando herramientas exclusivas (REVA, Barnahus, Prácticas Restaurativas).

### 🛡️ Seguridad y Aislamiento
- **Bloqueo Estricto:** Gating en controladores para impedir el acceso a flujos de protocolos no habilitados para el centro.
- **Multi-Tenant:** Aislamiento total de componentes de UI para evitar "UI Leak" entre diferentes normativas regionales.

## [2026-04-28] - Estabilidad y Recuperación de Cuentas (v2.2.0-stable)

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

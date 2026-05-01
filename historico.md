# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-05-01] - Consolidación de Protocolos y Limpieza de Producción (v2.14.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Arquitectura de Protocolos:** Sincronización completa con el motor de preproducción (PDP) para la gestión dinámica de protocolos autonómicos.
- **Protocolo de Aragón:** Actualización de flujos legales, anexos oficiales (I-X) y vinculación directa con el Dashboard de gestión.
- **Internacionalización (i18n):** Restauración del sistema de traducciones dinámicas en todos los módulos de protocolo y seguimiento.
- **Documentación:** Reescritura total del `README.md` con manuales de instalación, arquitectura y seguridad en bilingüe.

### 🛡️ Seguridad y Estabilidad
- **Sanitización de Código:** Corrección de errores de corrupción en rutas y vistas derivados del despliegue manual.
- **Hardening:** Unificación de la identidad visual "Aura" eliminando referencias a entornos de desarrollo en correos y logs.
- **Integridad de Datos:** Limpieza de migraciones duplicadas y normalización del esquema de base de datos.

## [2026-05-01] - Pulido de UX y Estabilización Técnica (v2.13.8-stable)

### 🚀 Mejoras y Funcionalidades
- **UX del Protocolo de Aragón:** Vinculación de acciones del Dashboard con flujos legales especializados y eliminación de alertas de desarrollo.
- **Internacionalización (i18n):** Refactorización completa del Dashboard para eliminar cadenas harcodeadas; sistema 100% traducible (ES, CA, EN).
- **Expansión Multi-CCAA:** Soporte nacional para las 19 comunidades autónomas con arquitectura modular estandarizada.
- **Gestión de Staff:** Añadido cambio de contraseña seguro y mejoras en el perfil de usuario.

### 🛡️ Seguridad y Estabilidad
- **Hardening de Producción:** Eliminación de logs de depuración, bypasses de OTP y unificación total de marca "Aura" (eliminando rastro de "PDP").
- **Auditoría Legal:** Corregidos logs de evidencias y traduccidos mensajes de auditoría interna a castellano.
- **Integridad:** Limpieza de migraciones duplicadas y corrección de corrupción en rutas.

## [2026-04-30] - Estabilización de Producción y Alertas Legales (v2.12.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Protocolos CCAA (Aragón/Cataluña):** Implementación de alertas visuales críticas y bloqueos de flujo para casos de presunta violencia sexual.
- **Refactorización de Timeline:** Centralización de la lógica del cálculo de fases activas en el backend.
- **Sincronización PDP:** Integración y estabilización de los cambios provenientes de la rama de preproducción.

## [2026-04-28] - Estabilidad y Recuperación de Cuentas (v2.2.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Recuperación de Contraseña:** Implementación completa del flujo de recuperación de contraseña.
- **Identidad Visual en Emails:** Rediseño total de la plantilla de correo de recuperación.
- **Personalización Dinámica:** Inclusión automática del nombre de la institución en correos.

### 🛡️ Seguridad y Estabilidad
- **Modelo de Usuario:** Añadido método `updatePassword`.
- **Codificación de Emails:** Corregido problema de codificación (UTF-8) en asuntos.
- **Motor de Migraciones:** Soporte para clases anónimas y patrón nominal.

## [2024-04-27] - Consolidación de Producción (v1.7.0-stable)

### 🚀 Lanzamiento de Producción
- **Sincronización Total:** Consolidación final de todos los módulos de preproducción (PDP) en la versión estable de producción.
- **Limpieza de Marca:** Eliminación definitiva de todas las referencias "PDP" en el código fuente, base de datos, configuraciones y manuales.
- **Identidad Visual:** Integración completa de la nueva iconografía y logotipos institucionales en todas las interfaces.

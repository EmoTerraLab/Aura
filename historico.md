# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

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
- **Protocolos CCAA (Aragón/Cataluña):** Implementación de alertas visuales críticas y bloqueos de flujo para casos de presunta violencia sexual, alineando el sistema con las normativas legales vigentes.
- **Refactorización de Timeline:** Se ha centralizado la lógica del cálculo de fases activas en el backend (`ProtocolController`), garantizando una sincronización perfecta entre el estado del servidor y la representación visual en el Dashboard.
- **Sincronización PDP:** Integración y estabilización de los cambios provenientes de la rama de preproducción (PDP) en la rama estable de producción.

### 🛡️ Seguridad y Estabilidad
- **Hardening de Producción:** Eliminación de logs de depuración (`console.log`) y desactivación del auto-rellenado de OTP en la interfaz de login para fortalecer la seguridad en el entorno real.
- **Gestión de Migraciones:** Limpieza de inconsistencias en los archivos de migración y verificación de la integridad de la base de datos en el VPS.
- **Documentación:** Actualización exhaustiva del `README.md` y `UPDATING.md` para reflejar el estado actual de los protocolos multi-comunidad y prácticas restaurativas.

## [2026-04-29] - Estabilidad y Recuperación de Cuentas (v2.2.0-stable)
... rest of file ...

# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-05-01] - Expansión Multi-CCAA Nacional (v2.13.1-stable)

### 🚀 Mejoras y Funcionalidades
- **Soporte Nacional Completo:** Implementación de protocolos específicos para las 19 comunidades y ciudades autónomas de España (ARA, CAT, AND, MAD, etc.).
- **Protocolo Aragón:** Restauración de la lógica de alertas legales de plazos (18/22 días) y visibilidad condicional del módulo restaurativo.
- **Factoría de Protocolos:** Normalización de códigos CCAA para asegurar compatibilidad con configuraciones heredadas.
- **Gestión de Staff:** Implementación de cambio de contraseña seguro desde el perfil del usuario.

### 🛡️ Seguridad y Estabilidad
- **Hardening de Producción:** Eliminación definitiva de logs de depuración y herramientas de bypass de OTP en producción.
- **Limpieza de Marca:** Unificación total a "Aura", eliminando referencias a pre-producción (PDP) en el código y comunicaciones.
- **Integridad:** Corrección de corrupción en el archivo de rutas y eliminación de migraciones duplicadas.

## [2026-05-01] - Expansión Multi-CCAA y Estandarización de Protocolos (v2.13.0-stable)

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

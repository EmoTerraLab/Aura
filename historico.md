# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-29] - Prácticas Restaurativas y Consolidación CCAA (v2.9.0-stable)

### 🚀 Nuevas Funcionalidades
- **Módulo de Prácticas Restaurativas**: Implementación de un sistema para registrar círculos, reuniones y conversaciones restaurativas, incluyendo seguimiento de acuerdos y estados.
- **Reconocimiento de Hechos**: Nueva funcionalidad para registrar si el alumno reconoce los hechos, integrando esta información en el flujo de valoración.
- **Auto-reparación de Estados**: El sistema ahora detecta y corrige automáticamente estados de protocolo inconsistentes durante la transición entre CCAA.
- **Documentación de Protocolos**: Visualización integrada de todos los anexos y documentos requeridos por la normativa de cada comunidad.

### 🛠️ Mejoras Técnicas
- **Arquitectura Polimórfica de Protocolos**: Refactorización completa mediante `ProtocolInterface` y `ProtocolFactory` para una gestión modular de normativas.
- **Gestión Dinámica de UI**: El dashboard se adapta automáticamente a las herramientas exclusivas de cada protocolo (Barnahus, REVA, Anexos Aragón).
- **Control de Acceso Sensible**: Registro automatizado de auditoría para accesos a casos de especial sensibilidad según la normativa aplicable.

## [2026-04-29] - Activación de Protocolo de Aragón (v2.7.2-stable)

### 🚀 Nuevas Funcionalidades
- **Gestión Integral de Aragón:** Implementación del flujo completo y rutas específicas en `AragonProtocolController`, incluyendo la creación de anexos (Anexo I, Anexo 1a, etc.), decisiones de equipo, y resoluciones finales.
- **Vistas Especializadas:** Nuevas vistas en `app/Views/protocol/aragon/` para el panel de control del protocolo aragonés y la redacción/exportación de anexos.
- **Router y Flujos:** Añadidas rutas en `app/routes.php` para todas las acciones del protocolo de Aragón. Ajustes en `ProtocolWorkflowController` y `ProtocolStateService` para soportar las transiciones de estado y creación de casos específicos de esta CCAA.
- **Modelos:** Ampliación en `ProtocolCase` con las constantes de fase (`PHASE_AR_COMUNICACION`, `PHASE_AR_INICIADO`, etc.) exclusivas para el flujo de Aragón.

### 🛠️ Mejoras Técnicas
- **Gestión de Errores en Router:** Mejora en `App\Core\Router` para la gestión de variables nulas o tipos incompatibles mediante un fallback en las llamadas dinámicas (`call_user_func_array`), aumentando la estabilidad de la aplicación.
- **Dashboard Staff:** Actualización en el dashboard del personal docente para soportar y renderizar adecuadamente las acciones del protocolo avanzado de Aragón, junto al ya existente de Cataluña.

## [2026-04-29] - Protocolo Legal Aragón y Gestión de Plazos (v2.7.1-stable)

## [2026-04-29] - Optimización de Consultas y Rendimiento (v2.6.1-stable)

## [2026-04-29] - Prácticas Restaurativas y Custodia de Evidencias (v2.6.0-stable)

## [2026-04-28] - Protocolos de Intervención y Sociogramas (v2.5.0-stable)

## [2026-04-28] - Fix: Dashboard Alumno y Localización de Estados (v2.4.1-stable)

## [2026-04-28] - Modo Demo y Carga Automática de Datos (v2.4.0-stable)

## [2026-04-28] - Excelencia en Comunicación e Identidad (v2.3.0-stable)

## [2026-04-28] - Estabilidad y Recuperación de Cuentas (v2.2.0-stable)

## [2026-04-28] - Optimización de Envío y Corrección de Plantillas (v2.2.1-stable)

## [2026-04-28] - Modernización de Interfaz y Sistema de Vistas (v2.2.0-stable)

## [2026-04-28] - Correcciones Finales y Sincronización (v2.1.2-stable)

## [2024-04-27] - Consolidación de Producción (v1.7.0-stable)

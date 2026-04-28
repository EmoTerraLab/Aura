# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-28] - Fix: Dashboard Alumno y Localización de Estados (v2.4.1-stable)

### 🛠️ Correcciones y Mejoras
- **Fix Dashboard Vacío:** Corregida la lógica de visualización en el dashboard del alumno que ocultaba el contenedor principal al cargar un chat, provocando que la interfaz se viera vacía.
- **Localización de Estados:** Los estados de los reportes (`new`, `in_progress`, `resolved`) ahora se muestran traducidos correctamente en toda la interfaz (ej: "En progreso" en lugar de "in_progress").
- **Consistencia i18n:** Añadidas claves de traducción de estados en los diccionarios principales.

## [2026-04-28] - Modo Demo y Carga Automática de Datos (v2.4.0-stable)

### 🚀 Mejoras para Presentación (Demo)
- **Bypass de OTP:** Implementado sistema de login simplificado para la cuenta `alumno@aura.test`. El sistema autocompleta el código `123456` y omite el envío real de correos para agilizar las demostraciones.
- **Semillas de Datos (Seeds):** Nueva migración `2024_01_11_000000_seed_demo_data.php` que puebla automáticamente la base de datos con usuarios de prueba (Admin, Orientador, Profesor, Alumnos), aulas y reportes de ejemplo.
- **Configuración de Desarrollo:** El motor de login ahora permite la devolución del código OTP en el JSON de respuesta para cuentas marcadas como demo.

## [2026-04-28] - Excelencia en Comunicación e Identidad (v2.3.0-stable)

### 🚀 Mejoras y Funcionalidades
- **Rediseño de Emails:** Nueva plantilla profesional para recuperación de contraseña con soporte para modo oscuro, diseño responsivo y branding completo de Aura.
- **Update Engine v2.3:** Actualización del motor de sincronización interno para soportar las nuevas estructuras de datos de la rama 2.x.

### 🛡️ Seguridad y Estabilidad
- **Motor de Migraciones:** Corrección crítica en el sistema de carga de migraciones (clases nominales).

## [2026-04-28] - Optimización de Envío y Corrección de Plantillas (v2.2.1-stable)

## [2026-04-28] - Modernización de Interfaz y Sistema de Vistas (v2.2.0-stable)

## [2026-04-28] - Correcciones Finales y Sincronización (v2.1.2-stable)

## [2026-04-27] - Consolidación de Producción (v1.7.0-stable)

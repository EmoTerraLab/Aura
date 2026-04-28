# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-28] - Protocolos de Intervención y Sociogramas (v2.5.0-stable)

### 🚀 Nuevas Funcionalidades
- **Protocolos CCAA Dinámicos:** Implementación de un sistema de guía de protocolos de actuación ante acoso escolar, adaptado automáticamente según la Comunidad Autónoma configurada en el centro.
- **Gestión de Casos de Protocolo:** Nuevo flujo de trabajo para el personal docente que permite abrir casos específicos de protocolo, realizar seguimiento, adjuntar comunicaciones y documentar el cierre del caso.
- **Módulo Sociométrico:** Implementación de encuestas sociométricas para alumnos, permitiendo mapear las dinámicas relacionales del aula y detectar líderes, alumnos aislados o en riesgo de exclusión.
- **Mapas de Seguridad:** Herramienta visual para que los centros identifiquen puntos negros de convivencia mediante el registro geolocalizado (dentro del centro) de incidentes.

### 🛠️ Mejoras Técnicas
- **Internacionalización Completa:** Actualización masiva de los diccionarios en Català, Galego, Euskara, English y Español, garantizando que el 100% de la interfaz sea bilingüe.
- **Motor de Vistas:** Refactorización de componentes comunes en la administración y el dashboard de staff para soportar los nuevos módulos de protocolo.
- **Seguridad de Datos:** Nuevas tablas de base de datos para la persistencia de casos de protocolo y resultados sociométricos con integridad referencial reforzada.

## [2026-04-28] - Fix: Dashboard Alumno y Localización de Estados (v2.4.1-stable)

### 🛠️ Correcciones y Mejoras
- **Fix Dashboard Vacío:** Corregida la lógica de visualización en el dashboard del alumno que ocultaba el contenedor principal al cargar un chat.
- **Localización de Estados:** Los estados de los reportes (`new`, `in_progress`, `resolved`) ahora se muestran traducidos correctamente.

## [2026-04-28] - Modo Demo y Carga Automática de Datos (v2.4.0-stable)

## [2026-04-28] - Excelencia en Comunicación e Identidad (v2.3.0-stable)

## [2026-04-28] - Optimización de Envío y Corrección de Plantillas (v2.2.1-stable)

## [2026-04-28] - Modernización de Interfaz y Sistema de Vistas (v2.2.0-stable)

## [2026-04-28] - Correcciones Finales y Sincronización (v2.1.2-stable)

## [2026-04-27] - Consolidación de Producción (v1.7.0-stable)

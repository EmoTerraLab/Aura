# Aura - Histórico de Cambios

Este documento registra las modificaciones, mejoras y correcciones realizadas en el proyecto Aura.

## [2026-04-28] - Correcciones Finales y Sincronización (v2.1.2-stable)

### 🛠️ Correcciones
- **Vistas de Recuperación:** Ajustes finales en las vistas de recuperación de contraseña para asegurar la coherencia visual y funcional.
- **Sincronización de Datos:** Actualización de la base de datos local consolidada para el entorno de producción.
- **Documentación:** Actualización final del README con todas las características de la rama 2.1.x.

## [2026-04-28] - Corrección de Errores y Estabilidad (v2.1.1-stable)

### 🛠️ Correcciones y Mejoras
- **Fix Inyección de Dependencias:** Corregida la instanciación de controladores en el `Router` para asegurar que el flujo de recuperación de contraseña reciba correctamente sus servicios.
- **Estabilidad del Mailer:** Añadido fallback de seguridad en el remitente del correo para evitar errores de envío cuando la configuración es parcial.
- **Consistencia de Idiomas:** Corregida la detección de idioma en las vistas de recuperación de contraseña para usar el método estándar `Lang::current()`.
- **Refactorización:** Eliminada la creación manual de modelos dentro del controlador de reset, delegando la responsabilidad al Router/DI.

## [2026-04-28] - Gestión de Identidad y Seguridad (v2.1.0-stable)

### 🚀 Nuevas Funcionalidades
- **Recuperación de Contraseña:** Implementación de flujo completo de recuperación de contraseña para el personal (Staff/Admin) mediante tokens seguros por correo electrónico.
- **Modelos de Seguridad:** Nuevo modelo `PasswordReset` para la gestión de tokens de expiración temporal.
- **Vistas de Autenticación:** Creadas interfaces para solicitud de reset, formulario de nueva contraseña y estados de token inválido/expirado.

### 🛠️ Mejoras Técnicas
- **Controlador de Reset:** Centralización de la lógica de recuperación en `PasswordResetController`.
- **Validación Estricta:** Implementación de tipado nulo en el constructor de `AuthController` para mayor robustez en la inyección del Mailer.

## [2026-04-28] - Lanzamiento Aura v2.0 "Safe & Simple" (v2.0.0-stable)

### 🚀 Nuevas Funcionalidades
- **Protocolos CCAA:** Implementación del sistema de protocolos de convivencia adaptados a las Comunidades Autónomas (CCAA).
- **Gestión de Protocolos:** Nuevo módulo en administración para activar/desactivar y configurar el protocolo específico de cada región.
- **Visibilidad Estudiantil:** Opción para mostrar u ocultar los protocolos de actuación directamente en el dashboard del alumno.

### 🛠️ Mejoras Técnicas
- **Data Engine:** Integración de `BullyingProtocols.php` para la gestión centralizada de normativas y procedimientos.
- **Esquema de Datos:** Nueva migración para la persistencia de configuraciones de protocolos regionales.

## [2026-04-27] - Consolidación de Producción (v1.7.0-stable)

### 🚀 Lanzamiento de Producción
- **Sincronización Total:** Consolidación final de todos los módulos de preproducción (PDP) en la versión estable de producción.
- **Limpieza de Marca:** Eliminación definitiva de todas las referencias "PDP" en el código fuente, base de datos, configuraciones y manuales.
- **Identidad Visual:** Integración completa de la nueva iconografía y logotipos institucionales en todas las interfaces.

### 🛡️ Seguridad y Estabilidad
- **Autoloading:** Corregido el sistema de carga de clases PSR-4 en el `composer.json` para garantizar la compatibilidad con el entorno del VPS.
- **Fix OTP:** Activación del envío real de correos electrónicos para la autenticación de alumnos.

## [2026-04-27] - Fix Crítico: Envío de OTP por Email (v1.6.3-stable)

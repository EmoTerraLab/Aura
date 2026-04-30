# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![i18n](https://img.shields.io/badge/i18n-es%20|%20ca%20|%20gl%20|%20eu%20|%20en-blue)

Aura es una solución integral para la gestión de informes y convivencia en centros educativos. Permite a los alumnos reportar incidencias de forma anónima o identificada y al personal del centro (profesores, orientadores y dirección) gestionar dichos casos mediante un sistema de tickets seguro, bilingüe y con autenticación de vanguardia.

---

## ✨ Características / Features

### 🔐 Seguridad / Security
- **WebAuthn**: Autenticación biométrica (Face ID, huella, passkeys) para alumnos.
- **TOTP 2FA**: Verificación en dos pasos mediante apps (Google Authenticator) para staff y admin.
- **Protección**: CSRF, XSS, inyección SQL (PDO) y headers HTTP de seguridad.
- **Rate Limiting**: Control de intentos de inicio de sesión.

### 🌍 Internacionalización / Internationalization
- Soporte nativo para 5 idiomas: Español, Català, Galego, Euskara y English.
- Cambio de idioma dinámico por usuario y configuración global.

### 📋 Gestión de Informes y Protocolos Legales / Report & Legal Protocol Management
- **Multi-CCAA (Nacional)**: Soporte completo para los protocolos legales de las 19 comunidades y ciudades autónomas de España.
- **Workflow Legal Estandarizado**: Seguimiento de fases, anexos oficiales y plazos legales automáticos según la normativa regional.
- **Prácticas Restaurativas**: Módulo para la gestión de medidas de reparación y convivencia.
- **Evidencias**: Gestión segura de archivos y pruebas documentales bajo custodia.
- **Mensajería interna**: Sistema de tickets con estados, respuestas internas y menciones entre el personal.
- **Anonimato**: Opción de reportes anónimos para proteger al alumno.

---

## 🔧 Requisitos del sistema / System Requirements

| Componente | Mínimo | Recomendado |
|---|---|---|
| PHP | 8.1 | 8.2+ |
| Servidor web | Apache 2.4 con mod_rewrite | Apache 2.4+ |
| Base de datos | SQLite 3 | SQLite 3.35+ |
| Composer | 2.x | Última versión |

---

## 🚀 Instalación y Despliegue

### Producción
1. Sincroniza los archivos con el servidor.
2. Ejecuta `composer install --no-dev --optimize-autoloader`.
3. Ejecuta las migraciones: `php migrate.php run`.
4. Asegura permisos en `storage/` y `database/`.

---
© 2026 EmoTerraLab — Aura Project

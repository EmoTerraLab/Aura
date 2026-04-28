# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![i18n](https://img.shields.io/badge/i18n-es%20|%20ca%20|%20gl%20|%20eu%20|%20en-blue)

Aura es una solución integral para la gestión de informes y convivencia en centros educativos. Permite a los alumnos reportar incidencias de forma anónima o identificada y al personal del centro (profesores, orientadores y dirección) gestionar dichos casos mediante un sistema de tickets seguro, bilingüe y con soporte de protocolos regionales.

Aura is a comprehensive solution for managing well-being reports in schools. It allows students to report incidents anonymously or identified, and school staff (teachers, counselors, and management) to manage these cases through a secure, bilingual ticket system with cutting-edge authentication.

---

## 📋 Índice / Table of Contents
1. [Características / Features](#-características--features)
2. [Requisitos / System Requirements](#-requisitos-del-sistema--system-requirements)
3. [Instalación / Installation](#-instalación--installation)
4. [Configuración / Configuration](#-configuración--configuration)
5. [Arquitectura / Architecture](#-arquitectura--architecture)
6. [Base de Datos / Database](#-base-de-datos--database)
7. [Seguridad / Security](#-seguridad--security)
8. [Rutas / Routes](#-rutas--routes)
9. [Contribuir / Contributing](#-contribuir--contributing)
10. [Licencia / License](#-licencia--license)

---

## ✨ Características / Features

### 🔐 Seguridad / Security
- **WebAuthn**: Autenticación biométrica (Face ID, huella, passkeys) para alumnos.
- **TOTP 2FA**: Verificación en dos pasos mediante apps (Google Authenticator) para staff y admin.
- **Protección**: CSRF, XSS, inyección SQL (PDO) y headers HTTP de seguridad.

### 📊 Análisis y Convivencia / Well-being Analysis
- **Sociogramas Automáticos**: Generación de mapas relacionales del aula para detectar líderes, aislados o riesgos de exclusión.
- **Mapas de Seguridad**: Identificación visual de zonas de riesgo dentro del centro educativo.
- **Protocolos CCAA**: Guías de actuación integradas basadas en la normativa específica de cada Comunidad Autónoma.

### 👥 Roles y Permisos / Roles & Permissions
- **Admin**: Gestión total de usuarios, aulas, configuración del sistema y correo.
- **Staff (Dirección/Orientador/Profesor)**: Gestión de informes, respuestas internas y menciones.
- **Alumno**: Creación de reportes y seguimiento de sus casos.

### 🌍 Internacionalización / Internationalization
- Soporte nativo para 5 idiomas: Español, Català, Galego, Euskara y English.
- Cambio de idioma dinámico por usuario y configuración global.

---

## 🔧 Requisitos del sistema / System Requirements

| Componente | Mínimo | Recomendado |
|---|---|---|
| PHP | 8.1 | 8.2+ |
| Servidor web | Apache 2.4 con mod_rewrite | Apache 2.4+ |
| Base de datos | SQLite 3 | SQLite 3.35+ |
| Composer | 2.x | Última versión |
| Extensiones PHP | openssl, pdo_sqlite, mbstring, gmp, json | + sodium |

---

## 🚀 Instalación / Installation

### Desarrollo Local / Local Development
1. **Clonar el repositorio:**
   ```bash
   git clone [url-del-repo]
   cd aura
   ```
2. **Instalar dependencias:**
   ```bash
   composer install
   ```
3. **Configurar base de datos:**
   El sistema creará `database/aura.sqlite` automáticamente al iniciar por primera vez. Asegúrate de que la carpeta tenga permisos de escritura.
4. **Permisos:**
   ```bash
   chmod -R 775 storage database
   ```

---

## 🏗️ Arquitectura / Architecture

Aura sigue un patrón **MVC Nativo** sin frameworks pesados, optimizado para rendimiento y facilidad de despliegue.

---

## 📄 Licencia / License
Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---
© 2026 EmoTerraLab — Aura Project
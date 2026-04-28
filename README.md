# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![i18n](https://img.shields.io/badge/i18n-es%20|%20ca%20|%20gl%20|%20eu%20|%20en-blue)

Aura es una solución integral para la gestión de informes y convivencia en centros educativos. Permite a los alumnos reportar incidencias de forma anónima o identificada y al personal del centro (profesores, orientadores y dirección) gestionar dichos casos mediante un sistema de tickets seguro, bilingüe y con soporte de protocolos regionales.

---

## ✨ Características Principales / Key Features

### 🔐 Seguridad y Acceso / Security & Access
- **WebAuthn**: Autenticación biométrica (Face ID, huella) para alumnos.
- **TOTP 2FA**: Verificación en dos pasos para staff y administradores.
- **Privacidad**: Opción de anonimato real para reportes de convivencia.

### 📊 Análisis Convivencial / Well-being Analysis
- **Sociogramas**: Sistema automático de detección de líderes, aislados y dinámicas de grupo.
- **Mapas de Seguridad**: Identificación visual de puntos críticos en el centro educativo.
- **Telemetría**: Estadísticas en tiempo real sobre el estado del bienestar escolar.

### 🎓 Protocolos Regionales / Regional Protocols
- **Adaptación CCAA**: Guías de actuación integradas basadas en la normativa de cada Comunidad Autónoma.
- **Gestión de Casos**: Flujo de trabajo reglado para la apertura, seguimiento y cierre de protocolos oficiales de acoso.

### 👥 Gestión y Comunicación / Management & Comms
- **Sistema de Tickets**: Gestión eficiente de informes con estados y prioridades.
- **Mensajería Interna**: Comunicación segura entre alumnos y staff dentro de cada caso.
- **Bilingüe**: Interfaz disponible en Español, Català, Galego, Euskara y English.

---

## 🔧 Requisitos del sistema / System Requirements

| Componente | Mínimo | Recomendado |
|---|---|---|
| PHP | 8.1 | 8.2+ |
| Servidor web | Apache 2.4 con mod_rewrite | Apache 2.4+ |
| Base de datos | SQLite 3 | SQLite 3.35+ |
| Extensiones PHP | openssl, pdo_sqlite, mbstring, gmp, json | + sodium |

---

## 🚀 Instalación Rápida / Quick Start

1. **Clonar y Preparar**:
   ```bash
   git clone [url-del-repo]
   composer install
   ```
2. **Permisos**: Asegurar escritura en `database/` y `storage/`.
3. **Servidor**: Apuntar el DocumentRoot a `public/`.
4. **Configuración**: Acceder al panel de administración para configurar el centro y el protocolo CCAA.

---
© 2026 EmoTerraLab — Aura Project

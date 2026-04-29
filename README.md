# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![i18n](https://img.shields.io/badge/i18n-es%20|%20ca%20|%20gl%20|%20eu%20|%20en-blue)

Aura es una solución integral para la gestión de informes y convivencia en centros educativos. Permite a los alumnos reportar incidencias de forma anónima o identificada y al personal del centro gestionar dichos casos mediante un sistema de tickets seguro, bilingüe, con soporte de protocolos regionales (Cataluña, Aragón, etc.) y enfoque restaurativo.

---

## ✨ Características Principales / Key Features

### 🔐 Seguridad y Acceso / Security & Access
- **WebAuthn**: Autenticación biométrica (Face ID, huella) para alumnos.
- **TOTP 2FA**: Verificación en dos pasos para staff y administradores.
- **Custodia de Evidencias**: Sistema seguro de almacenamiento de archivos vinculados a protocolos con registro de acceso legal.

### 📊 Análisis y Convivencia / Well-being Analysis
- **Sociogramas**: Sistema automático de detección de dinámicas de grupo y riesgos.
- **Mapas de Seguridad**: Identificación visual de puntos críticos en el centro.
- **Prácticas Restaurativas**: Módulo ERG para la gestión de círculos, reuniones y seguimiento de acuerdos.

### 🎓 Protocolos Regionales / Regional Protocols
- **Adaptación CCAA**: Guías de actuación integradas basadas en normativas autonómicas (Cataluña y Aragón implementados).
- **Gestión de Plazos**: Cálculo automático de días lectivos para cumplimiento normativo.
- **Anexos Oficiales**: Gestión de documentación legal y exportación de plantillas.

### 👥 Gestión y Comunicación / Management & Comms
- **Sistema de Tickets**: Gestión eficiente de informes con estados y prioridades.
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
© 2026 EmoTerraLab — Aura Project

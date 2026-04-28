# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-stable-blue)

Aura es una solución integral para la gestión de informes de convivencia en centros educativos. Permite a los alumnos reportar situaciones de bienestar de forma segura (y anónima si lo desean) y al personal docente gestionar estos casos de manera eficiente y colaborativa.

Aura is a comprehensive solution for managing well-being reports in schools. It allows students to safely report well-being situations (anonymously if they choose) and enables teaching staff to manage these cases efficiently and collaboratively.

---

## ✨ Características Principales / Key Features

### 🎓 Alumnos / Students
- **Reportes Seguros**: Interfaz intuitiva para crear informes de convivencia.
- **Anonimato**: Opción para realizar reportes sin revelar la identidad.
- **Acceso Simple**: Inicio de sesión mediante código OTP enviado al correo institucional o biometría (WebAuthn).

### 👥 Roles y Permisos / Roles & Permissions
- **Admin**: Gestión total de usuarios, aulas, configuración del sistema y correo.
- **Staff (Dirección/Orientador/Profesor)**: Gestión de informes, respuestas internas y menciones.
- **Alumno**: Creación de reportes y seguimiento de sus casos.

### 📋 Gestión de Informes / Report Management
- Sistema de tickets con estados (`new`, `in_progress`, `resolved`).
- Mensajería interna y menciones entre el personal.
- Opción de anonimato para mayor seguridad del alumno.

### 🔐 Seguridad Avanzada / Advanced Security
- **Recuperación de Cuenta**: Sistema de reset de contraseña mediante tokens de un solo uso por email.
- **MFA**: Soporte para TOTP y WebAuthn (Biometría).

### 🎓 Protocolos CCAA / Regional Protocols
- **Adaptación Local**: Sistema de protocolos de convivencia adaptados a la normativa de cada Comunidad Autónoma.
- **Transparencia**: Acceso directo para alumnos a los protocolos de actuación vigentes en su región.

---

## 🔧 Requisitos del sistema / System Requirements
...
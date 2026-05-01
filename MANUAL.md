# 📚 Manual de Usuario y Técnico — Aura
# User & Technical Manual — Aura

> **Versión:** 2.19.0-stable
> **Última actualización:** jueves, 30 de abril de 2026
> **Idiomas / Languages:** Español · English

---

## Índice / Table of Contents

### 🇪🇸 MANUAL DE USUARIO
1. [Introducción](#1-introducción)
2. [Acceso al sistema](#2-acceso-al-sistema)
3. [Panel de Alumno](#3-panel-de-alumno)
4. [Panel de Staff](#4-panel-de-staff)
5. [Panel de Administración](#5-panel-de-administración)
6. [Solución de problemas (usuario)](#6-solución-de-problemas-usuario)

### 🇬🇧 USER MANUAL
1. [Introduction](#1-introduction)
2. [System Access](#2-system-access)
3. [Student Panel](#3-student-panel)
4. [Staff Panel](#4-staff-panel)
5. [Admin Panel](#5-admin-panel)
6. [Troubleshooting (user)](#6-troubleshooting-user)

### 🛠️ MANUAL TÉCNICO / TECHNICAL MANUAL
1. [Arquitectura / Architecture](#1-arquitectura--architecture)
2. [Base de Datos / Database](#2-base-de-datos--database)
3. [Seguridad / Security](#3-seguridad--security)
4. [Guía de Desarrollo / Development Guide](#4-guía-de-desarrollo--development-guide)
5. [Despliegue / Deployment](#5-despliegue--deployment)

---

# 🇪🇸 MANUAL DE USUARIO

## 1. Introducción
Aura es una herramienta diseñada para mejorar la convivencia escolar, proporcionando un canal seguro y privado para que los alumnos puedan comunicar incidencias o preocupaciones. El sistema garantiza la confidencialidad y permite una gestión eficiente por parte del equipo docente y directivo.

### Roles del Sistema
- **Alumno:** Puede crear informes (anónimos o no) y hacer seguimiento de sus casos.
- **Staff (Profesor/Orientador/Dirección):** Gestiona los casos asignados, responde a los alumnos y colabora internamente.
- **Administrador:** Control total sobre usuarios, aulas y configuración técnica.

---

## 2. Acceso al sistema

### 2.1 Iniciar sesión
Acceda a la URL del centro y verá dos pestañas: **Alumno** y **Personal**.
- **Alumnos:** Introduzcan su email escolar. Recibirán un código OTP de 6 dígitos en su correo o se les pedirá su huella/FaceID si ya han registrado un dispositivo.
- **Personal:** Acceso mediante email y contraseña.

### 2.2 Verificación en dos pasos para alumnos (WebAuthn)
Aura utiliza biometría para que los alumnos no dependan de contraseñas.
1. **Registro:** En su perfil, haga clic en "Registrar dispositivo".
2. **Uso:** Al iniciar sesión, el navegador le pedirá su huella o FaceID.
3. **Fallback:** Si no tiene el dispositivo, puede solicitar un código por email.

### 2.3 Verificación para Staff y Admin (TOTP)
El personal debe usar una app como Google Authenticator.
1. **Activación:** Escanee el código QR desde su perfil.
2. **Acceso:** Introduzca el código de 6 dígitos de la app tras poner su contraseña.

---

## 3. Panel de Alumno
- **Dashboard:** Muestra el estado de sus informes abiertos.
- **Nuevo Informe:** Formulario para describir qué sucede, quién está involucrado y el nivel de urgencia.
- **Mensajes:** Chat seguro con el orientador asignado.

---

## 4. Panel de Staff
- **Bandeja de Entrada:** Lista de informes pendientes de atención.
- **Atender Caso:** Permite cambiar el estado (`En proceso`, `Resuelto`) y enviar mensajes al alumno o notas internas solo visibles para el staff.
- **Menciones:** Use `@nombre` en las notas internas para notificar a un compañero.

---

## 5. Panel de Administración
- **Usuarios:** Crear, editar o dar de baja a alumnos y personal.
- **Aulas:** Configurar grupos y asignarles un tutor responsable.
- **Configuración (Settings):**
    - **Escuela:** Nombre, logo y contacto.
    - **Correo:** Configuración del servidor SMTP (esencial para enviar códigos OTP).
    - **Seguridad:** Ajustes de tiempo de sesión e idiomas.

---

## 6. Solución de problemas (usuario)
- **No recibo el código:** Revise la carpeta de Spam. Si persiste, contacte con el administrador.
- **Error biometría:** Asegúrese de estar usando un navegador compatible (Chrome, Safari, Edge) y tener el Bluetooth/Biometría activo.

---

# 🇬🇧 USER MANUAL

## 1. Introduction
Aura is a tool designed to improve school well-being by providing a secure and private channel for students to communicate incidents or concerns. The system guarantees confidentiality and enables efficient management by the teaching and management team.

### System Roles
- **Student:** Can create reports (anonymous or not) and follow up on their cases.
- **Staff (Teacher/Counselor/Management):** Manages assigned cases, responds to students, and collaborates internally.
- **Administrator:** Full control over users, classrooms, and technical configuration.

---

## 2. System Access

### 2.1 Signing In
Access the school's URL and you will see two tabs: **Student** and **Staff**.
- **Students:** Enter your school email. You will receive a 6-digit OTP code in your email or be asked for your fingerprint/FaceID if you have already registered a device.
- **Staff:** Access via email and password.

### 2.2 Two-Step Verification for Students (WebAuthn)
Aura uses biometrics so students don't have to rely on passwords.
1. **Registration:** In your profile, click "Register device".
2. **Usage:** When signing in, the browser will ask for your fingerprint or FaceID.
3. **Fallback:** If you don't have the device, you can request a code via email.

### 2.3 Verification for Staff and Admin (TOTP)
Staff must use an app like Google Authenticator or FreeOTP.
1. **Activation:** Scan the QR code from your profile.
2. **Access:** Enter the 6-digit code from the app after entering your password.

---

## 3. Student Panel
- **Dashboard:** Shows the status of your open reports.
- **New Report:** Form to describe what is happening, who is involved, and the urgency level.
- **Messages:** Secure chat with the assigned counselor.

---

## 4. Staff Panel
- **Inbox:** List of reports pending attention.
- **Handle Case:** Allows changing the status (`In progress`, `Resolved`) and sending messages to the student or internal notes visible only to staff.
- **Mentions:** Use `@name` in internal notes to notify a colleague.

---

## 5. Admin Panel
- **Users:** Create, edit, or disable students and staff.
- **Classrooms:** Configure groups and assign a responsible tutor.
- **Settings:**
    - **School:** Name, logo, and contact.
    - **Mail:** SMTP server configuration (essential for sending OTP codes).
    - **Security:** Session time and language settings.

---

## 6. Troubleshooting (user)
- **I don't receive the code:** Check your Spam folder. If it persists, contact the administrator.
- **Biometry error:** Make sure you are using a compatible browser (Chrome, Safari, Edge) and have Bluetooth/Biometrics active.

---

# 🛠️ MANUAL TÉCNICO / TECHNICAL MANUAL

## 1. Arquitectura / Architecture
Aura utiliza un patrón **MVC Nativo**.
- **Model:** `app/Models/` (Interacción con SQLite vía PDO).
- **View:** `resources/views/` (PHP puro con escape de datos).
- **Controller:** `app/Controllers/` (Lógica de negocio).
- **Core:** `app/Core/` (Router, Auth, Session, Lang, etc.).

### Flujo de Petición / Request Flow:
1. `index.php` inicializa el entorno y el Core.
2. `Router.php` busca la coincidencia de URL y método.
3. `Middleware.php` valida la sesión y el rol.
4. El `Controller` procesa la petición y devuelve una `View`.

---

## 2. Base de Datos / Database
El esquema se encuentra en `database/migrations.sql`.
```sql
-- Ejemplo de tabla principal
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    role VARCHAR(255) CHECK (role IN ('admin', 'direccion', 'orientador', 'profesor', 'alumno')),
    -- ...
);
```
**Relaciones:**
- `reports.student_id` -> `student_profiles.id`
- `reports.classroom_id` -> `classrooms.id`
- `report_messages.report_id` -> `reports.id`

---

## 3. Seguridad / Security
- **CSRF:** Tokens obligatorios en todas las peticiones POST/PATCH/DELETE.
- **XSS:** Todas las variables en vistas pasan por `htmlspecialchars()`.
- **Inyección SQL:** Uso estricto de `PDO::prepare` y `bindValue`.
- **Headers:** `Content-Security-Policy`, `X-Frame-Options`, `Strict-Transport-Security`.

---

## 4. Guía de Desarrollo / Development Guide

### Añadir un nuevo Idioma
1. Cree un archivo en `lang/XX.php` (ej. `fr.php`).
2. Traduzca todas las claves del array.
3. El sistema lo detectará automáticamente.

### Crear una nueva Ruta
En `app/routes.php`:
```php
$router->get('/nueva-ruta', [MiController::class, 'miMetodo'], ['auth', 'role:admin']);
```

---

## 5. Despliegue / Deployment
1. **Requisitos:** PHP 8.1+, Apache con `mod_rewrite`, SQLite3.
2. **Permisos:** La carpeta `database/` y `storage/` deben tener permisos `0775` para el usuario `www-data`.
3. **SMTP:** Configure obligatoriamente un servidor SMTP en el panel admin para el funcionamiento del 2FA.

---

## Apéndices / Appendices

### A. Glosario / Glossary
- **OTP:** One Time Password (Contraseña de un solo uso).
- **TOTP:** Time-based One Time Password.
- **WebAuthn:** Estándar web para autenticación biométrica.

### B. Configuración SMTP / SMTP Setup
- **Gmail:** Requiere "Contraseñas de aplicación".
- **Brevo/Sendinblue:** Recomendado para estabilidad en envíos masivos.

---
© 2026 EmoTerraLab — Aura Project
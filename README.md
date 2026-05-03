# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![Version](https://img.shields.io/badge/version-2.23.0--stable-blue)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![i18n](https://img.shields.io/badge/i18n-es%20|%20ca%20|%20gl%20|%20eu%20|%20en-blue)
![Security](https://img.shields.io/badge/security-Bank--Grade-red)

Aura es una solución integral para la gestión de informes y convivencia en centros educativos. Permite a los alumnos reportar incidencias de forma anónima o identificada y al personal del centro (profesores, orientadores y dirección) gestionar dichos casos mediante un sistema de tickets seguro, bilingüe y con autenticación de vanguardia.

Aura is a comprehensive solution for managing well-being reports in schools. It allows students to report incidents anonymously or identified, and school staff (teachers, counselors, and management) to manage these cases through a secure, bilingual ticket system with cutting-edge authentication.

---

## 📋 Índice / Table of Contents
1. [Características / Features](#-características--features)
2. [Protocolos Autonómicos](#️-protocolos-autonómicos--regional-protocols)
3. [Seguridad Nivel Bancario](#-seguridad-nivel-bancario--bank-grade-security)
4. [Requisitos / System Requirements](#-requisitos-del-sistema--system-requirements)
5. [Instalación / Installation](#-instalación--installation)
6. [Configuración / Configuration](#-configuración--configuration)
7. [Arquitectura / Architecture](#-arquitectura--architecture)
8. [Base de Datos / Database](#-base-de-datos--database)
9. [Licencia / License](#-licencia--license)

---

## ✨ Características / Features

### 🌍 Internacionalización / Internationalization
- Soporte nativo para 5 idiomas: Español, Català, Galego, Euskara y English.
- Cambio de idioma dinámico por usuario y configuración global.

### 👥 Roles y Permisos / Roles & Permissions
- **Admin**: Gestión total de usuarios, aulas, configuración del sistema y correo.
- **Staff (Dirección/Orientador/Profesor)**: Gestión de informes, respuestas internas, asignación de equipos y control de flujo normativo.
- **Alumno**: Creación de reportes y seguimiento de sus casos, encuestas sociométricas.

### 📋 Gestión de Informes / Report Management
- Sistema de tickets con estados (`new`, `in_progress`, `resolved`).
- Mensajería interna e inserción de notas con menciones de personal (`@nombre`).
- Opción de anonimato garantizado en la base de datos para la seguridad del alumno.
- Herramientas restaurativas y mediación integradas.

---

## 🗺️ Protocolos Autonómicos / Regional Protocols

Aura no es un simple sistema de tickets; implementa flujos normativos legales y estrictos según la comunidad autónoma configurada.

- **Galicia (v2.23)**: Implementación del flujo normativo completo en 6 fases (Detección, Recogida, Análisis, Medidas, Seguimiento y Cierre). Incluye el gestor documental para Anexos (1 al 16) y módulos exclusivos para "Medidas Urxentes" y "Actuación Ciberacoso".
- **Aragón**: Gestión del Anexo I-a, constitución del equipo de valoración, entrevistas y checklist normativo.
- **Cataluña / C. Valenciana / Murcia**: Flujos específicos adaptados a sus respectivos decretos de convivencia.

Aura garantiza la **Auto-reparación** de estados, evitando que la normativa se salte fases requeridas legalmente.

---

## 🏦 Seguridad Nivel Bancario / Bank-Grade Security

Aura maneja información altamente sensible (PII de menores, incidentes de acoso). Su arquitectura de seguridad está diseñada siguiendo estándares OWASP y PCI-DSS:

- **Autenticación Multi-Factor**: WebAuthn biométrico (FaceID/Huella) para alumnos y TOTP (Google Authenticator) cifrado con AES-256-GCM para el personal.
- **Mitigación IDOR Estricta**: Cada endpoint, lectura y modificación verifica criptográficamente la propiedad y autorización mediante inyección de dependencias `findByIdWithDetails()`.
- **Protección contra Fuerza Bruta**: Implementación de Rate Limiting compuesto (`IP + Email`), mitigando ataques distribuidos de "Credential Stuffing".
- **Auditoría Inmutable (Non-Repudiation)**: Un `AuditLogger` centralizado guarda un rastro forense inalterable en la tabla `audit_logs` ante cualquier cambio de estado o intento de acceso fallido.
- **Blindaje de Archivos**: Los archivos `.sqlite` y bases de datos están protegidos contra acceso HTTP directo, y los endpoints de depuración se encuentran fuera del alcance público (DocumentRoot `/public`).

---

## 🔧 Requisitos del sistema / System Requirements

| Componente | Mínimo | Recomendado |
|---|---|---|
| PHP | 8.1 | 8.2+ |
| Servidor web | Apache 2.4 con mod_rewrite | Apache 2.4+ |
| Base de datos | SQLite 3 | SQLite 3.35+ |
| Composer | 2.x | Última versión |
| Extensiones PHP | openssl, pdo_sqlite, mbstring, gmp, json, sodium | + opcache |

> ⚠️ **Nota:** WebAuthn requiere **HTTPS** en entornos de producción. En localhost funciona sin certificado SSL.

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
   Aura utiliza un motor SQLite que se auto-desplegará en `database/aura.sqlite` al iniciar.
4. **Permisos:**
   Asegúrate de otorgar permisos de escritura al servidor web:
   ```bash
   chmod -R 775 storage database
   ```
5. **Configurar Apache:**
   Apunta el `DocumentRoot` **exclusivamente** a la carpeta `public/`.

### Producción / Production
1. Sube los archivos al servidor (omite `_dev_tools`, `.git` y `node_modules`).
2. Configura el VirtualHost apuntando obligatoriamente a `public/`. **Nunca expongas la carpeta raíz del proyecto**.
3. Activa `mod_rewrite` y un certificado SSL (Let's Encrypt).
4. Configura `APP_KEY` (32 bytes hex) y `APP_ENV=production` en el entorno o archivo central.
5. Inicia sesión como administrador por defecto y cambia las credenciales y el SMTP inmediatamente.

---

## ⚙️ Configuración / Configuration

### Apache VirtualHost (Ejemplo de Producción)
```apache
<VirtualHost *:443>
    ServerName colegio-aura.com
    DocumentRoot /var/www/aura/public

    <Directory /var/www/aura/public>
        AllowOverride All
        Require all granted
    </Directory>

    # Bloqueo adicional por si el .htaccess falla
    <Directory /var/www/aura/database>
        Require all denied
    </Directory>

    SSLEngine on
    # Certificados SSL...
</VirtualHost>
```

---

## 🏗️ Arquitectura / Architecture

Aura sigue un patrón **MVC Nativo Puro** sin frameworks pesados, optimizado para seguridad estricta y rendimiento de extremo a extremo.

```text
HTTP Request
    │
    ▼
 public/.htaccess  ──►  Redirige todo a index.php (Punto único de entrada)
    │
    ▼
 public/index.php  ──►  Bootstrap: CSP Headers, Session, Config, Router
    │
    ▼
 App\Core\Router   ──►  Matching de ruta, Contenedor DI (Dependency Injection)
    │
    ▼
 App\Core\Middleware ─►  Verificación Auth, Roles, CSRF Rotation
    │
    ▼
 Controller        ──►  Validación IDOR, Lógica de Negocio (ej. ProtocolWorkflow)
    │
    ▼
 Model             ──►  Acceso a datos protegidos (PDO Parametrizado)
    │
    ▼
 View (.php)       ──►  Renderizado HTML seguro (htmlspecialchars() automático)
    │
    ▼
 HTTP Response
```

---

## 🗄️ Base de Datos / Database

Estructura principal (SQLite 3.35+):
- `users`: Usuarios, passwords Bcrypt, tokens WebAuthn y secretos TOTP cifrados.
- `classrooms`: Gestión de aulas y tutores asignados.
- `reports`: Informes base de convivencia.
- `protocol_cases`: Instancia regional de un informe legal.
- `report_messages` / `protocol_followups`: Notas internas, evidencias y seguimientos forenses.
- `audit_logs`: Trazabilidad inmutable de operaciones sensibles (Logins, Cambios de Estado).

---

## 📄 Licencia / License
Este proyecto es software propietario/comercial. Su uso requiere autorización expresa de los autores originales. 

---
© 2026 EmoTerraLab — Proyecto Aura (GIR)
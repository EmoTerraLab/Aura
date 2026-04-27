# Aura — Plataforma de Gestión de Convivencia Escolar
# Aura — School Well-being Management Platform

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![i18n](https://img.shields.io/badge/i18n-es%20|%20ca%20|%20gl%20|%20eu%20|%20en-blue)

Aura es una solución integral para la gestión de informes y convivencia en centros educativos. Permite a los alumnos reportar incidencias de forma anónima o identificada y al personal del centro (profesores, orientadores y dirección) gestionar dichos casos mediante un sistema de tickets seguro, bilingüe y con autenticación de vanguardia.

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
- **Rate Limiting**: Control de intentos de inicio de sesión.

### 🌍 Internacionalización / Internationalization
- Soporte nativo para 5 idiomas: Español, Català, Galego, Euskara y English.
- Cambio de idioma dinámico por usuario y configuración global.

### 👥 Roles y Permisos / Roles & Permissions
- **Admin**: Gestión total de usuarios, aulas, configuración del sistema y correo.
- **Staff (Dirección/Orientador/Profesor)**: Gestión de informes, respuestas internas y menciones.
- **Alumno**: Creación de reportes y seguimiento de sus casos.

### 📋 Gestión de Informes / Report Management
- Sistema de tickets con estados (`new`, `in_progress`, `resolved`).
- Mensajería interna y menciones entre el personal.
- Opción de anonimato para mayor seguridad del alumno.

---

## 🔧 Requisitos del sistema / System Requirements

| Componente | Mínimo | Recomendado |
|---|---|---|
| PHP | 8.1 | 8.2+ |
| Servidor web | Apache 2.4 con mod_rewrite | Apache 2.4+ |
| Base de datos | SQLite 3 | SQLite 3.35+ |
| Composer | 2.x | Última versión |
| Extensiones PHP | openssl, pdo_sqlite, mbstring, gmp, json | + sodium |

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
   El sistema creará `database/aura.sqlite` automáticamente al iniciar por primera vez. Asegúrate de que la carpeta tenga permisos de escritura.
4. **Permisos:**
   ```bash
   chmod -R 775 storage database
   ```
5. **Configurar Apache:**
   Apunta el `DocumentRoot` a la carpeta `public/`.

### Producción / Production
1. Sube los archivos al servidor (excluyendo `.git` y `node_modules`).
2. Configura un VirtualHost apuntando a `public/`.
3. Asegúrate de que `mod_rewrite` esté activo.
4. Genera un certificado SSL (Let's Encrypt).
5. Configura el servidor SMTP desde el panel de administración una vez dentro.

---

## ⚙️ Configuración / Configuration

### Variables de Entorno (.env)
Aunque Aura utiliza configuración en base de datos para la mayoría de aspectos, se pueden definir:
| Variable | Descripción | Defecto |
|---|---|---|
| `APP_ENV` | Entorno (development/production) | `development` |

### Apache VirtualHost
```apache
<VirtualHost *:443>
    ServerName colegio-aura.com
    DocumentRoot /var/www/aura/public

    <Directory /var/www/aura/public>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    # Certificados SSL...
</VirtualHost>
```

---

## 🏗️ Arquitectura / Architecture

Aura sigue un patrón **MVC Nativo** sin frameworks pesados, optimizado para rendimiento y facilidad de despliegue.

```
HTTP Request
    │
    ▼
 public/.htaccess  ──►  Redirige todo a index.php
    │
    ▼
 public/index.php  ──►  Bootstrap: sesión, headers, Lang::init(), Config::init()
    │
    ▼
 App\Core\Router   ──►  Matching de ruta + método HTTP
    │
    ▼
 App\Core\Middleware ─►  Verificación auth + roles
    │
    ▼
 Controller        ──►  Lógica de negocio + llamadas a modelos
    │
    ▼
 Model             ──►  Acceso a datos (PDO + SQLite)
    │
    ▼
 View (.php)       ──►  Renderizado HTML con Lang::t()
    │
    ▼
 HTTP Response
```

---

## 🗄️ Base de Datos / Database

Utiliza **SQLite** para facilitar el despliegue. Estructura principal:
- `users`: Usuarios y credenciales TOTP/WebAuthn.
- `classrooms`: Gestión de grupos y tutores.
- `reports`: Informes de convivencia.
- `report_messages`: Historial de comunicación en informes.
- `report_mentions`: Sistema de avisos internos entre staff.
- `settings`: Configuración persistente del sistema.

---

## 🔒 Seguridad / Security

- **Autenticación Multi-factor**: Obligatoria según configuración.
- **WebAuthn**: Implementación de `lbuchs/webauthn`.
- **TOTP**: Implementación de `spomky-labs/otphp`.
- **Sesiones Seguras**: Regeneración de ID en login y expiración configurable.
- **Escape Automático**: Uso de `htmlspecialchars()` en todas las salidas a vista.

---

## 🛣️ Rutas / Routes

| Método | Ruta | Controlador | Middleware | Descripción |
|---|---|---|---|---|
| GET | `/login` | `AuthController@showLogin` | - | Formulario de acceso |
| POST | `/login/staff` | `AuthController@loginStaff` | - | Login personal |
| POST | `/login/otp/verify` | `AuthController@verifyOTP` | - | Login alumno (OTP) |
| GET | `/alumno/dashboard` | `StudentController@index` | `auth`, `alumno` | Inicio alumno |
| POST | `/alumno/report` | `ReportController@store` | `auth`, `alumno` | Nuevo informe |
| GET | `/staff/dashboard` | `StaffController@index` | `auth`, `staff` | Inicio staff |
| GET | `/admin` | `AdminController@index` | `auth`, `admin` | Panel administración |
| GET | `/admin/settings` | `SettingsController@index` | `auth`, `admin` | Configuración global |
| GET | `/api/telemetry` | `TelemetryController@getStats` | - | Métricas del sistema |

---

## 🤝 Contribuir / Contributing
1. Haz un fork del proyecto.
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`).
3. Haz commit de tus cambios (`git commit -m 'Add AmazingFeature'`).
4. Haz push a la rama (`git push origin feature/AmazingFeature`).
5. Abre un Pull Request.

---

## 📄 Licencia / License
Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---
© 2026 EmoTerraLab — Aura Project
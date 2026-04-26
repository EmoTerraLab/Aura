# Aura: Santuario Digital Escolar 🛡️  ![Version](https://img.shields.io/badge/version-1.2.3--stable-teal)

Aura es una plataforma web integral diseñada para transformar la convivencia escolar. Proporciona un canal seguro, confidencial y ágil donde los estudiantes pueden reportar incidencias o preocupaciones, y donde el equipo educativo puede gestionar casos de manera colaborativa y estructurada.

## 🌟 Características Principales

### 📱 Experiencia de Usuario (Novedad)
- **Diseño Mobile-First**: Interfaz 100% responsive optimizada para smartphones y tablets.
- **Detección Automática de Idioma**: El sistema identifica el idioma preferido del navegador del usuario para ofrecer una experiencia localizada inmediata.
- **Navegación Táctil**: Menús laterales colapsables y sistema de notificaciones optimizados.

### 🔒 Seguridad Avanzada (Novedad)
- **2FA WebAuthn**: Los alumnos pueden iniciar sesión usando biometría (Face ID, huella dactilar) o llaves de seguridad físicas.
- **2FA TOTP**: El personal educativo puede proteger sus cuentas mediante aplicaciones como Google Authenticator o FreeOTP, incluyendo códigos de recuperación de emergencia.
- **Validación CSRF & Sesiones Seguras**: Protección robusta contra ataques comunes y detección inteligente de HTTPS tras proxies.

### 🎓 Para Estudiantes (Panel Alumno)
- **Acceso Flexible**: Autenticación mediante códigos OTP por email o biometría WebAuthn.
- **Reportes de Incidencias**: Creación de avisos con niveles de urgencia y descripción detallada.
- **Anonimato Garantizado**: Opción de reportar de forma anónima, ocultando la identidad criptográficamente.

### 🏫 Para el Equipo Educativo (Panel Staff)
- **Bandeja de Entrada Inteligente**: Gestión centralizada de reportes según el rol.
- **Colaboración Interna**: Sistema de **Notas Internas** y **Menciones (@)**.
- **Gestión de Estados**: Ciclo de vida completo del reporte.

### ⚙️ Administración y Personalización
- **Configuración Dinámica**: Panel de ajustes para cambiar el nombre de la escuela, colores corporativos y configuración SMTP directamente desde la interfaz.
- **Gestión de Usuarios y Aulas**: Control total sobre la estructura del centro.

---

## 🏗️ Estructura del Proyecto

```text
aura/
├── app/
│   ├── Controllers/    # Lógica de control (Auth, 2FA, Admin, etc.)
│   ├── Core/           # Motor interno (Router, Config, Mailer, Database)
│   ├── Models/         # Capa de datos (User, Setting, Report, etc.)
│   ├── Views/          # Plantillas de interfaz (Tailwind CSS)
│   └── routes.php      # Definición de rutas y middlewares
├── database/           # SQLite, migraciones y semillas
├── public/             # Punto de entrada único y assets
├── vendor/             # Dependencias de Composer (PHPMailer, WebAuthn, OTP)
└── storage/            # Logs, telemetría y bloqueos
```

---

## 💻 Requisitos del Sistema

- **PHP 8.1+** (extensiones: `pdo_sqlite`, `mbstring`, `openssl`, `gmp`, `bcmath`).
- **Composer**: Para la gestión de librerías de seguridad.
- **Servidor Web**: Apache (con `mod_rewrite`) o Nginx.
- **Base de Datos**: SQLite3.

---

## 🚀 Instalación y Configuración

### 1. Clonar y dependencias
```bash
git clone https://github.com/EmoTerraLab/Aura.git
cd aura
composer install
```

### 2. Configuración de permisos
```bash
chmod -R 775 database storage
```

### 3. Ejecución (Local)
```bash
php -S localhost:8000 -t public
```

---

## 🛠️ Despliegue en Producción

Se incluye un script `install.sh` optimizado para entornos Ubuntu/Debian que configura Apache, PHP y los permisos necesarios de forma automática.

```bash
sudo ./install.sh
```

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---
*Desarrollado con ❤️ por EmoTerraLab.*

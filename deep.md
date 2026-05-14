# Documentación Técnica Profunda: Proyecto Aura (v2.32.2)




Aura es una plataforma SaaS multi-instancia de gestión escolar especializada en la prevención, detección y seguimiento de protocolos de acoso escolar y análisis de convivencia (sociometría). Está diseñada bajo una arquitectura modular que permite adaptar el flujo legal a las 19 comunidades y ciudades autónomas de España.

## 1. Estructura de Directorios (Árbol Completo)

```text
/
├── app/                        # Núcleo de la aplicación (PHP 8.2+)
│   ├── Controllers/            # Controladores MVC
│   │   ├── Admin/              # Gestión de sistema y actualizaciones
│   │   ├── AuthController.php  # Autenticación (Dual: Alumnos/Staff)
│   │   ├── ProtocolController.php # Lógica genérica de protocolos
│   │   ├── ProtocolWorkflowController.php # Flujo legal (Catalunya/Genérico)
│   │   ├── AragonProtocolController.php # Flujo específico de Aragón
│   │   └── ... (otros controladores especializados)
│   ├── Core/                   # Framework interno (Core Engine)
│   │   ├── Auth.php            # RBAC y Control de Acceso
│   │   ├── Config.php          # Configuración dinámica desde DB
│   │   ├── Database.php        # Capa de datos (PDO/SQLite)
│   │   ├── Router.php          # Enrutador dinámico con Middlewares
│   │   ├── Lang.php            # Motor de i18n (Multi-idioma)
│   │   └── AuditLogger.php     # Trazabilidad y auditoría de seguridad
│   ├── Models/                 # Capa de persistencia (Entidades)
│   │   ├── User.php            # Usuarios, roles y credenciales (2FA)
│   │   ├── ProtocolCase.php    # El expediente legal de acoso
│   │   └── ... (Report, Classroom, OTPCode, etc.)
│   ├── Services/               # Lógica de negocio (Services Layer)
│   │   └── Protocol/           # Patrón Factory para protocolos regionales
│   └── Views/                  # Plantillas de interfaz (PHP/HTML/Tailwind)
├── database/                   # Persistencia física
│   ├── aura.sqlite             # DB principal (SQLite 3)
│   ├── migrations/             # Control de versiones del esquema SQL
│   └── backups/                # Copias de seguridad automáticas (SQLite snapshots)
├── public/                     # Punto de entrada Web (Document Root)
│   ├── index.php               # Front Controller
│   ├── sw.js                   # Service Worker (PWA / Offline)
│   └── assets/                 # Recursos estáticos (JS, CSS, Images)
├── storage/                    # Archivos variables y privados
│   ├── evidence/               # Documentación confidencial de casos
│   ├── logs/                   # Logs de error y telemetría
│   └── maintenance/            # Flags de estado del sistema
├── lang/                       # Diccionarios: es, ca, gl, eu, en
├── vendor/                     # Dependencias (PHPMailer, WebAuthn, otphp, etc.)
├── Dockerfile                  # Empaquetado para despliegue Cloud
├── docker-compose.yml          # Orquestación local (Dev stack)
├── composer.json               # Gestión de librerías y autoloading
└── GUION.md                    # Historial de desarrollo y parches críticos
```

---

## 2. Análisis Detallado de los Componentes

### 🛡️ Arquitectura de Seguridad (Core)
El proyecto Aura prioriza la integridad de los datos sensibles (menores de edad).
*   **MFA / 2FA**: Implementación robusta de **TOTP** (Google Authenticator) y **WebAuthn** (Biometría/FIDO2).
*   **Audit Logging**: Cada acción sensible (ver un caso, descargar evidencia, cambiar una fase) se registra en `audit_logs` mediante `Core\AuditLogger`.
*   **Rate Limiting**: Protección contra fuerza bruta en login y generación de OTP integrada en el enrutador.
*   **Seguridad de Archivos**: Las evidencias en `storage/evidence/` son inaccesibles vía URL directa; se sirven mediante un controlador que verifica permisos en tiempo real.

### ⚙️ El Motor de Protocolos (Services & Factory)
El corazón de Aura es su capacidad de adaptarse a la ley regional española.
*   **`ProtocolFactory`**: Inyecta la lógica correcta basándose en el parámetro `ccaa_code` de la configuración.
*   **`ProtocolInterface`**: Obliga a cada implementación regional a definir sus propias fases, plazos legales (ej: 18 días para valoración en Aragón) y anexos necesarios.
*   **`GenericProtocol`**: Sirve como fallback para regiones sin flujo específico, permitiendo la consulta de la normativa sin bloquear la aplicación.

### 🗄️ Base de Datos y Migraciones
Aura utiliza **SQLite**, lo que facilita enormemente el despliegue en centros educativos sin necesidad de administrar servidores MySQL/PostgreSQL.
*   El sistema de **Migraciones** (`Core\Migrator`) permite actualizar el esquema de forma automática al detectar una versión nueva del software.
*   Se realizan backups automáticos del fichero `.sqlite` en cada actualización crítica.

### 📱 Experiencia de Usuario (Frontend & PWA)
*   **Frontend**: Basado en componentes PHP nativos con estilos de **Tailwind CSS** servidos vía CDN/Local.
*   **Interacción**: Uso intensivo de **AJAX/Fetch API** para una experiencia fluida sin recargas de página (SPA-like).
*   **PWA**: El Service Worker (`public/sw.js`) permite el acceso offline a la guía de protocolos, vital en situaciones de emergencia escolar sin conectividad.

### 🛠️ Flujo de Trabajo del Desarrollador
1.  **Rutas**: Se definen en `app/routes.php` asociando URIs a controladores y middlewares de rol.
2.  **Modelos**: Heredan de `Core\Model` para realizar operaciones CRUD sencillas sobre SQLite.
3.  **Vistas**: Se organizan por actor (`alumno`, `staff`, `admin`) y utilizan un sistema de layouts para evitar duplicación de código.

---

## 3. Guía Rápida para Nuevos Desarrolladores
-   **Configuración**: La aplicación lee sus ajustes de la tabla `settings`. El `ccaa_code` es la clave principal que cambia el comportamiento de todo el sitio.
-   **Roles**: Existen 5 niveles: `alumno`, `profesor`, `orientador`, `direccion` y `admin`.
-   **Depuración**: Activa el modo `development` en el `index.php` para ver errores detallados. Revisa `storage/logs/php_errors.log` para trazas de backend.

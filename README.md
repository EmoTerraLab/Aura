# Aura

![Versión](https://img.shields.io/badge/Versión-2.30.1-blue.svg)
![PHP](https://img.shields.io/badge/PHP-%3E%3D8.2-777BB4.svg?logo=php)
![Base de Datos](https://img.shields.io/badge/Database-SQLite_3-003B57.svg?logo=sqlite)
![Licencia](https://img.shields.io/badge/Licencia-Propietaria-red.svg)
![Seguridad](https://img.shields.io/badge/Seguridad-Auditada-success.svg)

## 📖 Descripción General

**Aura** es una plataforma integral SaaS, diseñada específicamente para la gestión escolar enfocada en la prevención, detección y seguimiento de casos de acoso escolar y mejora de la convivencia mediante análisis sociométricos. Aura permite a los centros educativos digitalizar y automatizar los complejos procesos legales y documentales exigidos por las consejerías de educación.

El problema fundamental que Aura resuelve es la enorme fragmentación burocrática y legal de España, donde cada Comunidad Autónoma tiene su propia normativa, fases, plazos y formularios obligatorios para tratar el acoso escolar. Aura unifica todo en una interfaz amigable, adaptando automáticamente los flujos de trabajo según la región del centro educativo.

Aura destaca técnica y arquitectónicamente por ser un software ultraligero y robusto. No depende de frameworks PHP pesados (como Laravel o Symfony) ni de servidores de bases de datos externos. Implementa una arquitectura **MVC puramente en Vanilla PHP** y almacena toda la información de manera autónoma en un fichero **SQLite3**, haciendo que su despliegue en infraestructuras escolares básicas sea rápido y seguro. Además, utiliza el patrón de diseño **Strategy** para desacoplar los protocolos regionales y está construida como una **Progressive Web App (PWA)**, lo que garantiza acceso a la guía de actuación incluso sin conexión a internet.

---

## ✨ Características Principales

*   **Soporte Multi-CCAA (Patrón Strategy):** Protocolos integrados y adaptados a las normativas de Aragón, Murcia, Galicia, Cataluña, Comunidad Valenciana, etc.
*   **Gestión de Expedientes:** Creación de casos, seguimiento de fases, control estricto de plazos legales y cierre con generación de PDF (anexos oficiales).
*   **Autenticación Fuerte:** Doble factor de autenticación (2FA) basado en TOTP y acceso biométrico sin contraseñas (WebAuthn/FIDO2).
*   **Análisis Sociométrico:** Módulo avanzado para encuestas de convivencia en el aula y generación automática de sociogramas.
*   **Progressive Web App (PWA):** Instalable en móviles y tablets, con acceso offline a protocolos de emergencia gracias al uso de Service Workers.
*   **Seguridad y Auditoría:** Protección integral contra CSRF y fuerza bruta (Rate Limiting). Registro minucioso de auditoría (Audit Log) para cada acción sensible de acceso o modificación.
*   **Control de Acceso Basado en Roles (RBAC):** Perfiles definidos: Alumno, Profesor, Orientador, Dirección y Administrador.
*   **Internacionalización (i18n):** Plataforma disponible en Español (`es`), Catalán (`ca`), Gallego (`gl`), Euskera (`eu`) e Inglés (`en`).

---

## 🏛️ Arquitectura del Proyecto

Aura implementa un **MVC (Modelo-Vista-Controlador)** personalizado y construido desde cero para garantizar un rendimiento óptimo y máxima seguridad.

*   **Patrón Front Controller:** Todo el tráfico HTTP pasa obligatoriamente por `public/index.php`. Esto oculta la estructura interna del servidor y permite inicializar variables, middlewares y el `Router` centralizado antes de cargar cualquier lógica de negocio.
*   **Patrón Strategy (Protocolos):** Debido a las variaciones legales, el sistema inyecta en tiempo real la clase correspondiente a la región seleccionada mediante el `ProtocolFactory`. Cada clase regional implementa la `ProtocolInterface`, asegurando que todas expongan los métodos necesarios (`getPhases()`, `getDaysLimit()`, etc.) pero contengan la lógica y anexos específicos de su comunidad.
*   **Middlewares y Core Engine:** Se utiliza un sistema de enrutamiento estricto que pasa por filtros de autenticación y sanitización antes de llegar al Controlador.

```text
Request ➔ public/index.php ➔ Router ➔ Security Middlewares (CSRF/RateLimit) ➔ Controller ➔ Service (ProtocolFactory) ➔ Model (SQLite) ➔ View (PWA) ➔ Response
```

---

## 📂 Estructura de Directorios

La estructura base está optimizada para la seguridad, manteniendo los ficheros lógicos y de datos fuera del acceso público del servidor web.

```text
Aura/
├── app/                        # Núcleo de la aplicación (Arquitectura MVC Vanilla)
│   ├── Controllers/            # Manejadores de solicitudes (AuthController, ProtocolController, etc.)
│   ├── Core/                   # Enrutador, Configuración, Interacción Base de Datos
│   ├── Models/                 # Entidades y queries SQL
│   ├── Services/               # Capa de lógica de negocio y ProtocolFactory
│   └── Views/                  # Interfaz gráfica y plantillas HTML/PHP
├── database/                   # Almacenamiento local (Sin necesidad de servidor MySQL)
│   ├── aura.sqlite             # Base de datos en producción
│   └── migrations/             # Control de versiones del esquema de BBDD
├── lang/                       # Diccionarios de idioma (i18n)
├── public/                     # DocumentRoot del servidor web (¡Única carpeta accesible!)
│   ├── assets/                 # CSS, JS, imágenes
│   ├── index.php               # Front Controller
│   └── sw.js                   # Service Worker (PWA)
├── storage/                    # Archivos generados dinámicamente y privados
│   ├── evidence/               # Archivos probatorios de los expedientes
│   └── logs/                   # Registro de errores y auditoría de seguridad
├── tests/                      # Suite de pruebas funcionales
├── composer.json               # Dependencias del proyecto
├── Dockerfile                  # Receta de contenedores
└── install.sh                  # Script automatizado de despliegue
```

---

## 💻 Tecnologías Utilizadas

### Backend
*   **Lenguaje:** PHP 8.2+
*   **Base de Datos:** SQLite3 (vía PDO)
*   **Gestor de Dependencias:** Composer

### Frontend
*   **Estilos:** Tailwind CSS (vía CDN/Local)
*   **Iconografía:** Google Material Symbols
*   **Lógica de cliente:** Vanilla JavaScript (ES6+), Fetch API

### Capacidades Avanzadas y Seguridad
*   **WebAuthn:** `lbuchs/webauthn` (Biometría y llaves de seguridad)
*   **2FA (TOTP):** `spomky-labs/otphp` y `bacon/bacon-qr-code`
*   **Mailing:** `phpmailer/phpmailer`
*   **PWA:** Service Workers nativos, Web App Manifest.
*   **Criptografía:** `bcrypt` para contraseñas, sanitización nativa `htmlspecialchars` y sentencias SQL preparadas obligatorias.

---

## ⚙️ Requisitos del Sistema

*   **Servidor Web:** Apache 2.4+ (con `mod_rewrite` habilitado) o Nginx.
*   **PHP:** Versión >= 8.2
*   **Extensiones PHP Obligatorias:** `ext-pdo`, `ext-sqlite3`, `ext-mbstring`, `ext-openssl`, `ext-fileinfo`, `ext-gd`.
*   **Almacenamiento:** Mínimo 1GB recomendado (dependerá de la cantidad de evidencias multimedia subidas al sistema).
*   **Sistema Operativo:** Basado en Linux (Ubuntu/Debian, CentOS, etc.) recomendado para producción.

---

## 🚀 Instalación

### Opción A: Instalación Automatizada (Recomendado para Linux)
Aura dispone de un script que instalará dependencias y asignará los permisos de carpetas correctamente.

```bash
git clone https://github.com/tu-organizacion/aura.git
cd aura
chmod +x install.sh
./install.sh
```

### Opción B: Instalación Manual
1.  **Clonar el repositorio y acceder al directorio:**
    ```bash
    git clone https://github.com/tu-organizacion/aura.git && cd aura
    ```
2.  **Instalar dependencias de Composer:**
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
3.  **Configurar permisos críticos:**
    Es vital que el servidor web (`www-data` o `apache`) tenga permisos de escritura en la base de datos y en el almacenamiento de evidencias.
    ```bash
    chmod -R 775 database storage
    chown -R www-data:www-data database storage
    ```
4.  **Generar Base de Datos:**
    ```bash
    php migrate.php
    ```

### Opción C: Despliegue con Docker (Desarrollo)
```bash
docker-compose up -d --build
```
*Aura estará disponible en `http://localhost:8000`.*

> ⚠️ **CRÍTICO:** El `DocumentRoot` de Apache/Nginx debe apuntar **EXCLUSIVAMENTE** al directorio `public/`. Nunca a la raíz del proyecto.

---

## 🔧 Configuración Post-Instalación

Aura guarda su configuración de manera dinámica en la tabla `settings` de la base de datos (administrable vía la interfaz web en `/admin/settings`).

**Pasos inmediatos tras instalar:**
1.  **Crear Administrador Principal:** Ingresa al sistema (si se crearon credenciales por defecto) y configura tu cuenta con una contraseña fuerte y activa el TOTP obligatoriamente.
2.  **Configurar Variables Core:** Define la variable de entorno o ajuste `APP_KEY` (necesaria para criptografía interna) y establece la zona horaria correcta.
3.  **Ajustes de Comunidad Autónoma:** En el panel de administración, selecciona el Código de Comunidad Autónoma (`ccaa_code`). Esto activará automáticamente el protocolo correspondiente a dicha región.
4.  **Configuración SMTP:** Asegura el envío de alertas y códigos OTP rellenando las credenciales de correo (Servidor, Puerto, Usuario, Contraseña) en el panel administrativo.

---

## 👨‍🏫 Uso y Flujos Básicos

1.  **Inicio de Sesión:** El personal entra mediante Credenciales + 2FA o WebAuthn. Los alumnos acceden a encuestas mediante un código OTP de un solo uso emitido por el profesor.
2.  **Apertura de Caso:** El orientador o dirección registra una sospecha. Se genera un `ProtocolCase` en la BBDD.
3.  **Fases y Anexos:** Dependiendo de la Comunidad Autónoma, la vista del caso mostrará las fases legales (ej. "Reunión de valoración", "Comunicación a inspección"). El sistema bloqueará fases futuras hasta que se completen y anexen los documentos de las fases previas.
4.  **Cierre:** Finalizado el proceso, se consolida la documentación en un paquete sellado (exportable en PDF).

---

## 🌍 Internacionalización (i18n)

Aura es completamente multi-idioma. Las traducciones residen en el directorio `lang/`.
Para añadir un nuevo idioma (ejemplo: Francés - `fr`):
1.  Crea el fichero `lang/fr.php`.
2.  Copia la estructura asociativa de `lang/es.php` y traduce los valores.
3.  El sistema detectará el archivo y permitirá la selección desde el panel o el perfil de usuario.

---

## 🧩 Añadir un Nuevo Protocolo Autonómico

Aura utiliza el patrón Strategy. Para integrar la normativa de una nueva comunidad (ej. Andalucía):

1.  **Crear la Clase del Protocolo:** Crea `App\Services\Protocol\AndaluciaProtocol.php` que implemente la interfaz `ProtocolInterface`.
    ```php
    namespace App\Services\Protocol;
    
    class AndaluciaProtocol implements ProtocolInterface {
        public function getPhases(): array { return [...]; }
        public function getDaysLimit(): int { return 30; }
        // ... implementar el resto de métodos
    }
    ```
2.  **Registrar en el Factory:** Añade el mapeo en `App\Services\Protocol\ProtocolFactory.php`.
    ```php
    case 'AND': return new AndaluciaProtocol();
    ```
3.  **Crear las Vistas:** Genera el directorio `app/Views/protocol/andalucia/` y dentro añade `case_detail.php` y las plantillas para sus anexos específicos (`anexo_1.php`, etc.).

---

## 🛡️ Seguridad y Auditoría

Aura maneja datos de menores altamente sensibles. Múltiples capas garantizan la protección:

*   **Rate Limiting:** El sistema rastrea intentos fallidos de login por IP/Usuario bloqueando ataques de diccionario.
*   **CSRF Tokens:** Cada formulario (POST/PUT/DELETE) requiere un token único válido por sesión.
*   **Prevención IDOR:** Todo acceso a `/storage/evidence/` verifica mediante un Controller si el usuario actual (Staff/Admin) tiene derechos sobre ese expediente.
*   **Audit Logger:** Cualquier modificación de un caso (cambios de fase, descargas, fallos de login) se inscribe de manera inmutable en `audit_logs` con IP, fecha y usuario.
*   **Protección SQL:** Uso obligatorio de `PDO Prepared Statements`. Jamás se concatena input de usuario.

*Si descubres una vulnerabilidad, por favor no la publiques en issues; contacta inmediatamente al equipo de desarrollo.*

---

## 🛠️ Mantenimiento

*   **Copias de Seguridad (Backups):** Cada vez que se realiza una acción crítica o una actualización, Aura copia `aura.sqlite` a la carpeta `database/backups/`. Revisa periódicamente este directorio.
*   **Logs:** Los errores fatales de PHP nunca se muestran al usuario. Revisa `storage/logs/php_errors.log` para depuración.
*   **Modo Mantenimiento:** Puedes crear un archivo en `storage/maintenance/` (o usar la opción en el panel Admin) para mostrar la vista genérica de mantenimiento mientras aplicas parches.

---

## 🤝 Contribución

Si deseas aportar código:
1.  Haz un *Fork* del repositorio y crea una rama descriptiva (`feature/nuevo-protocolo`, `fix/login-bug`).
2.  Sigue estrictamente los estándares de código **PSR-12**.
3.  Asegúrate de no usar frameworks externos. Todo PR debe ceñirse a Vanilla PHP.
4.  Agrega las pruebas necesarias en el directorio `tests/` y garantiza que la compatibilidad con PHP 8.2 se mantenga intacta.
5.  Envía el Pull Request con una descripción detallada.

---

## 📜 Licencia

**Propietaria y Confidencial.**  
Proyecto desarrollado exclusivamente por EmoTerraLab - Grupo de Innovación y Recursos (GIR). Prohibida su distribución, copia, ingeniería inversa o uso comercial sin autorización expresa de los propietarios.

---

## 👥 Créditos y Autores

*   **Líder de Proyecto / Arquitecto de Software:** <!-- TODO: verificar nombres reales -->
*   **Equipo de Desarrollo:** <!-- TODO: verificar nombres reales -->
*   **Asesoría Jurídica e Institucional:** <!-- TODO: verificar nombres reales -->

---

## 📚 Enlaces y Recursos

Para profundizar en la arquitectura y uso detallado del sistema, consulta los siguientes documentos ubicados en la raíz del repositorio:
*   📄 **[Guión Técnico (GUION.md)](GUION.md):** Decisiones de arquitectura y registro de desarrollo.
*   📖 **[Manual de Usuario (MANUAL.md)](MANUAL.md):** Guía funcional para administradores y orientadores.
*   🔄 **[Guía de Actualización (UPDATING.md)](UPDATING.md):** Instrucciones para migraciones de versión.
*   🛡️ **Reportes de Auditoría (`AUDIT_REPORT_*.md`):** Trazabilidad de parches de seguridad.
*   🔍 **[Análisis Profundo (deep.md)](deep.md):** Descripción exhaustiva de todo el árbol de archivos.
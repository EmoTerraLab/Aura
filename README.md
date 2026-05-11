<div align="center">
  <img src="public/assets/images/logo.png" alt="Aura Logo" width="150" height="auto" />
  <h1>Aura</h1>
  <p><em>Plataforma Empresarial de Gestión de Convivencia Escolar y Prevención del Acoso</em></p>

  <p>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white&style=for-the-badge" alt="Versión PHP"></a>
    <a href="#"><img src="https://img.shields.io/badge/Seguridad-Nivel_Bancario-red?style=for-the-badge&logo=security" alt="Nivel de Seguridad"></a>
    <a href="#"><img src="https://img.shields.io/badge/Arquitectura-MVC_Nativo-blueviolet?style=for-the-badge" alt="Arquitectura"></a>
    <a href="#"><img src="https://img.shields.io/badge/Licencia-Propietaria-darkgreen?style=for-the-badge" alt="Licencia"></a>
  </p>
</div>

---

Aura es una plataforma de gestión y reportes altamente segura, construida específicamente para instituciones educativas. Permite a los alumnos reportar incidentes de manera segura (de forma anónima o identificada) y dota a la administración del centro escolar de una automatización de flujos de trabajo regionales que cumplen con la normativa legal vigente.

## 🚀 Características Principales

### 🛡️ Seguridad y Auditoría (Esquema Nacional de Seguridad)
Aura está diseñada siguiendo las directrices del **Esquema Nacional de Seguridad (ENS)** aplicable a las Comunidades Autónomas en España, garantizando la confidencialidad, integridad y trazabilidad de los datos de menores:

*   **Autenticación Multifactor (MFA):** WebAuthn biométrico (FaceID/TouchID) para alumnos, garantizando un acceso sin contraseñas (Passwordless); y TOTP cifrado con AES-256-GCM para el personal administrativo y docente.
*   **Gestión de Sesiones y Cookies:** Cookies de sesión fortificadas con banderas `Secure` (forzado bajo HTTPS), `HttpOnly` (prevención de robo vía XSS) y directiva `SameSite=Lax` para mitigar ataques de navegación cruzada. Rotación estricta de ID de sesión post-login.
*   **Protección contra falsificación (CSRF):** Sistema de validación de tokens per-session acoplado a un motor de cabeceras seguras AJAX para todas las mutaciones de estado, evitando falsificación de peticiones en flujos de múltiples pasos.
*   **Blindaje de Nivel Bancario (Control de Acceso):** Prevención de vulnerabilidades IDOR (Insecure Direct Object Reference) en la lectura/escritura de expedientes mediante verificación criptográfica y delegación de propiedad estructurada en la base de datos.
*   **Mitigación de ataques de Fuerza Bruta:** Limitación de peticiones (Rate-Limiting) estricta e híbrida basada en "IP + Identificador" para mitigar ataques continuos como el *Credential Stuffing*.
*   **Registros de Auditoría Inmutables:** Sistema de bitácora (Audit Trail) forense de todas las interacciones del sistema, cumpliendo con los estándares de retención de pruebas del marco normativo autonómico para inspecciones educativas.

### 🗺️ Protocolos Legales Autonómicos (Máquina de Estados)
Aura adapta dinámicamente sus flujos de trabajo legales en base a las directivas regionales (Comunidades Autónomas):
*   **Galicia (v2.23):** Máquina de estados legal de 6 fases, 16 anexos en PDF autogenerados y módulos integrados de "Medidas Urxentes" y "Ciberacoso".
*   **Aragón:** Obliga al seguimiento del Anexo I-a, constitución de equipos de valoración y checklists normativos especializados.
*   **Murcia y C. Valenciana:** Flujos de trabajo personalizados y salvaguardias de cumplimiento estrictas que evitan transiciones de fase desordenadas.

### 🌐 Internacionalización y Accesibilidad
*   **5 Idiomas Nativos:** Totalmente localizado en Español, Català, Galego, Euskara e Inglés.
*   **Sociogramas Interactivos:** Integración nativa de `Cytoscape.js` para mapear las relaciones del aula y detectar vulnerabilidades visualmente.

---

## 🏗️ Arquitectura del Sistema

Aura opera sobre una arquitectura **MVC Nativa** (Modelo-Vista-Controlador). Al evitar activamente los frameworks monolíticos (como Laravel), Aura mantiene un tamaño mínimo sin sobrecarga, tiempos de respuesta ultra rápidos y reduce drásticamente los riesgos de seguridad en la cadena de suministro de dependencias.

<details>
<summary><b>📂 Haz clic para ver la Estructura de Directorios</b></summary>

```text
aura/
├── app/
│   ├── Controllers/    # Endpoints y validación estricta IDOR
│   ├── Core/           # Framework: Router, Auth, Middleware, AuditLogger
│   ├── Models/         # Interacciones PDO parametrizadas con SQLite
│   └── Views/          # Plantillas PHP con escape XSS automático
├── database/
│   ├── .htaccess       # Deniega acceso HTTP a la base de datos
│   └── aura.sqlite     # Capa de persistencia portable
├── public/             
│   ├── index.php       # Front Controller (Punto de Entrada Único)
│   └── .htaccess       # Reglas de enrutamiento (Mod_rewrite)
└── storage/            # Evidencias, anexos y registros
```
</details>

---

## 💻 Inicio Rápido y Despliegue

Aura está diseñado para una instalación local (on-premise) sin fricciones, eliminando la necesidad de gestionar demonios de bases de datos externas complejos gracias a su motor SQLite 3.35+ altamente optimizado.

### Requisitos Previos
*   Apache 2.4+ (con `mod_rewrite` activado)
*   PHP 8.2+ (extensiones: `pdo_sqlite`, `sodium`, `mbstring`)
*   Composer 2.x

### Pasos de Instalación

1. **Clonar el Repositorio**
   ```bash
   git clone https://github.com/tu-org/aura-pdp.git
   cd aura-pdp
   ```

2. **Instalar Dependencias**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Configurar Permisos**
   Concede al servidor web permisos de escritura sobre los directorios de persistencia:
   ```bash
   chmod -R 775 storage database
   ```

4. **Configuración del Servidor Web**
   > ⚠️ **CRÍTICO:** El `DocumentRoot` de Apache debe apuntar **EXCLUSIVAMENTE** al directorio `public/`. Nunca a la raíz del proyecto.

   ```apache
   <VirtualHost *:443>
       ServerName colegio-aura.com
       DocumentRoot /var/www/aura/public

       <Directory /var/www/aura/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

---

## 🔐 Administración y Configuración Inicial

Al ejecutar la aplicación por primera vez, la base de datos se generará automáticamente. Inicia sesión utilizando las credenciales administrativas por defecto proporcionadas por el líder de despliegue. 

**Acciones Críticas Inmediatas Post-Instalación:**
1. Configurar la variable de entorno `APP_KEY` (32 bytes en formato hexadecimal).
2. Cambiar la Contraseña del Administrador y habilitar TOTP obligatoriamente.
3. Configurar el Gateway SMTP (para el envío de correos automatizados) desde el panel de ajustes.

---

<div align="center">
  <b>© 2026 EmoTerraLab — Proyecto Aura (GIR)</b><br>
  <i>Confidencial y Propietario</i>
</div>
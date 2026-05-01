# 📚 Manual de Usuario y Técnico — Aura
# User & Technical Manual — Aura

> **Versión:** 2.13.8-stable
> **Última actualización:** viernes, 1 de mayo de 2026
> **Idiomas / Languages:** Español · English

---

## Índice / Table of Contents

### 🇪🇸 MANUAL DE USUARIO
1. [Introducción](#1-introducción)
2. [Acceso al sistema](#2-acceso-al-sistema)
3. [Panel de Alumno](#3-panel-de-alumno)
4. [Panel de Staff](#4-panel-de-staff)
5. [Gestión de Protocolos Legales (Nacional)](#5-gestión-de-protocolos-legales-nacional)
6. [Prácticas Restaurativas](#6-prácticas-restaurativas)
7. [Panel de Administración](#7-panel-de-administración)
8. [Solución de problemas (usuario)](#8-solución-de-problemas-usuario)

---

# 🇪🇸 MANUAL DE USUARIO

## 1. Introducción
Aura es una herramienta integral diseñada para la gestión de la convivencia escolar. Proporciona un canal seguro para que los alumnos comuniquen incidencias y permite al personal del centro gestionar protocolos legales según la normativa de cada comunidad autónoma de España.

### Roles del Sistema
- **Alumno:** Crea informes, realiza encuestas sociométricas y hace seguimiento de sus casos.
- **Staff (Profesor/Orientador/Dirección):** Gestiona informes, aplica protocolos legales, registra seguimientos y coordina medidas.
- **Administrador:** Configura el centro, gestiona usuarios/aulas y actualiza el sistema.

---

## 2. Acceso al sistema

### 2.1 Iniciar sesión
Acceda a la URL del centro:
- **Alumnos:** Usan email institucional y código OTP (por email) o Passkey (biometría).
- **Personal:** Acceso mediante email y contraseña + 2FA (Google Authenticator).

### 2.2 Gestión de Contraseñas (Staff)
Desde la bandeja de entrada, el personal puede acceder a su perfil para **cambiar su contraseña** de forma segura.

---

## 5. Gestión de Protocolos Legales (Nacional)
Aura incorpora una arquitectura modular que soporta los protocolos legales de las 19 comunidades y ciudades autónomas.

### 5.1 Activación
El administrador selecciona la CCAA en los Ajustes. El sistema adaptará automáticamente los formularios, plazos y anexos oficiales.

### 5.2 Caso de Aragón (Ejemplo)
- **Timeline:** Seguimiento de los 18 días lectivos para valoración y 22 para resolución.
- **Anexos:** Generación automática de los Anexos I a X listos para imprimir o guardar en PDF.
- **Alertas:** Avisos visuales cuando se detectan indicios de violencia sexual o acoso grave.

---

## 6. Prácticas Restaurativas
Módulo para gestionar la reparación del daño sin recurrir únicamente a medidas sancionadoras.
- **Reconocimiento de hechos:** Registro de si el alumnado implicado acepta la responsabilidad.
- **Acciones:** Creación de círculos, reuniones o conversaciones restaurativas.
- **Seguimiento:** Control de cumplimiento de los acuerdos alcanzados.

---

## 7. Panel de Administración
- **Configuración de CCAA:** Crucial para activar la normativa legal correspondiente.
- **Actualizaciones:** El sistema permite ejecutar migraciones y backups automáticos desde la interfaz.

---

# 🛠️ MANUAL TÉCNICO / TECHNICAL MANUAL

## 1. Arquitectura / Architecture
Aura utiliza un patrón **MVC Nativo**.
- **Model:** `app/Models/` (SQLite vía PDO).
- **View:** `app/Views/` (PHP puro con escape de datos).
- **Controller:** `app/Controllers/`.
- **Services:** `app/Services/` (Contiene la lógica de protocolos multi-CCAA).

### Motor de Protocolos
Los flujos legales se gestionan mediante `ProtocolInterface`. Cada comunidad tiene su propia clase en `app/Services/Protocol/` que define sus estados, plazos y documentos.

---

## 2. Base de Datos / Database
- **Migraciones:** Gestión incremental en `database/migrations/`.
- **Backup:** Copias de seguridad automáticas en cada actualización.

---

## 5. Despliegue / Deployment
1. **Sincronización:** Use `rsync` para subir los archivos.
2. **Dependencias:** `composer install --no-dev`.
3. **Persistencia:** Asegure permisos `664` en `database/aura.sqlite` y `775` en `storage/`.

---
© 2026 EmoTerraLab — Aura Project
# Aura: Santuario Digital Escolar 🛡️

Aura es una plataforma web integral diseñada para transformar la convivencia escolar. Proporciona un canal seguro, confidencial y ágil donde los estudiantes pueden reportar incidencias o preocupaciones, y donde el equipo educativo puede gestionar casos de manera colaborativa y estructurada.

## 🌟 Características Principales

### 🎓 Para Estudiantes (Panel Alumno)
- **Acceso Sin Contraseña**: Autenticación segura mediante códigos OTP (One-Time Password) enviados al correo institucional.
- **Reportes de Incidencias**: Creación de avisos con niveles de urgencia y descripción detallada.
- **Anonimato Garantizado**: Opción de reportar de forma anónima, ocultando la identidad criptográficamente frente a tutores.
- **Chat Seguro**: Comunicación directa con el personal asignado para seguimiento del caso.

### 🏫 Para el Equipo Educativo (Panel Staff)
- **Bandeja de Entrada Inteligente**: Gestión centralizada de reportes según el rol (Tutor, Orientador, Dirección).
- **Colaboración Interna**: Sistema de **Notas Internas** (invisibles para el alumno) y **Menciones (@)** para involucrar a otros profesionales.
- **Gestión de Estados**: Ciclo de vida del reporte (Recibido → En Revisión → Resuelto).
- **Resolución Formal**: Registro obligatorio de conclusiones finales para el cierre de casos.

### ⚙️ Administración
- **Gestión de Usuarios y Roles**: Control total sobre perfiles de alumnos y personal.
- **Configuración de Aulas**: Vinculación de alumnos con tutores específicos para una asignación automática de casos.

---

## 🏗️ Estructura del Proyecto

El proyecto sigue una arquitectura **MVC (Modelo-Vista-Controlador)** limpia y desacoplada:

```text
aura/
├── app/
│   ├── Controllers/    # Lógica de control por dominio (Auth, Report, Admin, etc.)
│   ├── Core/           # Motor interno (Router, Database, Auth, Session, CSRF)
│   ├── Models/         # Capa de datos y lógica de negocio (User, Report, etc.)
│   ├── Views/          # Plantillas de interfaz (Blade-like PHP templates)
│   └── routes.php      # Definición de todos los endpoints y middlewares
├── database/           # Archivos SQLite, migraciones y semillas (seeds)
├── public/             # Punto de entrada único (index.php) y assets estáticos
├── storage/            # Logs de telemetría y bloqueos de procesos
├── Dockerfile          # Configuración de contenedor para despliegue
└── docker-compose.yml  # Orquestación para entorno de desarrollo
```

---

## 💻 Requisitos del Sistema

- **PHP 8.1+** (con extensiones `pdo_sqlite`, `mbstring`, `openssl`).
- **Servidor Web**: Apache (con `mod_rewrite`) o Nginx.
- **Base de Datos**: SQLite3.
- **Docker & Docker Compose** (Opcional, para ejecución en contenedores).

---

## 🚀 Instalación y Configuración

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/aura.git
cd aura
```

### 2. Configuración de permisos
Asegúrate de que las carpetas de base de datos y logs tengan permisos de escritura:
```bash
chmod -R 775 database storage
```

### 3. Inicialización de la Base de Datos
El sistema está configurado para inicializar automáticamente `database/aura.sqlite` en el primer arranque utilizando `migrations.sql` y `seeds.sql`. No obstante, puedes hacerlo manualmente:
```bash
sqlite3 database/aura.sqlite < database/migrations.sql
sqlite3 database/aura.sqlite < database/seeds.sql
```

---

## 🛠️ Ejecución

### Opción A: Servidor Local de PHP (Desarrollo)
```bash
php -S localhost:8000 -t public
```
La aplicación estará disponible en `http://localhost:8000`.

### Opción B: Docker (Recomendado)
```bash
docker-compose up -d --build
```
La aplicación estará disponible en `http://localhost:8000`.

---

## 🔄 Flujo del Sistema (Ciclo MVC)

1. **Request**: El usuario accede a una URL (ej: `/alumno/dashboard`).
2. **Router**: `app/Core/Router.php` analiza la ruta y aplica Middlewares (ej: verificar sesión).
3. **Controller**: Se invoca a `StudentController@index`.
4. **Model**: El controlador solicita datos al modelo `Report::getByStudent()`.
5. **View**: El controlador pasa los datos a `View::render()`, que carga la plantilla desde `app/Views/alumno/dashboard.php`.

---

## 🛣️ Rutas Principales

| Método | Ruta | Descripción | Acceso |
|--------|------|-------------|--------|
| `GET` | `/login` | Pantalla de acceso unificada | Público |
| `POST` | `/login/otp/generate` | Generación de código para alumnos | Público |
| `GET` | `/alumno/dashboard` | Panel principal del estudiante | Alumno |
| `POST` | `/alumno/report` | Envío de nuevo caso | Alumno |
| `GET` | `/staff/inbox` | Bandeja de entrada de casos | Staff |
| `PATCH` | `/staff/reports/{id}` | Actualización de estado y resolución | Staff (Org/Dir) |
| `GET` | `/admin` | Panel de gestión global | Admin |

---

## 🔐 Seguridad y Buenas Prácticas

- **Capa Core Robusta**: Implementación propia de CSRF Protection en cada formulario POST.
- **Auditoría Inmutable**: Los registros de mensajes y cambios de estado no permiten edición ni borrado para garantizar la integridad legal del proceso educativo.
- **SQLite Optimizado**: Uso de `PRAGMA journal_mode = WAL` para permitir concurrencia en entornos escolares de tráfico medio.
- **Validación Estricta**: Middlewares personalizados para control de acceso basado en roles (`auth`, `role:alumno`, `roles:profesor,orientador`).

---

## 📈 Mejoras Futuras

- [ ] Sistema de notificaciones push para navegadores.
- [ ] Exportación de informes estadísticos en PDF/Excel para inspección educativa.
- [ ] Soporte para adjuntos multimedia (fotos/voz) en los reportes.
- [ ] Integración con sistemas de gestión escolar (SGE) vía API.

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---
*Desarrollado con ❤️ para transformar la convivencia en los centros educativos.*

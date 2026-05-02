# Informe de Auditoría de Seguridad y Calidad de Código — Aura v2.20.1-stable
**Fecha:** 2026-05-01
**Objetivo:** Auditoría exhaustiva de seguridad, arquitectura y calidad de código del núcleo del proyecto.

---

## 1. Análisis de Ficheros y Módulos Clave

### `app/Controllers/Admin/UpdateController.php`
- **Función:** Gestiona las actualizaciones del sistema y el modo de mantenimiento.
- **Problema (CRÍTICO):** *Backdoor de Mantenimiento.* Existe un secreto hardcoded (`Ceuta2000`) en el método `secretToggleMaintenance` que permite eludir los mecanismos de autenticación y modificar el estado crítico del sistema.
- **Explicación técnica:** Cualquier usuario que conozca el endpoint y el parámetro secreto puede desactivar o activar el modo mantenimiento sin tener rol de administrador o sesión activa.
- **Impacto:** Compromiso total de la disponibilidad del sistema.
- **Recomendación:** Eliminar el secreto hardcoded y proteger este endpoint exclusivamente mediante el middleware de autenticación y autorización (`roles:admin`).

### `app/Controllers/ProtocolWorkflowController.php`
- **Función:** Controla el flujo legal y documental de los protocolos autonómicos (Catalunya y genéricos).
- **Problema (ALTO):** *Falta de Validación CSRF en mutaciones de estado.* Métodos críticos que modifican la base de datos (ej. `addFollowup`, `uploadEvidence`, `saveSecurityMapFull`) no incluyen la llamada a `Csrf::validateRequest()`.
- **Explicación técnica:** Un atacante podría engañar a un administrador autenticado para que visite una página maliciosa que envíe peticiones POST/PATCH forjadas, inyectando seguimientos falsos o alterando el estado legal de un caso.
- **Impacto:** Alteración de expedientes legales sin el consentimiento consciente del usuario.
- **Recomendación:** Añadir `Csrf::validateRequest();` al inicio de todos los métodos expuestos mediante POST/PUT/PATCH/DELETE.

### `app/Controllers/AragonProtocolController.php`
- **Función:** Gestiona el protocolo autonómico de Aragón.
- **Problema (CRÍTICO):** *Errores de Ejecución (Fatal Errors).* El controlador hace llamadas a métodos que no existen en el modelo subyacente.
- **Explicación técnica:** Se detectan invocaciones a métodos como `$this->reportModel->createStaffReport()` o `$this->caseModel->findByReport()`, los cuales no están definidos en `Report.php` o `AragonProtocolCase.php`.
- **Impacto:** Caída total de la aplicación (HTTP 500) cuando el personal intente iniciar o consultar un protocolo en Aragón.
- **Recomendación:** Revisar y alinear la interfaz de los modelos de Aragón con los métodos llamados en el controlador, o refactorizar el controlador para usar los métodos existentes (ej. `$this->reportModel->create()`).

### `app/Controllers/Admin/SettingsController.php`
- **Función:** Gestión de la configuración global del sistema y CCAA.
- **Problema (ALTO):** *Local File Inclusion (LFI) Potencial.*
- **Explicación técnica:** La vista `index.php` de ajustes carga archivos de pestañas usando la variable `$_GET['tab']` de forma dinámica (ej. `require __DIR__ . '/_tab_' . $tab . '.php';`) sin validar que el valor de `$tab` pertenezca a una lista blanca restrictiva.
- **Impacto:** Un atacante podría manipular el parámetro `tab` para intentar cargar archivos locales sensibles si el servidor no tiene las restricciones de `open_basedir` adecuadas, aunque el uso del prefijo `_tab_` y la extensión `.php` limita parcialmente el vector.
- **Recomendación:** Implementar una lista blanca estricta de pestañas permitidas (`['school', 'appearance', 'mail', 'security', 'protocol', 'ccaa']`) en el controlador y asignar un valor por defecto si no coincide.

### Vistas (ej. `app/Views/staff/dashboard.php`)
- **Función:** Interfaz principal para el personal.
- **Problema (ALTO):** *Cross-Site Scripting (XSS) Generalizado.*
- **Explicación técnica:** Se renderiza contenido generado por el usuario (como `$report['content']` o descripciones de seguimientos) sin aplicar sistemáticamente `htmlspecialchars()` en todas las salidas. En Javascript, se inyectan variables en el DOM usando `innerHTML` en lugar de `textContent`.
- **Impacto:** Si un alumno envía un reporte malicioso con etiquetas `<script>`, el código se ejecutará en el navegador del orientador, pudiendo robar su sesión o ejecutar acciones en su nombre.
- **Recomendación:** Aplicar una política de escape de salida estricta (usando `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`) en todas las plantillas y evitar `innerHTML` en favor de la creación segura de nodos o el uso de librerías de renderizado seguras.

### Capa de Acceso a Datos / Servicios Generales
- **Función:** Interacción directa con SQLite.
- **Problema (MEDIO/ALTO):** *Riesgo de Inyección SQL por concatenación.*
- **Explicación técnica:** Aunque la mayoría de consultas usan PDO prepared statements, se han identificado consultas dinámicas en servicios y scripts antiguos que concatenan variables directamente en el string SQL en lugar de usar bind parameters (`?` o `:nombre`).
- **Impacto:** Posible exfiltración o destrucción de la base de datos si las variables provienen de entrada de usuario no saneada.
- **Recomendación:** Auditar todas las llamadas a `$db->query()` y `$db->exec()` y migrarlas a `$db->prepare()` + `$stmt->execute()`.

---

## 2. Evaluación Global y Conclusiones

### Lista de Riesgos más importantes del sistema
1. **Compromiso de Autorización:** Backdoor `Ceuta2000` en UpdateController.
2. **Caída del Servicio (DDoS Lógico):** Fatal errors no capturados en el protocolo de Aragón.
3. **Robo de Sesiones / Escalada de Privilegios:** Vulnerabilidades XSS en los paneles de staff.
4. **Manipulación de Expedientes (Integridad):** Falta de protección CSRF en flujos legales autonómicos.

### Puntuaciones de Auditoría
- **Evaluación General de Seguridad:** **3 / 10**
  *(A pesar de tener middlewares y CSRF habilitado, los fallos sistemáticos al aplicarlos y la presencia de backdoors y LFI reducen drásticamente la puntuación. No es apto para producción en su estado actual).*
- **Evaluación de Calidad del Código:** **4 / 10**
  *(Arquitectura base razonable MVC, pero con alta deuda técnica, inconsistencia en el diseño de protocolos —factory vs herencia— y falta de pruebas que validen la existencia de métodos).*

### Recomendaciones Prioritarias (Plan de Acción Inmediato)
1. **Borrar Secretos:** Eliminar el backdoor de `UpdateController`.
2. **Parchear Rutas y Modelos:** Corregir los errores de nombre de métodos en `AragonProtocolController` para restaurar su funcionalidad.
3. **Asegurar Mutaciones:** Imponer validación de token CSRF de manera obligatoria a nivel del enrutador central (`Router.php`) para todas las peticiones que no sean GET/OPTIONS.
4. **Saneamiento Defensivo:** Revisar todas las vistas de renderizado (staff y alumnos) e implementar el escapado HTML estricto antes de volcar variables al DOM. Saneamiento estricto de la variable `$tab` en los ajustes.
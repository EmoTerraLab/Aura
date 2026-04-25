# Manual de Usuario - Aura (Santuario Digital Escolar)

Bienvenido a **Aura**, la plataforma digital diseñada para mejorar la convivencia escolar, proporcionando un espacio seguro, confidencial y rápido donde los estudiantes pueden expresar sus preocupaciones, y donde el equipo educativo puede gestionar y resolver estas situaciones de forma colaborativa.

Este manual está dividido por los diferentes perfiles de usuario que interactúan con la plataforma.

---

## 🎓 1. Guía para Estudiantes (Alumnos)

Aura es tu **Espacio Seguro**. Si tienes un problema, has visto algo que no está bien, o simplemente necesitas hablar con alguien del equipo de orientación o tus profesores, este es el lugar.

### 1.1. Cómo acceder a Aura
1. Entra a la página web de Aura proporcionada por tu colegio.
2. En la pantalla principal, selecciona la pestaña **"Estudiante"**.
3. Introduce tu **Correo Institucional** (el que te ha dado el colegio) y pulsa **Continuar**.
4. Recibirás un correo con un **código de 6 dígitos (OTP)**. Introdúcelo en la pantalla para entrar.
   *Nota: No necesitas recordar ninguna contraseña, tu correo es tu llave de acceso.*

### 1.2. Cómo crear un nuevo aviso (Reporte)
Una vez dentro de tu panel (Dashboard):
1. Verás la sección **"¿Qué pasó?"**. Escribe detalladamente lo que ha ocurrido. Tómate tu tiempo, el sistema te pedirá al menos 5 letras para poder avanzar.
2. Pulsa **Siguiente Paso**.
3. **Detalles del aviso**:
   - Selecciona a quién le ha pasado: *A mí mismo* o *A otra persona*.
   - Indica el nivel de urgencia: *Baja Prioridad*, *Seguimiento* o *Alta Prioridad*.
4. **Anonimato**: Por defecto, la opción **"Enviar de forma anónima"** está marcada. 
   - Si la dejas marcada, tus profesores solo verán "Alumno Anónimo". Solo el equipo de soporte especializado (orientadores/dirección) sabrá quién eres si es estrictamente necesario para ayudarte.
5. Pulsa **Enviar Mensaje Seguro**. Verás una pantalla de confirmación.

### 1.3. Seguimiento de tus avisos
En la parte derecha (o debajo en móviles) verás la sección **"Tus Avisos"**.
- Haz clic en cualquier aviso para abrir el **Chat Seguro**.
- Podrás ver si tu caso está:
  - **🔵 Recibido**: El colegio lo ha recibido pero aún no lo ha revisado.
  - **🟡 En revisión**: Un profesional está trabajando en ello.
  - **🟢 Resuelto**: El caso se ha cerrado.
- **Chat**: Puedes enviar mensajes adicionales a los profesores a través de la caja de texto en la parte inferior del chat, creando una conversación fluida y privada.
- **Resolución**: Cuando un caso se marque como "Resuelto", verás un mensaje verde en la parte superior con la "Resolución Final" oficial escrita por el colegio. Ya no podrás enviar más mensajes en ese hilo.

---

## 🏫 2. Guía para el Equipo Educativo (Staff / Profesores / Orientadores)

El panel del Staff está diseñado para gestionar las incidencias de manera serena, ordenada y colaborativa.

### 2.1. Acceso y Roles
1. En la pantalla de login, selecciona la pestaña **"Personal"**.
2. Introduce tu correo y tu contraseña.
3. El sistema te mostrará información diferente dependiendo de tu rol:
   - **Tutor / Profesor**: Solo verás los avisos de los alumnos que pertenecen a las aulas donde tú eres el tutor asignado. Si un alumno ha marcado "Anónimo", no verás su nombre, solo "Alumno Anónimo".
   - **Orientador / Dirección**: Tienes acceso a todos los casos del colegio y puedes ver la identidad real de los alumnos (marcada con la etiqueta "Identidad Oculta" si el alumno pidió anonimato).

### 2.2. Gestión de Casos (Bandeja de Entrada)
- **Panel Izquierdo**: Muestra la lista de reportes activos. Puedes filtrar por "Todos" o "No leídos", y usar el buscador.
- **Panel Derecho**: Al hacer clic en un caso, verás todos los detalles: relato original, urgencia, objetivo y el **Chat Seguro**.

### 2.3. Comunicación y Notas Internas (Colaboración)
En la parte inferior del chat puedes escribir respuestas. Tienes dos formas de comunicarte:
1. **Mensaje Público**: Escribes y le das a enviar. El alumno lo leerá en su panel.
2. **🔒 Nota Interna (Solo Staff)**: Marca la casilla "Marcar como nota interna". Este mensaje aparecerá en color amarillo y **nunca será visible para el alumno**. Sirve para debatir el caso con otros profesores u orientadores.

### 2.4. Sistema de Menciones (@)
Si necesitas la ayuda de un compañero (por ejemplo, el orientador) en un caso específico:
1. En la caja de chat, empieza a escribir un símbolo **`@`**.
2. Aparecerá un menú desplegable con todo el personal del colegio.
3. Selecciona al compañero (ej. `@Lucia`). El sistema marcará automáticamente el mensaje como **Nota Interna**.
4. Tu compañero recibirá una notificación (un punto rojo en la campana superior de su pantalla) y podrá acceder directamente al caso.

### 2.5. Cambio de Estados y Resolución
En la parte superior derecha del caso, puedes cambiar el estado (`Recibido` -> `En Revisión`).
- **Cerrar un caso**: Si cambias el estado a **"Resuelto"**, el sistema te exigirá que escribas un **Resumen de Resolución Formal** (mínimo 5 caracteres). 
- Este resumen será lo que el alumno lea como respuesta final y conclusión de su solicitud de ayuda.

---

## ⚙️ 3. Guía para Administradores

El Administrador es responsable de gestionar las cuentas de usuario y la estructura básica (Aulas) de la plataforma Aura.

### 3.1. Panel de Control (Dashboard)
Al iniciar sesión como Administrador, accederás a una vista general con:
- Total de Usuarios.
- Total de Aulas.
- Total de Avisos Registrados.

### 3.2. Gestión de Usuarios
En la pestaña **Usuarios**:
- Puedes ver una lista de todos los estudiantes y miembros del personal.
- **Crear un Usuario**: Pulsa "+ Nuevo Usuario".
  - Completa el Nombre y Correo.
  - Asigna el Rol (`alumno`, `profesor`, `orientador`, `direccion`, `admin`).
  - **Importante (Alumnos)**: Si seleccionas el rol "Alumno", aparecerá un desplegable para **asignarle un Aula**. Todo alumno debe tener un aula asignada para que su tutor reciba los avisos.
  - *Las contraseñas de los alumnos se pueden dejar en blanco (usan código OTP).*

### 3.3. Gestión de Aulas
En la pestaña **Aulas**:
- Pulsa "+ Nueva Aula".
- Escribe el nombre (ej. "3º ESO B").
- **Tutor Asignado**: Selecciona de la lista qué Profesor será el responsable de recibir y gestionar los avisos de esa clase concreta.

---

## 🔒 4. Privacidad y Seguridad (Información para Padres y Comunidad)

Aura está construida bajo los más estrictos estándares de privacidad (RGPD y normativas educativas):

- **Ausencia de Contraseñas para Alumnos**: Los estudiantes no necesitan recordar contraseñas, minimizando el riesgo de robo de cuentas. El acceso se realiza mediante un código temporal de un solo uso (OTP) enviado a su correo institucional.
- **Anonimato Garantizado**: Si un estudiante elige la opción anónima, el sistema oculta su nombre criptográficamente a nivel de servidor frente a sus tutores directos, evitando represalias inmediatas o exposición no deseada en el aula.
- **Auditoría Inmutable**: Todos los mensajes (públicos y notas internas) y cambios de estado no pueden ser borrados ni editados por nadie, ni siquiera por el administrador, garantizando un registro transparente de cómo el colegio actuó ante cada situación.
- **Aislamiento de Datos**: Las bases de datos operan de manera local (o bajo el control del centro), y los apuntes internos de los profesionales de la salud mental o tutores están técnicamente bloqueados para que nunca lleguen al dispositivo del estudiante.

# Informe de Seguridad y Auditoría CSRF - 2026-05-05

## 1. Descripción del Problema
Los usuarios, incluyendo alumnos (`alumno@aura.test`) y personal administrativo, experimentan un error persistente: **"CSRF token inválido o expirado"** al intentar iniciar sesión. Este error ocurre sistemáticamente en varios dispositivos y navegadores, bloqueando completamente el acceso al sistema.

## 2. Diagnóstico Técnico
Tras analizar el flujo de autenticación y la gestión de sesiones en el backend, el problema radica en una **desincronización del token CSRF entre el cliente y el servidor** provocada por una rotación agresiva del token.

### Causa Raíz:
1. En el archivo `app/Core/Csrf.php` (línea 53), el método `validateRequest()` incluye la instrucción `self::regenerate()`, lo que significa que el token CSRF **se regenera y cambia en la sesión tras cada validación exitosa**.
2. El flujo de inicio de sesión de alumnos mediante OTP (One-Time Password) en `login.php` requiere **dos peticiones POST AJAX consecutivas**:
   - Petición 1: `/login/otp/generate` (Genera y envía el código OTP).
   - Petición 2: `/login/otp/verify` (Verifica el código introducido).
3. La función `fetchJson()` de la vista lee el token estático desde la etiqueta `<meta name="csrf-token">` generada al cargar la página.
4. **Fallo en la máquina de estados:** Al ejecutar la Petición 1, el servidor valida el token correctamente y **lo rota**. Cuando el cliente ejecuta la Petición 2, envía el token antiguo. El servidor lo compara con el nuevo token rotado, la validación falla y devuelve el error `403 CSRF token inválido o expirado`.

Este patrón "Per-Request Token" también provoca fallos de concurrencia: si el usuario abre la aplicación en múltiples pestañas o si la página realiza múltiples peticiones AJAX simultáneas, solo la primera funcionará.

## 3. Soluciones Propuestas

Existen dos enfoques técnicos para solucionar esta vulnerabilidad arquitectónica. Se recomienda implementar la **Solución A**.

### Solución A: Rotación Basada en Sesión (Recomendado)
Los tokens CSRF deben estar vinculados a la vida de la sesión (Per-Session) y solo deben rotar cuando cambia el estado de los privilegios del usuario (login / logout) para prevenir vulnerabilidades de fijación de sesión, no en cada petición.

**Paso 1:** En `app/Core/Csrf.php`, eliminar la rotación automática en cada validación.
```diff
  public static function validateRequest() {
      // ... validación ...
      if (!self::validateToken($token)) {
          http_response_code(403);
          echo json_encode(['error' => 'CSRF token inválido o expirado.']);
          exit;
      }
-     // SEC-010 FIX: Rotar token después de validación exitosa para prevenir replay
-     self::regenerate();
  }
```

**Paso 2:** Rotar el token únicamente en `Session::regenerate()`, que debe ser invocado explícitamente tras un inicio de sesión exitoso o al cerrar sesión.

### Solución B: Sincronización Estricta (Per-Request)
Si por requisitos normativos es obligatorio mantener la rotación por petición, el backend debe devolver el nuevo token y el cliente debe actualizar el DOM.

**Backend (`Csrf.php`):**
```php
public static function validateRequest() {
    // ... validación ...
    self::regenerate();
    header('X-CSRF-TOKEN: ' . self::generateToken()); // Enviar el nuevo token al cliente
}
```

**Frontend (`app/Views/layouts/app.php` - `fetchJson`):**
```javascript
const res = await fetch(url, /* ... */);
const newToken = res.headers.get('X-CSRF-TOKEN');
if (newToken) {
    document.querySelector('meta[name="csrf-token"]').content = newToken;
}
```

## 4. Auditoría General de Seguridad (Autenticación y Sesiones)

Tras la revisión del módulo de autenticación, se detectan los siguientes puntos críticos y buenas prácticas:

*   ✅ **Protección XSS:** El sistema utiliza escape automático en vistas y `httponly` en cookies de sesión.
*   ✅ **Defensas de Fuerza Bruta:** El sistema cuenta con mitigación mediante validación MFA y límites de ratio (Rate Limit por IP/Identificador).
*   ✅ **SameSite Headers:** Configurado correctamente a `Lax` en `Session.php`, lo cual protege contra CSRF basado en navegación cruzada.
*   ⚠️ **Fijación de Sesión (Session Fixation):** Es imprescindible asegurarse de que `Session::regenerate()` es llamado en el controlador **inmediatamente después de autenticar al usuario** de forma exitosa (antes de establecer los valores de usuario en `$_SESSION`).
*   ⚠️ **Detección de HTTPS e IP Spoofing:** En `Session.php`, la evaluación de `$_SERVER['HTTP_X_FORWARDED_PROTO']` debe realizarse únicamente si la petición proviene de proxies de confianza (reverse proxies) para evitar ataques de manipulación de cabeceras.
*   ⚠️ **Expiración Inactiva (Timeout):** Se recomienda añadir un mecanismo para invalidar la sesión tras X minutos de inactividad absoluta, forzando un nuevo login, ideal en entornos escolares compartidos.

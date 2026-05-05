# Informe de Seguridad - 2026-05-05

## Problema
Se ha reportado un error recurrente de **'CSRF token inválido o expirado'** durante el proceso de inicio de sesión, afectando especialmente al flujo de alumnos con OTP (One-Time Password) y, ocasionalmente, al personal docente. El problema persiste en múltiples dispositivos, lo que descarta un fallo local de caché y apunta a una desincronización en la gestión de sesiones o en la lógica del backend.

## Diagnóstico Técnico
Tras auditar el código fuente del sistema de autenticación y protección CSRF, se han identificado las siguientes causas raíz:

1.  **Desincronización por Rotación Agresiva (Causa Principal):**
    En `App\Core\Csrf::validateRequest()`, el sistema está configurado para regenerar el token CSRF inmediatamente después de cada validación exitosa (`self::regenerate()`).
    *   **Flujo Alumno:** Se realizan dos peticiones POST consecutivas: `generateOTP` y `verifyOTP`.
    *   La primera petición valida el Token A correctamente, pero el servidor genera automáticamente el Token B en la sesión.
    *   El navegador, que aún tiene el Token A en su etiqueta `<meta>`, envía de nuevo el Token A para la segunda petición.
    *   El servidor rechaza la segunda petición por desajuste con el nuevo Token B almacenado en la sesión.

2.  **Inestabilidad de Sesiones en Entorno Docker:**
    La configuración actual de `docker-compose.yml` no persiste el directorio de sesiones de PHP (habitualmente `/tmp` o `/var/lib/php/sessions` en Alpine). Si el contenedor de FrankenPHP se reinicia o se recicla por el orquestador, todas las sesiones activas se pierden instantáneamente, invalidando los tokens CSRF de los formularios cargados en los navegadores.

3.  **Detección de HTTPS Inconsistente:**
    La clase `App\Core\Session` marca la cookie como `Secure` basándose en `$_SERVER['HTTPS']`. Si el servidor está detrás de un proxy que termina SSL pero no pasa correctamente las cabeceras `X-Forwarded-Proto`, la cookie podría marcarse como segura en una conexión que el navegador percibe como insegura, o viceversa, provocando que el navegador no envíe la cookie de sesión de vuelta.

## Solución Propuesta

### 1. Corrección en el Backend (Csrf.php)
Deshabilitar la rotación automática en cada validación y delegarla a acciones críticas (Login/Logout), o permitir validaciones sin regeneración forzada.

```php
// En app/Core/Csrf.php
public static function validateRequest($regenerate = false) {
    // ... lógica de obtención de token ...
    if (!self::validateToken($token)) {
        // ... error 403 ...
    }
    if ($regenerate) {
        self::regenerate();
    }
}
```

### 2. Actualización de fetchJson (app/Views/layouts/app.php)
Implementar una cabecera de respuesta que permita al servidor enviar el nuevo token y que el frontend lo actualice dinámicamente.

### 3. Persistencia de Sesiones (docker-compose.yml)
Mapear el directorio de almacenamiento de sesiones para evitar pérdidas por reinicio.

```yaml
volumes:
  - ./storage/sessions:/tmp # O la ruta configurada en php.ini
```

## Recomendaciones de Seguridad (Auditoría)

1.  **Configuración de Cookies:** Se recomienda mantener `SameSite=Lax` y `HttpOnly=true` (actualmente implementado).
2.  **Headers de Seguridad:** Implementar `Strict-Transport-Security` (HSTS) para forzar el uso de HTTPS.
3.  **Rate Limiting:** Se ha observado que el sistema ya implementa un sistema de rate limiting en `AuthController.php`, lo cual es correcto. Se recomienda mover esta lógica a un Middleware global.
4.  **Auditoría de Logs:** Los logs de auditoría en `AuditLogger` deben revisarse periódicamente para detectar intentos de fuerza bruta en el OTP.
5.  **Estándares Regionales:** Asegurar que el tratamiento de datos cumple con el **ENS (Esquema Nacional de Seguridad)** de nivel medio, dado que se maneja información sensible de menores.

---
*Firma: Ingeniero Senior en Ciberseguridad - Aura Project*

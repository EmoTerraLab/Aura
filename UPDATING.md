# Guía de publicación de actualizaciones — Aura

## Para el desarrollador

### 1. Crear una migración nueva
Crea un archivo en `database/migrations/` siguiendo el formato `YYYY_MM_DD_HHMMSS_descripcion.php`.

Ejemplo de estructura:
```php
<?php
class Migration_YYYY_MM_DD_HHMMSS_descripcion
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function up(): void {
        $this->db->exec("...");
    }
    public function down(): void {}
}
```

### 2. Probar la migración en local
```bash
php migrate.php status    # Ver que aparece como pendiente
php migrate.php run       # Ejecutar
php migrate.php integrity # Verificar
```

### 3. Actualizar la versión en composer.json
Incrementar semver: MAJOR.MINOR.PATCH en el campo "version".

### 4. Subir el código al servidor del cliente
Sube los archivos excluyendo `.env`, `database/*.sqlite` y `database/backups/`.

---

## Historial de Versiones Recientes

### v2.13.1 — 2026-05-01
- **Estabilización Nacional**: Restauración de funcionalidades de Aragón (plazos legales y módulo restaurativo) y normalización de códigos CCAA (ARA, CAT, MAD, etc.).
- **Limpieza de Marca**: Unificación definitiva a "Aura", eliminando referencias a pre-producción (PDP) en el núcleo y comunicaciones.
- **Producción**: Endurecimiento de seguridad (hardening) y corrección de integridad en rutas.

### v2.13.0 — 2026-05-01
- **Expansión Nacional Multi-CCAA**: Soporte para las 19 comunidades y ciudades autónomas de España mediante una arquitectura modular.
- **Seguridad**: Implementación de cambio de contraseña para personal.

### v2.12.0 — 2026-04-30
- **Alertas Legales**: Implementación de alertas de violencia sexual en Aragón y Cataluña.
- **Refactorización de Timeline**: Lógica de pasos activos movida al backend para consistencia total.

### v2.11.0 — 2026-04-29
- **Sincronización PDP**: Integración inicial de cambios desde la rama de preproducción.

---

## Para el administrador del cliente

1. Ve a **Administración → Actualizaciones del sistema**.
2. Verifica las migraciones pendientes.
3. Haz clic en **Ejecutar actualización**.
4. El sistema gestionará el backup, las migraciones y la integridad automáticamente.
5. Si ocurre un error, se restaurará el backup y el sistema permanecerá en mantenimiento por seguridad.

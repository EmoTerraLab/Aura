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

### v2.11.0 — 2026-04-29
- **Refactorización de Timeline**: Lógica de cálculo de pasos activos movida al backend (`ProtocolController`) para mayor consistencia en la UI.
- **Seguridad y Alertas**: Implementación de alertas de protocolos de violencia sexual para Aragón y Cataluña.
- **Optimización de Base de Datos**: Añadidos índices de rendimiento y gestión de logs de acceso.
- **Sincronización PDP**: Integración de cambios estables provenientes de la rama de preproducción.

### v2.10.0
- **Arquitectura Multi-CCAA**: Refactorización del sistema de protocolos mediante `ProtocolInterface` y `ProtocolFactory`.
- **Protocolo Aragón**: Implementación completa del flujo legal de Aragón (Anexos I a X).

### v2.9.0
- **Módulo Restaurativo**: Gestión de reconocimientos y prácticas restaurativas.

---

## Para el administrador del cliente

1. Ve a **Administración → Actualizaciones del sistema**.
2. Verifica las migraciones pendientes.
3. Haz clic en **Ejecutar actualización**.
4. El sistema gestionará el backup, las migraciones y la integridad automáticamente.
5. Si ocurre un error, se restaurará el backup y el sistema permanecerá en mantenimiento por seguridad.

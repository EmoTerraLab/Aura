# Guía de publicación de actualizaciones — Aura

## Para el desarrollador

### 1. Crear una migración nueva
Crea un archivo en `database/migrations/` siguiendo el formato `YYYY_MM_DD_HHMMSS_descripcion.php`.

### 2. Probar la migración en local
```bash
php migrate.php status    # Ver que aparece como pendiente
php migrate.php run       # Ejecutar
php migrate.php integrity # Verificar
```

### 3. Actualizar la versión
Incrementar semver en `VERSION` y `composer.json`.

---

## Historial de Versiones Recientes

### v2.14.0 — 2026-05-01
- **Consolidación PDP**: Sincronización completa con el motor de preproducción.
- **Protocolo de Aragón**: Actualización de flujos legales y anexos oficiales (I-X).
- **Internacionalización**: Restauración del sistema i18n en módulos de protocolo.
- **Documentación**: Nuevo README.md integral y bilingüe.

### v2.13.8
- **Pulido de UX**: Vinculación de acciones del Dashboard con flujos legales.
- **Hardening**: Eliminación de logs y unificación de marca.

---

## Para el administrador del cliente

1. Ve a **Administración → Actualizaciones del sistema**.
2. Verifica las migraciones pendientes.
3. Haz clic en **Ejecutar actualización**.

# Guía de Implementación de Protocolos de Acoso (CCAA)

Este documento sirve como referencia para solicitar la implementación de nuevos protocolos legales de Comunidades Autónomas en **Aura**. Para que la IA pueda generar el código de forma precisa, se debe seguir la estructura y los códigos definidos a continuación.

---

## 1. Códigos Identificadores de CCAA

Utiliza estos códigos exactos (en minúsculas y sin espacios) al referirte a una comunidad:

| Comunidad Autónoma | Código |
| :--- | :--- |
| Andalucía | `andalucia` |
| Aragón | `aragon` |
| Asturias | `asturias` |
| Islas Baleares | `baleares` |
| Canarias | `canarias` |
| Cantabria | `cantabria` |
| Castilla-La Mancha | `castilla_la_mancha` |
| Castilla y León | `castilla_leon` |
| Cataluña | `cataluna` |
| Ceuta | `ceuta` |
| Comunidad Valenciana | `comunidad_valenciana` |
| Extremadura | `extremadura` |
| Galicia | `galicia` |
| Madrid | `madrid` |
| Melilla | `melilla` |
| Murcia | `murcia` |
| Navarra | `navarra` |
| País Vasco | `pais_vasco` |
| La Rioja | `rioja` |

---

## 2. Cómo explicar un protocolo

Para una implementación exitosa, el prompt debe incluir:

1.  **Identificación**: Nombre de la CCAA y su código.
2.  **Fases Cronológicas**: Una lista ordenada de los pasos legales.
    *   Nombre del paso.
    *   Plazo máximo (en días naturales o lectivos).
    *   Responsable (Director, Orientador, Tutor, etc.).
3.  **Acciones y Documentación**: Qué anexos o formularios se requieren en cada paso.
4.  **Lógica de Decisión**: Qué pasa si el centro decide que "SÍ hay indicios" vs "NO hay indicios".
5.  **Alertas**: Cuándo debe saltar un aviso visual de retraso.

---

## 3. Ejemplo de Prompt Detallado

Copia y adapta este ejemplo cuando quieras implementar una nueva CCAA:

> **PROMPT DE EJEMPLO:**
>
> "Quiero implementar el protocolo de **[NOMBRE_CCAA]** con código `[CODIGO_CCAA]`. 
>
> El flujo de trabajo es el siguiente:
>
> 1. **Fase de Comunicación (Día 0)**: 
>    - Acciones: El tutor recibe el aviso y tiene 24h para informar a dirección. 
>    - Responsable: Tutor.
>
> 2. **Valoración Inicial (Máximo 2 días)**:
>    - Acciones: Reunión del equipo de convivencia. Hay que rellenar el **Anexo I (Recogida de información)**.
>    - Decisión: Si no hay indicios, se cierra (Anexo II). Si hay indicios, se pasa a la Fase 3.
>    - Responsable: Orientador y Dirección.
>
> 3. **Medidas Urgentes (Inmediato)**:
>    - Acciones: Aplicar el **Mapa de Seguridad** para proteger a la víctima.
>
> 4. **Intervención y Seguimiento (Días 3 a 15)**:
>    - Acciones: Entrevistas con familias (Anexo III).
>    - Responsable: Equipo Directivo.
>
> 5. **Cierre y Informe Final (Día 30)**:
>    - Acciones: Redacción del informe REVA y envío a Inspección.
>
> Por favor, genera la clase `[CODIGO_CCAA]Protocol.php` en el Service de Protocolos, actualiza el `ProtocolFactory` y crea las migraciones o vistas necesarias para los anexos mencionados."

---

## 4. Notas Técnicas para el Usuario

*   **Anexos**: Si el protocolo tiene PDFs oficiales, menciónalos para que la IA cree las plantillas de exportación.
*   **Bypass**: Si existen casos especiales (ej. Violencia Sexual) que saltan pasos, especifícalo.
*   **Roles**: Asegúrate de que los roles que mencionas existan en el sistema (`admin`, `direccion`, `orientador`, `profesor`, `alumno`).
